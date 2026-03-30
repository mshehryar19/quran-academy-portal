@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Edit salary — {{ $salary_profile->user->name }}</h2>
    </div>

    <form method="post" action="{{ route('admin.salary-profiles.update', $salary_profile) }}" class="max-w-md space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700" for="base_salary_pkr">Base monthly (PKR)</label>
            <input id="base_salary_pkr" name="base_salary_pkr" type="number" step="0.01" min="0" value="{{ old('base_salary_pkr', $salary_profile->base_salary_pkr) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('base_salary_pkr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="effective_from">Effective from</label>
            <input id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', $salary_profile->effective_from->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('notes', $salary_profile->notes) }}</textarea>
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Update</button>
    </form>
@endsection
