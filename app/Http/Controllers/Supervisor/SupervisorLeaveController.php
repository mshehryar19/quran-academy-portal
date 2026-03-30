<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\SupervisorLeaveDecisionRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisorLeaveController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'pending')->toString();

        $query = LeaveRequest::query()
            ->with(['user.teacher'])
            ->orderByDesc('created_at');

        if ($tab === 'pending') {
            $query->whereNull('supervisor_decision');
        } elseif ($tab === 'history') {
            $query->whereNotNull('supervisor_decision');
        }

        $leaves = $query->paginate(20)->withQueryString();

        return view('supervisor.leaves.index', compact('leaves', 'tab'));
    }

    public function show(LeaveRequest $leave): View
    {
        $this->authorize('view', $leave);
        $leave->load(['user.teacher']);

        return view('supervisor.leaves.show', compact('leave'));
    }

    public function decide(SupervisorLeaveDecisionRequest $request, LeaveRequest $leave): RedirectResponse
    {
        $leave->update([
            'supervisor_decision' => $request->string('decision')->toString(),
            'supervisor_user_id' => $request->user()->id,
            'supervisor_comment' => $request->input('comment'),
            'supervisor_decided_at' => now(),
        ]);

        activity()
            ->performedOn($leave)
            ->causedBy($request->user())
            ->event('leave.supervisor_decision')
            ->withProperties(['decision' => $leave->supervisor_decision])
            ->log('Supervisor reviewed leave request');

        return redirect()->route('supervisor.leaves.index')->with('status', __('Supervisor decision recorded.'));
    }
}
