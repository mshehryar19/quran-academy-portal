@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Leave review</h2>
        <p class="text-sm text-gray-600">{{ $leave->user->name }} · {{ $leave->leave_type }} · {{ $leave->total_days }} day(s)</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm text-sm space-y-2">
            <p>{{ $leave->start_date->format('Y-m-d') }} — {{ $leave->end_date->format('Y-m-d') }}</p>
            <p>Paid: {{ $leave->is_paid ? 'Yes' : 'No' }}</p>
            <p class="whitespace-pre-wrap">{{ $leave->reason }}</p>
        </section>
        @can('supervisorDecide', $leave)
            <section class="rounded-lg border border-blue-200 bg-blue-50 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-blue-900">Your decision (first stage)</h3>
                <form method="post" action="{{ route('supervisor.leaves.decide', $leave) }}" class="mt-4 space-y-3">
                    @csrf
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="decision" value="approved" required> Approve</label>
                        <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="decision" value="rejected" required> Reject</label>
                    </div>
                    @error('decision')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="comment">Comment</label>
                        <textarea id="comment" name="comment" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" class="rounded-md bg-blue-900 px-4 py-2 text-sm text-white">Submit</button>
                </form>
            </section>
        @else
            <section class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600">
                Supervisor decision: <strong>{{ $leave->supervisor_decision ?? '—' }}</strong>
                @if($leave->supervisor_comment)<p class="mt-2">{{ $leave->supervisor_comment }}</p>@endif
            </section>
        @endcan
    </div>
    <p class="mt-6"><a href="{{ route('supervisor.leaves.index') }}" class="text-sm underline">Back</a></p>
@endsection
