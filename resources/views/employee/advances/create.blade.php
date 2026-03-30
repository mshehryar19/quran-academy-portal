@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Request advance (PKR)</h2>
    </div>

    <form method="post" action="{{ route('employee.advances.store') }}" class="max-w-md space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="amount_pkr">Amount (PKR)</label>
            <input id="amount_pkr" name="amount_pkr" type="number" step="0.01" min="0.01" value="{{ old('amount_pkr') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('amount_pkr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="reason">Reason</label>
            <textarea id="reason" name="reason" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Submit</button>
    </form>
@endsection
