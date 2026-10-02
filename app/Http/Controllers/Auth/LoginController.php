<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\LoginProtectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, LoginProtectionService $protection): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:25'],
        ]);

        $credentials['email'] = mb_strtolower(trim($credentials['email']));
        if ($protection->blocked($credentials['email'])) {
            return back()->withErrors(['email' => 'Acceso bloqueado por intentos fallidos. Intenta en '.$protection->seconds($credentials['email']).' segundos o recupera tu contraseña.'])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            $protection->failed($credentials['email']);
            if ($protection->blocked($credentials['email'])) {
                return back()->withErrors(['email' => 'Cinco intentos fallidos: acceso bloqueado temporalmente por correo. Puedes recuperar tu contraseña.'])->onlyInput('email');
            }
            $exists = User::whereRaw('LOWER(email) = ?', [$credentials['email']])->exists();
            return back()
                ->withErrors([
                    $exists ? 'password' : 'email' => $exists ? 'Contraseña incorrecta.' : 'No existe un usuario con ese correo.',
                ])
                ->onlyInput('email');
        }

        $protection->clear($credentials['email']);

        $user=Auth::user();
        if ($user->mfa_enabled) {
            Auth::logout();
            $request->session()->regenerate();
            try { app(\App\Services\LoginMfaService::class)->start($request,$user,false); }
            catch (\Throwable) { return back()->withErrors(['email'=>'No se pudo enviar el código MFA. El acceso sigue cerrado. Revisa SMTP.'])->onlyInput('email'); }
            return to_route('mfa.show');
        }
        app(\App\Services\SecurityAuditService::class)->record('login.success','info',$user->id,200);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function createRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registro exitoso. Ya puedes iniciar sesión con tus credenciales.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
