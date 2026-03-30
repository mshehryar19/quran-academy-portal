<?php

namespace App\Policies;

use App\Models\MonthlySalaryRecord;
use App\Models\User;

class MonthlySalaryRecordPolicy
{
    public function view(User $user, MonthlySalaryRecord $monthlySalaryRecord): bool
    {
        if ($user->can('salary.manage')) {
            return true;
        }

        if (! $user->can('salary.view')) {
            return false;
        }

        if ((int) $monthlySalaryRecord->user_id !== (int) $user->id) {
            return false;
        }

        return $monthlySalaryRecord->status === MonthlySalaryRecord::STATUS_FINALIZED;
    }
}
