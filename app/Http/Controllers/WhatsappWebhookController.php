<?php
namespace App\Http\Controllers;
use App\Models\Lead;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WhatsappWebhookController extends Controller
{
    public function verify(Request $r) {
        $expected=(string)config('whatsapp.verify_token');
        abort_unless($expected!=='' && $r->query('hub.mode',$r->query('hub_mode'))==='subscribe' && hash_equals($expected,(string)$r->query('hub.verify_token',$r->query('hub_verify_token'))),403);
        return response((string)$r->query('hub.challenge',$r->query('hub_challenge')))->header('Content-Type','text/plain');
    }
    public function receive(Request $r) {
        $secret=(string)config('whatsapp.app_secret'); $raw=$r->getContent();
        abort_if(strlen($raw)>1048576,413);
        abort_unless($secret!=='' && hash_equals('sha256='.hash_hmac('sha256',$raw,$secret),(string)$r->header('X-Hub-Signature-256')),403);
        $owner=User::whereRaw('LOWER(email) = ?',[mb_strtolower((string)config('cowapp.mail_settings_admin_email'))])->first();
        abort_unless($owner,503);
        foreach((array)$r->input('entry',[]) as $entry) foreach((array)($entry['changes']??[]) as $change) {
            $value=$change['value']??[];
            if (($value['metadata']['phone_number_id']??null)!==config('whatsapp.phone_id')) continue;
            foreach((array)($value['messages']??[]) as $message) {
                if (!isset($message['id'],$message['from']) || strlen($message['id'])>255 || !preg_match('/^[1-9][0-9]{7,14}$/',$message['from'])) continue;
                DB::transaction(function()use($message,$owner) {
                    if(WhatsappMessage::where('provider_id',$message['id'])->exists()) return;
                    $lead=$owner->leads()->firstOrCreate(['phone'=>$message['from']],['name'=>'WhatsApp '.$message['from'],'source'=>'whatsapp','status'=>'new']);
                    WhatsappMessage::firstOrCreate(['provider_id'=>$message['id']],['user_id'=>$owner->id,'lead_id'=>$lead->id,'direction'=>'in','phone'=>$message['from'],'body'=>mb_substr((string)($message['text']['body']??'[Mensaje no textual]'),0,5000),'status'=>'received']);
                });
            }
            foreach((array)($value['statuses']??[]) as $status) if(in_array($status['status']??'', ['sent','delivered','read','failed'],true)) WhatsappMessage::where('provider_id',$status['id']??'')->update(['status'=>$status['status']]);
        }
        return response()->json(['received'=>true]);
    }
}
