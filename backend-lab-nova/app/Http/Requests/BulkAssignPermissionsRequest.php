<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkAssignPermissionsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
            'modules' => 'required|array',
            'modules.*.module_id' => 'required|exists:modules,id',
            'modules.*.permission_ids' => 'required|array',
            'modules.*.permission_ids.*' => 'exists:permissions,id',
        ];
    }
}
