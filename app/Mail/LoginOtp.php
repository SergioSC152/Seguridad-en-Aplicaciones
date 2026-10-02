<?php
namespace App\Mail;
use Illuminate\Mail\Mailable;
class LoginOtp extends Mailable
{
    public function __construct(public string $code) {}
    public function build(): static { return $this->subject('CowApp: código de acceso MFA')->view('emails.auth.login-otp'); }
}
