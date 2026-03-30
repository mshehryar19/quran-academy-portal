@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Salary profiles (PKR)</h2>
            <p class="text-sm text-gray-600">Monthly payroll uses base salary here.</p>
        </div>
        <a href="{{ route('admin.salary-profiles.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">Add profile</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead>
            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                <th class="px-3 py-2">Employee</th>
                <th class="px-3 py-2 text-right">Base PKR</th>
                <th class="px-3 py-2">Effective</th>
                <th class="px-3 py-2"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($profiles as $p)
                <tr class="border-b border-gray-100">
                    <td class="px-3 py-2">{{ $p->user->name }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($p->base_salary_pkr, 2) }}</td>
                    <td class="px-3 py-2">{{ $p->effective_from->format('Y-m-d') }}</td>
                    <td class="px-3 py-2"><a href="{{ route('admin.salary-profiles.edit', $p) }}" class="underline">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $profiles->links() }}</div>
@endsection
