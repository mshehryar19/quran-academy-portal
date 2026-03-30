@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Student class attendance') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Per-session attendance markers with optional teacher/student filters.') }}</p>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500" for="teacher_id">{{ __('Teacher') }}</label>
            <select id="teacher_id" name="teacher_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected((string) request('teacher_id') === (string) $t->id)>{{ $t->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="student_id">{{ __('Student') }}</label>
            <select id="student_id" name="student_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                @foreach ($students as $s)
                    <option value="{{ $s->id }}" @selected((string) request('student_id') === (string) $s->id)>{{ $s->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="from">{{ __('From') }}</label>
            <input id="from" name="from" type="date" value="{{ request('from') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="to">{{ __('To') }}</label>
            <input id="to" name="to" type="date" value="{{ request('to') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="status">{{ __('Status') }}</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                <option value="present" @selected(request('status') === 'present')>present</option>
                <option value="absent" @selected(request('status') === 'absent')>absent</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.reports.student-attendance.export.excel', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export Excel') }}</a>
        <a href="{{ route('admin.reports.student-attendance.export.pdf', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export PDF') }}</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Session') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Teacher') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Student') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Marked') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($rows as $row)
                    @php
                        $session = $row->classSession;
                        $sch = $session?->classSchedule;
                    @endphp
                    <tr>
                        <td class="px-3 py-2 whitespace-nowrap">{{ $session?->session_date?->toDateString() }}</td>
                        <td class="px-3 py-2">{{ $sch?->teacher?->full_name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $sch?->student?->full_name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $row->status }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $row->marked_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rows->links() }}
    </div>
@endsection
