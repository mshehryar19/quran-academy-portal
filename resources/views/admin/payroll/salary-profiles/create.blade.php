@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">New salary profile (PKR)</h2>
    </div>

    <form method="post" action="{{ route('admin.salary-profiles.store') }}" class="max-w-md space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="user_id">Employee</label>
            <select id="user_id" name="user_id" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            @error('user_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="base_salary_pkr">Base monthly (PKR)</label>
            <input id="base_salary_pkr" name="base_salary_pkr" type="number" step="0.01" min="0" value="{{ old('base_salary_pkr') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('base_salary_pkr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="effective_from">Effective from</label>
            <input id="effective_from" name="effective_from" type="date" value="{{ old('effective_from', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Save</button>
    </form>
@endsection
