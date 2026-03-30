@props([
    'title' => __('Nothing here yet'),
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center']) }}>
    <p class="text-sm font-medium text-gray-800">{{ $title }}</p>
    @if ($description)
        <p class="mt-2 text-sm text-gray-600">{{ $description }}</p>
    @endif
    @if (trim($slot))
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
