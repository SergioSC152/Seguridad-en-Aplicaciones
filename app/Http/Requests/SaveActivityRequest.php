<?php
namespace App\Http\Requests;
use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveActivityRequest extends FormRequest
{
    public function authorize(): bool { $record=$this->route('activity'); return $record ? $this->user()->can('update',$record) : $this->user()->can('create',Activity::class); }
    public function rules(): array { return ['title'=>['required','string','max:160'], 'client_id'=>['nullable',Rule::exists('clients','id')->where('user_id',$this->user()->id)], 'sales_opportunity_id'=>['nullable',Rule::exists('sales_opportunities','id')->where('user_id',$this->user()->id)], 'due_at'=>['required','date'], 'status'=>['required',Rule::in(['pending','done','cancelled'])], 'notes'=>['nullable','string','max:5000']]; }
}

