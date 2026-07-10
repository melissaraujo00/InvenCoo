<?php

namespace App\Actions\Transfer;

use App\Enums\StatusEnum;
use App\Models\Transfer;
use App\Models\User;
use App\Notifications\TransferReceived;
use Illuminate\Support\Facades\Notification;

class ReceiveTransferAction
{
    public function execute(Transfer $transfer): void
    {
        // 1. Actualizamos exclusivamente el estado del documento, sin tocar el Kardex
        $transfer->update([
            'receipt_date' => now(),
            'status'       => StatusEnum::RECEIVED,
        ]);

        // 2. Notificamos a los administradores que el ciclo se cerró con éxito
        $admins = User::role('Administrador')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new TransferReceived($transfer));
        }
    }
}
