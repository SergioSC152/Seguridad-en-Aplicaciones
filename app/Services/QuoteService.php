<?php
namespace App\Services;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuoteService
{
    public function calculate(array $data): array
    {
        $grams=(int) round((float)$data['gross_kg']*1000);
        $shrink=(int) round((float)$data['shrink_percent']*100);
        $netGrams=intdiv($grams*(10000-$shrink)+5000,10000);
        $priceCents=(int) round((float)$data['price_per_kg']*100);
        $subtotal=intdiv($netGrams*$priceCents+500,1000);
        $withholding=intdiv($subtotal*(int)round((float)$data['withholding_percent']*100)+5000,10000);
        $commission=intdiv($subtotal*(int)round((float)$data['commission_percent']*100)+5000,10000);
        $other=(int)round((float)$data['other_deduction']*100);
        if ($withholding+$commission+$other>$subtotal) throw ValidationException::withMessages(['other_deduction'=>'Las deducciones superan el valor bruto.']);
        return ['subtotal_cents'=>$subtotal,'total_cents'=>$subtotal-$withholding-$commission-$other];
    }
    public function save(User $user, array $data, ?Quote $quote=null): Quote
    {
        $totals=$this->calculate($data);
        return DB::transaction(function () use ($user,$data,$totals,$quote) {
            if ($quote) {
                $quote=Quote::whereKey($quote->id)->lockForUpdate()->firstOrFail();
                if ($quote->status==='accepted') throw ValidationException::withMessages(['title'=>'Una cotización aceptada no puede modificarse.']);
                $quote->update($data+$totals+['status'=>'draft','contract_token_hash'=>null,'acceptance_otp_hash'=>null]);
                return $quote->refresh();
            }
            return Quote::create(['user_id'=>$user->id,'number'=>'COT-'.now()->format('Ymd').'-'.Str::upper(Str::random(8))]+$data+$totals);
        });
    }
    public function sendContract(Quote $quote): void
    {
        if ($quote->status==='accepted' || $quote->valid_until->endOfDay()->isPast()) throw ValidationException::withMessages(['contract'=>'Cotización aceptada o vencida.']);
        if (! $quote->client->email) throw ValidationException::withMessages(['client_id'=>'El cliente necesita un correo para recibir el contrato.']);
        $token=Str::random(64);
        $updated=Quote::whereKey($quote->id)->whereIn('status',['draft','sent'])->update(['status'=>'sent','contract_token_hash'=>hash('sha256',$token),'contract_expires_epoch'=>$quote->valid_until->endOfDay()->timestamp,'acceptance_otp_hash'=>null,'acceptance_attempts'=>0]);
        if (!$updated) throw ValidationException::withMessages(['contract'=>'La cotización cambió; actualiza la página.']);
        $url=route('contracts.show',['token'=>$token]);
        Mail::raw('Revisa la cotización '.$quote->number.' de CowApp y confirma si estás de acuerdo: '.$url.' . No compartas este enlace.', fn ($message) => $message->to($quote->client->email)->subject('CowApp: cotización '.$quote->number));
    }
    public function sendAcceptanceOtp(Quote $quote): void
    {
        $code=str_pad((string)random_int(0,999999),6,'0',STR_PAD_LEFT);
        $quote->update(['acceptance_otp_hash'=>Hash::make($code),'acceptance_expires_epoch'=>now()->timestamp+600,'acceptance_attempts'=>0]);
        Mail::raw('Código CowApp para aceptar la cotización '.$quote->number.': '.$code.'. Válido 10 minutos; usa el último código solicitado.', fn ($message) => $message->to($quote->client->email)->subject('CowApp: confirmación de cotización'));
        Quote::whereKey($quote->id)->where('acceptance_otp_hash',$quote->acceptance_otp_hash)->update(['acceptance_expires_epoch'=>now()->timestamp+600]);
    }
    public function accept(Quote $quote, string $code, string $ip): bool
    {
        return DB::transaction(function () use ($quote,$code,$ip) {
            $quote=Quote::whereKey($quote->id)->lockForUpdate()->firstOrFail();
            if ($quote->status!=='sent' || now()->timestamp >= ($quote->contract_expires_epoch ?? 0) || now()->timestamp >= ($quote->acceptance_expires_epoch ?? 0) || $quote->acceptance_attempts>=5 || ! $quote->acceptance_otp_hash) return false;
            if (!Hash::check($code,$quote->acceptance_otp_hash)) { $quote->increment('acceptance_attempts'); return false; }
            $quote->update(['status'=>'accepted','accepted_at'=>now(),'accepted_ip'=>$ip,'accepted_document_hash'=>hash('sha256',json_encode($quote->only(['number','client_id','title','gross_kg','shrink_percent','price_per_kg','withholding_percent','commission_percent','other_deduction','total_cents','terms']))),'acceptance_otp_hash'=>null]);
            return true;
        });
    }
    public function recordSale(Quote $quote): Sale
    {
        return DB::transaction(function () use ($quote) {
            $quote=Quote::whereKey($quote->id)->lockForUpdate()->firstOrFail();
            if ($quote->status!=='accepted') throw ValidationException::withMessages(['quote'=>'La cotización debe estar aceptada por el cliente.']);
            if ($existing=$quote->sale()->first()) return $existing;
            if ($quote->livestock_batch_id) {
                $batch=\App\Models\LivestockBatch::whereKey($quote->livestock_batch_id)->lockForUpdate()->firstOrFail();
                if ($batch->status!=='active') throw ValidationException::withMessages(['quote'=>'Este lote ya no está disponible para una nueva venta.']);
                if (\App\Models\Auction::where('livestock_batch_id',$batch->id)->where('status','awarded')->where('winner_client_id','!=',$quote->client_id)->exists()) throw ValidationException::withMessages(['quote'=>'El cliente no corresponde al adjudicatario del remate.']);
            }
            $sale=Sale::firstOrCreate(['quote_id'=>$quote->id],['user_id'=>$quote->user_id,'client_id'=>$quote->client_id,'total_cents'=>$quote->total_cents,'sold_at'=>today()]);
            if ($quote->batch) $quote->batch->update(['status'=>'sold','availability'=>'awarded','published'=>false]);
            return $sale;
        });
    }
}
