<?php

namespace App\Policies;

use App\Models\MailSetting;
use App\Models\User;

class MailSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('mail-settings.manage');
    }

    public function update(User $user, MailSetting $mailSetting): bool
    {
        return $this->viewAny($user);
    }
}
