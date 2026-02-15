<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;

        return [
              'name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'number' => 'required',
            'office_id' => 'required|exists:offices,id',
            'password' => 'nullable|min:8|confirmed',
            'status' => 'nullable|boolean',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id'
        ];
    }
}
