@extends('layouts.auth')

@section('content')
    <p class="mb-4 text-sm text-gray-600">
        Enter your account email and the system will send a password reset link.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-400 focus:outline-none">
        </div>

        <button type="submit" class="w-full rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-black">
            Send reset link
        </button>
    </form>
@endsection
