<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transfer;
use App\Enums\StatusEnum;

class TransferPolicy
{
    public function approve(User $user, Transfer $transfer)
    {
        return $user->hasRole('Administrador') && $transfer->status === StatusEnum::PENDING;
    }

    public function ship(User $user, Transfer $transfer)
    {
        return $user->hasRole('Bodega') && $user->office_id == $transfer->originating_branch && in_array($transfer->status, [StatusEnum::APPROVED, StatusEnum::PARTIALLY_APPROVED]);
    }
    
public function receive(User $user, Transfer $transfer)
{
    return $user->hasRole('Administrador Restaurante')
        && $user->office_id == $transfer->destination_branch
        && $transfer->status === StatusEnum::SHIPPED;
}

    public function reject(User $user, Transfer $transfer)
    {
        return $user->hasRole('Administrador') && $transfer->status === StatusEnum::PENDING;
    }

    public function create(User $user)
    {
        // Solo el Restaurante debería crear solicitudes de transferencia
        return $user->hasRole('Administrador Restaurante');
    }
}
