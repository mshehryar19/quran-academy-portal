<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $portalDisplayName ?? config('app.name', 'Quran Academy Portal') }} - Authentication</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body text-slate-900 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="w-full max-w-md rounded-2xl border border-slate-200/80 bg-white p-8 shadow-soft-lg">
            <div class="mb-6 text-center">
                <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-teal-500 text-lg font-bold text-white shadow-sm shadow-teal-500/30">Q</span>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">{{ $portalDisplayName ?? config('app.name', 'Quran Academy Portal') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ __('Internal access only') }}</p>
            </div>

            @include('layouts.partials.alerts')

            @yield('content')
        </div>
    </div>
</body>
</html>
