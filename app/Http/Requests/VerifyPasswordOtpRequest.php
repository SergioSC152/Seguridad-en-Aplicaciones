<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('password_reset_email');
    }

    public function rules(): array
    {
        return ['code' => ['required', 'string', 'digits:6']];
    }
}
