@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Add student</h2>
        <p class="text-sm text-gray-600">A user account with the Student role is created. Student ID is generated (STD-####).</p>
    </div>

    <form method="post" action="{{ route('admin.students.store') }}" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @include('admin.students._form', ['student' => null])
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Create student
            </button>
            <a href="{{ route('admin.students.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
