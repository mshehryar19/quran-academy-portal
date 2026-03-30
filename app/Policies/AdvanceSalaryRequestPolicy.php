<?php

namespace App\Policies;

use App\Models\AdvanceSalaryRequest;
use App\Models\User;

class AdvanceSalaryRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isPayrollStaff($user);
    }

    public function view(User $user, AdvanceSalaryRequest $advanceSalaryRequest): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return $this->isPayrollStaff($user)
            && (int) $advanceSalaryRequest->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $this->isPayrollStaff($user);
    }

    public function adminReview(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    protected function isPayrollStaff(User $user): bool
    {
        return $user->hasAnyRole(['Teacher', 'HR', 'Supervisor', 'Admin']);
    }
}
