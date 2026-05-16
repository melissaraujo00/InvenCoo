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

class ReceiveTransferAction
{
    public function execute(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $inMovement = Movement::create([
                'office_id'              => $transfer->destination_branch,
                'date_movement'          => now(),
                'type_id'                => Type::firstOrCreate(['name' => 'Transferencia Entrada'])->id,
                'user_id'                => Auth::id(),
                'transaction_id'         => 'TRF-IN-' . $transfer->id,
                'description'            => 'Entrada por transferencia #' . $transfer->id,
                'input_type'             => 'E',
                'origin_office_id'       => $transfer->originating_branch,
                'destination_office_id'  => $transfer->destination_branch,
            ]);

            foreach ($transfer->details as $detail) {
                $qty = $detail->quantity_sent;

                if ($qty <= 0) {
                    continue;
                }

                $product = Product::lockForUpdate()->find($detail->product_id);
                $product->increment('stock', $qty);

                // Recuperamos el costo exacto con el que salió de bodega
                // para que el restaurante asimile ese mismo valor en su Kardex
                $outDetail = MovementDetail::where('movement_id', $transfer->out_movement_id)
                    ->where('product_id', $detail->product_id)
                    ->first();

                $cost = $outDetail?->unit_price ?? 0;

                MovementDetail::create([
                    'movement_id' => $inMovement->id,
                    'product_id'  => $detail->product_id,
                    'quantity'    => $qty,
                    'unit_price'  => $cost,
                    'subtotal'    => $cost * $qty,
                    'stock_after' => $product->stock,
                ]);
            }

            $transfer->update([
                'receipt_date' => now(),
                'status'       => StatusEnum::RECEIVED,
            ]);
        });

        // Notificaciones FUERA de la transacción: un fallo de cURL no revierte la recepción
        try {
            $admins = User::role('Administrador')->get();

            foreach ($admins as $admin) {
                if ($admin->number) {
                    $admin->notify(new TransferWhatsappNotification(
                        $transfer,
                        'transfer_received_admi',
                        [(string) $transfer->id, route('transfers.show', $transfer)]
                    ));
                }
            }
        } catch (\Exception $e) {
            Log::error('Fallo WhatsApp en ReceiveTransferAction: ' . $e->getMessage());
        }
    }
}
