<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Tuition invoices') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background: #f3f4f6; }
        h1 { font-size: 15px; }
    </style>
</head>
<body>
    <h1>{{ __('Tuition invoices') }}</h1>
    <p>{{ __('Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('Invoice') }}</th>
                <th>{{ __('Student') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Paid') }}</th>
                <th>{{ __('Balance') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->student?->full_name ?? '—' }}</td>
                    <td>{{ $invoice->periodLabel() }}</td>
                    <td>{{ $invoice->currency }} {{ $invoice->total_amount }}</td>
                    <td>{{ $invoice->currency }} {{ $invoice->amount_paid }}</td>
                    <td>{{ $invoice->currency }} {{ $invoice->balanceFormatted() }}</td>
                    <td>{{ $invoice->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
