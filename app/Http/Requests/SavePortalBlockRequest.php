<?php
namespace App\Http\Requests;
use App\Models\PortalBlock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SavePortalBlockRequest extends FormRequest
{
    public function authorize(): bool { $record=$this->route('block'); return $record ? $this->user()->can('update',$record) : $this->user()->can('create',PortalBlock::class); }
    public function rules(): array { return ['kind'=>['required',Rule::in(['banner','testimonial','team','gallery','contact'])], 'title'=>['required','string','max:160'], 'body'=>['nullable','string','max:5000'], 'media_id'=>['nullable',Rule::exists('media','id')->where('active',true)], 'link_url'=>['nullable','url','max:2048','regex:~^https?://~i'], 'position'=>['required','integer','between:0,100000'], 'published'=>['required','boolean']]; }
}

