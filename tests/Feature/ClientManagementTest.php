<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_users_without_client_permission_cannot_open_the_module(): void
    {
        $this->get(route('admin.clients.index'))->assertRedirect(route('login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.clients.index'))->assertForbidden();
    }

    public function test_user_with_permission_can_create_edit_search_and_delete_owned_clients(): void
    {
        $user = $this->userWithClientPermission();

        $this->actingAs($user)->get(route('admin.clients.index'))->assertOk()->assertSee('Clientes ganaderos');

        $this->post(route('admin.clients.store'), $this->clientData([
            'email' => 'VENTAS@ESTANCIA.TEST',
            'document_number' => ' cc 12345 ',
        ]))->assertRedirect(route('admin.clients.index'))->assertSessionHasNoErrors();

        $client = Client::query()->where('user_id', $user->id)->firstOrFail();
        $this->assertSame('ventas@estancia.test', $client->email);
        $this->assertSame('CC 12345', $client->document_number);
        $this->assertSame($user->id, $client->user_id);

        $this->get(route('admin.clients.index', ['q' => 'ventas@estancia.test']))
            ->assertOk()->assertSee('Estancia El Roble');
        $this->get(route('admin.clients.edit', $client))->assertOk()->assertSee('Editar cliente');

        $this->put(route('admin.clients.update', $client), $this->clientData([
            'name' => 'Estancia El Roble SAS',
            'status' => 'inactive',
        ]))->assertRedirect(route('admin.clients.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Estancia El Roble SAS', 'status' => 'inactive']);
        $this->delete(route('admin.clients.destroy', $client))->assertRedirect(route('admin.clients.index'));
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    public function test_client_data_is_scoped_to_its_owner_even_when_both_users_have_permission(): void
    {
        $owner = $this->userWithClientPermission('owner@cowapp.test');
        $otherUser = $this->userWithClientPermission('other@cowapp.test');
        $client = $owner->clients()->create($this->clientData());

        $this->actingAs($otherUser)->get(route('admin.clients.index'))
            ->assertOk()->assertDontSee('Estancia El Roble');
        $this->get(route('admin.clients.edit', $client))->assertForbidden();
        $this->put(route('admin.clients.update', $client), $this->clientData(['name' => 'Cambio no autorizado']))
            ->assertForbidden();
        $this->delete(route('admin.clients.destroy', $client))->assertForbidden();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Estancia El Roble']);
    }

    public function test_duplicate_email_and_document_are_rejected_within_one_account(): void
    {
        $user = $this->userWithClientPermission();
        $user->clients()->create($this->clientData());

        $this->actingAs($user)->from(route('admin.clients.index'))
            ->post(route('admin.clients.store'), $this->clientData([
                'name' => 'Otra finca',
                'email' => 'VENTAS@ESTANCIA.TEST',
                'document_number' => 'cc 12345',
            ]))
            ->assertSessionHasErrors(['email', 'document_number']);

        $this->assertDatabaseCount('clients', 1);
    }

    public function test_server_rejects_invalid_type_status_and_oversized_notes(): void
    {
        $user = $this->userWithClientPermission();

        $this->actingAs($user)->from(route('admin.clients.index'))
            ->post(route('admin.clients.store'), $this->clientData([
                'client_type' => 'unknown',
                'status' => 'deleted',
                'notes' => str_repeat('x', 3001),
            ]))
            ->assertSessionHasErrors(['client_type', 'status', 'notes']);

        $this->assertDatabaseCount('clients', 0);
    }

    private function userWithClientPermission(string $email = 'clients@cowapp.test'): User
    {
        $permission = Permission::query()->where('code', 'clients.manage')->firstOrFail();
        $role = Role::query()->create([
            'name' => 'Gestor '.fake()->unique()->word(),
            'slug' => fake()->unique()->slug(),
        ]);
        $role->permissions()->attach($permission);

        return User::factory()->create(['email' => $email, 'role_id' => $role->id]);
    }

    /** @return array<string, mixed> */
    private function clientData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Estancia El Roble',
            'client_type' => 'business',
            'contact_person' => 'María Torres',
            'document_number' => 'CC 12345',
            'email' => 'ventas@estancia.test',
            'phone' => '3001234567',
            'municipality' => 'Villavicencio',
            'department' => 'Meta',
            'address' => 'Vía rural km 4',
            'status' => 'active',
            'notes' => 'Contacto preferente en horario de la mañana.',
        ], $overrides);
    }
}
