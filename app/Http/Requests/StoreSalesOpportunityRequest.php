<?php

namespace App\Http\Requests;

use App\Models\SalesOpportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', SalesOpportunity::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')->where('user_id', $this->user()->id)],
            'title' => ['required', 'string', 'max:180'],
            'livestock_summary' => ['nullable', 'string', 'max:200'],
            'head_count' => ['nullable', 'integer', 'between:1,1000000'],
            'estimated_value' => ['required', 'numeric', 'between:0,999999999999.99'],
            'stage' => ['required', Rule::in(array_keys(SalesOpportunity::STAGES))],
            'expected_close_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
