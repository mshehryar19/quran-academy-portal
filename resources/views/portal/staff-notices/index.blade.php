@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Staff notices') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Policy updates, warnings, and operational messages visible to your account.') }}</p>
    </div>

    <div class="space-y-3">
        @forelse ($notices as $n)
            <a href="{{ route('staff-notices.show', $n) }}" class="block rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-gray-300">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">{{ $n->category }}</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $n->title }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $n->short_alert }}</p>
                    </div>
                    <span class="text-xs text-gray-500">{{ $n->published_at?->format('Y-m-d') }}</span>
                </div>
            </a>
        @empty
            <p class="text-sm text-gray-600">{{ __('No notices for your account.') }}</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $notices->links() }}</div>
@endsection
