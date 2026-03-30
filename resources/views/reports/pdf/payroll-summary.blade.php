<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Payroll summary') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background: #f3f4f6; }
        h1 { font-size: 15px; }
    </style>
</head>
<body>
    <h1>{{ __('Payroll summary') }}</h1>
    <p>{{ __('Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('Employee') }}</th>
                <th>{{ __('Period') }}</th>
                <th>{{ __('Final payable PKR') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $rec)
                <tr>
                    <td>{{ $rec->user?->name ?? '—' }}</td>
                    <td>{{ $rec->periodLabel() }}</td>
                    <td>{{ $rec->final_payable_pkr }}</td>
                    <td>{{ $rec->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
