@extends('layouts.attendance')

@section('content')
    <div class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-600">Signed in as</p>
            <p class="text-lg font-semibold">{{ $teacher->full_name }}</p>
            <p class="text-xs text-slate-500">{{ $date->toDateString() }}</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Sign in (class)</h3>
            <form method="post" action="{{ route('attendance.sign-in') }}" class="mt-3 space-y-2">
                @csrf
                <label class="block text-xs text-slate-600" for="class_session_id">Today&rsquo;s class</label>
                <select id="class_session_id" name="class_session_id" required
                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
                    <option value="">— Select —</option>
                    @foreach ($sessions as $s)
                        @php($sch = $s->classSchedule)
                        <option value="{{ $s->id }}">
                            {{ $sch->classSlot->timeRangeLabel() }}
                            — {{ $sch->student->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('class_session_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="mt-2 w-full rounded-md bg-emerald-700 py-2 text-sm font-medium text-white hover:bg-emerald-800">
                    Record sign-in
                </button>
            </form>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Sign out</h3>
            <p class="mt-1 text-xs text-slate-500">Closes the most recent open sign-in for today.</p>
            <form method="post" action="{{ route('attendance.sign-out') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-md border border-slate-300 bg-white py-2 text-sm font-medium hover:bg-slate-50">
                    Record sign-out
                </button>
                @error('signout')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </form>
            @if ($openLogin)
                <p class="mt-2 text-xs text-amber-700">Open sign-in detected — use sign-out when finished.</p>
            @endif
        </div>

        <form method="post" action="{{ route('attendance.leave') }}">
            @csrf
            <button type="submit" class="w-full text-sm text-slate-500 underline">End attendance session</button>
        </form>
    </div>
@endsection
