@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">{{ $teacher->full_name }}</h2>
            <p class="text-sm text-gray-600">Teacher ID <span class="font-mono">{{ $teacher->public_id }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.teachers.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Back to list</a>
            @can('update', $teacher)
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-800">Edit</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Profile</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Email</dt><dd>{{ $teacher->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Phone</dt><dd>{{ $teacher->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Gender</dt><dd>{{ $teacher->gender ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Appointment</dt><dd>{{ $teacher->date_of_appointment?->format('Y-m-d') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Country</dt><dd>{{ $teacher->country ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Timezone</dt><dd>{{ $teacher->timezone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            {{ $teacher->status }}
                        </span>
                    </dd>
                </div>
            </dl>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Account</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User ID</dt><dd>{{ $teacher->user_id }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Login</dt><dd>{{ $teacher->user?->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User active</dt><dd>{{ $teacher->user?->is_active ? 'Yes' : 'No' }}</dd></div>
            </dl>
            @if ($teacher->address_line || $teacher->notes)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    @if ($teacher->address_line)
                        <p class="text-xs uppercase text-gray-500">Address</p>
                        <p class="text-sm">{{ $teacher->address_line }}</p>
                    @endif
                    @if ($teacher->notes)
                        <p class="mt-3 text-xs uppercase text-gray-500">Notes</p>
                        <p class="text-sm">{{ $teacher->notes }}</p>
                    @endif
                </div>
            @endif
        </section>
    </div>

    @can('viewAny', \App\Models\ClassSchedule::class)
        <section class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Class schedules</h3>
                @can('create', \App\Models\ClassSchedule::class)
                    <a href="{{ route('admin.class-schedules.create') }}" class="text-sm font-medium text-gray-900 underline">Add schedule</a>
                @endcan
            </div>
            @if ($teacher->classSchedules->isEmpty())
                <p class="mt-2 text-sm text-gray-600">No schedules linked.</p>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="py-2 pe-4">Student</th>
                            <th class="py-2 pe-4">Slot</th>
                            <th class="py-2 pe-4">Day</th>
                            <th class="py-2 pe-4">Effective</th>
                            <th class="py-2">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($teacher->classSchedules as $sch)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pe-4">
                                    <a href="{{ route('admin.students.show', $sch->student) }}" class="underline">{{ $sch->student->full_name }}</a>
                                </td>
                                <td class="py-2 pe-4 font-mono">{{ $sch->classSlot->timeRangeLabel() }}</td>
                                <td class="py-2 pe-4">{{ \App\Models\ClassSchedule::dayName($sch->day_of_week) }}</td>
                                <td class="py-2 pe-4">{{ $sch->start_date->format('Y-m-d') }} → {{ $sch->end_date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="py-2">
                                    <a href="{{ route('admin.class-schedules.show', $sch) }}" class="text-gray-900 underline">{{ $sch->status }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endcan

    @can('delete', $teacher)
        @if ($teacher->status === 'active')
            <form method="post" action="{{ route('admin.teachers.destroy', $teacher) }}" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4"
                  onsubmit="return confirm('Deactivate this teacher? They will not be able to sign in.');">
                @csrf
                @method('delete')
                <p class="text-sm text-red-800">Deactivate sets teacher and user status to inactive.</p>
                <button type="submit" class="mt-2 rounded-md bg-red-700 px-3 py-1.5 text-sm text-white hover:bg-red-800">
                    Deactivate teacher
                </button>
            </form>
        @endif
    @endcan
@endsection
