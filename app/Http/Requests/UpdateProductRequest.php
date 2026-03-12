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
                Rule::unique('products')->ignore($this->route('product'))
            ],
            'name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'office_id' => 'required|exists:offices,id',
            'stock' => 'required|integer|min:0',
            'stock_minimun' => 'required|integer|min:0',

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
            'stock_minimun' => 'stock mínimo'
        ];
    }
}
