<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('equipment');

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', Rule::unique('equipment', 'code')->ignore($id)],
            'description' => ['nullable', 'string'],
            'stock' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'in:available,maintenance,out_of_service'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}