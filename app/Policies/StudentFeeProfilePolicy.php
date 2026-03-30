<?php

namespace App\Policies;

use App\Models\StudentFeeProfile;
use App\Models\User;

class StudentFeeProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('invoice.manage');
    }

    public function view(User $user, StudentFeeProfile $studentFeeProfile): bool
    {
        return $user->can('invoice.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('invoice.manage');
    }

    public function update(User $user, StudentFeeProfile $studentFeeProfile): bool
    {
        return $user->can('invoice.manage');
    }

    public function delete(User $user, StudentFeeProfile $studentFeeProfile): bool
    {
        return $user->can('invoice.manage');
    }
}
