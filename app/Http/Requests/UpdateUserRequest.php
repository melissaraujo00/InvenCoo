<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user')?->id ?? $this->user?->id;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'number' => [
                'required',
                'string',
                'max:12',
                Rule::unique('users', 'number')->ignore($userId)
            ],
            'office_id' => ['required', 'exists:offices,id'],
            'status' => ['nullable', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id']
        ];

        // Solo validar password si viene en el request
        if ($this->has('password') && !empty($this->password)) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/'
            ];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        // Si password está vacío, eliminarlo del request
        if ($this->has('password') && empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }

        // Asegurar que status sea booleano
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras y espacios (ej: Juan Carlos)',
            'name.max' => 'El nombre es demasiado largo (máximo :max caracteres)',

            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.regex' => 'El apellido solo puede contener letras y espacios (ej: Pérez García)',
            'last_name.max' => 'El apellido es demasiado largo (máximo :max caracteres)',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'number.required' => 'El número de teléfono es obligatorio.',
            'number.unique' => 'Este número de teléfono ya está registrado.',

            'office_id.required' => 'La oficina es obligatoria.',
            'office_id.exists' => 'La oficina seleccionada no existe.',
            
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'roles.*.exists' => 'Uno de los roles seleccionados no existe.',
        ];
    }
}
