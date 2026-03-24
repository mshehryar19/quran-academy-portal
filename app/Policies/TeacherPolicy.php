<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('teacher.manage') || $user->can('teacher.view');
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('teacher.manage');
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $user->can('teacher.manage');
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->can('teacher.manage');
    }
}
