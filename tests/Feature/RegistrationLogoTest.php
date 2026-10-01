<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Database\Seeders\CowAppLogoSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_in_the_document_body_instead_of_the_title(): void
    {
        $response = $this->get(route('register'))->assertOk();
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML($response->getContent());
            $xpath = new DOMXPath($document);

            $this->assertSame('Crear cuenta | CowApp', $document->getElementsByTagName('title')->item(0)->textContent);
            foreach (['name', 'email', 'password', 'password_confirmation', '_token'] as $field) {
                $this->assertSame(1, $xpath->query('//body//form//input[@name="'.$field.'"]')->length);
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    public function test_logo_seeder_stores_the_image_and_url_without_duplicate_records(): void
    {
        Storage::fake('public');
        $this->seed(CowAppLogoSeeder::class);
        $this->seed(CowAppLogoSeeder::class);

        $this->assertSame(1, Media::where('name', Media::COWAPP_LOGO_NAME)->count());
        $this->assertDatabaseHas('media', [
            'name' => Media::COWAPP_LOGO_NAME,
            'path' => 'branding/cowapp-logo.png',
            'url' => '/storage/branding/cowapp-logo.png',
            'mime_type' => 'image/png',
            'active' => true,
        ]);
        Storage::disk('public')->assertExists('branding/cowapp-logo.png');
        $this->assertSame(
            hash_file('sha256', resource_path('branding/cowapp-logo.png')),
            hash('sha256', Storage::disk('public')->get('branding/cowapp-logo.png'))
        );
    }

    public function test_the_same_database_logo_url_is_used_on_public_auth_and_dashboard_pages(): void
    {
        $this->withoutVite();
        Media::create([
            'name' => Media::COWAPP_LOGO_NAME,
            'path' => 'branding/example.png',
            'url' => '/storage/branding/example.png',
            'active' => true,
        ]);

        foreach (['home', 'login', 'register'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('src="/storage/branding/example.png"', false);
        }
        $this->actingAs(User::factory()->create())->get(route('dashboard'))
            ->assertOk()->assertSee('src="/storage/branding/example.png"', false);
    }

    public function test_an_inactive_logo_is_not_shown_and_registration_remains_available(): void
    {
        Media::create([
            'name' => Media::COWAPP_LOGO_NAME,
            'path' => 'branding/inactive.png',
            'url' => '/storage/branding/inactive.png',
            'active' => false,
        ]);

        $this->get(route('register'))->assertOk()->assertSee('Crear cuenta')
            ->assertDontSee('src="/storage/branding/inactive.png"', false);
    }

    public function test_registration_still_creates_a_user_with_a_hashed_password(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Cuenta de prueba',
            'email' => 'registro@cowapp.test',
            'password' => 'ExamplePassword2026!',
            'password_confirmation' => 'ExamplePassword2026!',
        ])->assertRedirect(route('login'))->assertSessionHas('success');

        $user = User::where('email', 'registro@cowapp.test')->firstOrFail();
        $this->assertNotSame('ExamplePassword2026!', $user->password);
        $this->assertTrue(Hash::check('ExamplePassword2026!', $user->password));
    }
}
