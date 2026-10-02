<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecordMeasurementRequest;
use App\Models\LivestockBatch;
use App\Models\LivestockMeasurement;
use Illuminate\Support\Facades\DB;
class MeasurementController extends Controller {
    public function store(RecordMeasurementRequest $r,LivestockBatch $batch) {
        $data=$r->validated();
        $measurement=DB::transaction(function()use($r,$batch,$data){$batch->update($data);return LivestockMeasurement::create(['user_id'=>$r->user()->id,'livestock_batch_id'=>$batch->id,'source'=>'api']+$data);});
        return response()->json(['message'=>'Pesaje promedio registrado.','measurement'=>$measurement],201);
    }
}
