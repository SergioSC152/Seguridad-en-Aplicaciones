<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('quotes.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Quote $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, Quote $record): bool { return $this->view($user, $record); }
    public function delete(User $user, Quote $record): bool { return $this->view($user, $record); }
}

