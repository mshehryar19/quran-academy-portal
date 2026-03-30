<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentFeeProfile;
use Carbon\Carbon;

class StudentFeeProfileResolver
{
    /** Fee profile covering the given calendar month (GBP/USD monthly flat). */
    public function activeProfileForMonth(Student $student, int $year, int $month): ?StudentFeeProfile
    {
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        return StudentFeeProfile::query()
            ->where('student_id', $student->id)
            ->where('status', StudentFeeProfile::STATUS_ACTIVE)
            ->whereDate('effective_from', '<=', $monthEnd->toDateString())
            ->where(function ($q) use ($monthStart): void {
                $q->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $monthStart->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }
}
