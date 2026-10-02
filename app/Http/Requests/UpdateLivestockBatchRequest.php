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
            'livestock_category_id' => ['nullable', 'required_without:new_category_name', Rule::exists('livestock_categories', 'id')->where('user_id', $this->user()->id)],
            'new_category_name' => ['nullable', 'required_without:livestock_category_id', 'prohibited_with:livestock_category_id', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', Rule::unique('livestock_batches', 'code')->where('user_id', $this->user()->id)->ignore($batch)],
            'ear_tag' => ['nullable', 'string', 'max:80'],
            'head_count' => ['required', 'integer', 'min:1', 'max:1000000'],
            'average_weight_kg' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'farm_name' => ['nullable', 'string', 'max:150'],
            'paddock' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(LivestockBatch::STATUSES)],
            'notes' => ['nullable', 'string', 'max:5000'],
            'purpose'=>['sometimes',Rule::in(['cria','ceba','leche','genetica'])],
            'availability'=>['sometimes',Rule::in(['available','auction','bidding','reserved','awarded'])],
            'published'=>['sometimes','boolean'],
            'price_per_kg'=>['nullable','numeric','between:0,1000000'],
            'rfid'=>['nullable','string','max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=6000,max_height=6000'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
