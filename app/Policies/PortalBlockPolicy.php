<?php

namespace App\Policies;

use App\Models\PortalBlock;
use App\Models\User;

class PortalBlockPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('content.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, PortalBlock $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, PortalBlock $record): bool { return $this->view($user, $record); }
    public function delete(User $user, PortalBlock $record): bool { return $this->view($user, $record); }
}

