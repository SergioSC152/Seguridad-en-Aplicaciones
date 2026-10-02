<?php

namespace Tests\Feature;

use App\Models\MailSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ConfigureGmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_updates_existing_settings_and_stores_secret_encrypted_without_sending(): void
    {
        MailSetting::query()->create([
            'host' => 'sandbox.smtp.mailtrap.io', 'port' => 2525,
            'encryption' => 'tls', 'from_address' => 'old@example.com', 'from_name' => 'Old',
        ]);
        Mail::shouldReceive('purge')->with('smtp')->once();
        $secret = 'abcdefghijklmnop';

        $this->artisan('cowapp:correo-gmail')
            ->expectsQuestion('Gmail emisor', 'sender@gmail.com')
            ->expectsQuestion('Contraseña de aplicación Google (entrada oculta)', $secret)
            ->doesntExpectOutputToContain($secret)
            ->assertExitCode(0);

        $this->assertDatabaseCount('mail_settings', 1);
        $settings = MailSetting::query()->firstOrFail();
        $this->assertSame('smtp.gmail.com', $settings->host);
        $this->assertSame(587, $settings->port);
        $this->assertSame('sender@gmail.com', $settings->from_address);
        $this->assertSame($secret, $settings->password);
        $this->assertNotSame($secret, $settings->getRawOriginal('password'));
    }

    public function test_invalid_application_password_is_not_saved(): void
    {
        $this->artisan('cowapp:correo-gmail')
            ->expectsQuestion('Gmail emisor', 'sender@gmail.com')
            ->expectsQuestion('Contraseña de aplicación Google (entrada oculta)', 'short')
            ->expectsOutputToContain('Se requieren los 16 caracteres')
            ->assertExitCode(1);
        $this->assertDatabaseCount('mail_settings', 0);
    }

    public function test_smtp_failure_reports_authentication_without_printing_transport_secret(): void
    {
        $secret = 'abcdefghijklmnop';
        Mail::shouldReceive('purge')->with('smtp')->once();
        $mailer = Mockery::mock();
        $mailer->shouldReceive('raw')->once()->andThrow(new RuntimeException('535 Authentication failed: '.$secret));
        Mail::shouldReceive('mailer')->with('smtp')->once()->andReturn($mailer);

        $this->artisan('cowapp:correo-gmail', ['--probar' => true])
            ->expectsQuestion('Gmail emisor', 'sender@gmail.com')
            ->expectsQuestion('Contraseña de aplicación Google (entrada oculta)', $secret)
            ->expectsOutputToContain('Gmail rechazó la autenticación')
            ->doesntExpectOutputToContain($secret)
            ->assertExitCode(1);
        $this->assertDatabaseHas('mail_settings', ['host' => 'smtp.gmail.com']);
    }
}
