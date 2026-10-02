<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_limits_are_reported_in_spanish_and_email_over_100_characters_is_rejected(): void
    {
        app()->setLocale('es');
        $email=str_repeat('a',63).'@'.str_repeat('b',33).'.com';
        $this->post(route('register.store'), ['name'=>str_repeat('a',101),'email'=>$email,'password'=>str_repeat('a',26),'password_confirmation'=>str_repeat('a',26)])
            ->assertSessionHasErrors([
                'name'=>'El campo nombre no puede superar 100 caracteres.',
                'email'=>'El campo correo electrónico no puede superar 100 caracteres.',
                'password'=>'El campo contraseña no puede superar 25 caracteres.',
            ]);
        $this->assertDatabaseMissing('users',['email'=>$email]);
    }

    public function test_registration_rejects_oversized_name_even_without_browser_limits(): void
    {
        $this->post(route('register.store'), ['name'=>str_repeat('a',101),'email'=>'limits@example.com','password'=>'valid-password','password_confirmation'=>'valid-password'])
            ->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users',['email'=>'limits@example.com']);
        $this->post(route('register.store'), ['name'=>str_repeat('a',100),'email'=>'limits@example.com','password'=>'valid-password','password_confirmation'=>'valid-password'])
            ->assertSessionHasNoErrors()->assertRedirect(route('login'));
    }

    public function test_oversized_login_password_is_rejected_before_authentication(): void
    {
        $this->post(route('login.store'), ['email'=>'limits@example.com','password'=>str_repeat('a',26)])
            ->assertSessionHasErrors('password');
        $this->postJson('/api/login', ['email'=>'limits@example.com','password'=>str_repeat('a',26)])
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertGuest();
    }

    public function test_news_rejects_oversized_content_without_creating_a_record(): void
    {
        $user=User::factory()->create();
        config(['cowapp.mail_settings_admin_email'=>$user->email]);
        $this->actingAs($user)->post(route('admin.news.store'), ['title'=>'Texto de prueba','content'=>str_repeat('a',20001)])
            ->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('news',['title'=>'Texto de prueba']);
    }
}
