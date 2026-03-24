@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Add HR or Supervisor user</h2>
        <p class="text-sm text-gray-600">Password is set by the administrator. Share credentials through a secure channel.</p>
    </div>

    <form method="post" action="{{ route('admin.staff.store') }}" class="max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}"
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="role">Role</label>
                <select id="role" name="role" required
                        class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none">
                    <option value="">— Select —</option>
                    <option value="HR" @selected(old('role') === 'HR')>HR</option>
                    <option value="Supervisor" @selected(old('role') === 'Supervisor')>Supervisor</option>
                </select>
                @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="new-password"
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                       class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-900 focus:outline-none" required>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Create user
            </button>
            <a href="{{ route('admin.staff.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
@endsection
