@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Reports hub') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Operational and financial exports respect your role. PDF and Excel use current filters on each report page.') }}</p>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        @if ($user->hasAnyRole(['Admin', 'Supervisor', 'HR']))
            <a href="{{ route('admin.reports.employee-attendance') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Teachers') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ __('Employee attendance') }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ __('Login/logout events with filters and exports.') }}</p>
            </a>
            <a href="{{ route('admin.reports.class-sessions') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Academic') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ __('Class sessions') }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ __('Sessions, attendance markers, lesson summaries.') }}</p>
            </a>
            <a href="{{ route('admin.reports.student-attendance') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Students') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ __('Student class attendance') }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ __('Per-session present/absent with filters.') }}</p>
            </a>
        @endif

        @if ($user->hasAnyRole(['Admin', 'Accountant']))
            <a href="{{ route('admin.reports.financial') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Finance') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ __('Tuition invoices overview') }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ __('Balances, status filters, Excel/PDF.') }}</p>
            </a>
        @endif

        @can('salary.manage')
            <a href="{{ route('admin.reports.payroll') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Payroll') }}</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ __('Monthly salary records') }}</p>
                <p class="mt-1 text-xs text-gray-600">{{ __('PKR payroll summaries (sensitive).') }}</p>
            </a>
        @endcan
    </div>
@endsection
