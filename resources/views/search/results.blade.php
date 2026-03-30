@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Search') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Results respect your role. Minimum 2 characters.') }}</p>
    </div>

    <form method="get" action="{{ route('search') }}" class="mb-6 flex flex-wrap gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <input type="search" name="q" value="{{ $query }}" placeholder="{{ __('Name, ID, email, invoice #…') }}"
               class="min-w-[200px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm" minlength="2" required>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Search') }}</button>
    </form>

    @if (mb_strlen(trim($query)) < 2)
        <x-empty-state :title="__('Enter at least 2 characters')" :description="__('Try a teacher name, student ID, parent email, or invoice number.')" />
    @else
        <div class="space-y-8">
            <section>
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Teachers') }}</h3>
                @if ($teachers->isEmpty())
                    <p class="mt-2 text-sm text-gray-600">{{ __('No matches or no access.') }}</p>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm">
                        @foreach ($teachers as $t)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                                <span>{{ $t->full_name }} <span class="text-gray-500">({{ $t->public_id }})</span></span>
                                @can('view', $t)
                                    <a href="{{ route('admin.teachers.show', $t) }}" class="text-blue-700 underline">{{ __('Open') }}</a>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section>
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Students') }}</h3>
                @if ($students->isEmpty())
                    <p class="mt-2 text-sm text-gray-600">{{ __('No matches or no access.') }}</p>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm">
                        @foreach ($students as $s)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                                <span>{{ $s->full_name }} <span class="text-gray-500">({{ $s->public_id }})</span></span>
                                @can('view', $s)
                                    <a href="{{ route('admin.students.show', $s) }}" class="text-blue-700 underline">{{ __('Open') }}</a>
                                @else
                                    <span class="text-xs text-gray-500">{{ __('Roster reference only') }}</span>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section>
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Parents') }}</h3>
                @if ($parents->isEmpty())
                    <p class="mt-2 text-sm text-gray-600">{{ __('No matches or no access.') }}</p>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm">
                        @foreach ($parents as $p)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                                <span>{{ $p->full_name }} @if($p->email)<span class="text-gray-500">{{ $p->email }}</span>@endif</span>
                                @can('view', $p)
                                    <a href="{{ route('admin.parents.show', $p) }}" class="text-blue-700 underline">{{ __('Open') }}</a>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section>
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Invoices') }}</h3>
                @if ($invoices->isEmpty())
                    <p class="mt-2 text-sm text-gray-600">{{ __('No matches or no access.') }}</p>
                @else
                    <ul class="mt-2 divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm">
                        @foreach ($invoices as $inv)
                            <li class="flex flex-wrap items-center justify-between gap-2 px-3 py-2">
                                <span class="font-mono">{{ $inv->invoice_number }}</span>
                                <span class="text-gray-600">{{ $inv->student?->full_name }}</span>
                                @can('view', $inv)
                                    <a href="{{ route('admin.billing.invoices.show', $inv) }}" class="text-blue-700 underline">{{ __('Open') }}</a>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    @endif
@endsection
