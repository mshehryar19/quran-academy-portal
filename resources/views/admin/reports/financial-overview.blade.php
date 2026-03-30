@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Tuition invoices (report)') }}</h2>
        <p class="text-sm text-gray-600">
            {{ __('Outstanding total (non-cancelled)') }}:
            <strong>{{ number_format($outstanding, 2) }}</strong>
        </p>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500" for="student_id">{{ __('Student') }}</label>
            <select id="student_id" name="student_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                @foreach ($students as $s)
                    <option value="{{ $s->id }}" @selected((string) request('student_id') === (string) $s->id)>{{ $s->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="status">{{ __('Status') }}</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                <option value="unpaid" @selected(request('status') === 'unpaid')>unpaid</option>
                <option value="partially_paid" @selected(request('status') === 'partially_paid')>partially_paid</option>
                <option value="paid" @selected(request('status') === 'paid')>paid</option>
                <option value="overdue" @selected(request('status') === 'overdue')>overdue</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="billing_year">{{ __('Year') }}</label>
            <input id="billing_year" name="billing_year" type="number" value="{{ request('billing_year') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="billing_month">{{ __('Month') }}</label>
            <input id="billing_month" name="billing_month" type="number" min="1" max="12" value="{{ request('billing_month') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.reports.financial.export.excel', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export Excel') }}</a>
        <a href="{{ route('admin.reports.financial.export.pdf', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export PDF') }}</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Invoice') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Student') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Period') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Balance') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="px-3 py-2 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                        <td class="px-3 py-2">{{ $invoice->student?->full_name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $invoice->periodLabel() }}</td>
                        <td class="px-3 py-2">{{ $invoice->currency }} {{ $invoice->balanceFormatted() }}</td>
                        <td class="px-3 py-2">{{ $invoice->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
@endsection
