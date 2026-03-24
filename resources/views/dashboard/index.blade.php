@extends('layouts.app')

@section('content')
    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-xl font-semibold">Dashboard</h2>
        <p class="mt-1 text-sm text-gray-600">
            Welcome, {{ $user->name }}. Your current role is <strong>{{ $primaryRole }}</strong>.
        </p>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Authentication</p>
            <p class="mt-2 text-sm text-gray-700">Login, logout, and password reset are active.</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">RBAC</p>
            <p class="mt-2 text-sm text-gray-700">Role-based access is enabled for secure routing.</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Next Modules</p>
            <p class="mt-2 text-sm text-gray-700">Master data and scheduling will be added in next phase.</p>
        </div>
    </section>
@endsection
