@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Advance request</h2>
        <p class="text-sm text-gray-600">{{ $advanceSalaryRequest->user->name }} · {{ number_format($advanceSalaryRequest->amount_pkr, 2) }} PKR</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm">
        <p>Status: <strong>{{ $advanceSalaryRequest->status }}</strong></p>
        @if($advanceSalaryRequest->reason)<p class="mt-2 whitespace-pre-wrap">{{ $advanceSalaryRequest->reason }}</p>@endif
        @if($advanceSalaryRequest->deduction_period_year)
            <p class="mt-2 text-xs text-gray-600">Scheduled deduction period: {{ sprintf('%04d-%02d', $advanceSalaryRequest->deduction_period_year, $advanceSalaryRequest->deduction_period_month) }}</p>
        @endif
    </div>

    @if($advanceSalaryRequest->status === \App\Models\AdvanceSalaryRequest::STATUS_PENDING)
        <form method="post" action="{{ route('admin.advances.decide', $advanceSalaryRequest) }}" class="mt-6 max-w-md space-y-3 rounded-lg border border-amber-200 bg-amber-50 p-6">
            @csrf
            <div class="flex gap-4">
                <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="decision" value="{{ \App\Models\AdvanceSalaryRequest::STATUS_APPROVED }}" required> Approve</label>
                <label class="inline-flex items-center gap-2 text-sm"><input type="radio" name="decision" value="{{ \App\Models\AdvanceSalaryRequest::STATUS_REJECTED }}" required> Reject</label>
            </div>
            <textarea name="comment" rows="2" placeholder="Comment" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('comment') }}</textarea>
            @error('decision')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <button type="submit" class="rounded-md bg-amber-900 px-4 py-2 text-sm text-white">Submit</button>
        </form>
    @endif

    <p class="mt-6"><a href="{{ route('admin.advances.index') }}" class="text-sm underline">Back</a></p>
@endsection
