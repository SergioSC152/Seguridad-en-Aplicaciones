<?php
namespace App\Http\Requests;
use App\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveQuoteRequest extends FormRequest
{
    public function authorize(): bool { $q=$this->route('quote'); return $q ? $this->user()->can('update',$q) : $this->user()->can('create',Quote::class); }
    public function rules(): array { return [
        'client_id'=>['required',Rule::exists('clients','id')->where('user_id',$this->user()->id)],
        'livestock_batch_id'=>['nullable',Rule::exists('livestock_batches','id')->where('user_id',$this->user()->id)],
        'title'=>['required','string','max:160'], 'gross_kg'=>['required','numeric','between:0.001,1000000'],
        'price_per_kg'=>['required','numeric','between:0.01,1000000'],
        'shrink_percent'=>['required','numeric','between:0,99.99'], 'withholding_percent'=>['required','numeric','between:0,99.99'],
        'commission_percent'=>['required','numeric','between:0,99.99'], 'other_deduction'=>['required','numeric','between:0,999999999999.99'],
        'valid_until'=>['required','date','after_or_equal:today'], 'terms'=>['nullable','string','max:10000'],
    ]; }
}
