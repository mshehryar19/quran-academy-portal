@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">{{ $student->full_name }}</h2>
            <p class="text-sm text-gray-600">Student ID <span class="font-mono">{{ $student->public_id }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.students.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Back to list</a>
            @can('update', $student)
                <a href="{{ route('admin.students.edit', $student) }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-800">Edit</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Profile</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Email</dt><dd>{{ $student->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Phone</dt><dd>{{ $student->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Gender</dt><dd>{{ $student->gender ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Country</dt><dd>{{ $student->country ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Timezone</dt><dd>{{ $student->timezone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            {{ $student->status }}
                        </span>
                    </dd>
                </div>
            </dl>
            @if ($student->notes)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <p class="text-xs uppercase text-gray-500">Notes</p>
                    <p class="text-sm">{{ $student->notes }}</p>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Account</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User ID</dt><dd>{{ $student->user_id }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Login</dt><dd>{{ $student->user?->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User active</dt><dd>{{ $student->user?->is_active ? 'Yes' : 'No' }}</dd></div>
            </dl>

            <h4 class="mt-6 text-xs font-semibold uppercase tracking-wide text-gray-500">Linked parents</h4>
            @if ($student->parents->isEmpty())
                <p class="mt-2 text-sm text-gray-600">No parents linked yet.</p>
            @else
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                    @foreach ($student->parents as $p)
                        <li>
                            <a href="{{ route('admin.parents.show', $p) }}" class="text-gray-900 underline">{{ $p->full_name }}</a>
                            <span class="text-gray-500">({{ $p->email }})</span>
                        </li>
                    @endforeach
                </ul>
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
            @if ($student->classSchedules->isEmpty())
                <p class="mt-2 text-sm text-gray-600">No schedules linked.</p>
            @else
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                            <th class="py-2 pe-4">Teacher</th>
                            <th class="py-2 pe-4">Slot</th>
                            <th class="py-2 pe-4">Day</th>
                            <th class="py-2 pe-4">Effective</th>
                            <th class="py-2">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($student->classSchedules as $sch)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 pe-4">
                                    <a href="{{ route('admin.teachers.show', $sch->teacher) }}" class="underline">{{ $sch->teacher->full_name }}</a>
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

    @can('delete', $student)
        @if ($student->status === 'active')
            <form method="post" action="{{ route('admin.students.destroy', $student) }}" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4"
                  onsubmit="return confirm('Deactivate this student? They will not be able to sign in.');">
                @csrf
                @method('delete')
                <p class="text-sm text-red-800">Deactivate sets student and user status to inactive.</p>
                <button type="submit" class="mt-2 rounded-md bg-red-700 px-3 py-1.5 text-sm text-white hover:bg-red-800">
                    Deactivate student
                </button>
            </form>
        @endif
    @endcan
@endsection
