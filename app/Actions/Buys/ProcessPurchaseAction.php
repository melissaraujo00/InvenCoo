<?php
namespace App\Actions\Buys;

use App\Models\Buy;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Services\KardexService;
use App\Services\TaxCalculatorService;
use Illuminate\Support\Facades\DB;

class ProcessPurchaseAction
{
    public function __construct(
        private KardexService $kardex,
        private TaxCalculatorService $tax,
    ) {}

    public function execute(array $validated, $user): void
    {
        DB::transaction(function () use ($validated, $user) {
            $subtotal      = collect($validated['products'])->sum(fn($i) => $i['quantity'] * $i['price']);
            $globalDiscount = $validated['discount_type'] === 'global' ? ($validated['discount'] ?? 0) : 0;

            $buy = Buy::create([
                'supplier_id'   => $validated['supplier_id'],
                'document_type' => $validated['document_type'],
                'date'          => $validated['date'],
                'subtotal'      => $subtotal,
                'discount'      => $validated['discount_type'] === 'global'
                                    ? $globalDiscount
                                    : collect($validated['products'])->sum('discount'),
                'discount_type' => $validated['discount_type'],
                'total_iva'     => 0,
                'total'         => 0,
                'user_id'       => $user->id,
                'office_id'     => $user->office_id,
                'is_cancelled'  => false,
            ]);

            $movement = $this->kardex->createMovement([
                'office_id'      => $user->office_id,
                'date'           => $validated['date'],
                'type_name'      => 'compra',
                'type_description' => 'Movimiento por compra',
                'user_id'        => $user->id,
                'transaction_id' => 'COMPRA-' . $buy->id,
                'description'    => 'Compra doc: ' . strtoupper($validated['document_type']),
                'input_type'     => 'E',
            ]);

            $runningSubtotal = 0;

            foreach ($validated['products'] as $item) {
                $product     = Product::lockForUpdate()->find($item['product_id']);
                $lineTotal   = $item['price'] * $item['quantity'];
                $lineDiscount = $this->tax->calculateLineDiscount(
                    $lineTotal, $subtotal, $validated['discount_type'],
                    $globalDiscount, $item['discount'] ?? 0
                );
                $netLineTotal    = $lineTotal - $lineDiscount;
                $unitCostKardex  = $this->tax->extractNetUnitCost(
                    $validated['document_type'], $netLineTotal, $item['quantity']
                );

                PurchaseDetail::create([
                    'buy_id'     => $buy->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'discount'   => $lineDiscount,
                    'subtotal'   => $netLineTotal,
                ]);

                $product->increment('stock', $item['quantity']);
                $runningSubtotal += $netLineTotal;

                $this->kardex->addDetail($movement, [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitCostKardex,
                    'stock_after' => $product->stock,
                ]);
            }

            $totals = $this->tax->calculateTotals($validated['document_type'], $runningSubtotal);
            $buy->update(['total_iva' => $totals['iva'], 'total' => $totals['total']]);
        });
    }
}
