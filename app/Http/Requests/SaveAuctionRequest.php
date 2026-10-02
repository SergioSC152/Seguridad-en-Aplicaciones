<?php
namespace App\Http\Requests;
use App\Models\Auction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveAuctionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('create',Auction::class); }
    public function rules(): array { return ['livestock_batch_id'=>['required',Rule::exists('livestock_batches','id')->where('user_id',$this->user()->id)->where('status','active')],'title'=>['required','string','max:160'],'reserve'=>['required','numeric','between:0.01,1000000000']]; }
}
