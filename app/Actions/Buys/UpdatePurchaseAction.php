<?php

namespace App\Actions\Buys;

use App\Models\Buy;
use App\Models\Movement;
use App\Models\MovementDetail;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdatePurchaseAction
{
    public function execute(Buy $buy, array $validated): void
    {
        DB::transaction(function () use ($buy, $validated) {

            // 1. Revertir stock de los detalles anteriores
            foreach ($buy->details as $detail) {
                Product::lockForUpdate()->find($detail->product_id)
                    ->decrement('stock', $detail->quantity);
            }
            $buy->details()->delete();

            // 2. Recalcular totales con los nuevos datos
            $subtotal             = collect($validated['products'])->sum(fn($i) => $i['quantity'] * $i['price']);
            $discount             = $validated['discount'] ?? 0;
            $ivaRate              = ($validated['iva_rate'] ?? 0) / 100;
            $subtotalAfterDiscount = $subtotal - $discount;
            $totalIva             = $subtotalAfterDiscount * $ivaRate;
            $total                = $subtotalAfterDiscount + $totalIva;

            $buy->update([
                'supplier_id' => $validated['supplier_id'],
                'date'        => $validated['date'],
                'subtotal'    => $subtotal,
                'discount'    => $discount,
                'total_iva'   => $totalIva,
                'total'       => $total,
            ]);

            // 3. Movimiento de corrección en el Kardex
            $movement = Movement::create([
                'office_id'      => $buy->office_id,
                'date_movement'  => now(),
                'type_id'        => Type::firstOrCreate(
                                        ['name' => 'correccion_compra'],
                                        ['description' => 'Ajuste por edición de compra']
                                    )->id,
                'user_id'        => Auth::id(),
                'transaction_id' => 'CORRECCION-COMPRA-' . $buy->id . '-' . time(),
                'description'    => 'Ajuste por edición de compra #' . $buy->id,
                'input_type'     => 'E',
            ]);

            // 4. Crear nuevos detalles y actualizar stock
            foreach ($validated['products'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                $buy->details()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['quantity'] * $item['price'],
                ]);

                $product->increment('stock', $item['quantity']);

                MovementDetail::create([
                    'movement_id' => $movement->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['price'],
                    'subtotal'    => $item['quantity'] * $item['price'],
                    'stock_after' => $product->stock,
                ]);
            }
        });
    }
}
