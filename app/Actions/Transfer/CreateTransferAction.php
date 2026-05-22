<?php

namespace App\Actions\Transfer;

use App\Enums\StatusEnum;
use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Models\User;
use App\Notifications\TransferRequested;
use App\Notifications\TransferWhatsappNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CreateTransferAction
{
    public function execute(array $products, int $userId, int $destinationBranchId): Transfer
    {
        // La transacción solo abarca escrituras de base de datos
        $transfer = DB::transaction(function () use ($products, $userId, $destinationBranchId) {
            $transfer = Transfer::create([
                'originating_branch' => 1, // Cooperativa (bodega central)
                'destination_branch' => $destinationBranchId,
                'requesting_user'    => $userId,
                'creation_date'      => now(),
                'status'             => StatusEnum::PENDING,
            ]);

            foreach ($products as $item) {
                TransferDetail::create([
                    'transfer_id'        => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'quantity_sent'      => 0,
                ]);
            }

            return $transfer;
        });

        // Notificaciones FUERA de la transacción:
        // Notificaciones FUERA de la transacción
        $admins = User::role('Administrador')->get();

        // Mantienes la notificación genérica si la necesitas para la base de datos/campanita
        Notification::send($admins, new TransferRequested($transfer));


        return $transfer;
    }
}
