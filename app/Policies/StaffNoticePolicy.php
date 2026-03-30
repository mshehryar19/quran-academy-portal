<?php

namespace App\Policies;

use App\Models\StaffNotice;
use App\Models\User;

class StaffNoticePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'HR', 'Supervisor', 'Teacher', 'Accountant']);
    }

    public function view(User $user, StaffNotice $staffNotice): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return $staffNotice->isVisibleTo($user);
    }

    public function create(User $user): bool
    {
        return $user->can('notifications.manage');
    }

    public function delete(User $user, StaffNotice $staffNotice): bool
    {
        return $user->can('notifications.manage');
    }
}
