@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">My leave</h2>
            <p class="text-sm text-gray-600">
                Paid balance (this cycle): <strong>{{ $balance }}</strong> / {{ \App\Services\LeaveBalanceService::ANNUAL_PAID_ENTITLEMENT_DAYS }} days
                · Cycle {{ $cycleStart->format('Y-m-d') }} — {{ $cycleEnd->format('Y-m-d') }}
            </p>
        </div>
        <a href="{{ route('employee.leaves.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">New request</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Dates</th>
                <th class="px-3 py-2">Type</th>
                <th class="px-3 py-2">Paid</th>
                <th class="px-3 py-2">Days</th>
                <th class="px-3 py-2">Final</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($leaves as $leave)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">
                        <a href="{{ route('employee.leaves.show', $leave) }}" class="font-medium text-gray-900 underline">
                            {{ $leave->start_date->format('M j') }} – {{ $leave->end_date->format('M j, Y') }}
                        </a>
                    </td>
                    <td class="px-3 py-2">{{ $leave->leave_type }}</td>
                    <td class="px-3 py-2">{{ $leave->is_paid ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2">{{ $leave->total_days }}</td>
                    <td class="px-3 py-2">
                        @if($leave->isFullyResolved())
                            <span class="rounded px-2 py-0.5 text-xs {{ $leave->finalStatus() === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $leave->finalStatus() }}</span>
                        @elseif($leave->awaitingSupervisor())
                            <span class="text-amber-700">Pending supervisor</span>
                        @else
                            <span class="text-amber-700">Pending admin</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leaves->links() }}</div>
@endsection
