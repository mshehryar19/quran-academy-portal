<?php

namespace App\Policies;

use App\Models\LessonSummary;
use App\Models\User;

class LessonSummaryPolicy
{
    public function view(User $user, LessonSummary $lessonSummary): bool
    {
        if ($user->hasAnyRole(['Admin', 'Supervisor'])) {
            return true;
        }

        return $user->hasRole('Teacher')
            && $user->teacher
            && (int) $lessonSummary->teacher_id === (int) $user->teacher->id;
    }

    public function override(User $user, LessonSummary $lessonSummary): bool
    {
        return $user->hasRole('Admin');
    }
}
