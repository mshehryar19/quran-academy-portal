@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Leave request</h2>
        <p class="text-sm text-gray-600">{{ $leave->start_date->format('Y-m-d') }} — {{ $leave->end_date->format('Y-m-d') }} · {{ $leave->total_days }} day(s)</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm text-sm space-y-2">
            <p><span class="text-gray-500">Type</span><br><strong>{{ $leave->leave_type }}</strong></p>
            <p><span class="text-gray-500">Paid</span><br><strong>{{ $leave->is_paid ? 'Yes' : 'No' }}</strong></p>
            <p><span class="text-gray-500">Reason</span><br>{{ $leave->reason }}</p>
            @if($leave->attachment_path)
                <p>
                    <a href="{{ route('employee.leaves.attachment', $leave) }}" class="text-gray-900 underline">Download attachment</a>
                </p>
            @endif
        </section>
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm text-sm space-y-3">
            <h3 class="font-semibold text-gray-900">Workflow</h3>
            <p>
                <span class="text-gray-500">Supervisor</span><br>
                @if($leave->supervisor_decision)
                    <strong>{{ $leave->supervisor_decision }}</strong>
                    @if($leave->supervisorUser) — {{ $leave->supervisorUser->name }} @endif
                    <br><span class="text-xs text-gray-600">{{ $leave->supervisor_decided_at?->format('Y-m-d H:i') }}</span>
                    @if($leave->supervisor_comment)<br><em>{{ $leave->supervisor_comment }}</em>@endif
                @else
                    <span class="text-amber-700">Pending</span>
                @endif
            </p>
            <p>
                <span class="text-gray-500">Admin (final)</span><br>
                @if($leave->admin_decision)
                    <strong>{{ $leave->admin_decision }}</strong>
                    @if($leave->adminUser) — {{ $leave->adminUser->name }} @endif
                    <br><span class="text-xs text-gray-600">{{ $leave->admin_decided_at?->format('Y-m-d H:i') }}</span>
                    @if($leave->admin_comment)<br><em>{{ $leave->admin_comment }}</em>@endif
                @else
                    <span class="text-amber-700">Pending</span>
                @endif
            </p>
            <p>
                <span class="text-gray-500">Outcome</span><br>
                @if($leave->isFullyResolved())
                    <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold uppercase">{{ $leave->finalStatus() }}</span>
                @else
                    —
                @endif
            </p>
        </section>
    </div>
    <p class="mt-6"><a href="{{ route('employee.leaves.index') }}" class="text-sm text-gray-600 underline">Back to list</a></p>
@endsection
