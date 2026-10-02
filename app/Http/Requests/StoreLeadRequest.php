<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => filled($this->input('email')) ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'phone' => filled($this->input('phone')) ? trim((string) $this->input('phone')) : null,
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', Lead::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'farm_name' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'source' => ['required', Rule::in(array_keys(Lead::SOURCES))],
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'livestock_interest' => ['nullable', 'string', 'max:200'],
            'estimated_heads' => ['nullable', 'integer', 'between:1,1000000'],
            'follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'hectares'=>['nullable','numeric','between:0,1000000'],
            'carrying_capacity'=>['nullable','numeric','between:0,1000'],
            'budget'=>['nullable','numeric','between:0,100000000000'],
            'purpose'=>['nullable',Rule::in(['cria','ceba','leche','genetica'])],
        ];
    }
}
