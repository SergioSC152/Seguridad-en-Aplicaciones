<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Services\SecurityAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class SecurityController extends Controller
{
    public function index(Request $r) { Gate::authorize('permission','audit.view'); $d=$r->validate(['severity'=>['nullable','in:info,warning,critical']]); return view('admin.security.index',['events'=>SecurityEvent::query()->when(!$r->user()->isPlatformAdmin(),fn($q)=>$q->where('user_id',$r->user()->id))->when($d['severity']??null,fn($q,$s)=>$q->where('severity',$s))->latest()->paginate(30)]); }
    public function account() { return view('admin.security.account'); }
    public function snapshot(Request $r) { Gate::authorize('permission','audit.view'); return response()->json(SecurityEvent::query()->when(!$r->user()->isPlatformAdmin(),fn($q)=>$q->where('user_id',$r->user()->id))->latest()->limit(30)->get(['created_at','event','severity','ip','route','http_status'])); }
    public function update(Request $r,SecurityAuditService $audit) { $d=$r->validate(['password'=>['required','current_password', 'max:25'],'mfa_enabled'=>['required','boolean']]); $r->user()->forceFill(['mfa_enabled'=>(bool)$d['mfa_enabled'],'remember_token'=>\Illuminate\Support\Str::random(60)])->save(); $r->user()->tokens()->delete(); $audit->record('mfa.settings_changed','warning',$r->user()->id,200); return back()->with('success','Configuración MFA actualizada y tokens API anteriores revocados. Al activarla se requiere un código de correo para iniciar sesión.'); }
}
