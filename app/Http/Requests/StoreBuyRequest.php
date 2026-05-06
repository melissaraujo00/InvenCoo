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
            'date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'iva_rate' => 'nullable|numeric|min:0|max:100', // <-- NUEVO CAMPO OBLIGATORIO
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
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
