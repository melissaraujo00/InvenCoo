<?php

namespace App\Http\Controllers;

use App\Actions\Transfer\ApproveTransferAction;
use App\Actions\Transfer\CreateTransferAction;
use App\Actions\Transfer\ReceiveTransferAction;
use App\Actions\Transfer\ShipTransferAction;
use App\Enums\StatusEnum;
use App\Exceptions\BusinessRuleException;
use App\Http\Requests\ApproveTransferRequest;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Office;
use App\Models\Product;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TransferController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Bodega')) {


            $pendingShipments = Transfer::with(['destinationBranch', 'requestingUser', 'details.product'])
                ->where('originating_branch', $user->office_id)
                ->whereIn('status', [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED])
                ->orderBy('updated_at', 'desc')
                ->get();

            $transfers = Transfer::forUserContext($user)
                ->whereIn('status', [
                    StatusEnum::SHIPPED,
                    StatusEnum::RECEIVED,
                    StatusEnum::REJECTED
                ])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);

            return view('pages.transfers.bodega_index', compact('transfers', 'pendingShipments'));
        }

        $transfers = Transfer::forUserContext($user)->paginate(15);

        return view('pages.transfers.index', compact('transfers'));
    }

    public function create()
    {
        Gate::authorize('create', Transfer::class);
        $user = Auth::user();

        $sourceOffice = Office::where('is_main', true)->firstOrFail(); // Cooperativa
        $destinationOffice = Office::find($user->office_id);

        $products = Product::with([
        'movementDetails' => function ($query) use ($sourceOffice) {
            $query->whereHas('movement', fn($q) => $q->where('office_id', $sourceOffice->id))
                  ->latest('id'); // Trae solo los más recientes para mapearlos en memoria
            }
        ])->get();

        $products->each(function ($product) {
        $lastMovement = $product->movementDetails->first();
        $product->available_stock = $lastMovement ? $lastMovement->stock_after : ($product->stock ?? 0);
        });

        return view('pages.transfers.create', compact('sourceOffice', 'destinationOffice', 'products'));
    }

    public function store(StoreTransferRequest $request)
    {
        app(CreateTransferAction::class)->execute(
            $request->validated()['products'],
            $request->user()->id,
            $request->user()->office_id
        );

        return redirect()->route('transfers.index')
            ->with('success', 'Solicitud creada con éxito.');
    }

    public function approve(ApproveTransferRequest $request, Transfer $transfer)
    {
        try {
            // Toda la transacción pesada ocurre dentro del Action, aislada del HTTP
            app(ApproveTransferAction::class)->execute(
                $transfer,
                $request->validated()['details'],
                Auth::id()
            );

        } catch (BusinessRuleException $e) {
            // Captura limpia de errores controlados de negocio
            return redirect()->route('transfers.show', $transfer)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transferencia procesada y validada correctamente.');
    }

    public function show(Transfer $transfer)
    {
       $transfer->load(['originatingBranch', 'destinationBranch', 'requestingUser', 'authorizingUser', 'details.product']);
        return view('pages.transfers.show', compact('transfer'));
    }

    public function ship(Transfer $transfer)
    {
        Gate::authorize('ship', $transfer);

        app(ShipTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia enviada con costos registrados.');
    }

    public function receive(Transfer $transfer)
    {
        Gate::authorize('receive', $transfer);

        app(ReceiveTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.index')->with('success', 'Transferencia recibida e ingresada al Kardex local con éxito.');
    }

    public function reject(Transfer $transfer)
    {
        Gate::authorize('reject', $transfer);
        $transfer->update(['status' => StatusEnum::REJECTED]);
        return redirect()->route('transfers.index')->with('error', 'Transferencia rechazada.');
    }
}
