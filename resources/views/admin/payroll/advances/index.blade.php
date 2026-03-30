@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Advance salary requests (PKR)</h2>
        <div class="mt-2 flex gap-2 text-sm">
            <a href="{{ route('admin.advances.index', ['tab' => 'pending']) }}" class="{{ $tab === 'pending' ? 'font-semibold underline' : 'text-gray-600' }}">Pending</a>
            <span class="text-gray-300">|</span>
            <a href="{{ route('admin.advances.index', ['tab' => 'history']) }}" class="{{ $tab === 'history' ? 'font-semibold underline' : 'text-gray-600' }}">History</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Employee</th>
                <th class="px-3 py-2 text-right">Amount</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($requests as $req)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $req->created_at->format('Y-m-d') }}</td>
                    <td class="px-3 py-2">{{ $req->user->name }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($req->amount_pkr, 2) }}</td>
                    <td class="px-3 py-2">{{ $req->status }}</td>
                    <td class="px-3 py-2"><a href="{{ route('admin.advances.show', $req) }}" class="underline">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $requests->links() }}</div>
@endsection
