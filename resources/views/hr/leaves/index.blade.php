@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">HR leave monitoring</h2>
        <p class="text-sm text-gray-600">Read-only oversight — no final approvals.</p>
    </div>

    <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-4">
        @foreach ($stats as $label => $count)
            <div class="rounded-lg border border-gray-200 bg-white p-4 text-center shadow-sm">
                <p class="text-2xl font-semibold">{{ $count }}</p>
                <p class="text-xs uppercase text-gray-500">{{ str_replace('_', ' ', $label) }}</p>
            </div>
        @endforeach
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm text-sm">
        <div>
            <label class="block text-xs text-gray-500" for="user_id">Employee</label>
            <select id="user_id" name="user_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2">
                <option value="">All</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="final">Status</label>
            <select id="final" name="final" class="mt-1 rounded-md border border-gray-300 px-3 py-2">
                <option value="">All</option>
                <option value="open" @selected(request('final') === 'open')>Awaiting final</option>
                <option value="approved" @selected(request('final') === 'approved')>Approved</option>
                <option value="rejected" @selected(request('final') === 'rejected')>Rejected</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="is_paid">Paid leave</label>
            <select id="is_paid" name="is_paid" class="mt-1 rounded-md border border-gray-300 px-3 py-2">
                <option value="">Any</option>
                <option value="1" @selected(request('is_paid') === '1')>Paid</option>
                <option value="0" @selected(request('is_paid') === '0')>Unpaid</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-white">Filter</button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Employee</th>
                <th class="px-3 py-2">Dates</th>
                <th class="px-3 py-2">Paid</th>
                <th class="px-3 py-2">Supervisor</th>
                <th class="px-3 py-2">Admin</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($leaves as $leave)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $leave->user->name }}</td>
                    <td class="px-3 py-2">{{ $leave->start_date->format('M j') }} – {{ $leave->end_date->format('M j') }}</td>
                    <td class="px-3 py-2">{{ $leave->is_paid ? 'Y' : 'N' }}</td>
                    <td class="px-3 py-2">{{ $leave->supervisor_decision ?? '—' }}</td>
                    <td class="px-3 py-2">{{ $leave->admin_decision ?? '—' }}</td>
                    <td class="px-3 py-2"><a href="{{ route('hr.leaves.show', $leave) }}" class="underline">View</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leaves->links() }}</div>
@endsection
