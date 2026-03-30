@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Monthly salary records (PKR)</h2>
    </div>

    <section class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 shadow-sm">
        <h3 class="text-sm font-semibold text-blue-900">Recompute draft</h3>
        <p class="text-xs text-blue-800">Pulls late minutes from attendance, unpaid leave days, and approved advances for the month.</p>
        <form method="post" action="{{ route('admin.monthly-salary-records.recompute') }}" class="mt-3 flex flex-wrap items-end gap-3 text-sm">
            @csrf
            <div>
                <label class="block text-xs text-gray-600" for="user_id">Employee</label>
                <select id="user_id" name="user_id" required class="mt-1 rounded-md border border-gray-300 px-3 py-2">
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600" for="period_year">Year</label>
                <input id="period_year" name="period_year" type="number" value="{{ now()->year }}" required class="mt-1 w-24 rounded-md border border-gray-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600" for="period_month">Month</label>
                <input id="period_month" name="period_month" type="number" min="1" max="12" value="{{ now()->month }}" required class="mt-1 w-20 rounded-md border border-gray-300 px-3 py-2">
            </div>
            <button type="submit" class="rounded-md bg-blue-900 px-4 py-2 text-white">Run</button>
        </form>
        @error('period')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </section>

    <form method="get" class="mb-4 flex flex-wrap gap-3 rounded-lg border border-gray-200 bg-white p-4 text-sm shadow-sm">
        <select name="user_id" class="rounded-md border border-gray-300 px-3 py-2">
            <option value="">All staff</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-md border border-gray-300 px-3 py-2">
            <option value="">Any status</option>
            <option value="draft" @selected(request('status') === 'draft')>draft</option>
            <option value="finalized" @selected(request('status') === 'finalized')>finalized</option>
        </select>
        <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-white">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Employee</th>
                <th class="px-3 py-2">Period</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2 text-right">Payable PKR</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($records as $r)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $r->user->name }}</td>
                    <td class="px-3 py-2 font-mono">{{ $r->periodLabel() }}</td>
                    <td class="px-3 py-2">{{ $r->status }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($r->final_payable_pkr, 2) }}</td>
                    <td class="px-3 py-2"><a href="{{ route('admin.monthly-salary-records.show', $r) }}" class="underline">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
