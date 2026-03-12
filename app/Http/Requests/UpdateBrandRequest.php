<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
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
                'min:5',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                Rule::unique('brands')->ignore($this->route('brand')),

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
            'name.required' => 'La marca es obligatoria',
            'name.max' => 'La marca debe de ser maximo 50 caracteres',
            'name.regex' => 'La marca no debe de tener numeros',
            'name.unique'=> 'El nombre de la marca ya esta en uso',
            'name.min' => 'El minimo de caracter son 5',

            'description.max' => 'La descripcion debe de tenero un maximo de 255 caracteres',
            'description.min' => 'El minimo de caracteres son de 5'
        ];
    }
}
