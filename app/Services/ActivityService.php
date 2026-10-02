<?php
namespace App\Services;
use App\Models\Activity;
use App\Models\User;
class ActivityService
{
    public function save(User $user, array $data, ?Activity $record=null): Activity
    {
        if ($record) { $record->update($data); return $record->refresh(); }
        return Activity::create(['user_id'=>$user->id]+$data);
    }
}

