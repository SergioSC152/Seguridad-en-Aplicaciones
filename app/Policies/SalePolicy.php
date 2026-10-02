<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('sales.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Sale $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, Sale $record): bool { return $this->view($user, $record); }
    public function delete(User $user, Sale $record): bool { return $this->view($user, $record); }
}

