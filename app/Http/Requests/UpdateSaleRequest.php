<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('update',$this->route('sale')); }
    public function rules(): array { return ['paid'=>['required','numeric','min:0','max:'.($this->route('sale')->total_cents/100)],'ica_guide'=>['nullable','string','max:100'],'dispatched_at'=>['nullable','date'],'sold_at'=>['required','date']]; }
}
