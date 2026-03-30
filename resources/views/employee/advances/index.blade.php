@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Advance salary (PKR)</h2>
            <p class="text-sm text-gray-600">Admin approves; deduction applies in the scheduled payroll month.</p>
        </div>
        <a href="{{ route('employee.advances.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">New request</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2 text-right">Amount (PKR)</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Deduct in</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($requests as $req)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $req->created_at->format('Y-m-d') }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($req->amount_pkr, 2) }}</td>
                    <td class="px-3 py-2">{{ $req->status }}</td>
                    <td class="px-3 py-2 font-mono text-xs">
                        @if($req->deduction_period_year)
                            {{ sprintf('%04d-%02d', $req->deduction_period_year, $req->deduction_period_month) }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $requests->links() }}</div>
@endsection
