<?php

namespace Tests\Feature;

use App\Mail\PasswordRecoveryOtp;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordRecoveryOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_recovery_sends_a_generic_response_and_stores_only_a_hash(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'ranch@example.com']);

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect(route('password.otp.form'))
            ->assertSessionHas('status', 'Si el correo está registrado, recibirás instrucciones para continuar.');
        Mail::assertSent(PasswordRecoveryOtp::class);

        $otp = PasswordResetOtp::where('user_id', $user->id)->firstOrFail();
        $this->assertSame(0, $otp->attempts);
        $this->assertTrue($otp->expires_at->isFuture());
        $this->assertNull($otp->verified_at);
        $this->assertMatchesRegularExpression('/^\$2y\$/', $otp->code_hash);
    }

    public function test_valid_otp_resets_password_and_consumes_the_code(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'ranch@example.com']);
        $this->post(route('password.email'), ['email' => $user->email]);

        $code = null;
        Mail::assertSent(PasswordRecoveryOtp::class, function (PasswordRecoveryOtp $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $otp = PasswordResetOtp::where('user_id', $user->id)->firstOrFail();
        $this->assertNotSame($code, $otp->code_hash);
        $this->assertTrue(Hash::check($code, $otp->code_hash));

        $this->post(route('password.otp.verify'), ['code' => $code])
            ->assertRedirect(route('password.reset.form'))
            ->assertSessionHas('password_reset_user_id', $user->id);

        $this->post(route('password.reset'), [
            'password' => 'GanadoSeguro2026!',
            'password_confirmation' => 'GanadoSeguro2026!',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('GanadoSeguro2026!', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_otps', ['user_id' => $user->id]);
    }

    public function test_unknown_email_receives_the_same_confirmation_without_sending_mail(): void
    {
        Mail::fake();

        $response = $this->post(route('password.email'), ['email' => 'unknown@example.com']);

        $response->assertRedirect(route('password.otp.form'))
            ->assertSessionHas('status', 'Si el correo está registrado, recibirás instrucciones para continuar.');
        Mail::assertNothingSent();
        $this->assertDatabaseCount('password_reset_otps', 0);
    }

    public function test_wrong_code_is_limited_to_five_attempts(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'ranch@example.com']);
        $this->post(route('password.email'), ['email' => $user->email]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('password.otp.verify'), ['code' => '000000'])
                ->assertSessionHasErrors('code');
        }

        $this->assertSame(5, PasswordResetOtp::where('user_id', $user->id)->value('attempts'));
    }

    public function test_expired_code_cannot_be_verified(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'ranch@example.com']);
        $this->post(route('password.email'), ['email' => $user->email]);

        $code = null;
        Mail::assertSent(PasswordRecoveryOtp::class, function (PasswordRecoveryOtp $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->travel(11)->minutes();
        $this->post(route('password.otp.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');

        $this->assertNull(PasswordResetOtp::where('user_id', $user->id)->value('verified_at'));
    }

    public function test_otp_requests_are_throttled_per_email_and_ip(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'ranch@example.com']);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('password.email'), ['email' => $user->email])->assertRedirect();
        }

        $this->post(route('password.email'), ['email' => $user->email])->assertStatus(429);
    }

    public function test_recovery_pages_require_the_expected_guest_flow_state(): void
    {
        $this->get(route('password.otp.form'))->assertRedirect(route('password.request'));
        $this->get(route('password.reset.form'))->assertRedirect(route('password.request'));
        $this->get(route('password.request'))->assertOk();
    }
}
