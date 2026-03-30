<?php

namespace App\Services;

use App\Models\EmployeeAttendanceEvent;
use App\Models\Teacher;
use Carbon\Carbon;

class EmployeeAttendancePairingService
{
    public function findUnpairedLogin(Teacher $teacher, Carbon $date): ?EmployeeAttendanceEvent
    {
        $logins = EmployeeAttendanceEvent::query()
            ->where('teacher_id', $teacher->id)
            ->whereDate('attendance_date', $date->toDateString())
            ->where('event_type', 'login')
            ->orderBy('occurred_at')
            ->get();

        foreach ($logins as $login) {
            $paired = EmployeeAttendanceEvent::query()
                ->where('teacher_id', $teacher->id)
                ->where('event_type', 'logout')
                ->where('paired_login_event_id', $login->id)
                ->exists();

            if (! $paired) {
                return $login;
            }
        }

        return null;
    }
}
