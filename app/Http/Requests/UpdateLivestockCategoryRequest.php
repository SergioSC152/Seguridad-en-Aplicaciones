<?php

namespace App\Http\Requests;

use App\Models\LivestockCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLivestockCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('category'));
    }

    public function rules(): array
    {
        /** @var LivestockCategory $category */
        $category = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('livestock_categories', 'name')->where('user_id', $this->user()->id)->ignore($category)],
            'description' => ['nullable', 'string', 'max:500'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
