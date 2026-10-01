<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('role') ? 'update' : 'create', $this->route('role') ?? Role::class) ?? false;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('roles', 'name')->ignore($role?->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array', 'max:20'],
            'permissions.*' => ['required', 'string', 'distinct', Rule::exists('permissions', 'code')],
        ];
    }
}
