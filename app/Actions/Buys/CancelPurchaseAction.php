<?php

namespace App\Actions\Buys;

use App\Models\Buy;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CancelPurchaseAction
{
    public function execute(Buy $buy): void
    {
        $buy->load('details.product');

        DB::transaction(function () use ($buy) {
            foreach ($buy->details as $detail) {
                $detail->product->decrement('stock', $detail->quantity);
            }

            $movement = Movement::create([
                'office_id'      => $buy->office_id,
                'date_movement'  => now(),
                'type_id'        => Type::firstOrCreate(
                                        ['name' => 'anulacion_compra'],
                                        ['description' => 'Movimiento por anulación de compra']
                                    )->id,
                'user_id'        => Auth::id(),
                'transaction_id' => 'ANULACION-COMPRA-' . $buy->id . '-' . time(),
                'description'    => 'Anulación de compra #' . $buy->id,
                'input_type'     => 'S',
            ]);

            foreach ($buy->details as $detail) {
                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id'  => $detail->product_id,
                    'quantity'    => $detail->quantity,
                    'unit_price'  => $detail->price,
                    'subtotal'    => $detail->subtotal,
                    'stock_after' => $detail->product->stock,
                ]);
            }

            $buy->update(['is_cancelled' => true]);
        });
    }
}
