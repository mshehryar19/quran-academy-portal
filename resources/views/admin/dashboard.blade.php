@extends('layouts.app')

@section('content')
    @php
        $calMonth = now();
        $calStart = $calMonth->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::MONDAY);
        $calEnd = $calMonth->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
    @endphp

    <div class="mb-8">
        <h1 class="app-page-title">{{ __('Internal home') }}</h1>
        <p class="app-page-sub">
            {{ __('Admin') }} › {{ __('Overview') }} — {{ __('Signed in as') }} <strong>{{ $user->name }}</strong> ({{ $primaryRole }}).
            {{ __('Use the sidebar for master data, slots, schedules, reports, and staff notices.') }}
        </p>
    </div>

    @if ($quickStats ?? null)
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-dashboard-stat-card
                :title="__('Active teachers')"
                :value="number_format($quickStats['teachers_active'])"
                :trend-label="__('Current active records')"
                accent="teal"
            />
            <x-dashboard-stat-card
                :title="__('Active students')"
                :value="number_format($quickStats['students_active'])"
                :trend-label="__('Current active records')"
                accent="violet"
            />
            <x-dashboard-stat-card
                :title="__('Active schedules')"
                :value="number_format($quickStats['schedules_active'])"
                :trend-label="__('Current active records')"
                accent="amber"
            />
            <x-dashboard-stat-card
                :title="__('Unread alerts')"
                :value="number_format($unreadNotificationsCount ?? 0)"
                accent="emerald"
            >
                <a href="{{ route('notifications.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Open inbox') }} →</a>
            </x-dashboard-stat-card>
        </div>
    @else
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-dashboard-stat-card :title="__('Unread alerts')" :value="number_format($unreadNotificationsCount ?? 0)" accent="teal">
                <a href="{{ route('notifications.index') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Open inbox') }} →</a>
            </x-dashboard-stat-card>
            @can('salary.manage')
                @if (($pendingFinalLeaves ?? 0) > 0)
                    <div class="app-card border-amber-200 bg-gradient-to-br from-amber-500/10 via-white to-white p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">{{ __('Leave final queue') }}</p>
                        <p class="mt-2 text-3xl font-bold text-amber-950">{{ $pendingFinalLeaves }}</p>
                        <a href="{{ route('admin.leaves.final.index') }}" class="app-btn-primary mt-4 inline-flex bg-amber-600 shadow-amber-500/25 hover:bg-amber-700">{{ __('Review') }}</a>
                    </div>
                @endif
            @endcan
            @can('invoice.manage')
                @if ($outstandingTuition !== null)
                    <div class="app-card p-5">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Outstanding tuition') }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($outstandingTuition, 2) }}</p>
                        <a href="{{ route('admin.reports.financial') }}" class="mt-3 inline-flex text-xs font-semibold text-teal-600 hover:text-teal-700">{{ __('Financial report') }} →</a>
                    </div>
                @endif
            @endcan
            @can('reports.view')
                @if ($user->hasAnyRole(['Admin', 'HR', 'Supervisor', 'Accountant']))
                    <a href="{{ route('admin.reports.hub') }}" class="app-card flex flex-col justify-center p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Reports') }}</p>
                        <p class="mt-2 text-sm font-semibold text-slate-900">{{ __('Open reports hub') }}</p>
                    </a>
                @endif
            @endcan
        </div>
    @endif

    @can('salary.manage')
        @if (($pendingFinalLeaves ?? 0) > 0 && ($quickStats ?? null))
            <div class="app-card mb-6 border-amber-200 bg-amber-50/50 p-4 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-900">{{ __('Leave final queue') }}</p>
                    <p class="text-xs text-amber-800/90">{{ __(':count request(s) awaiting admin decision.', ['count' => $pendingFinalLeaves]) }}</p>
                </div>
                <a href="{{ route('admin.leaves.final.index') }}" class="app-btn-primary mt-3 inline-flex bg-amber-600 shadow-amber-500/25 hover:bg-amber-700 sm:mt-0">{{ __('Review') }}</a>
            </div>
        @endif
    @endcan

    @can('invoice.manage')
        @if ($outstandingTuition !== null && ($quickStats ?? null))
            <div class="app-card mb-6 p-5 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Outstanding tuition') }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($outstandingTuition, 2) }}</p>
                </div>
                <a href="{{ route('admin.reports.financial') }}" class="app-btn-secondary mt-3 sm:mt-0">{{ __('Financial report') }}</a>
            </div>
        @endif
    @endcan

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
        <div class="app-card p-5 lg:col-span-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">{{ $calMonth->translatedFormat('F Y') }}</h2>
                <span class="rounded-lg bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ __('This month') }}</span>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-semibold uppercase text-slate-400">
                @foreach (['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'] as $d)
                    <span>{{ $d }}</span>
                @endforeach
            </div>
            <div class="mt-2 grid grid-cols-7 gap-1 text-center text-sm">
                @php $cursor = $calStart->copy(); @endphp
                @while ($cursor <= $calEnd)
                    @php
                        $isCurrentMonth = $cursor->month === $calMonth->month;
                        $isToday = $cursor->isToday();
                    @endphp
                    <span
                        @class([
                            'flex h-9 items-center justify-center rounded-lg',
                            'text-slate-300' => ! $isCurrentMonth,
                            'text-slate-800' => $isCurrentMonth && ! $isToday,
                            'bg-teal-500 font-semibold text-white shadow-sm shadow-teal-500/30' => $isToday,
                        ])
                    >{{ $cursor->day }}</span>
                    @php $cursor->addDay(); @endphp
                @endwhile
            </div>
        </div>

        <div class="app-card p-5 lg:col-span-5">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">{{ __('Operations snapshot') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('Relative scale for teachers, students, schedules') }}</p>
                </div>
            </div>
            @if ($quickStats ?? null)
                @php
                    $max = max(1, $quickStats['teachers_active'], $quickStats['students_active'], $quickStats['schedules_active']);
                    $h = fn ($n) => (int) round(8 + ($n / $max) * 100);
                @endphp
                <div class="flex h-40 items-end justify-around gap-3 px-2">
                    <div class="flex w-16 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-teal-400/90 transition-all" style="height: {{ $h($quickStats['teachers_active']) }}px"></div>
                        <span class="text-[10px] font-semibold uppercase text-slate-500">{{ __('Teachers') }}</span>
                    </div>
                    <div class="flex w-16 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-violet-400/90 transition-all" style="height: {{ $h($quickStats['students_active']) }}px"></div>
                        <span class="text-[10px] font-semibold uppercase text-slate-500">{{ __('Students') }}</span>
                    </div>
                    <div class="flex w-16 flex-col items-center gap-2">
                        <div class="w-full rounded-t-lg bg-amber-400/90 transition-all" style="height: {{ $h($quickStats['schedules_active']) }}px"></div>
                        <span class="text-[10px] font-semibold uppercase text-slate-500">{{ __('Schedules') }}</span>
                    </div>
                </div>
            @else
                <p class="py-8 text-center text-sm text-slate-500">{{ __('Summary charts appear when your role includes organization-wide stats.') }}</p>
            @endif
        </div>

        <div class="app-card p-5 lg:col-span-3">
            <h2 class="text-sm font-bold text-slate-900">{{ __('Shortcuts') }}</h2>
            <p class="mt-1 text-xs text-slate-500">{{ __('Frequent admin actions') }}</p>
            <ul class="mt-4 space-y-2">
                <li>
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-2 text-sm font-medium text-slate-800 hover:border-teal-200 hover:bg-teal-50/50">
                        {{ __('Notifications') }}
                        <span class="text-teal-600">→</span>
                    </a>
                </li>
                @can('reports.view')
                    @if ($user->hasAnyRole(['Admin', 'HR', 'Supervisor', 'Accountant']))
                        <li>
                            <a href="{{ route('admin.reports.hub') }}" class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-2 text-sm font-medium text-slate-800 hover:border-teal-200 hover:bg-teal-50/50">
                                {{ __('Reports hub') }}
                                <span class="text-teal-600">→</span>
                            </a>
                        </li>
                    @endif
                @endcan
                @can('invoice.manage')
                    @if ($outstandingTuition !== null)
                        <li>
                            <a href="{{ route('admin.reports.financial') }}" class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-2 text-sm font-medium text-slate-800 hover:border-teal-200 hover:bg-teal-50/50">
                                {{ __('Financial report') }}
                                <span class="text-teal-600">→</span>
                            </a>
                        </li>
                    @endif
                @endcan
            </ul>
        </div>
    </div>

    @can('notifications.manage')
        @if ($recentStaffNotices?->isNotEmpty())
            <section class="mb-6">
                <h2 class="mb-3 text-lg font-bold text-slate-900">{{ __('Recent staff notices (admin)') }}</h2>
                <div class="app-table-wrap">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentStaffNotices as $n)
                                <tr>
                                    <td class="font-medium text-slate-900">{{ $n->title }}</td>
                                    <td>{{ $n->category }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.staff-notices.show', $n) }}" class="font-semibold text-teal-600 hover:text-teal-700">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.staff-notices.index') }}" class="mt-3 inline-flex text-sm font-semibold text-teal-600 hover:text-teal-700">{{ __('All notices') }} →</a>
            </section>
        @endif
    @endcan

    <h2 class="mb-3 text-lg font-bold text-slate-900">{{ __('Modules') }}</h2>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @can('viewAny', \App\Models\Teacher::class)
            <a href="{{ route('admin.teachers.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Teachers') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Open teacher list') }}</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\Student::class)
            <a href="{{ route('admin.students.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Students') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Open student list') }}</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\AcademyParent::class)
            <a href="{{ route('admin.parents.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Parents') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Open parent list') }}</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\ClassSlot::class)
            <a href="{{ route('admin.class-slots.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Class slots') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('30-minute time windows') }}</p>
            </a>
        @endcan
        @can('viewAny', \App\Models\ClassSchedule::class)
            <a href="{{ route('admin.class-schedules.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Schedules') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Weekly assignments') }}</p>
            </a>
        @endcan
        @can('reports.view')
            @if ($user->hasAnyRole(['Admin', 'Supervisor', 'HR']))
                <a href="{{ route('admin.reports.employee-attendance') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Operations') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Employee attendance events') }}</p>
                </a>
                <a href="{{ route('admin.reports.class-sessions') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Academic') }}</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Class sessions & summaries') }}</p>
                </a>
            @endif
        @endcan
        @can('salary.manage')
            <a href="{{ route('admin.monthly-salary-records.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Payroll (PKR)') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Monthly records & recompute') }}</p>
            </a>
            <a href="{{ route('admin.leaves.final.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Leave') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Final approvals') }}</p>
            </a>
        @endcan
        @can('invoice.manage')
            <a href="{{ route('admin.billing.invoices.index') }}" class="app-card group block p-5 transition hover:border-teal-200 hover:shadow-soft-lg">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Tuition (GBP/USD)') }}</p>
                <p class="mt-2 text-sm font-semibold text-slate-900 group-hover:text-teal-700">{{ __('Invoices & fee setup') }}</p>
            </a>
        @endcan
    </div>

    @if ($user->hasRole('Admin'))
        <div class="app-card mt-6 border-dashed border-slate-300 bg-slate-50/50 p-5 text-sm text-slate-700">
            <strong>{{ __('Admin:') }}</strong>
            {{ __('Create HR and Supervisor accounts from') }}
            <a href="{{ route('admin.staff.index') }}" class="font-semibold text-teal-700 underline decoration-teal-300 hover:text-teal-800">{{ __('HR / Supervisor users') }}</a>.
        </div>
    @endif
@endsection
