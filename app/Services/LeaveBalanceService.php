<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class LeaveBalanceService
{
    public const ANNUAL_PAID_ENTITLEMENT_DAYS = 12;

    /** @see docs/15-phase-5-summary.md — cycle anchor from teacher appointment or user created_at */
    public function appointmentAnchor(User $user): CarbonInterface
    {
        $user->loadMissing('teacher');
        if ($user->teacher?->date_of_appointment) {
            return Carbon::parse($user->teacher->date_of_appointment)->startOfDay();
        }

        return Carbon::parse($user->created_at)->startOfDay();
    }

    /** Start of the annual cycle containing $asOf (date only). */
    public function currentCycleStart(User $user, CarbonInterface $asOf): CarbonInterface
    {
        $anchor = $this->appointmentAnchor($user);
        $asOf = Carbon::parse($asOf)->startOfDay();

        if ($asOf->lt($anchor)) {
            return $anchor->copy();
        }

        $cursor = $anchor->copy();
        while ($cursor->copy()->addYear()->lte($asOf)) {
            $cursor->addYear();
        }

        return $cursor;
    }

    public function currentCycleEnd(User $user, CarbonInterface $asOf): CarbonInterface
    {
        return $this->currentCycleStart($user, $asOf)->copy()->addYear()->subDay();
    }

    /** Whole request counts toward the cycle that contains start_date. */
    public function usedPaidLeaveDaysInCycle(User $user, CarbonInterface $asOf): int
    {
        $start = $this->currentCycleStart($user, $asOf);
        $end = $this->currentCycleEnd($user, $asOf);

        return (int) LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('admin_decision', LeaveRequest::DECISION_APPROVED)
            ->where('is_paid', true)
            ->whereDate('start_date', '>=', $start->toDateString())
            ->whereDate('start_date', '<=', $end->toDateString())
            ->sum('total_days');
    }

    public function remainingPaidLeaveDays(User $user, ?CarbonInterface $asOf = null): int
    {
        $asOf ??= Carbon::now();

        return max(0, self::ANNUAL_PAID_ENTITLEMENT_DAYS - $this->usedPaidLeaveDaysInCycle($user, $asOf));
    }

    /** Inclusive calendar days between start and end (weekends included; no holiday table in this phase). */
    public static function inclusiveDayCount(CarbonInterface $start, CarbonInterface $end): int
    {
        return $start->startOfDay()->diffInDays($end->startOfDay()) + 1;
    }
}
