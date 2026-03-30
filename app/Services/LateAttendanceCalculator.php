<?php

namespace App\Services;

use Carbon\Carbon;

class LateAttendanceCalculator
{
    /**
     * Minutes after scheduled slot start that count as "late" for payroll prep.
     * 0–15 minutes after start: not late. 16+ minutes after start: late, full minutes after start count.
     */
    public function lateMinutesAfterSlotStart(Carbon $slotStart, Carbon $loginAt): int
    {
        if ($loginAt->lte($slotStart)) {
            return 0;
        }

        $minutesAfterStart = $slotStart->diffInMinutes($loginAt);

        if ($minutesAfterStart <= 15) {
            return 0;
        }

        return $minutesAfterStart;
    }
}
