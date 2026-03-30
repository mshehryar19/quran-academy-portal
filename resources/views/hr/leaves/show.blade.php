@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Leave record (HR)</h2>
        <p class="text-sm text-gray-600">{{ $leave->user->name }}</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm text-sm space-y-3">
        <p>{{ $leave->start_date->format('Y-m-d') }} — {{ $leave->end_date->format('Y-m-d') }} · {{ $leave->total_days }} days · {{ $leave->leave_type }} · Paid: {{ $leave->is_paid ? 'yes' : 'no' }}</p>
        <p class="whitespace-pre-wrap">{{ $leave->reason }}</p>
        <hr class="border-gray-100">
        <p><strong>Supervisor</strong> {{ $leave->supervisor_decision ?? 'pending' }} @if($leave->supervisorUser) ({{ $leave->supervisorUser->name }}) @endif</p>
        @if($leave->supervisor_comment)<p>{{ $leave->supervisor_comment }}</p>@endif
        <p><strong>Admin</strong> {{ $leave->admin_decision ?? 'pending' }} @if($leave->adminUser) ({{ $leave->adminUser->name }}) @endif</p>
        @if($leave->admin_comment)<p>{{ $leave->admin_comment }}</p>@endif
    </div>
    <p class="mt-6"><a href="{{ route('hr.leaves.index') }}" class="text-sm underline">Back</a></p>
@endsection
