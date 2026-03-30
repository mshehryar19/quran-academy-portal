<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\Invoice;
use App\Models\LeaveRequest;
use App\Models\StaffNotice;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $pendingFinalLeaves = 0;
        if ($user?->can('salary.manage')) {
            $pendingFinalLeaves = LeaveRequest::query()
                ->whereNotNull('supervisor_decision')
                ->whereNull('admin_decision')
                ->count();
        }

        $outstandingTuition = null;
        if ($user?->can('invoice.manage')) {
            $outstandingTuition = Invoice::query()
                ->where('status', '!=', Invoice::STATUS_CANCELLED)
                ->get()
                ->sum(fn (Invoice $i) => $i->balanceOutstanding());
        }

        $recentStaffNotices = null;
        if ($user?->can('notifications.manage')) {
            $recentStaffNotices = StaffNotice::query()->with('createdBy')->latest()->limit(5)->get();
        }

        $quickStats = null;
        if ($user?->hasAnyRole(['Admin', 'HR', 'Supervisor'])) {
            $quickStats = [
                'teachers_active' => Teacher::query()->where('status', 'active')->count(),
                'students_active' => Student::query()->where('status', 'active')->count(),
                'schedules_active' => ClassSchedule::query()->where('status', 'active')->count(),
            ];
        }

        return view('admin.dashboard', [
            'user' => $user,
            'primaryRole' => $user?->getRoleNames()->first() ?? 'Admin',
            'pendingFinalLeaves' => $pendingFinalLeaves,
            'outstandingTuition' => $outstandingTuition,
            'unreadNotificationsCount' => $user?->unreadNotifications()->count() ?? 0,
            'recentStaffNotices' => $recentStaffNotices,
            'quickStats' => $quickStats,
        ]);
    }
}
