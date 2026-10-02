<?php
namespace App\Http\Controllers;
use App\Models\Quote;
use App\Services\QuoteService;
use Illuminate\Http\Request;
use Throwable;
class ContractController extends Controller
{
    private function find(string $token): Quote { $quote=Quote::where('contract_token_hash',hash('sha256',$token))->with('client')->firstOrFail(); abort_if(now()->timestamp >= ($quote->contract_expires_epoch ?? 0),410,'Este enlace venció.'); return $quote; }
    public function show(string $token) { return view('contracts.show',['quote'=>$this->find($token),'token'=>$token]); }
    public function code(string $token, QuoteService $service) { $quote=$this->find($token); abort_if($quote->status!=='sent',409); try { $service->sendAcceptanceOtp($quote); } catch (Throwable) { return back()->withErrors(['code'=>'No se pudo enviar el código. Contacta al vendedor.']); } return back()->with('success','Código enviado al correo del cliente.'); }
    public function accept(Request $r, string $token, QuoteService $s) { $data=$r->validate(['code'=>['required','digits:6'],'agree'=>['accepted']]); if (! $s->accept($this->find($token),$data['code'],$r->ip())) return back()->withErrors(['code'=>'Código incorrecto, vencido o con cinco intentos fallidos.']); return back()->with('success','Aceptación registrada.'); }
}
