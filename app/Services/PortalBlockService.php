<?php
namespace App\Services;
use App\Models\PortalBlock;
use App\Models\User;
class PortalBlockService
{
    public function save(User $user, array $data, ?PortalBlock $record=null): PortalBlock
    {
        if ($record) { $record->update($data); return $record->refresh(); }
        return PortalBlock::create(['user_id'=>$user->id]+$data);
    }
}

