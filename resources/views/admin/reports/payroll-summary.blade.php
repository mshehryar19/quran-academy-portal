@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Payroll summary (report)') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Monthly PKR records — sensitive; export is logged.') }}</p>
    </div>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label class="block text-xs text-gray-500" for="user_id">{{ __('Employee') }}</label>
            <select id="user_id" name="user_id" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                @foreach ($staffUsers as $u)
                    <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="period_year">{{ __('Year') }}</label>
            <input id="period_year" name="period_year" type="number" value="{{ request('period_year') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="period_month">{{ __('Month') }}</label>
            <input id="period_month" name="period_month" type="number" min="1" max="12" value="{{ request('period_month') }}" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500" for="status">{{ __('Status') }}</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="">{{ __('All') }}</option>
                <option value="draft" @selected(request('status') === 'draft')>draft</option>
                <option value="finalized" @selected(request('status') === 'finalized')>finalized</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Filter') }}</button>
        </div>
    </form>

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('admin.reports.payroll.export.excel', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export Excel') }}</a>
        <a href="{{ route('admin.reports.payroll.export.pdf', request()->query()) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm hover:bg-gray-50">{{ __('Export PDF') }}</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">{{ __('Employee') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Period') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Final payable') }}</th>
                    <th class="px-3 py-2 text-left">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($records as $rec)
                    <tr>
                        <td class="px-3 py-2">{{ $rec->user?->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $rec->periodLabel() }}</td>
                        <td class="px-3 py-2">PKR {{ $rec->final_payable_pkr }}</td>
                        <td class="px-3 py-2">{{ $rec->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
@endsection
