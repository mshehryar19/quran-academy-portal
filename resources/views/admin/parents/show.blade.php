@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">{{ $parent->full_name }}</h2>
            <p class="text-sm text-gray-600">Parent / guardian record</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.parents.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Back to list</a>
            @can('update', $parent)
                <a href="{{ route('admin.parents.edit', $parent) }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-800">Edit</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Profile</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Email</dt><dd>{{ $parent->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Phone</dt><dd>{{ $parent->phone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Country</dt><dd>{{ $parent->country ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Timezone</dt><dd>{{ $parent->timezone ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Status</dt>
                    <dd>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $parent->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            {{ $parent->status }}
                        </span>
                    </dd>
                </div>
            </dl>
            @if ($parent->notes)
                <div class="mt-4 border-t border-gray-100 pt-4">
                    <p class="text-xs uppercase text-gray-500">Notes</p>
                    <p class="text-sm">{{ $parent->notes }}</p>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Account</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User ID</dt><dd>{{ $parent->user_id }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">Login</dt><dd>{{ $parent->user?->email }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-500">User active</dt><dd>{{ $parent->user?->is_active ? 'Yes' : 'No' }}</dd></div>
            </dl>

            <h4 class="mt-6 text-xs font-semibold uppercase tracking-wide text-gray-500">Linked students</h4>
            @if ($parent->students->isEmpty())
                <p class="mt-2 text-sm text-gray-600">No students linked.</p>
            @else
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm">
                    @foreach ($parent->students as $s)
                        <li>
                            <a href="{{ route('admin.students.show', $s) }}" class="font-mono text-gray-900 underline">{{ $s->public_id }}</a>
                            — {{ $s->full_name }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    @can('delete', $parent)
        @if ($parent->status === 'active')
            <form method="post" action="{{ route('admin.parents.destroy', $parent) }}" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4"
                  onsubmit="return confirm('Deactivate this parent? They will not be able to sign in.');">
                @csrf
                @method('delete')
                <p class="text-sm text-red-800">Deactivate sets parent and user status to inactive.</p>
                <button type="submit" class="mt-2 rounded-md bg-red-700 px-3 py-1.5 text-sm text-white hover:bg-red-800">
                    Deactivate parent
                </button>
            </form>
        @endif
    @endcan
@endsection
