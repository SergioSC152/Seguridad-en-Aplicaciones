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

    public function messages(): array
    {
        return [
            'name.regex' => 'El nombre solo admite letras, espacios, apóstrofos y guiones; no admite números.',
            'email.unique' => 'Ya existe un usuario con ese correo. Inicia sesión o recupera tu contraseña.',
            'email.not_in' => 'Este correo está reservado para la cuenta administradora.',
        ];
    }

    public function rules(): array
    {
        $rootEmail = config('cowapp.mail_settings_admin_email');

        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\p{M}]+(?:[ \x{0027}\x{2019}\-][\p{L}\p{M}]+)*$/u'],
            'email' => [
                'required', 'string', 'email', 'lowercase', 'max:100', Rule::unique('users', 'email'),
                Rule::notIn(is_string($rootEmail) && $rootEmail !== '' ? [mb_strtolower($rootEmail)] : []),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'max:25'],
        ];
    }
}
