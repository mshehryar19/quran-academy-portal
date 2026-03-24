@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Edit schedule #{{ $classSchedule->id }}</h2>
        <p class="text-sm text-gray-600">Changes are validated for conflicts when status is active.</p>
    </div>

    <form method="post" action="{{ route('admin.class-schedules.update', $classSchedule) }}" class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('put')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700" for="teacher_id">Teacher</label>
                <select id="teacher_id" name="teacher_id" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" @selected(old('teacher_id', $classSchedule->teacher_id) == $t->id)>{{ $t->public_id }} — {{ $t->full_name }}</option>
                    @endforeach
                </select>
                @error('teacher_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700" for="student_id">Student</label>
                <select id="student_id" name="student_id" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}" @selected(old('student_id', $classSchedule->student_id) == $s->id)>{{ $s->public_id }} — {{ $s->full_name }}</option>
                    @endforeach
                </select>
                @error('student_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700" for="class_slot_id">Class slot</label>
                <select id="class_slot_id" name="class_slot_id" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    @foreach ($slots as $sl)
                        <option value="{{ $sl->id }}" @selected(old('class_slot_id', $classSchedule->class_slot_id) == $sl->id)>{{ $sl->name ? $sl->name.' — ' : '' }}{{ $sl->timeRangeLabel() }} ({{ $sl->status }})</option>
                    @endforeach
                </select>
                @error('class_slot_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="day_of_week">Weekday</label>
                <select id="day_of_week" name="day_of_week" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    @include('admin.partials.day-of-week-options', ['selected' => old('day_of_week', $classSchedule->day_of_week)])
                </select>
                @error('day_of_week')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
                <select id="status" name="status" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    <option value="active" @selected(old('status', $classSchedule->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $classSchedule->status) === 'inactive')>Inactive</option>
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="start_date">Effective start</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $classSchedule->start_date->toDateString()) }}" required
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="end_date">Effective end (optional)</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $classSchedule->end_date?->toDateString()) }}"
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700" for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">{{ old('notes', $classSchedule->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Save</button>
            <a href="{{ route('admin.class-schedules.show', $classSchedule) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </form>
@endsection
