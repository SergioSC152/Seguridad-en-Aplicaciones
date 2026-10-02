<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RecordMeasurementRequest extends FormRequest {
    public function authorize(): bool {return $this->user()->can('update',$this->route('batch'));}
    public function rules(): array {return ['average_weight_kg'=>['required','numeric','between:0,999999.99'],'rfid'=>['nullable','string','max:100']];}
}
