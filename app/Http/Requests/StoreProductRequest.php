<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:products,code',
            'name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'office_id' => 'required|exists:offices,id',
            'stock' => 'required|integer|min:0',
            'stock_minimun' => 'required|integer|min:0',

            // Validación para proveedores
            'suppliers' => 'nullable|array',
            'suppliers.*.id' => 'required|exists:suppliers,id',
            'suppliers.*.price' => 'required|numeric|min:0'
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'código',
            'name' => 'nombre',
            'category_id' => 'categoría',
            'brand_id' => 'marca',
            'office_id' => 'oficina',
            'stock' => 'stock',
            'stock_minimun' => 'stock mínimo',
            'suppliers' => 'proveedores',
            'suppliers.*.price' => 'precio'
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El :attribute es obligatorio',
            'code.unique' => 'El :attribute ya está registrado',
            'name.required' => 'El :attribute es obligatorio',
            'category_id.required' => 'La :attribute es obligatoria',
            'office_id.required' => 'La :attribute es obligatoria',
            'suppliers.*.id.required' => 'Debe seleccionar un proveedor',
            'suppliers.*.price.required' => 'El precio del proveedor es obligatorio'
        ];
    }
}
