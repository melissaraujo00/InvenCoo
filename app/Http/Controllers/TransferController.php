<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Models\Office;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Type;
use App\Models\User;
use App\Notifications\TransferApproved;
use App\Notifications\TransferRequested;
use App\Notifications\TransferWhatsappNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WhatsApp\Component;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $transfers = Transfer::with(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product'])
            ->when($user->hasRole('Restaurante'), function ($q) use ($user) {
                // El restaurante solo ve sus solicitudes (desde su oficina)
                $q->where('requesting_user', $user->id);
            })
            ->when($user->hasRole('Bodega'), function ($q) use ($user) {
                // La bodega ve las transferencias donde su oficina es origen o destino
                $q->where('originating_branch', $user->office_id)
                  ->orWhere('destination_branch', $user->office_id);
            })
            // Admin ve todo
            ->orderBy('creation_date', 'desc')
            ->paginate(15);

        return view('pages.transfers.index', compact('transfers'));
    }

    public function create()
{
    $user = Auth::user();
    if (!$user->hasRole('Administrador Restaurante')) {
        abort(403, 'No autorizado.');
    }

    $originOffices = Office::where('id', $user->office_id)->get();
    $destinationOffices = Office::where('id', '!=', $user->office_id)->get();

    // Obtener todos los productos (globales)
    $products = Product::all();

    // Para cada producto, calcular el stock disponible en la oficina origen
    $products->each(function ($product) use ($user) {
        // Obtener el último movimiento de ese producto en la oficina origen
        $lastMovementDetail = MovementDetail::where('product_id', $product->id)
            ->whereHas('movement', function ($query) use ($user) {
                $query->where('office_id', $user->office_id);
            })
            ->orderBy('id', 'desc')
            ->first();

        $product->available_stock = $lastMovementDetail ? $lastMovementDetail->stock_after : 0;
    });

    return view('pages.transfers.create', compact('originOffices', 'destinationOffices', 'products'));
}

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'destination_branch' => 'required|exists:offices,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $user) {
            $transfer = Transfer::create([
                'originating_branch' => $user->office_id,
                'destination_branch' => $validated['destination_branch'],
                'requesting_user' => $user->id,
                'creation_date' => now(),
                'status' => StatusEnum::PENDING,
            ]);
            foreach ($validated['products'] as $item) {
                TransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'quantity_sent' => 0,
                ]);
            }

            // === NOTIFICACIÓN INTERNA a todos los administradores ===
            $admins = User::role('Administrador')->get();
            Notification::send($admins, new TransferRequested($transfer));

            // === NOTIFICACIÓN WHATSAPP (ya existente) ===
            $url = route('transfers.show', $transfer);
            foreach ($admins as $admin) {
                if ($admin->number) {
                    $admin->notify(new TransferWhatsappNotification(
                        $transfer,
                        'transfer_request_admin',
                        [
                            (string) $transfer->id,
                            (string) route('transfers.show', $transfer)
                        ]
                    ));
                }
            }
        });

        return redirect()->route('transfers.index')->with('success', 'Solicitud creada. Se ha notificado al administrador.');
    }

    public function approve(Request $request, Transfer $transfer)
    {
         if (!Auth::user()->hasRole('Administrador') || $transfer->status !== StatusEnum::PENDING) {
        abort(403, 'No autorizado.');
        }
        $validated = $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'required|exists:transfer_details,id',
            'details.*.quantity_sent' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($transfer, $validated) {
            $allApproved = true;
            $anyPartial = false;

            foreach ($validated['details'] as $item) {
                $detail = TransferDetail::findOrFail($item['id']);
                $requested = $detail->quantity_requested;
                $sent = $item['quantity_sent'];

                if ($sent > $requested) {
                    throw new \Exception('La cantidad enviada no puede superar la solicitada.');
                }
                $detail->update(['quantity_sent' => $sent]);

                if ($sent == 0) $allApproved = false;
                if ($sent > 0 && $sent < $requested) $anyPartial = true;
            }

            $status = StatusEnum::APPROVED;
            if (!$allApproved && !$anyPartial) $status = StatusEnum::REJECTED;
            elseif ($anyPartial) $status = StatusEnum::PARTIALLY_APPROVED;

            $transfer->update([
                'user_authorizes' => Auth::id(),
                'status' => $status,
            ]);

            // === NOTIFICACIÓN INTERNA al solicitante ===
            $requester = User::find($transfer->requesting_user);
            if ($requester) {
                $requester->notify(new TransferApproved($transfer));
            }

            // Aquí podrías enviar también notificaciones WhatsApp (opcional)
        });

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia procesada.');
    }


    public function show(Transfer $transfer)
    {
        $transfer->load(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product']);
        $user = Auth::user();
        $canApprove = $user->hasRole('Administrador') && $transfer->status === StatusEnum::PENDING;
        $canShip = $user->hasRole('Bodega') && $user->office_id == $transfer->originating_branch && in_array($transfer->status, [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED]);
        $canReceive = $user->hasRole('Bodega') && $user->office_id == $transfer->destination_branch && $transfer->status === StatusEnum::SHIPPED;

        return view('pages.transfers.show', compact('transfer', 'canApprove', 'canShip', 'canReceive'));
    }



    public function ship(Transfer $transfer)
    {
         $user = Auth::user();
    if (!$user->hasRole('Bodega') || $user->office_id != $transfer->originating_branch || !in_array($transfer->status, [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])) {
        abort(403, 'No autorizado.');
    }

        DB::transaction(function () use ($transfer) {
            // Crear movimiento de salida (S) en la oficina origen
            $outMovement = Movement::create([
                'office_id' => $transfer->originating_branch,
                'date_movement' => now(),
                'type_id' => Type::where('name', 'transferencia_salida')->first()->id,
                'user_id' => Auth::id(),
                'transaction_id' => 'TRF-OUT-' . $transfer->id,
                'description' => 'Salida por transferencia #' . $transfer->id,
                'input_type' => 'S',
                'origin_office_id' => $transfer->originating_branch,
                'destination_office_id' => $transfer->destination_branch,
            ]);

            foreach ($transfer->details as $detail) {
                $qty = $detail->quantity_sent;
                if ($qty > 0) {
                    // Restar stock en origen
                    $product = $detail->product;
                    $product->decrement('stock', $qty);

                    MovementDetail::create([
                        'movement_id' => $outMovement->id,
                        'product_id' => $detail->product_id,
                        'quantity' => $qty,
                        'unit_price' => 0, // o precio promedio
                        'subtotal' => 0,
                        'stock_after' => $product->stock,
                    ]);
                }
            }

            $transfer->update([
                'shipping_date' => now(),
                'out_movement_id' => $outMovement->id,
                'status' => StatusEnum::SHIPPED,
            ]);
        });

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia enviada.');
    }

    public function receive(Transfer $transfer)
    {
        $user = Auth::user();
    if (!$user->hasRole('Bodega') || $user->office_id != $transfer->destination_branch || $transfer->status !== StatusEnum::SHIPPED) {
        abort(403, 'No autorizado.');
    }

        DB::transaction(function () use ($transfer) {
            // Crear movimiento de entrada (E) en la oficina destino
            $inMovement = Movement::create([
                'office_id' => $transfer->destination_branch,
                'date_movement' => now(),
                'type_id' => Type::where('name', 'transferencia_entrada')->first()->id,
                'user_id' => Auth::id(),
                'transaction_id' => 'TRF-IN-' . $transfer->id,
                'description' => 'Entrada por transferencia #' . $transfer->id,
                'input_type' => 'E',
                'origin_office_id' => $transfer->originating_branch,
                'destination_office_id' => $transfer->destination_branch,
            ]);

            foreach ($transfer->details as $detail) {
                $qty = $detail->quantity_sent;
                if ($qty > 0) {
                    $product = $detail->product;
                    // Si el producto no existe en la oficina destino, se crea o se actualiza stock
                    $destProduct = Product::firstOrCreate(
                        ['code' => $product->code, 'office_id' => $transfer->destination_branch],
                        [
                            'name' => $product->name,
                            'category_id' => $product->category_id,
                            'brand_id' => $product->brand_id,
                            'stock_minimun' => $product->stock_minimun,
                            'unit' => $product->unit,
                            'stock' => 0,
                        ]
                    );
                    $destProduct->increment('stock', $qty);

                    MovementDetail::create([
                        'movement_id' => $inMovement->id,
                        'product_id' => $destProduct->id,
                        'quantity' => $qty,
                        'unit_price' => 0,
                        'subtotal' => 0,
                        'stock_after' => $destProduct->stock,
                    ]);
                }
            }

            $transfer->update([
                'receipt_date' => now(),
                'in_movement_id' => $inMovement->id,
                'status' => StatusEnum::RECEIVED,
            ]);
        });

        return redirect()->route('transfers.index')->with('success', 'Transferencia recibida correctamente.');
    }

    public function reject(Transfer $transfer)
    {
       if (!Auth::user()->hasRole('Administrador') || $transfer->status !== StatusEnum::PENDING) {
        abort(403, 'No autorizado.');
    }
        $transfer->update(['status' => StatusEnum::REJECTED]);
        return redirect()->route('transfers.index')->with('error', 'Transferencia rechazada.');
    }
}
