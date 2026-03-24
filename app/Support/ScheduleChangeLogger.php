<?php

namespace App\Support;

use App\Models\ClassSchedule;
use App\Models\ScheduleChangeLog;
use App\Models\User;

class ScheduleChangeLogger
{
    public static function log(ClassSchedule $schedule, string $action, array $properties = [], ?User $user = null): void
    {
        ScheduleChangeLog::query()->create([
            'class_schedule_id' => $schedule->id,
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'properties' => $properties === [] ? null : $properties,
            'created_at' => now(),
        ]);
    }
}
