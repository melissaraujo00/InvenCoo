<?php

namespace App\Http\Controllers;

use App\Actions\Transfer\CreateTransferAction;
use App\Actions\Transfer\ReceiveTransferAction;
use App\Actions\Transfer\ShipTransferAction;
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
        $role = $user->getRoleNames()->first();

        if ($role === 'Administrador') {
            $transfers = Transfer::with(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product'])
                ->orderBy('creation_date', 'desc')
                ->paginate(15);
            return view('pages.transfers.index', compact('transfers'));
        }

        if ($role === 'Bodega') {
            $pendingShipments = Transfer::with(['destinationBranch', 'requestingUser', 'details.product'])
                ->where('originating_branch', $user->office_id)
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
        $destinationOffice = Office::find($user->office_id);

        $products = Product::all();
        $products->each(function ($product) use ($sourceOffice) {
            $lastMovementDetail = MovementDetail::where('product_id', $product->id)
                ->whereHas('movement', function ($query) use ($sourceOffice) {
                    $query->where('office_id', $sourceOffice->id);
                })
                ->orderBy('id', 'desc')
                ->first();

            // CORRECCIÓN: Si no hay movimientos, leemos el stock general como fallback preventivo
            $product->available_stock = $lastMovementDetail ? $lastMovementDetail->stock_after : ($product->stock ?? 0);
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

        app(CreateTransferAction::class)->execute(
            $validated['products'], $request->user()->id, $request->user()->office_id
        );

        return redirect()->route('transfers.index')->with('success', 'Solicitud creada. El administrador ha sido notificado.');
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

        try {
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

                    // Validación de stock real con bloqueo
                    $product = Product::lockForUpdate()->find($detail->product_id);
                    if ($sent > $product->stock) {
                        throw new \Exception("No hay stock suficiente de {$product->name} en la cooperativa para aprobar {$sent} unidades. Stock disponible: {$product->stock}.");
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

                // Notificaciones
                $requester = User::find($transfer->requesting_user);
                if ($requester) {
                    $requester->notify(new TransferApproved($transfer));
                }

                $bodegas = User::role('Bodega')->where('office_id', $transfer->originating_branch)->get();
                foreach ($bodegas as $bodega) {
                    $bodega->notify(new TransferReadyToShip($transfer));
                    if ($bodega->number) {
                        $bodega->notify(new TransferWhatsappNotification($transfer, 'transfer_approved', [(string) $transfer->id, route('transfers.show', $transfer)]));
                    }
                }
            });
        } catch (\Exception $e) {
            // Si la excepción estalla, la atrapamos aquí, abortamos la transacción de DB
            // y redireccionamos al usuario con el mensaje de error para que lo corrija.
            return redirect()->route('transfers.show', $transfer)->with('error', $e->getMessage());
        }

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia procesada y validada correctamente.');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product']);
        $user = Auth::user();
        $canApprove = $user->hasRole('Administrador') && $transfer->status === StatusEnum::PENDING;
        $canShip = $user->hasRole('Bodega') && $user->office_id == $transfer->originating_branch && in_array($transfer->status, [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED]);
        $canReceive = $user->hasRole('Administrador Restaurante') && $user->office_id == $transfer->destination_branch && $transfer->status === StatusEnum::SHIPPED;

        return view('pages.transfers.show', compact('transfer', 'canApprove', 'canShip', 'canReceive'));
    }

    public function ship(Transfer $transfer)
    {
        $user = Auth::user();
        if (!$user->hasRole('Bodega') || $user->office_id != $transfer->originating_branch || !in_array($transfer->status, [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])) {
            abort(403, 'No autorizado.');
        }

        app(ShipTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia enviada con costos registrados.');
    }

    public function receive(Transfer $transfer)
    {
        $user = Auth::user();
        if (!$user->hasRole('Administrador Restaurante') || $user->office_id != $transfer->destination_branch || $transfer->status !== StatusEnum::SHIPPED) {
            abort(403, 'No autorizado.');
        }

        app(ReceiveTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.index')->with('success', 'Transferencia recibida e ingresada al Kardex local con éxito.');
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
