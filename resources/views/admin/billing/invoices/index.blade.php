@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Invoices / vouchers</h2>
            <p class="text-sm text-gray-600">International tuition billing (GBP/USD).</p>
        </div>
        <a href="{{ route('admin.billing.invoices.generate') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Generate month</a>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 text-sm shadow-sm">
        <select name="student_id" class="rounded-md border border-gray-300 px-3 py-2">
            <option value="">All students</option>
            @foreach ($students as $s)
                <option value="{{ $s->id }}" @selected((string) request('student_id') === (string) $s->id)>{{ $s->full_name }}</option>
            @endforeach
        </select>
        <input type="number" name="billing_year" value="{{ request('billing_year') }}" placeholder="Year" class="w-24 rounded-md border border-gray-300 px-3 py-2">
        <input type="number" name="billing_month" value="{{ request('billing_month') }}" placeholder="Month" min="1" max="12" class="w-24 rounded-md border border-gray-300 px-3 py-2">
        <select name="status" class="rounded-md border border-gray-300 px-3 py-2">
            <option value="">Any status</option>
            @foreach (['unpaid', 'partially_paid', 'paid', 'overdue', 'cancelled'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-white">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Number</th>
                <th class="px-3 py-2">Student</th>
                <th class="px-3 py-2">Period</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-right">Paid</th>
                <th class="px-3 py-2 text-right">Balance</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoices as $inv)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2 font-mono text-xs">{{ $inv->invoice_number }}</td>
                    <td class="px-3 py-2">{{ $inv->student->full_name }}</td>
                    <td class="px-3 py-2 font-mono">{{ $inv->periodLabel() }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($inv->total_amount, 2) }} {{ $inv->currency }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($inv->amount_paid, 2) }}</td>
                    <td class="px-3 py-2 text-right">{{ $inv->balanceFormatted() }}</td>
                    <td class="px-3 py-2">{{ $inv->status }}</td>
                    <td class="px-3 py-2"><a href="{{ route('admin.billing.invoices.show', $inv) }}" class="underline">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection
