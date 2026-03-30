@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Leave — supervisor review</h2>
        <div class="mt-2 flex gap-2 text-sm">
            <a href="{{ route('supervisor.leaves.index', ['tab' => 'pending']) }}" class="{{ $tab === 'pending' ? 'font-semibold underline' : 'text-gray-600' }}">Pending</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('supervisor.leaves.index', ['tab' => 'history']) }}" class="{{ $tab === 'history' ? 'font-semibold underline' : 'text-gray-600' }}">History</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Employee</th>
                <th class="px-3 py-2">Dates</th>
                <th class="px-3 py-2">Days</th>
                <th class="px-3 py-2">Paid</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($leaves as $leave)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $leave->user->name }}</td>
                    <td class="px-3 py-2">{{ $leave->start_date->format('M j') }} – {{ $leave->end_date->format('M j') }}</td>
                    <td class="px-3 py-2">{{ $leave->total_days }}</td>
                    <td class="px-3 py-2">{{ $leave->is_paid ? 'Y' : 'N' }}</td>
                    <td class="px-3 py-2"><a href="{{ route('supervisor.leaves.show', $leave) }}" class="underline">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leaves->links() }}</div>
@endsection
