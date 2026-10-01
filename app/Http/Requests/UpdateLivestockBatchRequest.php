<?php

namespace App\Http\Requests;

use App\Models\LivestockBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLivestockBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('batch'));
    }

    public function rules(): array
    {
        /** @var LivestockBatch $batch */
        $batch = $this->route('batch');

        return [
            'livestock_category_id' => ['required', Rule::exists('livestock_categories', 'id')->where('user_id', $this->user()->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('livestock_batches', 'code')->where('user_id', $this->user()->id)->ignore($batch)],
            'ear_tag' => ['nullable', 'string', 'max:80'],
            'head_count' => ['required', 'integer', 'min:1', 'max:1000000'],
            'average_weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'farm_name' => ['nullable', 'string', 'max:150'],
            'paddock' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(LivestockBatch::STATUSES)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
