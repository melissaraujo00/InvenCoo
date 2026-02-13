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
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'number'    => 'required|string|max:20',
            'office_id' => 'required|exists:offices,id',
            'status'    => 'required|boolean', 
            'password'  => 'nullable|string|min:8|confirmed',
        ];
    }
}
