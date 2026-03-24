@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-black">
            Reset password
        </button>
    </form>
@endsection
