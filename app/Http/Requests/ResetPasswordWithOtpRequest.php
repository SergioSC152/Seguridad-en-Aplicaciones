<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordWithOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('password_reset_user_id');
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed', 'max:25'],
        ];
    }
}
