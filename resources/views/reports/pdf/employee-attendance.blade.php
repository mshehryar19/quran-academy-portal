<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Employee attendance') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        h1 { font-size: 16px; }
    </style>
</head>
<body>
    <h1>{{ __('Employee attendance') }}</h1>
    <p>{{ __('Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>{{ __('Teacher') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Event') }}</th>
                <th>{{ __('Occurred at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $e)
                <tr>
                    <td>{{ $e->teacher?->full_name ?? '—' }}</td>
                    <td>{{ $e->attendance_date?->toDateString() ?? '' }}</td>
                    <td>{{ $e->event_type }}</td>
                    <td>{{ $e->occurred_at?->format('Y-m-d H:i') ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
