<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        // Traemos los productos ordenados de menor a mayor stock
        return Product::orderBy('stock', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre del Producto',
            'Stock Actual',
            'Stock Mínimo',
            'Estado',
            'Última Actualización'
        ];
    }

    public function map($product): array
    {
        // Regla de negocio en el reporte
        $estado = $product->stock <= $product->min_stock ? 'CRÍTICO' : 'Normal';

        return [
            $product->id,
            $product->name,
            $product->stock,
            $product->stock_minimun,
            $estado,
            $product->updated_at->format('d/m/Y H:i'),
        ];
    }
}
