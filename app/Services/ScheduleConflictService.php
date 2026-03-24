<?php

namespace App\Services;

use App\Models\ClassSchedule;
use Illuminate\Database\Query\Builder;

class ScheduleConflictService
{
    private const FAR_END = '9999-12-31';

    /**
     * Returns an error message string if a conflict exists, otherwise null.
     */
    public function validateNewOrUpdate(
        int $teacherId,
        int $studentId,
        int $classSlotId,
        int $dayOfWeek,
        string $startDate,
        ?string $endDate,
        ?int $excludeClassScheduleId = null,
    ): ?string {
        if ($this->hasOverlapConflict('teacher_id', $teacherId, $classSlotId, $dayOfWeek, $startDate, $endDate, $excludeClassScheduleId)) {
            return __('This teacher already has an active class in this slot on the same weekday with overlapping dates.');
        }

        if ($this->hasOverlapConflict('student_id', $studentId, $classSlotId, $dayOfWeek, $startDate, $endDate, $excludeClassScheduleId)) {
            return __('This student already has an active class in this slot on the same weekday with overlapping dates.');
        }

        return null;
    }

    /**
     * Whether the teacher has any active schedule blocking the slot+weekday+date range.
     */
    public function hasOverlapConflict(
        string $column,
        int $id,
        int $classSlotId,
        int $dayOfWeek,
        string $startDate,
        ?string $endDate,
        ?int $excludeClassScheduleId,
    ): bool {
        $rangeEnd = $endDate ?? self::FAR_END;

        return ClassSchedule::query()
            ->where('status', 'active')
            ->where($column, $id)
            ->where('class_slot_id', $classSlotId)
            ->where('day_of_week', $dayOfWeek)
            ->when($excludeClassScheduleId, fn ($q) => $q->where('id', '!=', $excludeClassScheduleId))
            ->whereRaw(
                '? <= COALESCE(end_date, ?) AND ? >= start_date',
                [$startDate, self::FAR_END, $rangeEnd]
            )
            ->exists();
    }

    /**
     * Quick check: is teacher busy in this slot on this weekday for an active overlapping range?
     */
    public function isTeacherBusy(
        int $teacherId,
        int $classSlotId,
        int $dayOfWeek,
        string $onDate,
    ): bool {
        return ClassSchedule::query()
            ->where('status', 'active')
            ->where('teacher_id', $teacherId)
            ->where('class_slot_id', $classSlotId)
            ->where('day_of_week', $dayOfWeek)
            ->whereRaw(
                '? BETWEEN start_date AND COALESCE(end_date, ?)',
                [$onDate, self::FAR_END]
            )
            ->exists();
    }

    /**
     * Is teacher free (no active schedule) for slot+weekday on a given calendar date?
     */
    public function isTeacherFree(
        int $teacherId,
        int $classSlotId,
        int $dayOfWeek,
        string $onDate,
    ): bool {
        return ! $this->isTeacherBusy($teacherId, $classSlotId, $dayOfWeek, $onDate);
    }

    /**
     * Raw SQL overlap helper for complex queries (e.g. reporting).
     *
     * @param  Builder|\Illuminate\Database\Eloquent\Builder  $query
     */
    public function applyActiveDateOverlap($query, string $startDate, ?string $endDate): void
    {
        $rangeEnd = $endDate ?? self::FAR_END;
        $query->whereRaw(
            '? <= COALESCE(end_date, ?) AND ? >= start_date',
            [$startDate, self::FAR_END, $rangeEnd]
        );
    }
}
