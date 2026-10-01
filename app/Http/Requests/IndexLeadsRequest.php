<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLeadsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Lead::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', Rule::in(array_keys(Lead::SOURCES))],
            'status' => ['nullable', Rule::in(array_keys(Lead::STATUSES))],
        ];
    }
}
