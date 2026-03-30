@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Student fee profiles</h2>
            <p class="text-sm text-gray-600">Monthly flat tuition — default <strong>GBP</strong>, optional <strong>USD</strong>. Separate from employee PKR payroll.</p>
        </div>
        <a href="{{ route('admin.billing.student-fee-profiles.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Add fee profile</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Student</th>
                <th class="px-3 py-2 text-right">Monthly amount</th>
                <th class="px-3 py-2">CCY</th>
                <th class="px-3 py-2">Effective</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($profiles as $p)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $p->student->full_name }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($p->monthly_fee_amount, 2) }}</td>
                    <td class="px-3 py-2 font-mono">{{ $p->currency }}</td>
                    <td class="px-3 py-2 text-xs">{{ $p->effective_from->format('Y-m-d') }} @if($p->effective_to) — {{ $p->effective_to->format('Y-m-d') }} @endif</td>
                    <td class="px-3 py-2">{{ $p->status }}</td>
                    <td class="px-3 py-2 space-x-2 whitespace-nowrap">
                        <a href="{{ route('admin.billing.student-fee-profiles.show', $p) }}" class="underline">View</a>
                        <a href="{{ route('admin.billing.student-fee-profiles.edit', $p) }}" class="underline">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $profiles->links() }}</div>
@endsection
