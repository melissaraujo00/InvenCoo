<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'document_type' => 'required|in:factura,credito_fiscal,exento',
            'date' => 'required|date',
            'discount_type' => 'required|in:global,item',
            'discount' => 'nullable|numeric|min:0', // Descuento global
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0', // Descuento por artículo
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'proveedor',
            'date' => 'fecha',
            'discount' => 'descuento',
            'products' => 'productos',
            'products.*.product_id' => 'producto',
            'products.*.quantity' => 'cantidad',
            'products.*.price' => 'precio',
        ];
    }
}
