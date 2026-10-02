<?php
namespace App\Services;
use App\Models\Sale;
use Illuminate\Validation\ValidationException;
class SaleService
{
    public function update(Sale $sale,array $data): void
    {
        if (!empty($data['dispatched_at']) && empty($data['ica_guide'])) throw ValidationException::withMessages(['ica_guide'=>'Registra la referencia de guía ICA antes del despacho.']);
        $paid=(int)round($data['paid']*100); unset($data['paid']);
        if (!empty($data['ica_guide']) && !$sale->guide_recorded_at) $data['guide_recorded_at']=now();
        $sale->update($data+['paid_cents'=>$paid]);
    }
}
