@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ $invoice->invoice_number }}</h2>
        <p class="text-sm text-gray-600">{{ $invoice->periodLabel() }} · {{ $invoice->currency }}</p>
    </div>

    <dl class="mb-6 max-w-md space-y-2 rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm">
        <div class="flex justify-between"><dt class="text-gray-500">Tuition</dt><dd>{{ number_format($invoice->tuition_amount, 2) }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Tax</dt><dd>{{ number_format($invoice->tax_amount, 2) }}</dd></div>
        <div class="flex justify-between font-semibold"><dt>Total</dt><dd>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Paid</dt><dd>{{ number_format($invoice->amount_paid, 2) }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Balance</dt><dd>{{ $invoice->balanceFormatted() }} {{ $invoice->currency }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Due</dt><dd>{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd>{{ $invoice->status }}</dd></div>
    </dl>

    <section class="rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm">
        <h3 class="font-semibold text-gray-800">Payments recorded</h3>
        <ul class="mt-2 space-y-2">
            @forelse ($invoice->payments as $p)
                <li>{{ $p->paid_on->format('Y-m-d') }} — {{ number_format($p->amount, 2) }} {{ $p->currency }} ({{ $p->method }})</li>
            @empty
                <li class="text-gray-500">No payments yet.</li>
            @endforelse
        </ul>
    </section>

    <p class="mt-6"><a href="{{ route('student.billing.index') }}" class="text-sm underline">Back</a></p>
@endsection
