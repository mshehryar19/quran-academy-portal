@extends('layouts.attendance')

@section('content')
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold">Enter attendance ID</h2>
        <p class="mt-1 text-sm text-slate-600">Use the digits assigned by admin. No portal password required.</p>

        <form method="post" action="{{ route('attendance.establish') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="sr-only" for="attendance_digits">Attendance ID</label>
                <input id="attendance_digits" name="attendance_digits" type="text" inputmode="numeric" pattern="\d*"
                       autocomplete="off" autofocus
                       class="w-full rounded-md border border-slate-300 px-4 py-3 text-center text-2xl font-mono tracking-widest shadow-sm focus:border-slate-900 focus:outline-none"
                       placeholder="••••••" maxlength="16" required>
                @error('attendance_digits')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full rounded-md bg-slate-900 py-3 text-sm font-medium text-white hover:bg-slate-800">
                Continue
            </button>
        </form>
    </div>
@endsection
