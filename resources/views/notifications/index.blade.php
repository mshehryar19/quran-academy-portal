@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold">{{ __('Notifications') }}</h2>
            <p class="text-sm text-gray-600">{{ __('History of portal alerts (schedule, billing, attendance, notices).') }}</p>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button type="submit" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm shadow-sm hover:bg-gray-50">
                {{ __('Mark all as read') }}
            </button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('When') }}</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('Title') }}</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('Message') }}</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-700"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($notifications as $n)
                    @php
                        $data = $n->data ?? [];
                        $title = $data['title'] ?? '—';
                        $body = $data['body'] ?? '';
                        $unread = $n->read_at === null;
                    @endphp
                    <tr class="{{ $unread ? 'bg-blue-50/40' : '' }}">
                        <td class="whitespace-nowrap px-4 py-2 text-gray-600">{{ $n->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $title }}</td>
                        <td class="max-w-md px-4 py-2 text-gray-700">{{ \Illuminate\Support\Str::limit($body, 160) }}</td>
                        <td class="whitespace-nowrap px-4 py-2 text-right">
                            @if ($unread)
                                <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-blue-700 underline">{{ __('Mark read') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">{{ __('No notifications.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endsection
