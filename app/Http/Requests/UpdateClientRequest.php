<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => filled($this->input('email')) ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'document_number' => filled($this->input('document_number')) ? mb_strtoupper(trim((string) $this->input('document_number'))) : null,
        ]);
    }

    public function authorize(): bool
    {
        $client = $this->route('client');

        return $client instanceof Client && ($this->user()?->can('update', $client) ?? false);
    }

    public function rules(): array
    {
        /** @var Client $client */
        $client = $this->route('client');
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:160'],
            'client_type' => ['required', Rule::in(Client::TYPES)],
            'contact_person' => ['nullable', 'string', 'max:160'],
            'document_number' => ['nullable', 'string', 'max:40', Rule::unique('clients', 'document_number')->where('user_id', $userId)->ignore($client)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('clients', 'email')->where('user_id', $userId)->ignore($client)],
            'phone' => ['nullable', 'string', 'max:40'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(Client::STATUSES)],
            'notes' => ['nullable', 'string', 'max:3000'],
            'ica_registration'=>['nullable','string','max:100'],
            'credit_limit'=>['sometimes','numeric','between:0,100000000000'],
            'vip'=>['sometimes','boolean'],
        ];
    }
}
