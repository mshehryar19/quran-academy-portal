<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\HomeworkTask;
use App\Models\Invoice;
use App\Models\StaffNotice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $recentStaffNotices = null;
        if ($user && $user->hasAnyRole(['Admin', 'HR', 'Supervisor', 'Teacher', 'Accountant'])) {
            $recentStaffNotices = StaffNotice::query()
                ->published()
                ->notExpired()
                ->visibleToUser($user)
                ->latest()
                ->limit(4)
                ->get();
        }

        $teacherSnapshot = null;
        if ($user && $user->hasRole('Teacher') && $user->teacher) {
            $tid = $user->teacher->id;
            $teacherSnapshot = [
                'sessions_today' => ClassSession::query()
                    ->whereDate('session_date', today())
                    ->whereHas('classSchedule', fn ($q) => $q->where('teacher_id', $tid))
                    ->count(),
                'pending_homework' => HomeworkTask::query()
                    ->where('teacher_id', $tid)
                    ->where('status', 'pending')
                    ->count(),
            ];
        }

        $parentSnapshot = null;
        if ($user && $user->hasRole('Parent') && $user->academyParent) {
            $parent = $user->academyParent;
            $ids = $parent->students()->pluck('students.id');
            $parentSnapshot = [
                'children' => $parent->students()->count(),
                'open_invoices' => Invoice::query()
                    ->whereIn('student_id', $ids)
                    ->whereIn('status', [
                        Invoice::STATUS_UNPAID,
                        Invoice::STATUS_PARTIALLY_PAID,
                        Invoice::STATUS_OVERDUE,
                    ])
                    ->count(),
            ];
        }

        $studentSnapshot = null;
        if ($user && $user->hasRole('Student') && $user->student) {
            $sid = $user->student->id;
            $studentSnapshot = [
                'sessions_today' => ClassSession::query()
                    ->whereDate('session_date', today())
                    ->whereHas('classSchedule', fn ($q) => $q->where('student_id', $sid))
                    ->count(),
                'billing_attention' => Invoice::query()
                    ->where('student_id', $sid)
                    ->whereIn('status', [
                        Invoice::STATUS_UNPAID,
                        Invoice::STATUS_PARTIALLY_PAID,
                        Invoice::STATUS_OVERDUE,
                    ])
                    ->count(),
            ];
        }

        return view('dashboard.index', [
            'user' => $user,
            'primaryRole' => $user?->getRoleNames()->first() ?? 'Unassigned',
            'unreadNotificationsCount' => $user?->unreadNotifications()->count() ?? 0,
            'recentStaffNotices' => $recentStaffNotices,
            'teacherSnapshot' => $teacherSnapshot,
            'parentSnapshot' => $parentSnapshot,
            'studentSnapshot' => $studentSnapshot,
        ]);
    }
}
