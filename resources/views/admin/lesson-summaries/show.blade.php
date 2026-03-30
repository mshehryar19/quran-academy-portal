@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">Lesson summary</h2>
            <p class="text-sm text-gray-600">
                Session {{ $lessonSummary->classSession->session_date->format('Y-m-d') }} ·
                {{ $lessonSummary->classSession->classSchedule->classSlot->timeRangeLabel() }} ·
                {{ $lessonSummary->student->full_name }}
            </p>
        </div>
        <a href="{{ route('admin.reports.class-sessions') }}" class="text-sm text-gray-600 underline">Back to sessions</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Current record</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-gray-500">Topic</dt><dd>{{ $lessonSummary->lesson_topic ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Surah / lesson</dt><dd>{{ $lessonSummary->surah_or_lesson ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Memorization</dt><dd class="whitespace-pre-wrap">{{ $lessonSummary->memorization_progress ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Performance</dt><dd class="whitespace-pre-wrap">{{ $lessonSummary->performance_notes ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Homework (summary)</dt><dd class="whitespace-pre-wrap">{{ $lessonSummary->homework_assigned ?? '—' }}</dd></div>
            </dl>
        </section>

        @if(auth()->user()->hasRole('Admin'))
            <section class="rounded-lg border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <h3 class="text-sm font-semibold uppercase tracking-wide text-amber-900">Admin override</h3>
                <p class="mt-1 text-xs text-amber-800">Corrections are logged; teachers cannot edit locked summaries.</p>
                <form method="post" action="{{ route('admin.lesson-summaries.update', $lessonSummary) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="lesson_topic">Lesson topic</label>
                        <input id="lesson_topic" name="lesson_topic" type="text" value="{{ old('lesson_topic', $lessonSummary->lesson_topic) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="surah_or_lesson">Surah or lesson</label>
                        <input id="surah_or_lesson" name="surah_or_lesson" type="text" value="{{ old('surah_or_lesson', $lessonSummary->surah_or_lesson) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="memorization_progress">Memorization progress</label>
                        <textarea id="memorization_progress" name="memorization_progress" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('memorization_progress', $lessonSummary->memorization_progress) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="performance_notes">Performance notes</label>
                        <textarea id="performance_notes" name="performance_notes" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('performance_notes', $lessonSummary->performance_notes) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="homework_assigned">Homework assigned</label>
                        <textarea id="homework_assigned" name="homework_assigned" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('homework_assigned', $lessonSummary->homework_assigned) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700" for="reason">Reason (optional)</label>
                        <textarea id="reason" name="reason" rows="2" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                    </div>
                    <button type="submit" class="rounded-md bg-amber-900 px-4 py-2 text-sm text-white hover:bg-amber-950">Save correction</button>
                </form>
            </section>
        @endif
    </div>

    @if ($lessonSummary->overrides->isNotEmpty())
        <section class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Override history</h3>
            <ul class="mt-3 space-y-4 text-sm">
                @foreach ($lessonSummary->overrides as $ov)
                    <li class="border-b border-gray-100 pb-3">
                        <p class="text-xs text-gray-500">{{ $ov->created_at->format('Y-m-d H:i') }} — {{ $ov->adminUser?->name }}</p>
                        @if ($ov->reason)
                            <p class="text-xs text-gray-600">Reason: {{ $ov->reason }}</p>
                        @endif
                        <pre class="mt-2 overflow-x-auto rounded bg-gray-50 p-2 text-xs">{{ json_encode($ov->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
@endsection
