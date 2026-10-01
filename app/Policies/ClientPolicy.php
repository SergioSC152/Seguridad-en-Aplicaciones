<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('clients.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('clients.manage');
    }

    public function view(User $user, Client $client): bool
    {
        return $this->ownsClient($user, $client);
    }

    public function update(User $user, Client $client): bool
    {
        return $this->ownsClient($user, $client);
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->ownsClient($user, $client);
    }

    private function ownsClient(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.manage') && $client->user_id === $user->id;
    }
}
