@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Add parent</h2>
        <p class="text-sm text-gray-600">Creates a user with the Parent role. Link one or more students below.</p>
    </div>

    <form method="post" action="{{ route('admin.parents.store') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @include('admin.parents._form', ['parent' => null, 'students' => $students])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Create parent
            </button>
            <a href="{{ route('admin.parents.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
