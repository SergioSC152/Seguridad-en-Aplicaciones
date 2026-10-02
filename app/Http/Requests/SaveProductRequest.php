<?php
namespace App\Http\Requests;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveProductRequest extends FormRequest
{
    public function authorize(): bool { $record=$this->route('product'); return $record ? $this->user()->can('update',$record) : $this->user()->can('create',Product::class); }
    public function rules(): array { return ['name'=>['required','string','max:160'], 'kind'=>['required',Rule::in(['product','service'])], 'purpose'=>['nullable',Rule::in(['cria','ceba','leche','genetica'])], 'price'=>['required','numeric','between:0,1000000'], 'description'=>['nullable','string','max:5000'], 'media_id'=>['nullable',Rule::exists('media','id')->where('active',true)], 'published'=>['required','boolean'], 'active'=>['required','boolean']]; }
}

