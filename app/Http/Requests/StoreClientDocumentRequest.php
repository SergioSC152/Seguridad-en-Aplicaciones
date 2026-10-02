<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreClientDocumentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('update',$this->route('client')); }
    public function rules(): array { return ['kind'=>['required',Rule::in(['ruv','brucellosis','tuberculosis','fedegan','ica','other'])],'reference'=>['nullable','string','max:100'],'expires_at'=>['nullable','date'],'file'=>['required','file','mimes:pdf,jpg,jpeg,png','max:5120']]; }
}
