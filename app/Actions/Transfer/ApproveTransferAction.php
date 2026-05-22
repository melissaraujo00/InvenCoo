<?php
namespace App\Actions\Transfer;

use App\Models\Transfer;
use App\Models\TransferDetail;
use App\Enums\StatusEnum;
use App\Exceptions\BusinessRuleException;
use App\Models\Product;
use App\Models\User;
use App\Notifications\TransferApproved;
use App\Notifications\TransferReadyToShip;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ApproveTransferAction
{
    public function execute(Transfer $transfer, array $details, int $authorizingUserId): void
    {
        DB::transaction(function () use ($transfer, $details, $authorizingUserId) {
            $allApproved = true;
            $anyPartial = false;

            foreach ($details as $item) {
                $detail = TransferDetail::findOrFail($item['id']);
                $sent = $item['quantity_sent'];

                if ($sent > $detail->quantity_requested) {
                    // 3. Usa la excepción de negocio correcta
                    throw new BusinessRuleException("La cantidad enviada del producto no puede superar la solicitada.");
                }

                // 4. RESTAURADO: Bloqueo pesimista y validación de stock real
                $product = Product::lockForUpdate()->find($detail->product_id);
                if ($sent > $product->stock) {
                    throw new BusinessRuleException("No hay stock suficiente de {$product->name} en la sucursal de origen. Stock disponible: {$product->stock}.");
                }

                $detail->update(['quantity_sent' => $sent]);

                if ($sent == 0) $allApproved = false;
                if ($sent > 0 && $sent < $detail->quantity_requested) $anyPartial = true;
            }

            $status = StatusEnum::APPROVED;
            if (!$allApproved && !$anyPartial) {
                $status = StatusEnum::REJECTED;
            } elseif ($anyPartial) {
                $status = StatusEnum::PARTIALLY_APPROVED;
            }

            $transfer->update([
                'user_authorizes' => $authorizingUserId, // Se usa el parámetro, no el helper web
                'status' => $status,
            ]);

            // Dispara evento (Asegúrate de que tus Listeners estén configurados)
           $requester = User::find($transfer->requesting_user);
            if ($requester) {
               Notification::send($requester, new TransferApproved($transfer));
            }

            // Buscamos estrictamente a los bodegueros de la sucursal de ORIGEN
            $bodegueros = User::role('Bodega')
                              ->where('office_id', $transfer->originating_branch)
                              ->get();

            if ($bodegueros->isNotEmpty()) {
                // AQUÍ es donde verdaderamente pertenece la notificación "Lista para enviar"
                Notification::send($bodegueros, new TransferReadyToShip($transfer));
            }
        });
    }
}
