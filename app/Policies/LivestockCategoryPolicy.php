<?php

namespace App\Policies;

use App\Models\LivestockCategory;
use App\Models\User;

class LivestockCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LivestockCategory $category): bool
    {
        return $category->user_id === $user->id;
    }

    public function delete(User $user, LivestockCategory $category): bool
    {
        return $category->user_id === $user->id;
    }
}
