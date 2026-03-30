@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ __('Staff notices & broadcasts') }}</h2>
            <p class="text-sm text-gray-600">{{ __('Policy, violation, and operational messages to staff (portal / email / WhatsApp-ready).') }}</p>
        </div>
        <a href="{{ route('admin.staff-notices.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('New notice') }}</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Title') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Category') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Audience') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Channels') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Published') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($notices as $n)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.staff-notices.show', $n) }}" class="font-medium text-gray-900 underline">{{ $n->title }}</a>
                        </td>
                        <td class="px-3 py-2">{{ $n->category }}</td>
                        <td class="px-3 py-2">{{ $n->recipient_mode }}</td>
                        <td class="px-3 py-2 text-xs">{{ implode(', ', $n->channels ?? []) }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $n->published_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $notices->links() }}</div>
@endsection
