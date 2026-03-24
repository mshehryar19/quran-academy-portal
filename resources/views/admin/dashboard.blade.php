@extends('layouts.app')

@section('content')
    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Internal home</h2>
        <p class="mt-1 text-sm text-gray-600">
            Signed in as <strong>{{ $user->name }}</strong> ({{ $primaryRole }}).
        </p>
        <p class="mt-3 text-sm text-gray-600">
            Use the sidebar for master data, class slots, and recurring schedules. Attendance and lesson workflows are in later phases.
        </p>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        @can('viewAny', \App\Models\Teacher::class)
            <a href="{{ route('admin.teachers.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">Teachers</p>
                <p class="mt-1 text-sm font-medium text-gray-900">Open teacher list</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\Student::class)
            <a href="{{ route('admin.students.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">Students</p>
                <p class="mt-1 text-sm font-medium text-gray-900">Open student list</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\AcademyParent::class)
            <a href="{{ route('admin.parents.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">Parents</p>
                <p class="mt-1 text-sm font-medium text-gray-900">Open parent list</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\ClassSlot::class)
            <a href="{{ route('admin.class-slots.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">Class slots</p>
                <p class="mt-1 text-sm font-medium text-gray-900">30-minute time windows</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\ClassSchedule::class)
            <a href="{{ route('admin.class-schedules.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">Schedules</p>
                <p class="mt-1 text-sm font-medium text-gray-900">Weekly assignments</p>
            </a>
        @endcan
    </section>

    @if (auth()->user()?->hasRole('Admin'))
        <section class="mt-6 rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-700">
            <strong>Admin:</strong> create HR and Supervisor accounts from <a href="{{ route('admin.staff.index') }}" class="font-medium text-gray-900 underline">HR / Supervisor users</a>.
        </section>
    @endif
@endsection
