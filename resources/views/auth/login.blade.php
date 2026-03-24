@extends('layouts.auth')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input id="remember" type="checkbox" name="remember" class="rounded border-gray-300">
                Remember me
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-gray-700 underline hover:text-gray-900">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-black">
            Log in
        </button>
    </form>
@endsection
