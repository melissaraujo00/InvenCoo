<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBuyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo administradores pueden editar
        return auth()->user()?->hasRole('Administrador') ?? false;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'iva_rate' => 'nullable|numeric|min:0|max:100',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Debes seleccionar un proveedor.',
            'office_id.required' => 'Debes seleccionar una oficina.',
            'date.required' => 'La fecha es obligatoria.',
            'products.required' => 'Agrega al menos un producto.',
            'products.min' => 'Agrega al menos un producto.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',
            'products.*.price.min' => 'El precio debe ser mayor o igual a 0.',
        ];
    }
}
