@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('System settings') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Branding, defaults, and invoice numbering. Changes are audited.') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700" for="system_name">{{ __('Portal / system name') }}</label>
            <input id="system_name" name="system_name" type="text" required value="{{ old('system_name', $values['system_name'] ?? '') }}"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('system_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700" for="default_currency">{{ __('Default currency label') }}</label>
            <select id="default_currency" name="default_currency" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @foreach (['GBP', 'USD', 'EUR', 'PKR'] as $cur)
                    <option value="{{ $cur }}" @selected(old('default_currency', $values['default_currency'] ?? 'GBP') === $cur)>{{ $cur }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">{{ __('Used for display defaults; tuition may still use profile currency.') }}</p>
            @error('default_currency')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700" for="default_timezone">{{ __('Default timezone') }}</label>
            <input id="default_timezone" name="default_timezone" type="text" required value="{{ old('default_timezone', $values['default_timezone'] ?? config('app.timezone')) }}"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm" placeholder="UTC">
            <p class="mt-1 text-xs text-gray-500">{{ __('PHP timezone identifier (e.g. Asia/Karachi). Applied as reference for new configuration.') }}</p>
            @error('default_timezone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700" for="invoice_number_prefix">{{ __('Invoice number prefix') }}</label>
            <input id="invoice_number_prefix" name="invoice_number_prefix" type="text" required pattern="[A-Za-z0-9]+" maxlength="12"
                   value="{{ old('invoice_number_prefix', $values['invoice_number_prefix'] ?? 'INV') }}"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono uppercase">
            <p class="mt-1 text-xs text-gray-500">{{ __('Letters and numbers only. Affects newly generated invoice numbers (format PREFIX-YEAR-SEQ).') }}</p>
            @error('invoice_number_prefix')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Save settings') }}</button>
    </form>
@endsection
