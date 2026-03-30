<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveMonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $query = LeaveRequest::query()
            ->with(['user.teacher', 'supervisorUser', 'adminUser'])
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('final')) {
            $f = $request->string('final')->toString();
            if ($f === 'approved') {
                $query->where('admin_decision', LeaveRequest::DECISION_APPROVED);
            } elseif ($f === 'rejected') {
                $query->where('admin_decision', LeaveRequest::DECISION_REJECTED);
            } elseif ($f === 'open') {
                $query->whereNull('admin_decision');
            }
        }
        if ($request->filled('is_paid')) {
            $query->where('is_paid', $request->boolean('is_paid'));
        }

        $leaves = $query->paginate(30)->withQueryString();

        $stats = [
            'total' => LeaveRequest::query()->count(),
            'pending_final' => LeaveRequest::query()->whereNull('admin_decision')->count(),
            'approved' => LeaveRequest::query()->where('admin_decision', LeaveRequest::DECISION_APPROVED)->count(),
            'rejected' => LeaveRequest::query()->where('admin_decision', LeaveRequest::DECISION_REJECTED)->count(),
        ];

        $users = User::query()
            ->role(['Teacher', 'HR', 'Supervisor', 'Admin'])
            ->orderBy('name')
            ->get();

        return view('hr.leaves.index', compact('leaves', 'stats', 'users'));
    }

    public function show(LeaveRequest $leave): View
    {
        $this->authorize('view', $leave);
        $leave->load(['user.teacher', 'supervisorUser', 'adminUser']);

        return view('hr.leaves.show', compact('leave'));
    }
}
