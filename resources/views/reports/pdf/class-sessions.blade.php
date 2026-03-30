<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Class sessions') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background: #f3f4f6; }
        h1 { font-size: 15px; }
    </style>
</head>
<body>
    <h1>{{ __('Class sessions') }}</h1>
    <p>{{ __('Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Teacher') }}</th>
                <th>{{ __('Student') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $session)
                @php $sch = $session->classSchedule; @endphp
                <tr>
                    <td>{{ $session->session_date?->toDateString() ?? '' }}</td>
                    <td>{{ $sch?->teacher?->full_name ?? '—' }}</td>
                    <td>{{ $sch?->student?->full_name ?? '—' }}</td>
                    <td>{{ $session->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
