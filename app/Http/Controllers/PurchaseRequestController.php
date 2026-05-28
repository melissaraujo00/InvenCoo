<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseRequest\CreatePurchaseRequestAction;
use App\Enums\StatusEnum;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Notifications\PurchaseRequestApproved;
use App\Notifications\PurchaseRequestProcessed;
use App\Notifications\PurchaseRequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class PurchaseRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si es Bodega, lo mandamos a su panel de control operativo
        if ($user->hasRole('Bodega')) {

            // 1. Lo que urge: Solicitudes aprobadas que Bodega DEBE comprar
            $pendingPurchases = PurchaseRequest::with(['requestingUser', 'authorizingUser', 'details.product'])
                ->where('status', StatusEnum::APPROVED)
                ->orderBy('updated_at', 'desc')
                ->get();

            // 2. El Historial: Lo que ya se compró/recibió (para que lleven control)
            $history = PurchaseRequest::with(['requestingUser', 'authorizingUser'])
                ->where('status', StatusEnum::RECEIVED) // O el estado final que uses
                ->latest()
                ->paginate(15);

            return view('pages.purchases.bodega_index', compact('pendingPurchases', 'history'));
        }

        // Para el Administrador o Restaurante, mostramos la tabla normal con todo
        $requests = PurchaseRequest::with(['requestingUser', 'authorizingUser'])->latest()->paginate(15);
        return view('pages.purchases.index', compact('requests'));
    }

    public function create()
    {
        Gate::authorize('create', PurchaseRequest::class);
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('pages.purchases.create', compact('products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        app(CreatePurchaseRequestAction::class)->execute(
            $request->validated()['products'],
            Auth::id(),
            null, // El autorizador es nulo al crearse
            $request->validated()['note'] ?? null
        );

        return redirect()->route('purchases.index')
            ->with('success', 'Solicitud de compra generada y enviada a revisión.');
    }

    // CAMBIO CLAVE: El parámetro debe llamarse $purchase para que Laravel lo enlace correctamente
    public function show(PurchaseRequest $purchase)
    {
        // Renombramos la variable internamente para no tener que modificar tu archivo show.blade.php
        $purchaseRequest = $purchase;

        $purchaseRequest->load(['requestingUser', 'authorizingUser', 'details.product']);
        return view('pages.purchases.show', compact('purchaseRequest'));
    }

    // --- Métodos de Cambio de Estado ---

    public function approve(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('approve', $purchaseRequest);

        if ($purchaseRequest->status !== StatusEnum::PENDING) {
            return back()->with('error', 'Solo se pueden aprobar solicitudes pendientes.');
        }

        $purchaseRequest->update([
            'status' => StatusEnum::APPROVED,
            'authorizing_user_id' => Auth::id()
        ]);

        $bodegueros = \App\Models\User::role('Bodega')->get();
        if ($bodegueros->isNotEmpty()) {
            Notification::send($bodegueros, new PurchaseRequestApproved($purchaseRequest));
        }

        return back()->with('success', 'Solicitud de compra aprobada. Bodega ha sido notificada.');
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('reject', $purchaseRequest);

        if ($purchaseRequest->status !== StatusEnum::PENDING) {
            return back()->with('error', 'Solo se pueden rechazar solicitudes pendientes.');
        }

        $purchaseRequest->update([
            'status' => StatusEnum::REJECTED,
            'authorizing_user_id' => Auth::id()
        ]);

        $requester = $purchaseRequest->requestingUser;
        if ($requester) {
            Notification::send($requester, new PurchaseRequestRejected($purchaseRequest));
        }

        return back()->with('success', 'Solicitud de compra rechazada.');
    }

    public function process(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('process', $purchaseRequest);

        if ($purchaseRequest->status !== StatusEnum::APPROVED) {
            return back()->with('error', 'Solo se pueden procesar solicitudes que ya fueron aprobadas.');
        }

        $purchaseRequest->update([
            'status' => StatusEnum::RECEIVED,
        ]);

        $requester = $purchaseRequest->requestingUser;
        if ($requester) {
            Notification::send($requester, new PurchaseRequestProcessed($purchaseRequest));
        }

        return back()->with('success', 'Compra marcada como procesada exitosamente.');
    }
}
