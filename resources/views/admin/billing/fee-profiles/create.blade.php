@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">New fee profile</h2>
    </div>

    <form method="post" action="{{ route('admin.billing.student-fee-profiles.store') }}" class="max-w-lg space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="student_id">Student</label>
            <select id="student_id" name="student_id" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @foreach ($students as $s)
                    <option value="{{ $s->id }}" @selected(old('student_id') == $s->id)>{{ $s->full_name }}</option>
                @endforeach
            </select>
            @error('student_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="monthly_fee_amount">Monthly fee</label>
            <input id="monthly_fee_amount" name="monthly_fee_amount" type="number" step="0.01" min="0" value="{{ old('monthly_fee_amount') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="currency">Currency</label>
            <select id="currency" name="currency" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="GBP" @selected(old('currency', 'GBP') === 'GBP')>GBP (default)</option>
                <option value="USD" @selected(old('currency') === 'USD')>USD</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_from">Effective from</label>
                <input id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_to">Effective to</label>
                <input id="effective_to" name="effective_to" type="date" value="{{ old('effective_to') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
            <select id="status" name="status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="active" @selected(old('status', 'active') === 'active')>active</option>
                <option value="inactive" @selected(old('status') === 'inactive')>inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Save</button>
    </form>
@endsection
