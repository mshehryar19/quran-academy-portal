@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold">{{ $invoice->invoice_number }}</h2>
            <p class="text-sm text-gray-600">{{ $invoice->student->full_name }} · {{ $invoice->periodLabel() }} · {{ $invoice->currency }}</p>
        </div>
        <a href="{{ route('admin.billing.invoices.index') }}" class="text-sm underline">Back to list</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 text-sm shadow-sm space-y-2">
            <div class="flex justify-between"><span class="text-gray-500">Tuition</span><span>{{ number_format($invoice->tuition_amount, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tax</span><span>{{ number_format($invoice->tax_amount, 2) }}</span></div>
            <div class="flex justify-between font-semibold"><span>Total</span><span>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Paid</span><span>{{ number_format($invoice->amount_paid, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Balance</span><span>{{ $invoice->balanceFormatted() }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Due</span><span>{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="uppercase">{{ $invoice->status }}</span></div>
            @if($invoice->tax_detail)
                <pre class="mt-2 max-h-32 overflow-auto rounded bg-gray-50 p-2 text-xs">{{ json_encode($invoice->tax_detail, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @endif
            @if($invoice->status === \App\Models\Invoice::STATUS_CANCELLED)
                <p class="text-red-700">Voided: {{ $invoice->void_reason }}</p>
            @endif
        </section>

        @if($invoice->status !== \App\Models\Invoice::STATUS_CANCELLED)
            @can('payment.manage')
                <section class="rounded-lg border border-blue-200 bg-blue-50 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-blue-900">Record payment</h3>
                    <form method="post" action="{{ route('admin.billing.invoices.payments.store', $invoice) }}" class="mt-3 space-y-3 text-sm">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-600" for="amount">Amount</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0.01" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600" for="currency">Currency (must match invoice)</label>
                            <select id="currency" name="currency" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                                <option value="{{ $invoice->currency }}" selected>{{ $invoice->currency }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600" for="paid_on">Paid on</label>
                            <input id="paid_on" name="paid_on" type="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600" for="method">Method</label>
                            <select id="method" name="method" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                                @foreach (\App\Models\InvoicePayment::methods() as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600" for="reference">Reference</label>
                            <input id="reference" name="reference" type="text" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600" for="gateway_transaction_id">Gateway / external ID (optional)</label>
                            <input id="gateway_transaction_id" name="gateway_transaction_id" type="text" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">
                        </div>
                        <button type="submit" class="rounded-md bg-blue-900 px-4 py-2 text-white">Save payment</button>
                    </form>
                    @error('amount')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('currency')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </section>
            @endcan

            @can('invoice.manage')
                <section class="rounded-lg border border-amber-200 bg-amber-50 p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-amber-900">Tax / total (manual)</h3>
                    <p class="text-xs text-amber-800">Total = tuition + tax. Tax automation can be refined later; use JSON detail for breakdown when needed.</p>
                    <form method="post" action="{{ route('admin.billing.invoices.tax', $invoice) }}" class="mt-3 space-y-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', $invoice->tax_amount) }}" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        <button type="submit" class="rounded-md bg-amber-900 px-4 py-2 text-sm text-white">Recalculate total</button>
                    </form>
                </section>

                @if((float) $invoice->amount_paid <= 0)
                    <section class="rounded-lg border border-red-200 bg-red-50 p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-red-900">Void invoice</h3>
                        <form method="post" action="{{ route('admin.billing.invoices.void', $invoice) }}" class="mt-3 space-y-2" onsubmit="return confirm('Void this invoice?');">
                            @csrf
                            <textarea name="void_reason" required placeholder="Reason" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('void_reason') }}</textarea>
                            <button type="submit" class="rounded-md bg-red-800 px-4 py-2 text-sm text-white">Void</button>
                        </form>
                    </section>
                @endif
            @endcan
        @endif
    </div>

    <section class="mt-8 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700">Payments</h3>
        <table class="mt-3 min-w-full text-sm">
            <thead>
            <tr class="border-b text-left text-xs uppercase text-gray-500">
                <th class="py-2">Date</th>
                <th class="py-2 text-right">Amount</th>
                <th class="py-2">Method</th>
                <th class="py-2">Status</th>
                <th class="py-2">By</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($invoice->payments as $pay)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $pay->paid_on->format('Y-m-d') }}</td>
                    <td class="py-2 text-right">{{ number_format($pay->amount, 2) }} {{ $pay->currency }}</td>
                    <td class="py-2">{{ $pay->method }}</td>
                    <td class="py-2">{{ $pay->payment_status }}</td>
                    <td class="py-2 text-xs">{{ $pay->recordedBy?->name }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-gray-500">No payments yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
