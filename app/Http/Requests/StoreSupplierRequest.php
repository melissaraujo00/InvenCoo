<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'company_name' => [
                'required',
                'string',
                'min:5',
                'max:50',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'unique:suppliers,company_name'
            ],
            'contact_name' => [
                'required',
                'string',
                'min:3',
                'max:75',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'number_phone' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'regex:/^[0-9+\-\s]+$/'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
                'min:5'                               
            ]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'nombre de la empresa',
            'contact_name' => 'nombre del contacto',
            'number_phone' => 'número de teléfono',
            'description' => 'descripción'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Company Name
            'company_name.required' => 'El :attribute es obligatorio',
            'company_name.min' => 'El :attribute debe tener al menos :min caracteres',
            'company_name.max' => 'El :attribute no puede tener más de :max caracteres',
            'company_name.regex' => 'El :attribute solo puede contener letras y espacios',
            'company_name.unique' => 'El :attribute ya está registrado',

            // Contact Name
            'contact_name.required' => 'El :attribute es obligatorio',
            'contact_name.min' => 'El :attribute debe tener al menos :min caracteres',
            'contact_name.max' => 'El :attribute no puede tener más de :max caracteres',
            'contact_name.regex' => 'El :attribute solo puede contener letras y espacios',

            // Phone Number
            'number_phone.required' => 'El :attribute es obligatorio',
            'number_phone.min' => 'El :attribute debe tener al menos :min caracteres',
            'number_phone.max' => 'El :attribute no puede tener más de :max caracteres',
            'number_phone.regex' => 'El :attribute solo puede contener números, +, - y espacios',

            // Description
            'description.max' => 'La :attribute no puede tener más de :max caracteres',
            'description.min' => 'La :attribute debe tener al menos :min caracteres'
        ];
    }
}
