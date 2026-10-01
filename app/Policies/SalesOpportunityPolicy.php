<?php

namespace App\Policies;

use App\Models\SalesOpportunity;
use App\Models\User;

class SalesOpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('sales-pipeline.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('sales-pipeline.manage');
    }

    public function view(User $user, SalesOpportunity $opportunity): bool
    {
        return $this->ownsOpportunity($user, $opportunity);
    }

    public function update(User $user, SalesOpportunity $opportunity): bool
    {
        return $this->ownsOpportunity($user, $opportunity);
    }

    public function delete(User $user, SalesOpportunity $opportunity): bool
    {
        return $this->ownsOpportunity($user, $opportunity);
    }

    private function ownsOpportunity(User $user, SalesOpportunity $opportunity): bool
    {
        return $user->hasPermissionTo('sales-pipeline.manage') && $opportunity->user_id === $user->id;
    }
}
