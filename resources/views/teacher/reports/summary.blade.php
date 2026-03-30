@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('My teaching summary') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Limited to your own attendance events, lesson summaries, and homework stats.') }}</p>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500" for="from">{{ __('From') }}</label>
            <input id="from" name="from" type="date" value="{{ $from }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="to">{{ __('To') }}</label>
            <input id="to" name="to" type="date" value="{{ $to }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Apply') }}</button>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500">{{ __('Homework tasks') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $homeworkStats['assigned'] }}</p>
            <p class="text-xs text-gray-600">{{ __('Completed') }}: {{ $homeworkStats['completed'] }} &middot; {{ __('Pending') }}: {{ $homeworkStats['pending'] }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500">{{ __('Attendance events (sample)') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $attendanceEvents->count() }}</p>
            <p class="text-xs text-gray-600">{{ __('Rows shown below (max 100).') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-gray-500">{{ __('Lesson summaries') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $lessonSummaries->count() }}</p>
        </div>
    </div>

    <section class="mt-8">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Recent lesson summaries') }}</h3>
        <ul class="mt-2 space-y-2 text-sm">
            @forelse ($lessonSummaries as $ls)
                <li class="rounded border border-gray-100 bg-white p-3">
                    <span class="text-gray-600">{{ $ls->submitted_at?->format('Y-m-d') }}</span>
                    — {{ \Illuminate\Support\Str::limit($ls->lesson_topic ?? $ls->surah_or_lesson ?? '—', 80) }}
                </li>
            @empty
                <li class="text-gray-600">{{ __('No summaries in range.') }}</li>
            @endforelse
        </ul>
    </section>

    <section class="mt-8">
        <h3 class="text-sm font-semibold text-gray-900">{{ __('Attendance events') }}</h3>
        <div class="mt-2 overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs uppercase text-gray-500">
                        <th class="px-3 py-2">{{ __('When') }}</th>
                        <th class="px-3 py-2">{{ __('Type') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendanceEvents as $ev)
                        <tr class="border-b border-gray-100">
                            <td class="px-3 py-2 whitespace-nowrap">{{ $ev->occurred_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2">{{ $ev->event_type }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
