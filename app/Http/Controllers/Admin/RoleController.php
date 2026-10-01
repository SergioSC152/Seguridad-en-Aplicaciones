<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\SaveRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private readonly RoleManagementService $roles) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->withCount('users')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('label')->get(),
            'users' => User::query()->with('role')->orderBy('name')->paginate(12),
        ]);
    }

    public function store(SaveRoleRequest $request): RedirectResponse
    {
        $this->roles->create($request->validated());

        return to_route('admin.roles.index')->with('success', 'Rol creado con sus permisos.');
    }

    public function update(SaveRoleRequest $request, Role $role): RedirectResponse
    {
        Gate::authorize('update', $role);
        $this->roles->update($role, $request->validated());

        return to_route('admin.roles.index')->with('success', 'Rol y permisos actualizados.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);
        if ($role->users()->exists()) {
            return to_route('admin.roles.index')->with('error', 'No se puede eliminar un rol asignado a usuarios.');
        }

        $role->delete();

        return to_route('admin.roles.index')->with('success', 'Rol eliminado.');
    }

    public function assign(AssignRoleRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('viewAny', Role::class);

        abort_if($user->isPlatformAdmin(), 403, 'La cuenta raíz conserva el acceso de emergencia.');
        $user->role_id = $request->validated('role_id');
        $user->save();

        return to_route('admin.roles.index')->with('success', 'Rol asignado a la cuenta.');
    }
}
