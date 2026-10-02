<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('activities.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Activity $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, Activity $record): bool { return $this->view($user, $record); }
    public function delete(User $user, Activity $record): bool { return $this->view($user, $record); }
}

