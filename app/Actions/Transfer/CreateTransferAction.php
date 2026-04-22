<?php
namespace App\Actions\Transfer;

use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Enums\StatusEnum;
use App\Notifications\TransferRequested;
use App\Notifications\TransferWhatsappNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class CreateTransferAction
{
    public function execute(array $products, int $userId, int $destinationBranchId): Transfer
    {
        return DB::transaction(function () use ($products, $userId, $destinationBranchId) {
            $transfer = Transfer::create([
                'originating_branch' => 1, // Cooperativa
                'destination_branch' => $destinationBranchId,
                'requesting_user' => $userId,
                'creation_date' => now(),
                'status' => StatusEnum::PENDING,
            ]);

            foreach ($products as $item) {
                TransferDetail::create([
                    'transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity_requested' => $item['quantity'],
                    'quantity_sent' => 0,
                ]);
            }

            // Notificaciones
            $admins = User::role('Administrador')->get();
            Notification::send($admins, new TransferRequested($transfer));
            foreach ($admins as $admin) {
                if ($admin->number) {
                    $admin->notify(new TransferWhatsappNotification($transfer, 'transfer_request_admin', [
                        (string) $transfer->id,
                        route('transfers.show', $transfer)
                    ]));
                }
            }

            return $transfer;
        });
    }
}
