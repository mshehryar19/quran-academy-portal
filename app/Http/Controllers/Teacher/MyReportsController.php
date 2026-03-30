<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceEvent;
use App\Models\HomeworkTask;
use App\Models\LessonSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Teacher', 'permission:reports.view']);
    }

    public function __invoke(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_if(! $teacher, 404);

        $from = $request->input('from');
        $to = $request->input('to');

        $attendanceQuery = EmployeeAttendanceEvent::query()
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('occurred_at');

        if ($from) {
            $attendanceQuery->whereDate('attendance_date', '>=', (string) $from);
        }
        if ($to) {
            $attendanceQuery->whereDate('attendance_date', '<=', (string) $to);
        }

        $attendanceEvents = $attendanceQuery->limit(100)->get();

        $lessonSummaries = LessonSummary::query()
            ->where('teacher_id', $teacher->id)
            ->when($from, fn ($q) => $q->whereDate('submitted_at', '>=', (string) $from))
            ->when($to, fn ($q) => $q->whereDate('submitted_at', '<=', (string) $to))
            ->orderByDesc('submitted_at')
            ->limit(50)
            ->get();

        $homeworkStats = [
            'assigned' => HomeworkTask::query()->where('teacher_id', $teacher->id)->count(),
            'completed' => HomeworkTask::query()->where('teacher_id', $teacher->id)->where('status', 'completed')->count(),
            'pending' => HomeworkTask::query()->where('teacher_id', $teacher->id)->where('status', 'pending')->count(),
        ];

        return view('teacher.reports.summary', compact(
            'teacher',
            'attendanceEvents',
            'lessonSummaries',
            'homeworkStats',
            'from',
            'to'
        ));
    }
}
