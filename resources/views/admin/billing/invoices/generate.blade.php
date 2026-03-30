@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Generate monthly invoices</h2>
        <p class="text-sm text-gray-600">Creates one invoice per student with an active fee profile covering the month. Skips students who already have a non-cancelled invoice for that period. CLI: <code class="rounded bg-gray-100 px-1">php artisan billing:generate-month {year} {month}</code></p>
    </div>

    <form method="post" action="{{ route('admin.billing.invoices.generate.run') }}" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="billing_year">Year</label>
                <input id="billing_year" name="billing_year" type="number" value="{{ old('billing_year', now()->year) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="billing_month">Month</label>
                <input id="billing_month" name="billing_month" type="number" min="1" max="12" value="{{ old('billing_month', now()->month) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Limit to students (optional)</p>
            <p class="text-xs text-gray-500">Leave all unchecked to include every eligible student.</p>
            <div class="mt-2 max-h-48 space-y-1 overflow-y-auto rounded border border-gray-200 p-2 text-sm">
                @foreach ($students as $s)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" @checked(in_array($s->id, old('student_ids', [])))>
                        {{ $s->full_name }}
                    </label>
                @endforeach
            </div>
            @error('student_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Generate</button>
    </form>
@endsection
