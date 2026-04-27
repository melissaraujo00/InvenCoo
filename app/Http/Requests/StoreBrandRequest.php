<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.\']+$/',
                'unique:brands'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
                'min:5'
            ]
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Marca',
            'description' => 'descripción'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la marca es obligatorio.',
            'name.max' => 'La marca no debe exceder los 50 caracteres.',
            'name.regex' => 'La marca contiene caracteres no permitidos.',
            'name.unique'=> 'Esta marca ya se encuentra registrada.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'description.max' => 'La descripción no debe exceder los 255 caracteres.',
            'description.min' => 'La descripción debe tener al menos 5 caracteres.'
        ];
    }
}
