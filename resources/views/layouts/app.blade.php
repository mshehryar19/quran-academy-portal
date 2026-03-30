<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $portalDisplayName ?? config('app.name', 'Quran Academy Portal') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="app-body text-slate-900 antialiased">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            @keydown.escape.window="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm lg:hidden"
            x-cloak
            aria-hidden="true"
        ></div>

        @include('layouts.sidebar')

        <div class="flex min-h-screen flex-col lg:pl-64">
            @include('layouts.header')

            <main class="app-main flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @include('layouts.partials.alerts')
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
