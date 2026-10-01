<?php

namespace App\Policies;

use App\Models\LivestockBatch;
use App\Models\User;

class LivestockBatchPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, LivestockBatch $batch): bool
    {
        return $batch->user_id === $user->id;
    }

    public function delete(User $user, LivestockBatch $batch): bool
    {
        return $batch->user_id === $user->id;
    }
}
