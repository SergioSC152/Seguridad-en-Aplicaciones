<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_real_account_scoped_inventory_metrics(): void
    {
        $user = User::factory()->create(['name' => 'Ana Ganadera']);
        $category = $user->livestockCategories()->create(['name' => 'Brahman', 'active' => true]);
        $user->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'OWN-LOT-1',
            'head_count' => 10,
            'average_weight_kg' => 400,
            'farm_name' => 'Finca propia',
            'status' => 'active',
        ]);
        $user->livestockBatches()->create([
            'livestock_category_id' => $category->id,
            'code' => 'OWN-LOT-2',
            'head_count' => 3,
            'average_weight_kg' => 300,
            'status' => 'sold',
        ]);

        $otherUser = User::factory()->create();
        $otherCategory = $otherUser->livestockCategories()->create(['name' => 'Angus', 'active' => true]);
        $otherUser->livestockBatches()->create([
            'livestock_category_id' => $otherCategory->id,
            'code' => 'FOREIGN-LOT',
            'head_count' => 999,
            'average_weight_kg' => 800,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ana Ganadera')
            ->assertSee('10')
            ->assertSee('400.00 kg')
            ->assertSee('OWN-LOT-1')
            ->assertDontSee('FOREIGN-LOT')
            ->assertDontSee('999');
    }

    public function test_dashboard_renders_an_empty_inventory_state(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Aún no hay lotes en tu inventario.')
            ->assertSee('Registrar el primer lote');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_hides_commercial_data_and_links_without_permissions(): void
    {
        config(['cowapp.mail_settings_admin_email' => 'root@cowapp.test']);
        $user = User::factory()->create();
        $user->clients()->create(['name' => 'Cliente reservado', 'client_type' => 'individual', 'status' => 'active']);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('metrics', fn (array $metrics) => $metrics['clients'] === null
                && $metrics['leads'] === null && $metrics['open_pipeline_value'] === null)
            ->assertDontSeeText('Gestión comercial')
            ->assertDontSee(route('admin.clients.index'), false)
            ->assertDontSee(route('admin.leads.index'), false)
            ->assertDontSee(route('admin.sales-pipeline.index'), false)
            ->assertDontSee(route('admin.roles.index'), false)
            ->assertDontSee(route('admin.settings.mail.edit'), false);

        $this->get(route('admin.clients.index'))->assertForbidden();
        $this->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_dashboard_shows_only_the_commercial_module_granted_to_the_role(): void
    {
        config(['cowapp.mail_settings_admin_email' => 'root@cowapp.test']);
        $role = \App\Models\Role::create(['name' => 'Clientes', 'slug' => 'clientes']);
        $role->permissions()->attach(\App\Models\Permission::where('code', 'clients.manage')->firstOrFail());
        $user = User::factory()->create(['role_id' => $role->id]);
        $user->clients()->create(['name' => 'Cliente propio', 'client_type' => 'individual', 'status' => 'active']);
        $otherUser = User::factory()->create();
        $otherUser->clients()->create(['name' => 'Cliente ajeno', 'client_type' => 'individual', 'status' => 'active']);

        $this->actingAs($user)->get(route('dashboard'))->assertOk()
            ->assertViewHas('metrics', fn (array $metrics) => $metrics['clients'] === 1 && $metrics['leads'] === null)
            ->assertSee('Clientes registrados')
            ->assertSee(route('admin.clients.index'), false)
            ->assertDontSee('Prospectos registrados')
            ->assertDontSee('Oportunidades abiertas')
            ->assertDontSee(route('admin.leads.index'), false);
    }

    public function test_root_dashboard_uses_owned_commercial_data_and_excludes_closed_pipeline_value(): void
    {
        config(['cowapp.mail_settings_admin_email' => 'root@cowapp.test']);
        $root = User::factory()->create(['email' => 'root@cowapp.test']);
        $client = $root->clients()->create(['name' => 'Cliente propio', 'client_type' => 'individual', 'status' => 'active']);
        $root->leads()->create(['name' => 'Prospecto propio', 'status' => 'new', 'source' => 'manual']);
        $root->salesOpportunities()->create(['client_id' => $client->id, 'title' => 'Abierta', 'stage' => 'contact', 'estimated_value' => 1200]);
        $root->salesOpportunities()->create(['client_id' => $client->id, 'title' => 'Ganada', 'stage' => 'won', 'estimated_value' => 8000]);
        $otherUser = User::factory()->create();
        $otherClient = $otherUser->clients()->create(['name' => 'Cliente ajeno', 'client_type' => 'individual', 'status' => 'active']);
        $otherUser->salesOpportunities()->create(['client_id' => $otherClient->id, 'title' => 'Ajena', 'stage' => 'contact', 'estimated_value' => 9999]);

        $this->actingAs($root)->get(route('dashboard'))->assertOk()
            ->assertViewHas('metrics', fn (array $metrics) => $metrics['clients'] === 1
                && $metrics['leads'] === 1 && $metrics['new_leads'] === 1
                && $metrics['open_opportunities'] === 1 && $metrics['won_opportunities'] === 1
                && $metrics['open_pipeline_value'] === 1200.0)
            ->assertSee(route('admin.roles.index'), false)
            ->assertSee(route('admin.settings.mail.edit'), false);
    }
}
