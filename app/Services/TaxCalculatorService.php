<?php
namespace App\Services;

class TaxCalculatorService
{
    public function calculateLineDiscount(
        float $lineTotal,
        float $subtotal,
        string $discountType,
        float $globalDiscount,
        float $lineDiscount
    ): float {
        if ($discountType === 'global') {
            return ($lineTotal / $subtotal) * $globalDiscount;
        }
        return $lineDiscount;
    }

    public function extractNetUnitCost(
        string $documentType,
        float $netLineTotal,
        int $qty
    ): float {
        if ($documentType === 'factura') {
            return ($netLineTotal / $qty) / 1.13;
        }
        return $netLineTotal / $qty;
    }

    public function calculateTotals(string $documentType, float $runningSubtotal): array
    {
        $iva = match($documentType) {
            'credito_fiscal' => $runningSubtotal * 0.13,
            'factura'        => $runningSubtotal - ($runningSubtotal / 1.13),
            default          => 0,
        };

        $total = $documentType === 'credito_fiscal'
            ? $runningSubtotal + $iva
            : $runningSubtotal;

        return ['iva' => $iva, 'total' => $total];
    }
}
