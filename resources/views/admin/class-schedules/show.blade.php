@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">Schedule #{{ $classSchedule->id }}</h2>
            <p class="text-sm text-gray-600">Recurring weekly class assignment</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.class-schedules.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Back to list</a>
            @can('update', $classSchedule)
                <a href="{{ route('admin.class-schedules.edit', $classSchedule) }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-800">Edit</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Assignment</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Teacher</dt>
                    <dd><a href="{{ route('admin.teachers.show', $classSchedule->teacher) }}" class="underline">{{ $classSchedule->teacher->full_name }}</a></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Student</dt>
                    <dd><a href="{{ route('admin.students.show', $classSchedule->student) }}" class="underline">{{ $classSchedule->student->full_name }}</a></dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Slot</dt>
                    <dd class="font-mono">{{ $classSchedule->classSlot->timeRangeLabel() }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Weekday</dt>
                    <dd>{{ \App\Models\ClassSchedule::dayName($classSchedule->day_of_week) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $classSchedule->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $classSchedule->status }}</span>
                    </dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Effective</dt>
                    <dd>{{ $classSchedule->start_date->format('Y-m-d') }} → {{ $classSchedule->end_date?->format('Y-m-d') ?? 'open' }}</dd></div>
            </dl>
            @if ($classSchedule->notes)
                <p class="mt-4 text-xs uppercase text-gray-500">Notes</p>
                <p class="text-sm">{{ $classSchedule->notes }}</p>
            @endif
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Change log</h3>
            @if ($classSchedule->changeLogs->isEmpty())
                <p class="mt-2 text-sm text-gray-600">No history entries yet.</p>
            @else
                <ul class="mt-3 max-h-80 space-y-3 overflow-y-auto text-sm">
                    @foreach ($classSchedule->changeLogs as $log)
                        <li class="border-b border-gray-100 pb-2">
                            <p class="font-medium">{{ $log->action }}</p>
                            <p class="text-xs text-gray-500">{{ $log->created_at->format('Y-m-d H:i') }}
                                @if ($log->user) · {{ $log->user->name }} @endif
                            </p>
                            @if ($log->properties)
                                <pre class="mt-1 overflow-x-auto rounded bg-gray-50 p-2 text-xs">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    @can('delete', $classSchedule)
        @if ($classSchedule->status === 'active')
            <form method="post" action="{{ route('admin.class-schedules.destroy', $classSchedule) }}" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4"
                  onsubmit="return confirm('Deactivate this schedule?');">
                @csrf
                @method('delete')
                <p class="text-sm text-red-800">Deactivate stops this recurring assignment (internal reschedule/replacement can use edit).</p>
                <button type="submit" class="mt-2 rounded-md bg-red-700 px-3 py-1.5 text-sm text-white hover:bg-red-800">Deactivate schedule</button>
            </form>
        @endif
    @endcan
@endsection
