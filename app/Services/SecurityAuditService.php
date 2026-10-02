<?php
namespace App\Services;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;
class SecurityAuditService
{
    public function record(string $event,string $severity='info',?int $userId=null,?int $status=null): void
    {
        try {
            if (!Schema::hasTable('security_events')) return;
            SecurityEvent::create(['user_id'=>$userId,'event'=>$event,'severity'=>$severity,'ip'=>request()->ip(),'route'=>request()->route()?->getName(),'http_status'=>$status]);
        } catch(Throwable $e) { Log::warning('No se pudo guardar un evento de auditoría.',['exception'=>$e::class]); }
    }
}
