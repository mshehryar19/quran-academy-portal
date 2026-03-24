@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-semibold">HR &amp; Supervisor users</h2>
            <p class="text-sm text-gray-600">Admin-only: create internal accounts with HR or Supervisor roles.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}"
           class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Add staff user
        </a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
        <table id="staff-table" class="display w-full text-sm" style="width:100%">
            <thead>
            <tr class="border-b border-gray-200 text-left">
                <th class="px-3 py-2">Name</th>
                <th class="px-3 py-2">Email</th>
                <th class="px-3 py-2">Role</th>
                <th class="px-3 py-2">Active</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($staff as $user)
                <tr>
                    <td class="px-3 py-2">{{ $user->name }}</td>
                    <td class="px-3 py-2">{{ $user->email }}</td>
                    <td class="px-3 py-2">{{ $user->getRoleNames()->first() }}</td>
                    <td class="px-3 py-2">{{ $user->is_active ? 'Yes' : 'No' }}</td>
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
            $('#staff-table').DataTable({
                pageLength: 25,
                order: [[0, 'asc']]
            });
        });
    </script>
@endpush
