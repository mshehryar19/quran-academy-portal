@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">Students</h2>
            <p class="text-sm text-gray-600">Search by name, student ID, email, phone, or status.</p>
        </div>
        @can('create', \App\Models\Student::class)
            <a href="{{ route('admin.students.create') }}"
               class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Add student
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
            <a href="{{ route('admin.students.index') }}" class="rounded-md px-2 py-2 text-sm text-gray-600 hover:underline">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table id="students-table" class="display w-full text-sm" style="width:100%">
            <thead>
            <tr class="border-b border-gray-200 text-left">
                <th class="px-3 py-2">ID</th>
                <th class="px-3 py-2">Student ID</th>
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Phone</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="px-3 py-2">{{ $student->id }}</td>
                    <td class="px-3 py-2 font-mono">{{ $student->public_id }}</td>
                    <td class="px-3 py-2">{{ $student->full_name }}</td>
                    <td class="px-3 py-2">{{ $student->email }}</td>
                    <td class="px-3 py-2">{{ $student->phone ?? '—' }}</td>
                    <td class="px-3 py-2">
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $student->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            {{ $student->status }}
                        </span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <a href="{{ route('admin.students.show', $student) }}" class="text-gray-900 underline">View</a>
                        @can('update', $student)
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('admin.students.edit', $student) }}" class="text-gray-900 underline">Edit</a>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#students-table').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                columnDefs: [{orderable: false, targets: -1}]
            });
        });
    </script>
@endpush
