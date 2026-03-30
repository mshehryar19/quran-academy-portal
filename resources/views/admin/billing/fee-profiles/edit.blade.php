@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Edit fee — {{ $profile->student->full_name }}</h2>
    </div>

    <form method="post" action="{{ route('admin.billing.student-fee-profiles.update', $profile) }}" class="max-w-lg space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700" for="monthly_fee_amount">Monthly fee</label>
            <input id="monthly_fee_amount" name="monthly_fee_amount" type="number" step="0.01" min="0" value="{{ old('monthly_fee_amount', $profile->monthly_fee_amount) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="currency">Currency</label>
            <select id="currency" name="currency" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="GBP" @selected(old('currency', $profile->currency) === 'GBP')>GBP</option>
                <option value="USD" @selected(old('currency', $profile->currency) === 'USD')>USD</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_from">Effective from</label>
                <input id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', $profile->effective_from->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="effective_to">Effective to</label>
                <input id="effective_to" name="effective_to" type="date" value="{{ old('effective_to', $profile->effective_to?->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
            <select id="status" name="status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="active" @selected(old('status', $profile->status) === 'active')>active</option>
                <option value="inactive" @selected(old('status', $profile->status) === 'inactive')>inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('notes', $profile->notes) }}</textarea>
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Update</button>
    </form>
@endsection
