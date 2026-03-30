@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Class sessions</h2>
        <p class="text-sm text-gray-600">Occurrences linked to schedules — student attendance, lesson summaries, homework.</p>
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
            <label class="block text-xs text-gray-500" for="status">Session status</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="scheduled" @selected(request('status') === 'scheduled')>scheduled</option>
                <option value="completed" @selected(request('status') === 'completed')>completed</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Filter</button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.reports.class-sessions.export.excel', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">Export Excel</a>
        <a href="{{ route('admin.reports.class-sessions.export.pdf', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">Export PDF</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Teacher</th>
                <th class="px-3 py-2">Student</th>
                <th class="px-3 py-2">Slot</th>
                <th class="px-3 py-2">Student att.</th>
                <th class="px-3 py-2">Lesson</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($sessions as $s)
                @php($sch = $s->classSchedule)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $s->session_date->format('Y-m-d') }}</td>
                    <td class="px-3 py-2">{{ $sch->teacher->full_name }}</td>
                    <td class="px-3 py-2">{{ $sch->student->full_name }}</td>
                    <td class="px-3 py-2 font-mono">{{ $sch->classSlot->timeRangeLabel() }}</td>
                    <td class="px-3 py-2">{{ $s->studentAttendance?->status ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $s->lessonSummary ? 'yes' : '—' }}</td>
                    <td class="px-3 py-2">
                        @if ($s->lessonSummary)
                            <a href="{{ route('admin.lesson-summaries.show', $s->lessonSummary) }}" class="underline">View summary</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sessions->links() }}</div>
@endsection
