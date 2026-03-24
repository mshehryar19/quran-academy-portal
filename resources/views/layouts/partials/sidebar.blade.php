<nav class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
    <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Navigation</h2>

    <ul class="space-y-1 text-sm">
        <li>
            <a href="{{ route('dashboard') }}"
               class="block rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                Portal dashboard
            </a>
        </li>

        @if (auth()->user()?->hasAnyRole(['Admin', 'HR', 'Supervisor']))
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Internal home
                </a>
            </li>
        @endif

        @can('viewAny', \App\Models\Teacher::class)
            <li>
                <a href="{{ route('admin.teachers.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.teachers.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Teachers
                </a>
            </li>
        @endcan

        @can('viewAny', \App\Models\Student::class)
            <li>
                <a href="{{ route('admin.students.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.students.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Students
                </a>
            </li>
        @endcan

        @can('viewAny', \App\Models\AcademyParent::class)
            <li>
                <a href="{{ route('admin.parents.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.parents.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Parents
                </a>
            </li>
        @endcan

        @if (auth()->user()?->hasRole('Admin'))
            <li>
                <a href="{{ route('admin.staff.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.staff.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    HR / Supervisor users
                </a>
            </li>
        @endif

        @can('viewAny', \App\Models\ClassSlot::class)
            <li>
                <a href="{{ route('admin.class-slots.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.class-slots.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Class slots
                </a>
            </li>
        @endcan

        @can('viewAny', \App\Models\ClassSchedule::class)
            <li>
                <a href="{{ route('admin.class-schedules.index') }}"
                   class="block rounded px-3 py-2 {{ request()->routeIs('admin.class-schedules.*') ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Class schedules
                </a>
            </li>
        @endcan

        <li>
            <span class="block rounded px-3 py-2 text-gray-400">Attendance &amp; lessons in next phases</span>
        </li>
    </ul>
</nav>
