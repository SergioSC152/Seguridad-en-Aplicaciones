<?php

namespace App\Http\Requests;

use App\Models\MailSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMailSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', MailSetting::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'host' => ['required', 'string', 'max:255', 'regex:/^(?=.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)(?:\.(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?))*$/'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:1024'],
            'encryption' => ['required', Rule::in(['tls', 'ssl'])],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
        ];
    }
}
