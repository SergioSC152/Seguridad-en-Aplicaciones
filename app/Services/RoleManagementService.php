<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleManagementService
{
    /** @param array{name:string,description?:?string,permissions?:list<string>} $data */
    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::query()->create([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name']),
                'description' => $data['description'] ?? null,
            ]);
            $this->syncPermissions($role, $data['permissions'] ?? []);

            return $role->load('permissions');
        });
    }

    /** @param array{name:string,description?:?string,permissions?:list<string>} $data */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->fill([
                'name' => $data['name'],
                'slug' => $this->uniqueSlug($data['name'], $role),
                'description' => $data['description'] ?? null,
            ])->save();
            $this->syncPermissions($role, $data['permissions'] ?? []);

            return $role->load('permissions');
        });
    }

    /** @param list<string> $codes */
    private function syncPermissions(Role $role, array $codes): void
    {
        $permissionIds = Permission::query()->whereIn('code', $codes)->pluck('id');
        $role->permissions()->sync($permissionIds);
    }

    private function uniqueSlug(string $name, ?Role $current = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Role::query()->where('slug', $slug)->when($current, fn ($query) => $query->whereKeyNot($current->id))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
