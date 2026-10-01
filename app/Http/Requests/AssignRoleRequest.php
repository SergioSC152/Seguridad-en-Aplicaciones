<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Role::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')],
        ];
    }
}
