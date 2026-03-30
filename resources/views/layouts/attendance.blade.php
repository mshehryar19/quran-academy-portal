<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee attendance — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-gray-900 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-lg items-center justify-between px-4 py-3">
                <div>
                    <h1 class="text-sm font-semibold">Employee attendance</h1>
                    <p class="text-xs text-slate-500">Separate from the main portal — digits ID only</p>
                </div>
                <a href="{{ url('/login') }}" class="text-xs text-slate-600 underline">Portal login</a>
            </div>
        </header>

        <main class="mx-auto max-w-lg px-4 py-8">
            @include('layouts.partials.alerts')
            @yield('content')
        </main>
    </div>
</body>
</html>
