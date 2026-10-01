<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLivestockCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('livestock_categories', 'name')->where('user_id', $this->user()->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
