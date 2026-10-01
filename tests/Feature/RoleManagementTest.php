<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cowapp.mail_settings_admin_email' => 'root@cowapp.test']);
    }

    public function test_only_platform_admin_can_manage_roles_and_assignments(): void
    {
        $staff = User::factory()->create(['email' => 'staff@cowapp.test']);
        $this->actingAs($staff)->get(route('admin.roles.index'))->assertForbidden();

        $root = User::factory()->create(['email' => 'root@cowapp.test']);
        $this->actingAs($root)->get(route('admin.roles.index'))->assertOk()->assertSee('Roles y permisos');
    }

    public function test_root_can_create_role_assign_permissions_and_grant_them_to_an_account(): void
    {
        $root = User::factory()->create(['email' => 'root@cowapp.test']);
        $staff = User::factory()->create(['email' => 'editor@cowapp.test']);
        $permission = Permission::query()->where('code', 'content.manage')->firstOrFail();

        $this->actingAs($root)->post(route('admin.roles.store'), [
            'name' => 'Editor de contenido',
            'description' => 'Administra publicaciones y multimedia.',
            'permissions' => ['content.manage'],
        ])->assertRedirect(route('admin.roles.index'))->assertSessionHasNoErrors();

        $role = Role::query()->where('name', 'Editor de contenido')->firstOrFail();
        $this->assertTrue($role->permissions->contains('id', $permission->id));

        $this->put(route('admin.roles.assign', $staff), ['role_id' => $role->id])
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame($role->id, $staff->fresh()->role_id);
        $this->assertTrue($staff->fresh()->hasPermissionTo('content.manage'));
        $this->assertFalse($staff->fresh()->hasPermissionTo('mail-settings.manage'));

        $staff = $staff->fresh();
        $this->actingAs($staff)->get(route('admin.media.index'))->assertOk();
        $this->actingAs($staff)->get(route('admin.settings.mail.edit'))->assertForbidden();
    }

    public function test_content_routes_deny_users_without_the_permission(): void
    {
        $staff = User::factory()->create(['email' => 'staff@cowapp.test']);

        $this->actingAs($staff)->get(route('admin.media.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.news.index'))->assertForbidden();
    }

    public function test_mail_settings_permission_can_be_granted_without_granting_role_administration(): void
    {
        $role = Role::query()->create(['name' => 'Operador de correo', 'slug' => 'operador-correo']);
        $role->permissions()->attach(Permission::query()->where('code', 'mail-settings.manage')->value('id'));
        $staff = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($staff)->get(route('admin.settings.mail.edit'))->assertOk();
        $this->actingAs($staff)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_root_role_assignment_is_immutable_and_assigned_roles_cannot_be_deleted(): void
    {
        $root = User::factory()->create(['email' => 'root@cowapp.test']);
        $role = Role::query()->create(['name' => 'Contenido', 'slug' => 'contenido']);
        $staff = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($root)->put(route('admin.roles.assign', $root), ['role_id' => $role->id])->assertForbidden();
        $this->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
        $this->assertSame($role->id, $staff->fresh()->role_id);
    }

    public function test_platform_admin_fails_closed_when_its_email_is_not_configured(): void
    {
        config(['cowapp.mail_settings_admin_email' => null]);
        $user = User::factory()->create(['email' => 'root@cowapp.test']);

        $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_public_registration_cannot_claim_the_platform_admin_email_with_different_case(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Impostor',
            'email' => 'ROOT@COWAPP.TEST',
            'password' => 'ExamplePassword2026!',
            'password_confirmation' => 'ExamplePassword2026!',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('users', ['name' => 'Impostor']);
    }
}
