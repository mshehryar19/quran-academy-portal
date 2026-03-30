@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Teachers</h2>
            <p class="text-sm text-gray-600">Search by name, ID, email, phone, or status.</p>
        </div>
        @can('create', \App\Models\Teacher::class)
            <a href="{{ route('admin.teachers.create') }}"
               class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Add teacher
            </a>
        @endcan
    </div>

    <form method="get" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <div class="min-w-[200px] flex-1">
            <label class="block text-xs font-medium text-gray-500" for="q">Search</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Name, ID, email, phone…"
                   class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500" for="status">Status</label>
            <select id="status" name="status"
                    class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <button type="submit" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium hover:bg-gray-50">
            Apply filters
        </button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.teachers.index') }}" class="rounded-md px-2 py-2 text-sm text-gray-600 hover:underline">Clear</a>
        @endif
    </form>

    @if ($teachers->isEmpty())
        <x-empty-state title="No teachers match these filters" description="Adjust search or clear filters to see more records.">
            @if (request()->hasAny(['q', 'status']))
                <a href="{{ route('admin.teachers.index') }}" class="text-sm text-blue-700 underline">Clear filters</a>
            @endif
        </x-empty-state>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="border-b border-gray-200 bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Teacher ID</th>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Phone</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($teachers as $teacher)
                    <tr class="border-b border-gray-100">
                        <td class="px-3 py-2">{{ $teacher->id }}</td>
                        <td class="px-3 py-2 font-mono">{{ $teacher->public_id }}</td>
                        <td class="px-3 py-2">{{ $teacher->full_name }}</td>
                        <td class="px-3 py-2">{{ $teacher->email }}</td>
                        <td class="px-3 py-2">{{ $teacher->phone ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $teacher->status }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <a href="{{ route('admin.teachers.show', $teacher) }}" class="text-gray-900 underline">View</a>
                            @can('update', $teacher)
                                <span class="text-gray-400">|</span>
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="text-gray-900 underline">Edit</a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $teachers->links() }}</div>
    @endif
@endsection
