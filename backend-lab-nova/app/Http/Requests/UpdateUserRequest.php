<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:60',
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($this->user()->id)],
            'password' => 'nullable|string|min:6',
            'phone' => 'sometimes|string|max:20',
            'status' => 'sometimes|boolean',
            'role_id' => 'sometimes|exists:roles,id',
            'gender' => 'nullable|string|max:14',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:100',
            'addon_address' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }
}
