<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products')->ignore($this->route('product')),
            ],
            'name'                => 'required|string|max:100|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\/]+$/',
            'category_id'         => 'required|exists:categories,id',
            'brand_id'            => 'nullable|exists:brands,id',
            'stock'               => 'required|integer|min:0',
            'stock_minimun'       => 'required|integer|min:0',
            'unit'                => 'required|string|max:20|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/',
            'suppliers'           => 'nullable|array',
            'suppliers.*.id'      => 'required|exists:suppliers,id',
            'suppliers.*.price'   => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'code'                => 'código',
            'name'                => 'nombre',
            'category_id'         => 'categoría',
            'brand_id'            => 'marca',
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
            'code.required'       => 'El :attribute es obligatorio.',
            'code.unique'         => 'El :attribute ya está registrado.',
            'name.required'       => 'El :attribute es obligatorio.',
            'name.regex'          => 'El :attribute solo puede contener letras y espacios.',
            'category_id.required' => 'La :attribute es obligatoria.',
            'stock.required'      => 'El :attribute es obligatorio.',
            'stock.integer'       => 'El :attribute debe ser un número entero.',
            'stock.min'           => 'El :attribute no puede ser negativo.',
            'stock_minimun.required' => 'El :attribute es obligatorio.',
            'stock_minimun.integer' => 'El :attribute debe ser un número entero.',
            'stock_minimun.min'   => 'El :attribute no puede ser negativo.',
            'unit.required'       => 'La :attribute es obligatoria.',
            'unit.regex'          => 'La :attribute solo puede contener letras y espacios.',
            'suppliers.*.id.required' => 'Debe seleccionar un proveedor.',
            'suppliers.*.id.exists' => 'El proveedor seleccionado no es válido.',
            'suppliers.*.price.required' => 'El precio del proveedor es obligatorio.',
            'suppliers.*.price.numeric' => 'El precio debe ser un número.',
            'suppliers.*.price.min' => 'El precio no puede ser negativo.',
        ];
    }
}
