<?php

namespace App\Policies;

use App\Models\EmployeeSalaryProfile;
use App\Models\User;

class EmployeeSalaryProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('salary.manage');
    }

    public function view(User $user, EmployeeSalaryProfile $employeeSalaryProfile): bool
    {
        return $user->can('salary.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('salary.manage');
    }

    public function update(User $user, EmployeeSalaryProfile $employeeSalaryProfile): bool
    {
        return $user->can('salary.manage');
    }
}
