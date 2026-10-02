<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Services\LoginMfaService;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class MfaController extends Controller
{
    public function show(Request $r) { if (!$r->session()->has('login_mfa')) return to_route('login'); return view('auth.mfa'); }
    public function verify(Request $r,LoginMfaService $mfa,SecurityAuditService $audit) {
        $digits=$r->input('code_digits'); if(is_array($digits) && count($digits)===6 && count(array_filter($digits,'is_string'))===6)$r->merge(['code'=>implode('',$digits)]);
        $d=$r->validate(['code'=>['required','digits:6']]); $user=$mfa->verify($r,$d['code']);
        if (!$user) return back()->withErrors(['code'=>'Código incorrecto o verificación vencida. Tras cinco intentos vuelve a iniciar sesión.']);
        $remember=(bool)$r->session()->get('login_mfa.remember'); $r->session()->forget('login_mfa'); Auth::login($user,$remember); $r->session()->regenerate(); $audit->record('login.mfa_success','info',$user->id,200); return redirect()->intended(route('dashboard'));
    }
}
