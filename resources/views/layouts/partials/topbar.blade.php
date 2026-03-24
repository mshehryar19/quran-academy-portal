<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-lg font-semibold">Quran Academy Online Portal</h1>
            <p class="text-xs text-gray-500">Phase 3 — scheduling core</p>
        </div>

        <div class="flex items-center gap-4 text-sm">
            <div class="text-right">
                <p class="font-medium">{{ auth()->user()?->name }}</p>
                <p class="text-xs text-gray-500">
                    Role: {{ auth()->user()?->getRoleNames()->first() ?? 'Unassigned' }}
                </p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
