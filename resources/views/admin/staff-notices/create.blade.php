@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold">{{ __('Create staff notice') }}</h2>
        <p class="text-sm text-gray-600">{{ __('Recipients receive a short portal alert; full text can go by email when selected.') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.staff-notices.store') }}" class="max-w-3xl space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="title">{{ __('Title / subject') }}</label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="short_alert">{{ __('Short alert (portal)') }}</label>
            <textarea id="short_alert" name="short_alert" rows="2" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('short_alert') }}</textarea>
            @error('short_alert')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="full_message">{{ __('Full message') }}</label>
            <textarea id="full_message" name="full_message" rows="8" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">{{ old('full_message') }}</textarea>
            @error('full_message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700" for="category">{{ __('Category') }}</label>
                <select id="category" name="category" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                    @foreach (['policy', 'violation', 'warning', 'reminder', 'operational_alert'] as $c)
                        <option value="{{ $c }}" @selected(old('category', 'operational_alert') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700" for="severity">{{ __('Severity (optional)') }}</label>
                <input id="severity" name="severity" type="text" value="{{ old('severity') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Recipients') }}</label>
            <select name="recipient_mode" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                <option value="all_staff" @selected(old('recipient_mode') === 'all_staff')>{{ __('All staff roles') }}</option>
                <option value="roles" @selected(old('recipient_mode') === 'roles')>{{ __('Selected roles') }}</option>
                <option value="users" @selected(old('recipient_mode') === 'users')>{{ __('Selected users') }}</option>
            </select>
            @error('recipient_mode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">{{ __('Roles (when “Selected roles”)') }}</p>
            <div class="mt-2 flex flex-wrap gap-3 text-sm">
                @foreach ($roles as $r)
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="role_names[]" value="{{ $r }}" @checked(collect(old('role_names', []))->contains($r))>
                        {{ $r }}
                    </label>
                @endforeach
            </div>
            @error('role_names')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="user_ids">{{ __('Users (when “Selected users”)') }}</label>
            <select id="user_ids" name="user_ids[]" multiple size="8" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(collect(old('user_ids', []))->contains($u->id))>{{ $u->name }} ({{ $u->getRoleNames()->first() }})</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">{{ __('Hold Ctrl/Cmd to select multiple.') }}</p>
            @error('user_ids')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">{{ __('Channels') }}</p>
            <div class="mt-2 flex flex-wrap gap-4 text-sm">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="channels[]" value="portal" @checked(collect(old('channels', ['portal']))->contains('portal'))>
                    {{ __('Portal') }}
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="channels[]" value="email" @checked(collect(old('channels', []))->contains('email'))>
                    {{ __('Email') }}
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="channels[]" value="whatsapp" @checked(collect(old('channels', []))->contains('whatsapp'))>
                    {{ __('WhatsApp (prepared / logged)') }}
                </label>
            </div>
            @error('channels')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700" for="expires_at">{{ __('Expires at (optional)') }}</label>
            <input id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white">{{ __('Publish & dispatch') }}</button>
            <a href="{{ route('admin.staff-notices.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection
