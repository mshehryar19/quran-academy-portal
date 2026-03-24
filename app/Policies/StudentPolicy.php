<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('student.manage') || $user->can('student.view');
    }

    public function view(User $user, Student $student): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('student.manage');
    }

    public function update(User $user, Student $student): bool
    {
        return $user->can('student.manage');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->can('student.manage');
    }
}
