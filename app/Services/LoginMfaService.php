<?php
namespace App\Services;
use App\Mail\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class LoginMfaService
{
    public function start(Request $request,User $user,bool $remember): void
    {
        $code=str_pad((string)random_int(0,999999),6,'0',STR_PAD_LEFT);
        Mail::to($user->email)->send(new LoginOtp($code));
        $request->session()->put('login_mfa',['user_id'=>$user->id,'hash'=>Hash::make($code),'expires'=>now()->timestamp+600,'attempts'=>0,'remember'=>$remember]);
    }
    public function verify(Request $request,string $code): ?User
    {
        $challenge=$request->session()->get('login_mfa');
        if (!$challenge || now()->timestamp >= $challenge['expires'] || $challenge['attempts']>=5) { $request->session()->forget('login_mfa'); return null; }
        if (!Hash::check($code,$challenge['hash'])) { $challenge['attempts']++; $request->session()->put('login_mfa',$challenge); app(SecurityAuditService::class)->record('login.mfa_failed','warning',$challenge['user_id'],401); return null; }
        return User::find($challenge['user_id']);
    }
}
