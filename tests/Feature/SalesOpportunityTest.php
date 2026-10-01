<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SalesOpportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesOpportunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_users_without_pipeline_permission_cannot_open_the_module(): void
    {
        $this->get(route('admin.sales-pipeline.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.sales-pipeline.index'))->assertForbidden();
    }

    public function test_authorized_user_can_create_filter_edit_update_and_delete_opportunities(): void
    {
        $user = $this->userWithPipelinePermission();
        $client = $user->clients()->create($this->clientData());

        $this->actingAs($user)->get(route('admin.sales-pipeline.index'))->assertOk()->assertSee('Pipeline comercial');
        $this->post(route('admin.sales-pipeline.store'), $this->opportunityData($client))->assertRedirect(route('admin.sales-pipeline.index'))->assertSessionHasNoErrors();

        $opportunity = SalesOpportunity::query()->firstOrFail();
        $this->assertSame($user->id, $opportunity->user_id);
        $this->assertSame($client->id, $opportunity->client_id);
        $this->get(route('admin.sales-pipeline.index', ['q' => 'Novillos', 'stage' => 'contact']))
            ->assertOk()->assertSee('Novillos para venta');
        $this->get(route('admin.sales-pipeline.edit', $opportunity))->assertOk()->assertSee('Editar oportunidad');

        $this->put(route('admin.sales-pipeline.update', $opportunity), $this->opportunityData($client, [
            'title' => 'Venta de novillos actualizada', 'stage' => 'negotiation',
        ]))->assertRedirect(route('admin.sales-pipeline.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sales_opportunities', ['id' => $opportunity->id, 'stage' => 'negotiation']);

        $this->delete(route('admin.sales-pipeline.destroy', $opportunity))->assertRedirect(route('admin.sales-pipeline.index'));
        $this->assertDatabaseMissing('sales_opportunities', ['id' => $opportunity->id]);
    }

    public function test_opportunities_and_client_links_are_isolated_between_users(): void
    {
        $owner = $this->userWithPipelinePermission('owner-pipeline@cowapp.test');
        $other = $this->userWithPipelinePermission('other-pipeline@cowapp.test');
        $client = $owner->clients()->create($this->clientData());
        $opportunity = $owner->salesOpportunities()->create($this->opportunityData($client));

        $this->actingAs($other)->get(route('admin.sales-pipeline.index'))
            ->assertOk()->assertDontSee('Novillos para venta');
        $this->get(route('admin.sales-pipeline.edit', $opportunity))->assertForbidden();
        $this->put(route('admin.sales-pipeline.update', $opportunity), $this->opportunityData($client))->assertForbidden();
        $this->delete(route('admin.sales-pipeline.destroy', $opportunity))->assertForbidden();
        $this->post(route('admin.sales-pipeline.store'), $this->opportunityData($client))->assertSessionHasErrors('client_id');
        $this->assertDatabaseCount('sales_opportunities', 1);
    }

    public function test_server_rejects_unknown_stages_invalid_counts_and_values(): void
    {
        $user = $this->userWithPipelinePermission();
        $client = $user->clients()->create($this->clientData());

        $this->actingAs($user)->from(route('admin.sales-pipeline.index'))
            ->post(route('admin.sales-pipeline.store'), $this->opportunityData($client, [
                'stage' => 'invoiced', 'head_count' => 0, 'estimated_value' => -1,
            ]))->assertSessionHasErrors(['stage', 'head_count', 'estimated_value']);

        $this->assertDatabaseCount('sales_opportunities', 0);
    }

    public function test_client_permission_alone_does_not_grant_pipeline_access(): void
    {
        $permission = Permission::query()->where('code', 'clients.manage')->firstOrFail();
        $role = Role::query()->create(['name' => 'Solo clientes', 'slug' => 'solo-clientes']);
        $role->permissions()->attach($permission);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)->get(route('admin.sales-pipeline.index'))->assertForbidden();
    }

    private function userWithPipelinePermission(string $email = 'pipeline@cowapp.test'): User
    {
        $permission = Permission::query()->where('code', 'sales-pipeline.manage')->firstOrFail();
        $role = Role::query()->create(['name' => 'Gestor comercial '.fake()->unique()->word(), 'slug' => fake()->unique()->slug()]);
        $role->permissions()->attach($permission);

        return User::factory()->create(['email' => $email, 'role_id' => $role->id]);
    }

    private function clientData(): array
    {
        return ['name' => 'Finca Las Palmas', 'client_type' => 'business', 'status' => 'active'];
    }

    private function opportunityData(Client $client, array $overrides = []): array
    {
        return array_merge([
            'client_id' => $client->id,
            'title' => 'Novillos para venta',
            'livestock_summary' => 'Novillos cebú',
            'head_count' => 24,
            'estimated_value' => '48000000.00',
            'stage' => 'contact',
            'expected_close_date' => '2026-11-15',
            'notes' => 'Llamar para coordinar pesaje.',
        ], $overrides);
    }
}
