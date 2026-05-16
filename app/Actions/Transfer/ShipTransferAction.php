<?php

namespace App\Actions\Transfer;

use App\Enums\StatusEnum;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Product;
use App\Models\Transfer;
use App\Models\Type;
use App\Notifications\TransferWhatsappNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipTransferAction
{
    public function execute(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $outMovement = Movement::create([
                'office_id'              => $transfer->originating_branch,
                'date_movement'          => now(),
                'type_id'                => Type::firstOrCreate(['name' => 'Transferencia Salida'])->id,
                'user_id'                => Auth::id(),
                'transaction_id'         => 'TRF-OUT-' . $transfer->id,
                'description'            => 'Salida por transferencia #' . $transfer->id,
                'input_type'             => 'S',
                'origin_office_id'       => $transfer->originating_branch,
                'destination_office_id'  => $transfer->destination_branch,
            ]);

            foreach ($transfer->details as $detail) {
                $qty = $detail->quantity_sent;

                if ($qty <= 0) {
                    continue;
                }

                $product = Product::lockForUpdate()->find($detail->product_id);
                $product->decrement('stock', $qty);

                // Arrastramos el costo real de la última entrada para no registrar costo $0.00
                $lastInputDetail = MovementDetail::where('product_id', $detail->product_id)
                    ->whereHas('movement', fn($q) => $q->where('input_type', 'E'))
                    ->orderBy('id', 'desc')
                    ->first();

                $cost = $lastInputDetail?->unit_price ?? 0;

                MovementDetail::create([
                    'movement_id' => $outMovement->id,
                    'product_id'  => $detail->product_id,
                    'quantity'    => $qty,
                    'unit_price'  => $cost,
                    'subtotal'    => $cost * $qty,
                    'stock_after' => $product->stock,
                ]);
            }

            $transfer->update([
                'shipping_date'   => now(),
                'out_movement_id' => $outMovement->id,
                'status'          => StatusEnum::SHIPPED,
            ]);
        });

        // Notificaciones FUERA de la transacción: un fallo de cURL no revierte el envío
        try {
            $requester = User::find($transfer->requesting_user);

            if ($requester?->number) {
                $requester->notify(new TransferWhatsappNotification(
                    $transfer,
                    'transfer_ready_for_ship',
                    [(string) $transfer->id, route('transfers.show', $transfer)]
                ));
            }
        } catch (\Exception $e) {
            Log::error('Fallo WhatsApp en ShipTransferAction: ' . $e->getMessage());
        }
    }
}
