@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Request leave</h2>
        <p class="text-sm text-gray-600">Paid leave remaining this cycle: <strong>{{ $balance }}</strong> days (medical requires attachment).</p>
    </div>

    <form method="post" action="{{ route('employee.leaves.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="leave_type">Leave type</label>
            <select id="leave_type" name="leave_type" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @foreach (\App\Models\LeaveRequest::types() as $t)
                    <option value="{{ $t }}" @selected(old('leave_type') === $t)>{{ $t }}</option>
                @endforeach
            </select>
            @error('leave_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="is_paid">Paid leave (counts toward 12-day allowance)</label>
            <select id="is_paid" name="is_paid" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="1" @selected(old('is_paid', '1') === '1' || old('is_paid', '1') === true)>Yes</option>
                <option value="0" @selected((string) old('is_paid', '1') === '0')>No</option>
            </select>
            @error('is_paid')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="start_date">Start</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="end_date">End</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="reason">Reason</label>
            <textarea id="reason" name="reason" rows="4" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
            @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="attachment">Medical attachment (PDF/JPEG/PNG, max 5MB)</label>
            <input id="attachment" name="attachment" type="file" class="mt-1 w-full text-sm">
            @error('attachment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Submit</button>
    </form>
@endsection
