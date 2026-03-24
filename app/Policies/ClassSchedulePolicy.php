<?php

namespace App\Policies;

use App\Models\ClassSchedule;
use App\Models\User;

class ClassSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('schedule.manage');
    }

    public function view(User $user, ClassSchedule $classSchedule): bool
    {
        return $user->can('schedule.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('schedule.manage');
    }

    public function update(User $user, ClassSchedule $classSchedule): bool
    {
        return $user->can('schedule.manage');
    }

    public function delete(User $user, ClassSchedule $classSchedule): bool
    {
        return $user->can('schedule.manage');
    }
}
