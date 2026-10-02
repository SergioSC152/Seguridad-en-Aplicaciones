<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordOtpRequest extends FormRequest
{
    protected function prepareForValidation(): void {
        $digits=$this->input('code_digits');
        if (is_array($digits) && count($digits)===6 && count(array_filter($digits,'is_string'))===6) $this->merge(['code'=>implode('',$digits)]);
    }
    public function authorize(): bool
    {
        return $this->session()->has('password_reset_email');
    }

    public function rules(): array
    {
        return ['code' => ['required', 'string', 'digits:6']];
    }
}
