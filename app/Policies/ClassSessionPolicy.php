<?php

namespace App\Policies;

use App\Models\ClassSession;
use App\Models\User;

class ClassSessionPolicy
{
    public function view(User $user, ClassSession $classSession): bool
    {
        $classSession->loadMissing('classSchedule');

        if ($user->hasAnyRole(['Admin', 'Supervisor'])) {
            return true;
        }

        if ($user->hasRole('Teacher') && $user->teacher) {
            return (int) $classSession->classSchedule->teacher_id === (int) $user->teacher->id;
        }

        return false;
    }

    public function manageAsTeacher(User $user, ClassSession $classSession): bool
    {
        $classSession->loadMissing('classSchedule');

        return $user->hasRole('Teacher')
            && $user->teacher
            && (int) $classSession->classSchedule->teacher_id === (int) $user->teacher->id;
    }
}
