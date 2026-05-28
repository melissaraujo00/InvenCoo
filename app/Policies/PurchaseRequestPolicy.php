<?php

namespace App\Policies;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PurchaseRequestPolicy
{
    /**
     * Determina si el usuario puede ver la lista de solicitudes.
     * El controlador se encarga de filtrar qué registros ve cada quién.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver el detalle de una solicitud en específico.
     */
    public function view(User $user, PurchaseRequest $purchaseRequest): bool
    {
        // Admin y Bodega tienen acceso global a los detalles
        if ($user->hasRole(['Administrador', 'Bodega'])) {
            return true;
        }

        // El usuario normal solo puede ver el detalle si él fue quien lo solicitó
        return $user->id === $purchaseRequest->requesting_user_id;
    }

    /**
     * Determina si el usuario puede crear nuevas solicitudes de compra.
     */
    public function create(User $user): bool
    {
        // Todos los roles operativos pueden hacer requisiciones
        return $user->hasRole(['Administrador', 'Bodega', 'Administrador Restaurante']);
    }

    /**
     * Determina si el usuario puede aprobar la solicitud.
     */
    public function approve(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determina si el usuario puede rechazar la solicitud.
     */
    public function reject(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determina si el usuario puede marcar la solicitud como procesada/comprada.
     */
    public function process(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->hasRole('Bodega');
    }
}
