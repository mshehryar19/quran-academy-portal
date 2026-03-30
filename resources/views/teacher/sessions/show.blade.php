@php
    $sch = $classSession->classSchedule;
@endphp
@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">Class session</h2>
            <p class="text-sm text-gray-600">
                {{ $classSession->session_date->format('Y-m-d') }} ·
                <span class="font-mono">{{ $sch->classSlot->timeRangeLabel() }}</span> ·
                {{ $sch->student->full_name }}
            </p>
        </div>
        <a href="{{ route('teacher.schedule.index', ['date' => $classSession->session_date->toDateString()]) }}" class="text-sm text-gray-600 underline">Back to schedule</a>
    </div>

    <div class="space-y-6">
        {{-- Student attendance --}}
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Student attendance</h3>
            <form method="post" action="{{ route('teacher.sessions.student-attendance', $classSession) }}" class="mt-4 space-y-4">
                @csrf
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="status" value="present" @checked(old('status', $classSession->studentAttendance?->status) === 'present') required>
                        <span>Present</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="status" value="absent" @checked(old('status', $classSession->studentAttendance?->status) === 'absent')>
                        <span>Absent</span>
                    </label>
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="teacher_available_for_reassignment" value="1" @checked(old('teacher_available_for_reassignment', $classSession->studentAttendance?->teacher_available_for_reassignment))>
                    Teacher available for reassignment (only applies when student is absent)
                </label>
                @error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800">Save attendance</button>
            </form>
        </section>

        {{-- Lesson summary --}}
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Lesson summary</h3>
            @if ($classSession->lessonSummary)
                <div class="mt-3 space-y-2 text-sm">
                    <p><span class="text-gray-500">Topic:</span> {{ $classSession->lessonSummary->lesson_topic ?? '—' }}</p>
                    <p><span class="text-gray-500">Surah / lesson:</span> {{ $classSession->lessonSummary->surah_or_lesson ?? '—' }}</p>
                    <p><span class="text-gray-500">Memorization:</span> {{ $classSession->lessonSummary->memorization_progress ?? '—' }}</p>
                    <p><span class="text-gray-500">Performance:</span> {{ $classSession->lessonSummary->performance_notes ?? '—' }}</p>
                    <p><span class="text-gray-500">Homework (in summary):</span> {{ $classSession->lessonSummary->homework_assigned ?? '—' }}</p>
                    <p class="text-xs text-gray-500">Submitted {{ $classSession->lessonSummary->submitted_at->format('Y-m-d H:i') }} — locked (Admin can correct in admin area)</p>
                </div>
            @else
                <form method="post" action="{{ route('teacher.sessions.lesson-summary', $classSession) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-500" for="lesson_topic">Lesson topic</label>
                        <input id="lesson_topic" name="lesson_topic" type="text" value="{{ old('lesson_topic') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500" for="surah_or_lesson">Surah or lesson practiced</label>
                        <input id="surah_or_lesson" name="surah_or_lesson" type="text" value="{{ old('surah_or_lesson') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500" for="memorization_progress">Memorization progress</label>
                        <textarea id="memorization_progress" name="memorization_progress" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('memorization_progress') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500" for="performance_notes">Performance notes</label>
                        <textarea id="performance_notes" name="performance_notes" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('performance_notes') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500" for="homework_assigned">Homework assigned (summary)</label>
                        <textarea id="homework_assigned" name="homework_assigned" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('homework_assigned') }}</textarea>
                    </div>
                    @error('lesson')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800">Submit summary (locks)</button>
                </form>
            @endif
        </section>

        {{-- Homework tasks --}}
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Homework / tasks</h3>
            @if ($classSession->homeworkTasks->isNotEmpty())
                <ul class="mt-3 max-h-64 space-y-3 overflow-y-auto text-sm">
                    @foreach ($classSession->homeworkTasks as $task)
                        <li class="rounded border border-gray-100 p-3">
                            <p class="font-medium">{{ $task->title }}</p>
                            <p class="text-xs text-gray-500">{{ $task->assigned_date->toDateString() }} @if($task->due_date) — due {{ $task->due_date->toDateString() }} @endif</p>
                            <p class="text-gray-700">{{ $task->description }}</p>
                            <p class="mt-1 text-xs">Status: <strong>{{ $task->status }}</strong></p>
                            <form method="post" action="{{ route('teacher.homework.update', $task) }}" class="mt-2 flex flex-wrap gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border border-gray-300 px-2 py-1 text-xs">
                                    <option value="pending" @selected($task->status === 'pending')>Pending</option>
                                    <option value="completed" @selected($task->status === 'completed')>Completed</option>
                                    <option value="not_completed" @selected($task->status === 'not_completed')>Not completed</option>
                                </select>
                                <button type="submit" class="rounded bg-gray-800 px-2 py-1 text-xs text-white">Update</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-2 text-sm text-gray-600">No tasks yet.</p>
            @endif

            <form method="post" action="{{ route('teacher.sessions.homework', $classSession) }}" class="mt-4 space-y-2 border-t border-gray-100 pt-4">
                @csrf
                <p class="text-xs font-medium text-gray-500">Add task</p>
                <input id="hw_title" name="title" type="text" placeholder="Title" value="{{ old('title') }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <textarea name="description" rows="2" placeholder="Instructions" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
                <div class="flex gap-2">
                    <input name="assigned_date" type="date" value="{{ old('assigned_date', $classSession->session_date->toDateString()) }}" required class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                    <input name="due_date" type="date" value="{{ old('due_date') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800">Add task</button>
            </form>
        </section>

        {{-- Progress notes --}}
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Progress notes</h3>
            @if ($classSession->progressNotes->isNotEmpty())
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($classSession->progressNotes as $note)
                        <li class="border-b border-gray-100 pb-2">
                            <p>{{ $note->body }}</p>
                            <p class="text-xs text-gray-500">{{ $note->recorded_at->format('Y-m-d H:i') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
            <form method="post" action="{{ route('teacher.sessions.progress-notes', $classSession) }}" class="mt-4 space-y-2">
                @csrf
                <textarea name="body" rows="3" placeholder="Short observation for future reporting" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm" required>{{ old('body') }}</textarea>
                <button type="submit" class="rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50">Add note</button>
            </form>
        </section>
    </div>
@endsection
