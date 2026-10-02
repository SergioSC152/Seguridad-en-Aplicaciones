<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class LoginProtectionService
{
    private function key(string $email): string { return 'login-failures:'.hash('sha256', mb_strtolower(trim($email))); }
    public function blocked(string $email): bool { return $this->seconds($email)>0; }
    public function seconds(string $email): int { return max(0,(int)Cache::get($this->key($email).':blocked_until',0)-now()->timestamp); }
    public function failed(string $email): void {
        $attempts=RateLimiter::hit($this->key($email), 900);
        if($attempts>=5) Cache::put($this->key($email).':blocked_until',now()->timestamp+900,900);
        $userId=\App\Models\User::whereRaw('LOWER(email) = ?',[mb_strtolower(trim($email))])->value('id');
        app(SecurityAuditService::class)->record($this->blocked($email)?'login.blocked':'login.failed','warning',$userId,401);
    }
    public function clear(string $email): void { RateLimiter::clear($this->key($email)); Cache::forget($this->key($email).':blocked_until'); }
}
