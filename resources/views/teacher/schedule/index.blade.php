@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h2 class="text-xl font-semibold">My classes</h2>
            <p class="text-sm text-gray-600">Schedules for the selected day.</p>
        </div>
        <form method="get" class="flex flex-wrap items-end gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-500" for="date">Date</label>
                <input id="date" name="date" type="date" value="{{ $date->toDateString() }}"
                       class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
            </div>
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800">Go</button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-4 py-2">Time</th>
                <th class="px-4 py-2">Student</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @forelse ($sessions as $session)
                @php($sch = $session->classSchedule)
                <tr class="border-b border-gray-100">
                    <td class="px-4 py-3 font-mono">{{ $sch->classSlot->timeRangeLabel() }}</td>
                    <td class="px-4 py-3">{{ $sch->student->full_name }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $session->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $session->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('teacher.sessions.show', $session) }}" class="font-medium text-gray-900 underline">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No classes scheduled for this day.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
