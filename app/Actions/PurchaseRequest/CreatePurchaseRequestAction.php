<?php

namespace App\Actions\PurchaseRequest; // <-- El namespace correcto

use App\Enums\StatusEnum;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\User;
use App\Notifications\PurchaseRequestCreated; // <-- La clase real
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CreatePurchaseRequestAction
{
    public function execute(array $products, int $userRequestId, ?int $authorizingUserId, ?string $note): PurchaseRequest
    {
        $purchase = DB::transaction(function () use ($products, $userRequestId, $authorizingUserId, $note) {
            $purchase = PurchaseRequest::create([
                'requesting_user_id'  => $userRequestId,
                'authorizing_user_id' => $authorizingUserId,
                'status'              => StatusEnum::PENDING,
                'note'                => $note
            ]);

            foreach ($products as $item) {
                PurchaseRequestDetail::create([
                    'purchase_request_id' => $purchase->id,
                    'product_id'          => $item['product_id'],
                    'quantity'            => $item['quantity'],
                ]);
            }

            return $purchase;
        });

        // Notificaciones FUERA de la transacción
        $admins = User::role('Administrador')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new PurchaseRequestCreated($purchase)); // <-- Constructor correcto
        }

        return $purchase; // <-- Variable correcta
    }
}
