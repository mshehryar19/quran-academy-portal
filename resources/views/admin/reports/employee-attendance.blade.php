@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Employee attendance events</h2>
        <p class="text-sm text-gray-600">Login/logout records for salary-prep (deduction logic is a later phase).</p>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500" for="teacher_id">Teacher</label>
            <select id="teacher_id" name="teacher_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected((string) request('teacher_id') === (string) $t->id)>{{ $t->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="from">From</label>
            <input id="from" name="from" type="date" value="{{ request('from') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="to">To</label>
            <input id="to" name="to" type="date" value="{{ request('to') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="event_type">Type</label>
            <select id="event_type" name="event_type" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="login" @selected(request('event_type') === 'login')>login</option>
                <option value="logout" @selected(request('event_type') === 'logout')>logout</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Filter</button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.reports.employee-attendance.export.excel', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">Export Excel</a>
        <a href="{{ route('admin.reports.employee-attendance.export.pdf', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">Export PDF</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">When</th>
                <th class="px-3 py-2">Teacher</th>
                <th class="px-3 py-2">Type</th>
                <th class="px-3 py-2">Late (min)</th>
                <th class="px-3 py-2">Session</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($events as $ev)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $ev->occurred_at->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-2">{{ $ev->teacher->full_name }}</td>
                    <td class="px-3 py-2">{{ $ev->event_type }}</td>
                    <td class="px-3 py-2">{{ $ev->late_minutes ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs">{{ $ev->classSession?->id ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if ($unpairedLogins->isNotEmpty())
        <section class="mt-8 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <h3 class="text-sm font-semibold text-amber-900">Open sign-ins (did not logout yet)</h3>
            <p class="mt-1 text-xs text-amber-800">These login events have no matching logout for the same filters (max 50 shown).</p>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($unpairedLogins as $login)
                    <li class="flex flex-wrap gap-2 border-b border-amber-100 pb-2">
                        <span>{{ $login->occurred_at->format('Y-m-d H:i') }}</span>
                        <span class="font-medium">{{ $login->teacher->full_name }}</span>
                        @if ($login->classSession)
                            <span class="text-gray-600">Session #{{ $login->class_session_id }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <p class="mt-4 text-xs text-gray-500">
        &ldquo;Did not logout&rdquo; is indicated by login rows that never received a paired logout (see above when present).
        &ldquo;Did not login&rdquo; for a scheduled day is visible when comparing class sessions to attendance events (full cross-report can be added with the reports module).
    </p>
    <div class="mt-4">{{ $events->links() }}</div>
@endsection
