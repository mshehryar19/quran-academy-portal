@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="app-page-title">{{ __('Dashboard') }}</h1>
        <p class="app-page-sub">
            {{ __('Portal') }} › {{ __('Overview') }} — {{ __('Welcome') }}, {{ $user->name }}.
            {{ __('Your current role is') }} <strong>{{ $primaryRole }}</strong>.
        </p>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-dashboard-stat-card :title="__('Unread alerts')" :value="number_format($unreadNotificationsCount ?? 0)" accent="teal">
            <a href="{{ route('notifications.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Notifications') }} →</a>
        </x-dashboard-stat-card>

        @if ($user->hasRole('Teacher') && $user->teacher)
            <a href="{{ route('teacher.schedule.index') }}" class="app-card group block bg-gradient-to-br from-teal-500/10 via-white to-white p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __("Today's classes") }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Open my schedule & sessions') }}</p>
            </a>
            @can('reports.view')
                <a href="{{ route('teacher.reports.summary') }}" class="app-card group block bg-gradient-to-br from-violet-500/10 via-white to-white p-5 transition hover:border-violet-200 hover:shadow-soft-lg">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('My work') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-violet-700">{{ __('Teaching summary & attendance sample') }}</p>
                </a>
            @endcan
        @else
            <div class="app-card flex flex-col justify-center bg-gradient-to-br from-slate-500/5 via-white to-white p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Portal') }}</p>
                <p class="mt-2 text-sm text-slate-700">{{ __('Use the sidebar for your role tools.') }}</p>
            </div>
        @endif
    </div>

    @if ($teacherSnapshot ?? null)
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-dashboard-stat-card :title="__('Today\'s sessions')" :value="number_format($teacherSnapshot['sessions_today'])" accent="amber">
                <a href="{{ route('teacher.schedule.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Open schedule') }} →</a>
            </x-dashboard-stat-card>
            <x-dashboard-stat-card :title="__('Pending homework tasks')" :value="number_format($teacherSnapshot['pending_homework'])" accent="violet" />
        </div>
    @endif

    @if ($parentSnapshot ?? null)
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-dashboard-stat-card :title="__('Linked children')" :value="number_format($parentSnapshot['children'])" accent="teal" />
            <x-dashboard-stat-card :title="__('Open tuition invoices')" :value="number_format($parentSnapshot['open_invoices'])" accent="emerald">
                <a href="{{ route('parent.billing.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Billing') }} →</a>
            </x-dashboard-stat-card>
        </div>
    @endif

    @if ($studentSnapshot ?? null)
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <x-dashboard-stat-card :title="__('Classes today')" :value="number_format($studentSnapshot['sessions_today'])" accent="teal" />
            <x-dashboard-stat-card :title="__('Invoices needing attention')" :value="number_format($studentSnapshot['billing_attention'])" accent="amber">
                @can('student_billing.view')
                    <a href="{{ route('student.billing.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('My tuition') }} →</a>
                @endcan
            </x-dashboard-stat-card>
        </div>
    @endif

    @if ($recentStaffNotices?->isNotEmpty())
        <section class="app-card mb-6 p-5">
            <h2 class="text-base font-bold text-slate-900">{{ __('Recent staff notices') }}</h2>
            <ul class="mt-3 space-y-3 text-sm">
                @foreach ($recentStaffNotices as $n)
                    <li class="flex flex-wrap items-baseline gap-x-2 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                        <a href="{{ route('staff-notices.show', $n) }}" class="font-semibold text-slate-900 hover:text-teal-700">{{ $n->title }}</a>
                        <span class="text-slate-500">({{ $n->category }})</span>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('staff-notices.index') }}" class="mt-4 inline-flex text-sm font-semibold text-teal-600 hover:text-teal-700">{{ __('All staff notices') }} →</a>
        </section>
    @endif

    @if ($user->hasRole('Teacher'))
        <p class="mb-6 text-sm text-slate-600">
            {{ __('Employee attendance sign-in/out uses your digits-only attendance ID on the') }}
            <a href="{{ route('attendance.identify') }}" class="font-semibold text-teal-700 underline decoration-teal-200 hover:text-teal-800">{{ __('dedicated attendance page') }}</a>
            {{ __('(separate from this dashboard).') }}
        </p>
    @endif

    @if ($user->can('leave.request') || $user->can('salary.view'))
        <div class="flex flex-wrap gap-3">
            @can('leave.request')
                <a href="{{ route('employee.leaves.index') }}" class="app-btn-secondary">{{ __('My leave') }}</a>
            @endcan
            @can('salary.view')
                <a href="{{ route('employee.salary.index') }}" class="app-btn-secondary">{{ __('My salary (PKR)') }}</a>
            @endcan
            @if ($user->hasAnyRole(['Teacher', 'HR', 'Supervisor', 'Admin']))
                <a href="{{ route('employee.advances.index') }}" class="app-btn-secondary">{{ __('Advance requests') }}</a>
            @endif
            @can('student_billing.view')
                @if ($user->hasRole('Student'))
                    <a href="{{ route('student.billing.index') }}" class="app-btn-secondary">{{ __('My tuition') }}</a>
                @endif
                @if ($user->hasRole('Parent'))
                    <a href="{{ route('parent.billing.index') }}" class="app-btn-secondary">{{ __('Children tuition') }}</a>
                @endif
            @endcan
            @can('invoice.manage')
                <a href="{{ route('admin.billing.invoices.index') }}" class="app-btn-primary">{{ __('Tuition invoices') }}</a>
            @endcan
        </div>
    @endif
@endsection
