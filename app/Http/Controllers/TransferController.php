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

        $sourceOffice = Office::where('is_main', true)->firstOrFail(); 
        $destinationOffice = Office::find($user->office_id);

        $products = Product::with([
        'movementDetails' => function ($query) use ($sourceOffice) {
            $query->whereHas('movement', fn($q) => $q->where('office_id', $sourceOffice->id))
                  ->latest('id'); 
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
            app(ApproveTransferAction::class)->execute(
                $transfer,
                $request->validated()['details'],
                Auth::id()
            );

        } catch (BusinessRuleException $e) {
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
        // La validación de acceso ahora recae enteramente en el middleware definido en web.php
        app(ShipTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.show', $transfer)->with('success', 'Transferencia enviada con costos registrados.');
    }

    public function receive(Transfer $transfer)
    {
        // Eliminado Gate::authorize para evitar colisión con las rutas
        app(ReceiveTransferAction::class)->execute($transfer);

        return redirect()->route('transfers.index')->with('success', 'Transferencia recibida e ingresada al Kardex local con éxito.');
    }

    public function reject(Transfer $transfer)
    {
        // Eliminado Gate::authorize para evitar colisión con las rutas
        $transfer->update(['status' => StatusEnum::REJECTED]);
        return redirect()->route('transfers.index')->with('error', 'Transferencia rechazada.');
    }
}
