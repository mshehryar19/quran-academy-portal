<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $staffNotice->title }}</title>
</head>
<body style="font-family: system-ui, sans-serif; line-height: 1.5; color: #111;">
    <h1 style="font-size: 1.25rem;">{{ $staffNotice->title }}</h1>
    <p style="color:#555;font-size:0.875rem;">{{ __('Category') }}: {{ $staffNotice->category }}
        @if ($staffNotice->severity)
            &mdash; {{ __('Severity') }}: {{ $staffNotice->severity }}
        @endif
    </p>
    <div style="white-space: pre-wrap;">{{ $staffNotice->full_message }}</div>
    <p style="margin-top:1.5rem;">
        <a href="{{ route('staff-notices.show', $staffNotice) }}" style="color:#111;">{{ __('Open in portal') }}</a>
    </p>
    <p style="color:#888;font-size:0.75rem;">{{ config('app.name') }}</p>
</body>
</html>
