<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_authenticated_dashboard_are_not_stored_in_browser_cache(): void
    {
        $this->withoutVite();
        $login = $this->get(route('login'))->assertOk();
        $this->assertStringContainsString('no-store', $login->headers->get('Cache-Control'));

        $dashboard = $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk();
        $this->assertStringContainsString('no-store', $dashboard->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $dashboard->headers->get('Cache-Control'));
        $dashboard->assertHeader('Pragma', 'no-cache');
    }

    public function test_dashboard_and_admin_routes_cannot_be_reopened_after_logout(): void
    {
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk();
        $logout = $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertStringContainsString('no-store', $logout->headers->get('Cache-Control'));
        $this->assertGuest();

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.clients.index'))->assertRedirect(route('login'));
        $this->get(route('admin.livestock-batches.index'))->assertRedirect(route('login'));
    }
}
