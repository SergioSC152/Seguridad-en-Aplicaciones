<?php
namespace App\Services;
use App\Models\Lead;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
class WhatsappService
{
    public function configured(): bool { return filled(config('whatsapp.token')) && filled(config('whatsapp.phone_id')) && preg_match('/^v[0-9]+\.[0-9]+$/',(string)config('whatsapp.graph_version')); }
    public function send(Lead $lead,string $body): void {
        if (!$this->configured()) throw ValidationException::withMessages(['message'=>'WhatsApp Cloud API no está configurada.']);
        $phone=preg_replace('/\D/','',(string)$lead->phone);
        if (!preg_match('/^[1-9][0-9]{7,14}$/',$phone)) throw ValidationException::withMessages(['lead_id'=>'El teléfono necesita código de país y entre 8 y 15 dígitos.']);
        $phoneId=(string)config('whatsapp.phone_id');
        if (!preg_match('/^[0-9]+$/',$phoneId)) throw ValidationException::withMessages(['message'=>'Identificador de teléfono Meta inválido.']);
        $response=Http::withToken(config('whatsapp.token'))->timeout(15)->post('https://graph.facebook.com/'.config('whatsapp.graph_version').'/'.$phoneId.'/messages',['messaging_product'=>'whatsapp','to'=>$phone,'type'=>'text','text'=>['body'=>$body]]);
        if (!$response->successful() || !$response->json('messages.0.id')) throw ValidationException::withMessages(['message'=>'Meta rechazó el envío. Revisa permisos, número, ventana de atención y necesidad de plantilla aprobada.']);
        WhatsappMessage::create(['user_id'=>$lead->user_id,'lead_id'=>$lead->id,'provider_id'=>$response->json('messages.0.id'),'direction'=>'out','phone'=>$phone,'body'=>$body,'status'=>'accepted']);
    }
}
