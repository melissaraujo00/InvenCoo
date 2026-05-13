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
            //'code'                => 'required|string|max:50|unique:products,code',
            'name'                => 'required|string|max:100|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\/]+$/',
            'category_id'         => 'required|exists:categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'stock'               => 'required|integer|min:0',
            'stock_minimun'       => 'required|integer|min:0',
            'unit_id'             => 'nullable|exists:units,id',
            'suppliers'           => 'nullable|array',
            'suppliers.*.id'      => 'required|exists:suppliers,id',
            'suppliers.*.price'   => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            //'code'                => 'código',
            'name'                => 'nombre',
            'category_id'         => 'categoría',
            'brand_id'            => 'marca',
            // 'office_id'         => 'oficina',
            'stock'               => 'stock',
            'stock_minimun'       => 'stock mínimo',
            'unit'                => 'unidad',
            'suppliers'           => 'proveedores',
            'suppliers.*.price'   => 'precio',
        ];
    }

    public function messages(): array
    {
        return [
           // 'code.required'       => 'El :attribute es obligatorio',
            'name.required'       => 'El :attribute es obligatorio',
            'name.regex'          => 'El :attribute solo puede contener letras y espacios',
            'category_id.required' => 'La :attribute es obligatoria',
            'stock.required'      => 'El :attribute es obligatorio',
            'stock_minimun.required' => 'El :attribute es obligatorio',
            'unit.required'       => 'La :attribute es obligatoria',
            'unit.regex'          => 'La :attribute solo puede contener letras y espacios',
            'suppliers.*.id.required' => 'Debe seleccionar un proveedor',
            'suppliers.*.price.required' => 'El precio del proveedor es obligatorio',
        ];
    }
}
