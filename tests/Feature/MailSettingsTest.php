<?php

namespace Tests\Feature;

use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cowapp.mail_settings_admin_email' => 'admin@cowapp.test']);
    }

    public function test_only_the_configured_admin_can_open_the_mail_settings_page(): void
    {
        $this->get(route('admin.settings.mail.edit'))->assertRedirect(route('login'));

        $user = User::factory()->create(['email' => 'staff@cowapp.test']);
        $this->actingAs($user)->get(route('admin.settings.mail.edit'))->assertForbidden();

        $admin = User::factory()->create(['email' => 'ADMIN@cowapp.test']);
        $response = $this->actingAs($admin)->get(route('admin.settings.mail.edit'));
        $response->assertOk()->assertSee('Configuración SMTP')->assertDontSee('CLAVE_DEL_CORREO');
    }

    public function test_missing_admin_configuration_denies_all_authenticated_users(): void
    {
        config(['cowapp.mail_settings_admin_email' => null]);
        $user = User::factory()->create(['email' => 'admin@cowapp.test']);

        $this->actingAs($user)->get(route('admin.settings.mail.edit'))->assertForbidden();
    }

    public function test_admin_can_save_encrypted_smtp_settings_without_returning_secret(): void
    {
        $admin = User::factory()->create(['email' => 'admin@cowapp.test']);

        $response = $this->actingAs($admin)->put(route('admin.settings.mail.update'), [
            'host' => 'smtp.cowapp.test',
            'port' => 587,
            'username' => 'mailer@cowapp.test',
            'password' => 'VerySecretSmtpPassword!2026',
            'encryption' => 'tls',
            'from_address' => 'noreply@cowapp.test',
            'from_name' => 'CowApp',
        ]);

        $response->assertRedirect(route('admin.settings.mail.edit'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $settings = MailSetting::query()->firstOrFail();
        $this->assertNotSame('VerySecretSmtpPassword!2026', $settings->getRawOriginal('password'));
        $this->assertSame('VerySecretSmtpPassword!2026', $settings->password);
        $this->assertArrayNotHasKey('password', $settings->toArray());
        $this->assertSame('smtp.cowapp.test', config('mail.mailers.smtp.host'));
        $this->assertSame('noreply@cowapp.test', config('mail.from.address'));

        $this->get(route('admin.settings.mail.edit'))
            ->assertOk()
            ->assertDontSee('VerySecretSmtpPassword!2026');
    }

    public function test_blank_password_on_update_preserves_existing_encrypted_password(): void
    {
        $admin = User::factory()->create(['email' => 'admin@cowapp.test']);
        MailSetting::query()->create($this->validSettings(['password' => 'ExistingSecret2026!']));

        $response = $this->actingAs($admin)->put(route('admin.settings.mail.update'), $this->validSettings([
            'host' => 'smtp-updated.cowapp.test',
            'password' => '',
        ]))->assertSessionHasNoErrors();

        $settings = MailSetting::query()->firstOrFail();
        $this->assertSame('ExistingSecret2026!', $settings->password);
        $this->assertSame('smtp-updated.cowapp.test', $settings->host);
    }

    public function test_invalid_or_insecure_smtp_values_are_rejected(): void
    {
        $admin = User::factory()->create(['email' => 'admin@cowapp.test']);

        $response = $this->actingAs($admin)->put(route('admin.settings.mail.update'), $this->validSettings([
            'host' => 'smtp..cowapp.test',
            'port' => 70000,
            'encryption' => 'none',
            'from_address' => 'invalid-address',
            'password' => 'DoNotFlashThisSecret!2026',
        ]));

        $response->assertSessionHasErrors(['host', 'port', 'encryption', 'from_address'])
            ->assertSessionMissing('_old_input.password');
        $this->assertDatabaseCount('mail_settings', 0);
    }

    /** @return array<string, mixed> */
    private function validSettings(array $overrides = []): array
    {
        return array_merge([
            'host' => 'smtp.cowapp.test',
            'port' => 587,
            'username' => 'mailer@cowapp.test',
            'password' => '',
            'encryption' => 'tls',
            'from_address' => 'noreply@cowapp.test',
            'from_name' => 'CowApp',
        ], $overrides);
    }
}
