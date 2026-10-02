<?php

namespace App\Policies;

use App\Models\ClientDocument;
use App\Models\User;

class ClientDocumentPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('clients.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, ClientDocument $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, ClientDocument $record): bool { return $this->view($user, $record); }
    public function delete(User $user, ClientDocument $record): bool { return $this->view($user, $record); }
}

