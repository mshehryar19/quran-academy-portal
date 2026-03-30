<?php

namespace App\Services;

use App\Models\AdvanceSalaryRequest;
use App\Models\EmployeeAttendanceEvent;
use App\Models\EmployeeSalaryProfile;
use App\Models\LeaveRequest;
use App\Models\MonthlySalaryRecord;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollComputationService
{
    /**
     * Late PKR per minute = base_monthly_salary / assumed_working_minutes_per_month.
     * Assumption: 22 working days × 8 hours × 60 minutes = 10,560 (documented for refinement).
     */
    public const ASSUMED_WORKING_MINUTES_PER_MONTH = 10560;

    /**
     * Unpaid leave daily rate = base / assumed working days per month (22).
     */
    public const ASSUMED_WORKING_DAYS_PER_MONTH = 22;

    public function pkPerLateMinute(string|float $baseSalaryPkr): string
    {
        $base = (float) $baseSalaryPkr;
        if ($base <= 0) {
            return '0.00';
        }

        return bcdiv((string) $base, (string) self::ASSUMED_WORKING_MINUTES_PER_MONTH, 6);
    }

    public function sumLateMinutesForUserMonth(User $user, int $year, int $month): int
    {
        $teacherId = Teacher::query()->where('user_id', $user->id)->value('id');
        if (! $teacherId) {
            return 0;
        }

        return (int) EmployeeAttendanceEvent::query()
            ->where('teacher_id', $teacherId)
            ->where('event_type', 'login')
            ->whereYear('occurred_at', $year)
            ->whereMonth('occurred_at', $month)
            ->sum(DB::raw('COALESCE(late_minutes, 0)'));
    }

    /** Approved unpaid leave days that overlap this calendar month (is_paid = false). */
    public function unpaidLeaveDaysInCalendarMonth(User $user, int $year, int $month): int
    {
        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $requests = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('admin_decision', LeaveRequest::DECISION_APPROVED)
            ->where('is_paid', false)
            ->whereDate('start_date', '<=', $monthEnd->toDateString())
            ->whereDate('end_date', '>=', $monthStart->toDateString())
            ->get(['start_date', 'end_date']);

        $total = 0;
        foreach ($requests as $req) {
            $rangeStart = $req->start_date->max($monthStart);
            $rangeEnd = $req->end_date->min($monthEnd);
            if ($rangeStart->lte($rangeEnd)) {
                $total += LeaveBalanceService::inclusiveDayCount($rangeStart, $rangeEnd);
            }
        }

        return $total;
    }

    /** Approved advances scheduled for deduction in this payroll month (approved, not yet deducted). */
    public function pendingAdvanceTotalForMonth(User $user, int $year, int $month): string
    {
        $sum = AdvanceSalaryRequest::query()
            ->where('user_id', $user->id)
            ->where('status', AdvanceSalaryRequest::STATUS_APPROVED)
            ->where('deduction_period_year', $year)
            ->where('deduction_period_month', $month)
            ->sum('amount_pkr');

        return number_format((float) $sum, 2, '.', '');
    }

    public function buildOrRefreshDraft(User $user, int $year, int $month, ?User $actor = null): MonthlySalaryRecord
    {
        $existing = MonthlySalaryRecord::query()
            ->where('user_id', $user->id)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        if ($existing && $existing->status === MonthlySalaryRecord::STATUS_FINALIZED) {
            throw ValidationException::withMessages([
                'period' => __('This salary period is finalized and cannot be recomputed.'),
            ]);
        }

        $profile = EmployeeSalaryProfile::query()->where('user_id', $user->id)->firstOrFail();

        $base = (string) $profile->base_salary_pkr;
        $lateMinutes = $this->sumLateMinutesForUserMonth($user, $year, $month);
        $perMinute = $this->pkPerLateMinute($base);
        $lateDeduction = bcmul((string) $lateMinutes, $perMinute, 4);
        $lateDeduction = number_format((float) $lateDeduction, 2, '.', '');

        $unpaidDays = $this->unpaidLeaveDaysInCalendarMonth($user, $year, $month);
        $dailyRate = bcdiv($base, (string) self::ASSUMED_WORKING_DAYS_PER_MONTH, 6);
        $leaveDeduction = bcmul((string) $unpaidDays, $dailyRate, 4);
        $leaveDeduction = number_format((float) $leaveDeduction, 2, '.', '');

        $advanceTotal = $this->pendingAdvanceTotalForMonth($user, $year, $month);

        $final = (float) $base;
        $final -= (float) $lateDeduction;
        $final -= (float) $leaveDeduction;
        $final -= (float) $advanceTotal;
        $final = round($final, 2);

        $notes = sprintf(
            'Late rate PKR/min (6dp basis): %s; unpaid leave days in month: %d; daily rate basis: base/%d.',
            $perMinute,
            $unpaidDays,
            self::ASSUMED_WORKING_DAYS_PER_MONTH
        );

        $record = MonthlySalaryRecord::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'period_year' => $year,
                'period_month' => $month,
            ],
            [
                'base_salary_pkr' => $base,
                'total_late_minutes' => $lateMinutes,
                'late_deduction_pkr' => $lateDeduction,
                'leave_deduction_pkr' => $leaveDeduction,
                'unpaid_leave_days_in_period' => $unpaidDays,
                'advance_deduction_pkr' => $advanceTotal,
                'other_adjustment_pkr' => 0,
                'final_payable_pkr' => number_format($final, 2, '.', ''),
                'status' => MonthlySalaryRecord::STATUS_DRAFT,
                'calculation_notes' => $notes,
                'last_computed_by_user_id' => $actor?->id,
                'last_computed_at' => now(),
            ]
        );

        return $record;
    }

    public function finalizeRecord(MonthlySalaryRecord $record, User $admin): void
    {
        if ($record->status === MonthlySalaryRecord::STATUS_FINALIZED) {
            return;
        }

        DB::transaction(function () use ($record, $admin): void {
            $record->update([
                'status' => MonthlySalaryRecord::STATUS_FINALIZED,
                'last_computed_by_user_id' => $admin->id,
                'last_computed_at' => now(),
            ]);

            AdvanceSalaryRequest::query()
                ->where('user_id', $record->user_id)
                ->where('status', AdvanceSalaryRequest::STATUS_APPROVED)
                ->where('deduction_period_year', $record->period_year)
                ->where('deduction_period_month', $record->period_month)
                ->update(['status' => AdvanceSalaryRequest::STATUS_DEDUCTED]);
        });
    }
}
