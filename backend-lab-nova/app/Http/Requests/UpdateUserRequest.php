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
            'name' => ['sometimes', 'string', 'max:50', 'regex:/^[\p{L}\s]+$/u'],
            'last_name' => ['sometimes', 'string', 'max:60', 'regex:/^[\p{L}\s]+$/u'],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($this->route('id'))],
            'password' => 'nullable|string|min:6',
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($this->route('id'))],
            'status' => 'sometimes|boolean',
            'role_id' => 'sometimes|exists:roles,id',
            'gender_id' => 'nullable|exists:genders,id',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string|max:100',
            'addon_address' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'     => 'El nombre solo puede contener letras y espacios.',
            'last_name.regex'=> 'El apellido solo puede contener letras y espacios.',
            'email.email'    => 'Formato de correo inválido.',
            'email.unique'   => 'El correo ya se encuentra registrado.',
            'phone.unique'   => 'El teléfono ya se encuentra registrado.',
            'role_id.exists' => 'Rol no válido.',
            'password.min'   => 'La contraseña debe tener al menos 6 caracteres.',
        ];
    }
}
