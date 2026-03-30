<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClassSessionService
{
    /**
     * Ensure one ClassSession per applicable active schedule for this teacher on the given calendar date.
     *
     * @return Collection<int, ClassSession>
     */
    public function syncSessionsForTeacherAndDate(Teacher $teacher, Carbon $date): Collection
    {
        $dow = (int) $date->format('N');

        $schedules = ClassSchedule::query()
            ->where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->where('day_of_week', $dow)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
            })
            ->with('classSlot')
            ->get();

        $sessions = collect();

        foreach ($schedules as $schedule) {
            $sessions->push(ClassSession::query()->firstOrCreate(
                [
                    'class_schedule_id' => $schedule->id,
                    'session_date' => $date->toDateString(),
                ],
                ['status' => 'scheduled']
            ));
        }

        return $sessions
            ->map(fn (ClassSession $s) => $s->load(['classSchedule.classSlot', 'classSchedule.student']))
            ->sortBy(fn (ClassSession $s) => (string) $s->classSchedule->classSlot->start_time)
            ->values();
    }
}
