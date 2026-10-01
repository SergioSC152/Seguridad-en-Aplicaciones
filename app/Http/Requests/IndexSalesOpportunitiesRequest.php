<?php

namespace App\Http\Requests;

use App\Models\SalesOpportunity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexSalesOpportunitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', SalesOpportunity::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'stage' => ['nullable', Rule::in(array_keys(SalesOpportunity::STAGES))],
            'client_id' => ['nullable', 'integer', Rule::exists('clients', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
