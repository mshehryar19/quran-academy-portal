@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">My tuition &amp; invoices</h2>
        <p class="text-sm text-gray-600">International fees (GBP/USD). View only.</p>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Invoice</th>
                <th class="px-3 py-2">Period</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-right">Balance</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($invoices as $inv)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2 font-mono text-xs">{{ $inv->invoice_number }}</td>
                    <td class="px-3 py-2 font-mono">{{ $inv->periodLabel() }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($inv->total_amount, 2) }} {{ $inv->currency }}</td>
                    <td class="px-3 py-2 text-right">{{ $inv->balanceFormatted() }} {{ $inv->currency }}</td>
                    <td class="px-3 py-2">{{ $inv->status }}</td>
                    <td class="px-3 py-2"><a href="{{ route('student.billing.invoices.show', $inv) }}" class="underline">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection
