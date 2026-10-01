<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rootEmail = config('cowapp.mail_settings_admin_email');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'lowercase', 'max:255', Rule::unique('users', 'email'),
                Rule::notIn(is_string($rootEmail) && $rootEmail !== '' ? [mb_strtolower($rootEmail)] : []),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
