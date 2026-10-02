<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CaptureLeadRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>['required','string','max:160'],'email'=>['required','email','max:100'],'phone'=>['nullable','string','max:40'],'farm_name'=>['nullable','string','max:160'],'municipality'=>['nullable','string','max:100'],'livestock_batch_id'=>['nullable',Rule::exists('livestock_batches','id')->where('published',true)->where('status','active')],'hectares'=>['nullable','numeric','between:0,1000000'],'carrying_capacity'=>['nullable','numeric','between:0,1000'],'budget'=>['nullable','numeric','between:0,100000000000'],'purpose'=>['required',Rule::in(['cria','ceba','leche','genetica'])],'notes'=>['nullable','string','max:3000'],'consent'=>['accepted']]; }
}
