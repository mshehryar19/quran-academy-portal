@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Children&apos;s tuition</h2>
        <p class="text-sm text-gray-600">Invoices and balances per linked student (view only).</p>
    </div>

    @if ($students->isEmpty())
        <p class="rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600 shadow-sm">No students linked to this parent account.</p>
    @endif

    <ul class="space-y-3">
        @foreach ($students as $s)
            <li class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <a href="{{ route('parent.billing.student.invoices', $s) }}" class="font-medium text-gray-900 underline">{{ $s->full_name }}</a>
                <span class="text-sm text-gray-500">— open invoices</span>
            </li>
        @endforeach
    </ul>
@endsection
