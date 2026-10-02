<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordWithOtpRequest;
use App\Http\Requests\SendPasswordOtpRequest;
use App\Http\Requests\VerifyPasswordOtpRequest;
use App\Services\PasswordRecoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    public function __construct(private readonly PasswordRecoveryService $recovery) {}

    public function requestForm(): View
    {
        return view('auth.password.request');
    }

    public function send(SendPasswordOtpRequest $request): RedirectResponse
    {
        $email = Str::lower(trim($request->validated('email')));
        $expires = $this->recovery->sendCode($email);
        $request->session()->put('password_reset_expires', $expires);
        $request->session()->put('password_reset_email', $email);

        return to_route('password.otp.form')->with(
            'status',
            'Si el correo está registrado, recibirás instrucciones para continuar.'
        );
    }

    public function otpForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return to_route('password.request');
        }

        return view('auth.password.verify-otp', ['maskedEmail' => $this->maskEmail($email), 'expiresAt' => $request->session()->get('password_reset_expires'), 'serverNow' => now()->timestamp]);
    }

    public function verify(VerifyPasswordOtpRequest $request): RedirectResponse
    {
        $user = $this->recovery->verifyCode(
            $request->session()->get('password_reset_email'),
            $request->validated('code')
        );

        if (! $user) {
            return back()->withErrors(['code' => 'No se pudo verificar. Usa el último código recibido; revisa el contador y el límite de cinco intentos.']);
        }

        $request->session()->regenerate();
        $request->session()->put('password_reset_user_id', $user->id);
        $request->session()->forget('password_reset_email');

        return to_route('password.reset.form');
    }

    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return to_route('password.request');
        }

        $request->session()->put('password_reset_expires', $this->recovery->sendCode($email));

        return back()->with('status', 'Si el correo está registrado, enviaremos un nuevo código.');
    }

    public function resetForm(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('password_reset_user_id');

        if (! $userId || ! $this->recovery->canReset((int) $userId)) {
            $request->session()->forget(['password_reset_user_id', 'password_reset_email']);

            return to_route('password.request')->withErrors(['email' => 'La verificación venció. Solicita un nuevo código.']);
        }

        return view('auth.password.reset');
    }

    public function reset(ResetPasswordWithOtpRequest $request): RedirectResponse
    {
        $userId = (int) $request->session()->get('password_reset_user_id');

        if (! $this->recovery->resetPassword($userId, $request->validated('password'))) {
            $request->session()->forget(['password_reset_user_id', 'password_reset_email']);

            return to_route('password.request')->withErrors(['email' => 'La verificación venció. Solicita un nuevo código.']);
        }

        $request->session()->forget(['password_reset_user_id', 'password_reset_email']);
        $request->session()->regenerateToken();

        return to_route('login')->with('success', 'Tu contraseña se actualizó. Ya puedes iniciar sesión.');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($local, 0, 1).'***@'.$domain;
    }
}
