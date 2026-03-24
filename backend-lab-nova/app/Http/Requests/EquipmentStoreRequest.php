<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:equipment,code'],
            'description' => ['nullable', 'string'],
            'stock' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,maintenance,out_of_service'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
