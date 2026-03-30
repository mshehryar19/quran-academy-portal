<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\StoreLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestController extends Controller
{
    public function index(Request $request, LeaveBalanceService $balanceService): View
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $leaves = LeaveRequest::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('start_date')
            ->paginate(15);

        $balance = $balanceService->remainingPaidLeaveDays($request->user());
        $cycleStart = $balanceService->currentCycleStart($request->user(), now());
        $cycleEnd = $balanceService->currentCycleEnd($request->user(), now());

        return view('employee.leaves.index', compact('leaves', 'balance', 'cycleStart', 'cycleEnd'));
    }

    public function create(Request $request, LeaveBalanceService $balanceService): View
    {
        $this->authorize('create', LeaveRequest::class);

        $balance = $balanceService->remainingPaidLeaveDays($request->user());

        return view('employee.leaves.create', compact('balance'));
    }

    public function store(StoreLeaveRequestRequest $request, LeaveBalanceService $balanceService): RedirectResponse
    {
        $start = Carbon::parse($request->string('start_date')->toString())->startOfDay();
        $end = Carbon::parse($request->string('end_date')->toString())->startOfDay();
        $totalDays = LeaveBalanceService::inclusiveDayCount($start, $end);

        $path = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower((string) $file->getClientOriginalExtension());
            $ext = match ($ext) {
                'pdf' => 'pdf',
                'jpg', 'jpeg' => 'jpg',
                'png' => 'png',
                default => 'bin',
            };
            $path = $file->storeAs('leave_attachments', Str::uuid()->toString().'.'.$ext, 'local');
        }

        $leave = LeaveRequest::query()->create([
            'user_id' => $request->user()->id,
            'leave_type' => $request->string('leave_type')->toString(),
            'is_paid' => $request->boolean('is_paid'),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_days' => $totalDays,
            'reason' => $request->string('reason')->toString(),
            'attachment_path' => $path,
        ]);

        activity()
            ->performedOn($leave)
            ->causedBy($request->user())
            ->event('leave.submitted')
            ->log('Leave request submitted');

        return redirect()->route('employee.leaves.show', $leave)->with('status', __('Leave request submitted.'));
    }

    public function show(Request $request, LeaveRequest $leave): View
    {
        $this->authorize('view', $leave);

        $leave->load(['supervisorUser', 'adminUser']);

        return view('employee.leaves.show', compact('leave'));
    }

    public function downloadAttachment(Request $request, LeaveRequest $leave): StreamedResponse
    {
        $this->authorize('downloadAttachment', $leave);

        if (! $leave->attachment_path || ! Storage::disk('local')->exists($leave->attachment_path)) {
            abort(404);
        }

        activity()
            ->performedOn($leave)
            ->causedBy($request->user())
            ->event('leave.attachment_downloaded')
            ->log('Leave medical attachment downloaded');

        return Storage::disk('local')->download($leave->attachment_path);
    }
}
