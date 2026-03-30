@php
    use Illuminate\Support\Facades\Auth;
@endphp

<aside class="fixed inset-y-0 left-0 hidden w-64 flex-col bg-white border-r lg:flex">
    <div class="flex flex-col h-full">
        <div class="px-4 py-4 border-b">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-900">{{ config('app.name', 'Quran Academy') }}</span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto px-2 py-3">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                        {{ __('Dashboard') }}
                    </a>
                </li>

                @can('viewAny', \App\Models\Teacher::class)
                    <li>
                        <a href="{{ route('admin.teachers.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Teachers') }}
                        </a>
                    </li>
                @endcan

                @can('viewAny', \App\Models\Student::class)
                    <li>
                        <a href="{{ route('admin.students.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Students') }}
                        </a>
                    </li>
                @endcan

                @can('viewAny', \App\Models\AcademyParent::class)
                    <li>
                        <a href="{{ route('admin.parents.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Parents') }}
                        </a>
                    </li>
                @endcan

                @can('viewAny', \App\Models\ClassSchedule::class)
                    <li>
                        <a href="{{ route('admin.class-schedules.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Schedules') }}
                        </a>
                    </li>
                @endcan

                @can('viewAny', \App\Models\ClassSlot::class)
                    <li>
                        <a href="{{ route('admin.class-slots.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Class slots') }}
                        </a>
                    </li>
                @endcan

                @can('reports.view')
                    <li>
                        <a href="{{ route('admin.reports.hub') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Reports') }}
                        </a>
                    </li>
                @endcan

                @can('salary.manage')
                    <li>
                        <a href="{{ route('admin.monthly-salary-records.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Payroll') }}
                        </a>
                    </li>
                @endcan

                @can('invoice.manage')
                    <li>
                        <a href="{{ route('admin.billing.invoices.index') }}"
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg hover:bg-slate-100 text-slate-700">
                            {{ __('Billing') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </nav>

        @if (Auth::check())
            <div class="p-4 border-t">
                <div class="text-sm font-semibold text-slate-900 truncate">{{ Auth::user()?->name }}</div>
                <div class="text-xs text-slate-500 truncate">{{ Auth::user()?->email }}</div>
            </div>
        @endif
    </div>
</aside>

