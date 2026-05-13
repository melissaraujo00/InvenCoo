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
use App\Notifications\TransferReadyToShip;
use App\Notifications\TransferReceived;
use App\Notifications\TransferRequested;
use App\Notifications\TransferWhatsappNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;


class TransferController extends Controller
{
   public function index(Request $request)
{
    $user = Auth::user();
    $role = $user->getRoleNames()->first(); // 'Administrador', 'Administrador Restaurante' o 'Bodega'

    // Administrador ve todo
    if ($role === 'Administrador') {
        $transfers = Transfer::with(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product'])
            ->orderBy('creation_date', 'desc')
            ->paginate(15);
        return view('pages.transfers.index', compact('transfers'));
    }

    // Bodega (Cooperativa) – separar pendientes de envío e historial
    if ($role === 'Bodega') {
        $pendingShipments = Transfer::with(['destinationBranch', 'requestingUser', 'details.product'])
            ->where('originating_branch', $user->office_id) // Cooperativa
            ->whereIn('status', [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])
            ->orderBy('creation_date', 'asc')
            ->get();

        $history = Transfer::with(['destinationBranch', 'requestingUser', 'details.product'])
            ->where('originating_branch', $user->office_id)
            ->whereIn('status', [StatusEnum::SHIPPED, StatusEnum::RECEIVED, StatusEnum::REJECTED])
            ->orderBy('creation_date', 'desc')
            ->get();

        return view('pages.transfers.bodega_index', compact('pendingShipments', 'history'));
    }

    // Administrador Restaurante – solo las transferencias donde su oficina es destino
    $transfers = Transfer::with(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product'])
        ->where('destination_branch', $user->office_id)
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

    $sourceOffice = Office::find(1); // Cooperativa
    $destinationOffice = Office::find($user->office_id); // Restaurante del usuario

    $products = Product::all();
    $products->each(function ($product) use ($sourceOffice) {
        $lastMovementDetail = MovementDetail::where('product_id', $product->id)
            ->whereHas('movement', function ($query) use ($sourceOffice) {
                $query->where('office_id', $sourceOffice->id);
            })
            ->orderBy('id', 'desc')
            ->first();
        $product->available_stock = $lastMovementDetail ? $lastMovementDetail->stock_after : 0;
    });

    return view('pages.transfers.create', compact('sourceOffice', 'destinationOffice', 'products'));
}

    public function store(Request $request)
{
    $user = Auth::user();
    if (!$user->hasRole('Administrador Restaurante')) {
        abort(403);
    }

    $validated = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($validated, $user) {
        // Origen siempre Cooperativa (id=1), destino la oficina del usuario
        $transfer = Transfer::create([
            'originating_branch' => 1,
            'destination_branch' => $user->office_id,
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

        // Notificar a Administradores (Cooperativa)
        $admins = User::role('Administrador')->get();
        Notification::send($admins, new TransferRequested($transfer));

        // Notificación WhatsApp (ya existente)
        foreach ($admins as $admin) {
            if ($admin->number) {
                $admin->notify(new TransferWhatsappNotification($transfer, 'transfer_request_admin', [
                    (string) $transfer->id,
                    route('transfers.show', $transfer)
                ]));
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
            // Notificar al solicitante (ya existente)
            $requester = User::find($transfer->requesting_user);
            if ($requester) {
                $requester->notify(new TransferApproved($transfer));
            }

            // Notificar a los usuarios con rol Bodega en la oficina origen (Cooperativa)
            $bodegas = User::role('Bodega')->where('office_id', $transfer->originating_branch)->get();
            foreach ($bodegas as $bodega) {
                // Notificación interna (base de datos)
                $bodega->notify(new TransferReadyToShip($transfer));

                // Notificación WhatsApp (usando tu clase existente)
                if ($bodega->number) {
                    $bodega->notify(new TransferWhatsappNotification(
                        $transfer,
                        'transfer_approved',  // Nombre de la plantilla en WhatsApp Business
                        [
                            (string) $transfer->id,
                            route('transfers.show', $transfer)
                        ]
                    ));
                }
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
    // Solo Administrador Restaurante puede recibir
    $canReceive = $user->hasRole('Administrador Restaurante') && $user->office_id == $transfer->destination_branch && $transfer->status === StatusEnum::SHIPPED;

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
            'type_id' => Type::where('name', 'Transferencia Salida')->first()->id,
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
                $product = $detail->product;
                $product->decrement('stock', $qty);

                MovementDetail::create([
                    'movement_id' => $outMovement->id,
                    'product_id' => $detail->product_id,
                    'quantity' => $qty,
                    'unit_price' => 0,
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

    // Notificación WhatsApp al solicitante (Administrador Restaurante)
    $requester = User::find($transfer->requesting_user);
    if ($requester && $requester->number) {
        try {
            $requester->notify(new TransferWhatsappNotification(
                $transfer,
                'transfer_ready_for_ship', // Plantilla de WhatsApp (debes crearla)
                [
                    (string) $transfer->id,
                    route('transfers.show', $transfer)
                ]
            ));
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación WhatsApp de envío: ' . $e->getMessage());
        }
    }

    return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia enviada.');
}

    public function receive(Transfer $transfer)
{
    $user = Auth::user();
    // Solo Administrador Restaurante puede recibir
    if (!$user->hasRole('Administrador Restaurante') || $user->office_id != $transfer->destination_branch || $transfer->status !== StatusEnum::SHIPPED) {
        abort(403, 'No autorizado.');
    }

    // Solo actualizamos el estado y la fecha de recepción, sin movimientos ni stock
    $transfer->update([
        'receipt_date' => now(),
        'status' => StatusEnum::RECEIVED,
    ]);

    // Notificación a administradores (opcional)
    $admins = User::role('Administrador')->get();
    foreach ($admins as $admin) {
        if ($admin->number) {
            try {
                $admin->notify(new TransferWhatsappNotification(
                    $transfer,
                    'transfer_received_admi',
                    [(string) $transfer->id, route('transfers.show', $transfer)]
                ));
            } catch (\Exception $e) {
                Log::error('Error al enviar WhatsApp a admin: ' . $e->getMessage());
            }
        }
        $admin->notify(new TransferReceived($transfer)); // si creaste esta notificación
    }

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
