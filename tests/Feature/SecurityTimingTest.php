<?php
namespace Tests\Feature;
use App\Mail\PasswordRecoveryOtp;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\PasswordRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
class SecurityTimingTest extends TestCase
{
    use RefreshDatabase;
    public function test_otp_remains_valid_at_nine_minutes_fifty_nine_seconds_despite_database_datetime_offset(): void {
        Mail::fake();$user=User::factory()->create();$service=app(PasswordRecoveryService::class);$service->sendCode($user->email);
        $code=null;Mail::assertSent(PasswordRecoveryOtp::class,function($mail)use(&$code){$code=$mail->code;return true;});
        $otp=PasswordResetOtp::where('user_id',$user->id)->firstOrFail();$otp->update(['expires_at'=>now()->subHours(5)]);
        $this->travel(599)->seconds();$this->assertSame($user->id,$service->verifyCode($user->email,$code)?->id);
    }
    public function test_otp_is_rejected_at_exactly_ten_minutes(): void {
        Mail::fake();$user=User::factory()->create();$service=app(PasswordRecoveryService::class);$service->sendCode($user->email);
        $code=null;Mail::assertSent(PasswordRecoveryOtp::class,function($mail)use(&$code){$code=$mail->code;return true;});
        $this->travel(600)->seconds();$this->assertNull($service->verifyCode($user->email,$code));
    }
    public function test_five_incorrect_passwords_block_the_email_across_ips_and_correct_password_is_rejected_until_timeout(): void {
        $user=User::factory()->create(['email'=>'account@example.com','password'=>'CorrectPassword2026!']);
        for($n=0;$n<5;$n++)$this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.'.($n+1)])->post(route('login.store'),['email'=>strtoupper($user->email),'password'=>'Wrong'])->assertSessionHasErrors('email');
        $this->withServerVariables(['REMOTE_ADDR'=>'192.0.2.99'])->post(route('login.store'),['email'=>$user->email,'password'=>'CorrectPassword2026!'])->assertSessionHasErrors('email');$this->assertGuest();
        $this->travel(901)->seconds();$this->post(route('login.store'),['email'=>$user->email,'password'=>'CorrectPassword2026!'])->assertRedirect(route('dashboard'));$this->assertAuthenticatedAs($user);
    }
    public function test_mfa_enabled_account_does_not_enter_dashboard_after_password_alone(): void {
        Mail::fake();$user=User::factory()->create(['password'=>'CorrectPassword2026!']);$user->forceFill(['mfa_enabled'=>true])->save();
        $this->post(route('login.store'),['email'=>$user->email,'password'=>'CorrectPassword2026!'])->assertRedirect(route('mfa.show'));$this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $code=null;Mail::assertSent(\App\Mail\LoginOtp::class,function($mail)use(&$code){$code=$mail->code;return true;});
        $this->post(route('mfa.verify'),['code'=>$code])->assertRedirect(route('dashboard'));$this->assertAuthenticatedAs($user);
    }
}
