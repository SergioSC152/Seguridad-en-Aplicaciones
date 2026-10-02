<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool { return $user->hasPermissionTo('products.manage'); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function view(User $user, Product $record): bool { return $this->viewAny($user) && $record->user_id === $user->id; }
    public function update(User $user, Product $record): bool { return $this->view($user, $record); }
    public function delete(User $user, Product $record): bool { return $this->view($user, $record); }
}

