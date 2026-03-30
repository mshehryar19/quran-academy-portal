@php
    use Illuminate\Support\Facades\Auth;
@endphp

<header class="sticky top-0 z-20 border-b bg-white/90 backdrop-blur">
    <div class="flex items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            {{-- Mobile: open sidebar (the Alpine state lives in layouts/app.blade.php) --}}
            <button
                type="button"
                class="lg:hidden inline-flex items-center justify-center p-2 rounded-lg hover:bg-slate-50 text-slate-700"
                @click="sidebarOpen = true"
                aria-label="{{ __('Open sidebar') }}"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

            <div class="text-sm font-semibold text-slate-900">
                {{ $portalDisplayName ?? config('app.name', 'Quran Academy Portal') }}
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if (Auth::check())
                <div class="hidden sm:block text-sm">
                    <div class="font-medium text-slate-900 truncate max-w-[220px]">{{ Auth::user()?->name }}</div>
                    <div class="text-xs text-slate-500 truncate max-w-[220px]">{{ Auth::user()?->email }}</div>
                </div>

                <a href="{{ route('notifications.index') }}" class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-slate-50 text-slate-700" aria-label="{{ __('Notifications') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </a>
            @endif
        </div>
    </div>
</header>

