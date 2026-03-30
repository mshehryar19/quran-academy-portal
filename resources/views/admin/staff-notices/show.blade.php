@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ $staffNotice->title }}</h2>
            <p class="text-sm text-gray-600">{{ $staffNotice->category }} @if ($staffNotice->severity) &mdash; {{ $staffNotice->severity }} @endif</p>
        </div>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.staff-notices.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5">{{ __('Back') }}</a>
            <form method="POST" action="{{ route('admin.staff-notices.destroy', $staffNotice) }}" onsubmit="return confirm('{{ __('Delete this notice record?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-md border border-red-200 bg-red-50 px-3 py-1.5 text-red-800">{{ __('Delete') }}</button>
            </form>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-xs text-gray-500">{{ __('Short alert') }}</p>
        <p class="mt-1 text-sm text-gray-800">{{ $staffNotice->short_alert }}</p>
        <hr class="my-4">
        <p class="text-xs text-gray-500">{{ __('Full message') }}</p>
        <div class="prose prose-sm mt-2 max-w-none whitespace-pre-wrap text-gray-900">{{ $staffNotice->full_message }}</div>
        <dl class="mt-6 grid grid-cols-1 gap-2 text-sm md:grid-cols-2">
            <div><dt class="text-gray-500">{{ __('Recipient mode') }}</dt><dd>{{ $staffNotice->recipient_mode }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Channels') }}</dt><dd>{{ implode(', ', $staffNotice->channels ?? []) }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Created by') }}</dt><dd>{{ $staffNotice->createdBy?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">{{ __('Published') }}</dt><dd>{{ $staffNotice->published_at?->format('Y-m-d H:i') }}</dd></div>
        </dl>
        @if ($staffNotice->roleTargets->isNotEmpty())
            <p class="mt-4 text-xs font-medium text-gray-700">{{ __('Target roles') }}</p>
            <ul class="list-inside list-disc text-sm">
                @foreach ($staffNotice->roleTargets as $tr)
                    <li>{{ $tr->role_name }}</li>
                @endforeach
            </ul>
        @endif
        @if ($staffNotice->userTargets->isNotEmpty())
            <p class="mt-4 text-xs font-medium text-gray-700">{{ __('Target users') }}</p>
            <ul class="list-inside list-disc text-sm">
                @foreach ($staffNotice->userTargets as $tu)
                    <li>{{ $tu->user?->name ?? $tu->user_id }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
