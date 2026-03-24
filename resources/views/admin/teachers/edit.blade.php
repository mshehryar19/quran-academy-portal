@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Edit teacher</h2>
        <p class="text-sm text-gray-600">Teacher ID: <span class="font-mono">{{ $teacher->public_id }}</span></p>
    </div>

    <form method="post" action="{{ route('admin.teachers.update', $teacher) }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('put')
        @include('admin.teachers._form', ['teacher' => $teacher])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Save changes
            </button>
            <a href="{{ route('admin.teachers.show', $teacher) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
