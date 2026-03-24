@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Class schedules</h2>
            <p class="text-sm text-gray-600">Recurring weekly assignments (one teacher, one student, one slot).</p>
        </div>
        @can('create', \App\Models\ClassSchedule::class)
            <a href="{{ route('admin.class-schedules.create') }}"
               class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Add schedule
            </a>
        @endcan
    </div>

    <form method="get" class="mb-4 grid grid-cols-1 gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm md:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="block text-xs font-medium text-gray-500" for="q">Search teacher/student</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="teacher_id">Teacher</label>
            <select id="teacher_id" name="teacher_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected((string) request('teacher_id') === (string) $t->id)>{{ $t->public_id }} — {{ $t->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="student_id">Student</label>
            <select id="student_id" name="student_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                @foreach ($students as $s)
                    <option value="{{ $s->id }}" @selected((string) request('student_id') === (string) $s->id)>{{ $s->public_id }} — {{ $s->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="class_slot_id">Slot</label>
            <select id="class_slot_id" name="class_slot_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                @foreach ($slots as $sl)
                    <option value="{{ $sl->id }}" @selected((string) request('class_slot_id') === (string) $sl->id)>{{ $sl->name ? $sl->name.' — ' : '' }}{{ $sl->timeRangeLabel() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="day_of_week">Weekday</label>
            <select id="day_of_week" name="day_of_week" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                @foreach ([1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'] as $val => $label)
                    <option value="{{ $val }}" @selected((string) request('day_of_week') === (string) $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="status">Status</label>
            <select id="status" name="status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
            <button type="submit" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">Apply filters</button>
            @if (request()->hasAny(['q', 'teacher_id', 'student_id', 'class_slot_id', 'day_of_week', 'status']))
                <a href="{{ route('admin.class-schedules.index') }}" class="text-sm text-gray-600 hover:underline">Clear</a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table id="schedules-table" class="display w-full text-sm" style="width:100%">
            <thead>
            <tr class="border-b border-gray-200 text-left">
                <th class="px-3 py-2">ID</th>
                <th class="px-3 py-2">Teacher</th>
                <th class="px-3 py-2">Student</th>
                <th class="px-3 py-2">Slot</th>
                <th class="px-3 py-2">Day</th>
                <th class="px-3 py-2">From</th>
                <th class="px-3 py-2">Until</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($schedules as $row)
                <tr>
                    <td class="px-3 py-2">{{ $row->id }}</td>
                    <td class="px-3 py-2">{{ $row->teacher->full_name }}</td>
                    <td class="px-3 py-2">{{ $row->student->full_name }}</td>
                    <td class="px-3 py-2 font-mono">{{ $row->classSlot->timeRangeLabel() }}</td>
                    <td class="px-3 py-2">{{ \App\Models\ClassSchedule::dayName($row->day_of_week) }}</td>
                    <td class="px-3 py-2">{{ $row->start_date->format('Y-m-d') }}</td>
                    <td class="px-3 py-2">{{ $row->end_date?->format('Y-m-d') ?? '—' }}</td>
                    <td class="px-3 py-2">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $row->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $row->status }}</span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <a href="{{ route('admin.class-schedules.show', $row) }}" class="text-gray-900 underline">View</a>
                        @can('update', $row)
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('admin.class-schedules.edit', $row) }}" class="text-gray-900 underline">Edit</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#schedules-table').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [{orderable: false, targets: -1}]
            });
        });
    </script>
@endpush
