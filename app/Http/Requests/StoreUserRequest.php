<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:50',
            'last_name' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:50',
            'email' => 'required|email|unique:users',
            'number' => 'required|unique:users',
            'password' => 'required|min:8|confirmed',
            'office_id' => 'required|exists:offices,id',
            'roles' => 'sometimes|array|min:1',
            'roles.*' => 'exists:roles,id'
        ];
    }

   public function messages()
{
    return [
        // Nombre
        'name.required' => 'Por favor, ingrese el nombre del usuario',
        'name.regex' => 'El nombre solo puede contener letras y espacios (ej: Juan Carlos)',
        'name.max' => 'El nombre es demasiado largo (máximo :max 50)',

        // Apellido
        'last_name.required' => 'Por favor, ingrese el apellido del usuario',
        'last_name.regex' => 'El apellido solo puede contener letras y espacios (ej: Pérez García)',
        'last_name.max' => 'El apellido es demasiado largo (máximo :max 50)',

        // Email
        'email.required' => 'El correo electrónico es obligatorio',
        'email.email' => 'Ingrese un correo electrónico válido (ej: usuario@dominio.com)',
        'email.unique' => 'Este correo electrónico ya está registrado. ¿Olvidó su contraseña?',

        // Número de teléfono/ID
        'number.required' => 'El número de teléfono o identificación es obligatorio',
        'number.unique' => 'Este número ya está asociado a otra cuenta en el sistema',
        'number.regex' => 'El número debe tener un formato válido (ej: 04121234567)',

        // Contraseña
        'password.required' => 'Debe establecer una contraseña para el usuario',
        'password.min' => 'La contraseña debe tener al menos :min caracteres por seguridad',
        'password.confirmed' => 'La confirmación de contraseña no coincide',

        // Oficina
        'office_id.required' => 'Seleccione una oficina para asignar al usuario',
        'office_id.exists' => 'La oficina seleccionada no es válida o ha sido eliminada',

        // Roles
        'roles.required' => 'Debe asignar al menos un rol al usuario',
        'roles.array' => 'Los roles deben ser enviados en un formato válido',
        'roles.*.exists' => 'El rol #:position no existe en el sistema',
    ];
}
}
