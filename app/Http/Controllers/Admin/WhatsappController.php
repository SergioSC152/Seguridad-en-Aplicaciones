<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\WhatsappMessage;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
class WhatsappController extends Controller {
    public function index(WhatsappService $service){Gate::authorize('permission','leads.manage');return view('admin.whatsapp.index',['messages'=>WhatsappMessage::where('user_id',auth()->id())->latest()->paginate(30),'leads'=>Lead::where('user_id',auth()->id())->whereNotNull('phone')->get(),'configured'=>$service->configured()]);}
    public function send(Request $r,WhatsappService $s){Gate::authorize('permission','leads.manage');$d=$r->validate(['lead_id'=>['required',Rule::exists('leads','id')->where('user_id',$r->user()->id)],'message'=>['required','string','max:4096']]);try{$s->send(Lead::where('user_id',$r->user()->id)->findOrFail($d['lead_id']),$d['message']);}catch(\Illuminate\Validation\ValidationException $e){throw $e;}catch(\Throwable){return back()->with('error','No se pudo conectar con Meta.');}return back()->with('success','Meta aceptó el mensaje; la entrega se actualiza por webhook.');}
}
