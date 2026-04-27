<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
                'max:50',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.]+$/', 
                'unique:categories',
                'min:3'
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
            'name' => 'categoría',
            'description' => 'descripción'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.max' => 'La categoría no debe exceder los 50 caracteres.',
            'name.regex' => 'La categoría contiene caracteres no permitidos (se permiten letras, números, guiones y puntos).',
            'name.unique'=> 'El nombre de esta categoría ya está en uso.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'description.max' => 'La descripción no debe exceder los 255 caracteres.',
            'description.min' => 'La descripción debe tener al menos 5 caracteres.',
        ];
    }
}
