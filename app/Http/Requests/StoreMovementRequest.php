<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'office_id' => 'required|exists:offices,id',
            'date_movement' => 'required|date',
            'type_id' => 'required|exists:types,id',
            'amount' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'input_type' => 'required|in:E,S',
        ];
    }
}
