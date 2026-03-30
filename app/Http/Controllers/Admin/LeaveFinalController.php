<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\AdminLeaveDecisionRequest;
use App\Models\LeaveRequest;
use App\Services\SystemNotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveFinalController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'pending')->toString();

        $query = LeaveRequest::query()
            ->with(['user.teacher', 'supervisorUser'])
            ->orderByDesc('created_at');

        if ($tab === 'pending') {
            $query->whereNotNull('supervisor_decision')->whereNull('admin_decision');
        } else {
            $query->whereNotNull('admin_decision');
        }

        $leaves = $query->paginate(20)->withQueryString();

        return view('admin.leaves.final-index', compact('leaves', 'tab'));
    }

    public function show(LeaveRequest $leave): View
    {
        $this->authorize('view', $leave);
        $leave->load(['user.teacher', 'supervisorUser', 'adminUser']);

        return view('admin.leaves.final-show', compact('leave'));
    }

    public function decide(AdminLeaveDecisionRequest $request, LeaveRequest $leave, SystemNotificationDispatcher $notifications): RedirectResponse
    {
        $leave->update([
            'admin_decision' => $request->string('decision')->toString(),
            'admin_user_id' => $request->user()->id,
            'admin_comment' => $request->input('comment'),
            'admin_decided_at' => now(),
        ]);

        activity()
            ->performedOn($leave)
            ->causedBy($request->user())
            ->event('leave.admin_final')
            ->withProperties([
                'decision' => $leave->admin_decision,
                'supervisor_decision' => $leave->supervisor_decision,
            ])
            ->log('Admin final leave decision');

        $leave->refresh();
        $notifications->leaveFinalDecision($leave);

        return redirect()->route('admin.leaves.final.index')->with('status', __('Final leave decision recorded.'));
    }
}
