@props([
    'title',
    'value',
    'trendLabel' => null,
    'accent' => 'teal',
])

@php
    $accentClasses = [
        'teal' => 'from-teal-500/10 border-teal-200',
        'violet' => 'from-violet-500/10 border-violet-200',
        'amber' => 'from-amber-500/10 border-amber-200',
        'emerald' => 'from-emerald-500/10 border-emerald-200',
    ];

    $accentTextClasses = [
        'teal' => 'text-teal-700',
        'violet' => 'text-violet-700',
        'amber' => 'text-amber-700',
        'emerald' => 'text-emerald-700',
    ];

    $bgClass = $accentClasses[$accent] ?? $accentClasses['teal'];
    $labelClass = $accentTextClasses[$accent] ?? $accentTextClasses['teal'];
@endphp

<div {{ $attributes->merge(['class' => "app-card border bg-gradient-to-br via-white to-white p-5 {$bgClass}"]) }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $title }}</p>
    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>

    @if ($trendLabel)
        <p class="mt-1 text-xs font-medium {{ $labelClass }}">{{ $trendLabel }}</p>
    @endif

    @if (trim($slot) !== '')
        <div class="mt-1">
            {{ $slot }}
        </div>
    @endif
</div>
