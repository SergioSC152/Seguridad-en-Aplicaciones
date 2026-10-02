<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('auctions.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Auction $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, Auction $record): bool { return $this->view($user, $record); }
    public function delete(User $user, Auction $record): bool { return $this->view($user, $record); }
}

