<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('leads.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('leads.manage');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $this->ownsLead($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $this->ownsLead($user, $lead);
    }

    private function ownsLead(User $user, Lead $lead): bool
    {
        return $user->hasPermissionTo('leads.manage') && $lead->user_id === $user->id;
    }
}
