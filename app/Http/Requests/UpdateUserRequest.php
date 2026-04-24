<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Si password está vacío, eliminarlo del request para que no intente validarlo ni guardarlo
        if ($this->has('password') && empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }

        // Asegurar que status sea booleano real para la BD
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    public function rules()
    {
        $userId = $this->route('user')?->id ?? $this->user?->id;

        return [
            'name' => ['required', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', 'max:50'],
            'last_name' => ['required', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'number' => ['required', 'string', 'max:12', Rule::unique('users', 'number')->ignore($userId)],
            'office_id' => ['required', 'exists:offices,id'],
            'status' => ['required', 'boolean'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,id'],
            // Regla moderna, exacta a la de creación, pero nullable
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Por favor, ingrese el nombre del usuario.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'name.max' => 'El nombre es demasiado largo (máximo 50 caracteres).',
            'last_name.required' => 'Por favor, ingrese el apellido del usuario.',
            'last_name.regex' => 'El apellido solo puede contener letras y espacios.',
            'last_name.max' => 'El apellido es demasiado largo (máximo 50 caracteres).',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'number.required' => 'El número de teléfono es obligatorio.',
            'number.unique' => 'Este número ya está asociado a otra cuenta.',
            'password.required' => 'Debe establecer una contraseña para el usuario.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'office_id.required' => 'Seleccione una oficina para el usuario.',
            'office_id.exists' => 'La oficina seleccionada no es válida.',
        ];
    }
}