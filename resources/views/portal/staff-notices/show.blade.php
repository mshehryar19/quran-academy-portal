@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('staff-notices.index') }}" class="text-sm text-blue-700 underline">{{ __('← Back to notices') }}</a>
        <h2 class="mt-4 text-xl font-semibold">{{ $staffNotice->title }}</h2>
        <p class="text-sm text-gray-600">{{ $staffNotice->category }} @if ($staffNotice->severity) — {{ $staffNotice->severity }} @endif</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <p class="text-xs text-gray-500">{{ __('Summary') }}</p>
        <p class="mt-1 text-sm text-gray-800">{{ $staffNotice->short_alert }}</p>
        <hr class="my-4">
        <p class="text-xs text-gray-500">{{ __('Full message') }}</p>
        <div class="prose prose-sm mt-2 max-w-none whitespace-pre-wrap text-gray-900">{{ $staffNotice->full_message }}</div>
        <p class="mt-6 text-xs text-gray-500">{{ __('Published') }}: {{ $staffNotice->published_at?->format('Y-m-d H:i') }}</p>
    </div>
@endsection
