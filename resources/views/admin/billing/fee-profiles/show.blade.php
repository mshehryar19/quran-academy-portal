@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">Fee profile</h2>
            <p class="text-sm text-gray-600">{{ $profile->student->full_name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.billing.student-fee-profiles.edit', $profile) }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">Edit</a>
            <form method="post" action="{{ route('admin.billing.student-fee-profiles.destroy', $profile) }}" onsubmit="return confirm('Delete this fee profile?');">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">Delete</button>
            </form>
        </div>
    </div>

    <dl class="max-w-lg space-y-2 rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm">
        <div class="flex justify-between"><dt class="text-gray-500">Amount</dt><dd class="font-medium">{{ number_format($profile->monthly_fee_amount, 2) }} {{ $profile->currency }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Effective</dt><dd>{{ $profile->effective_from->format('Y-m-d') }} — {{ $profile->effective_to?->format('Y-m-d') ?? 'open' }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd>{{ $profile->status }}</dd></div>
        @if($profile->notes)<div><dt class="text-gray-500">Notes</dt><dd class="mt-1">{{ $profile->notes }}</dd></div>@endif
    </dl>
@endsection
