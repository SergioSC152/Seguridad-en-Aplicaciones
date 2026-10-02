<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CredentialMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_distinguishes_missing_user_and_wrong_password(): void
    {
        $this->post(route('login.store'), ['email'=>'missing@example.com','password'=>'wrong-password'])
            ->assertSessionHasErrors(['email'=>'No existe un usuario con ese correo.']);
        $user = User::factory()->create();
        $this->post(route('login.store'), ['email'=>$user->email,'password'=>'wrong-password'])
            ->assertSessionHasErrors(['password'=>'Contraseña incorrecta.']);
        $this->assertGuest();
    }

    public function test_duplicate_registration_has_a_specific_message(): void
    {
        $user = User::factory()->create();
        $this->post(route('register.store'), ['name'=>'Empleado','email'=>strtoupper($user->email),'password'=>'valid-password','password_confirmation'=>'valid-password'])
            ->assertSessionHasErrors(['email'=>'Ya existe un usuario con ese correo. Inicia sesión o recupera tu contraseña.']);
    }

    public function test_api_login_and_registration_use_the_same_specific_messages(): void
    {
        $this->postJson('/api/login', ['email'=>'missing-api@example.com','password'=>'wrong-password'])
            ->assertUnauthorized()->assertJsonPath('message','No existe un usuario con ese correo.');
        $user = User::factory()->create();
        $this->postJson('/api/login', ['email'=>$user->email,'password'=>'wrong-password'])
            ->assertUnauthorized()->assertJsonPath('message','Contraseña incorrecta.');
        $this->postJson('/api/register', ['name'=>'Empleado','email'=>$user->email,'password'=>'valid-password'])
            ->assertUnprocessable()->assertJsonPath('errors.email.0','Ya existe un usuario con ese correo. Inicia sesión o recupera tu contraseña.');
    }
}
