@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
        <div>
            <h2 class="text-xl font-semibold">{{ $classSlot->name ?? 'Class slot #'.$classSlot->id }}</h2>
            <p class="text-sm text-gray-600 font-mono">{{ $classSlot->timeRangeLabel() }} · {{ $classSlot->duration_minutes }} minutes</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.class-slots.index') }}" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm hover:bg-gray-50">Back</a>
            @can('update', $classSlot)
                <a href="{{ route('admin.class-slots.edit', $classSlot) }}" class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-800">Edit</a>
            @endcan
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
            <div><dt class="text-gray-500">Status</dt>
                <dd><span class="rounded-full px-2 py-0.5 text-xs {{ $classSlot->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $classSlot->status }}</span></dd></div>
            <div><dt class="text-gray-500">Schedules using slot</dt><dd>{{ $classSlot->class_schedules_count }}</dd></div>
        </dl>
        @if ($classSlot->notes)
            <p class="mt-4 text-xs uppercase text-gray-500">Notes</p>
            <p class="text-sm">{{ $classSlot->notes }}</p>
        @endif
    </div>

    @can('delete', $classSlot)
        <form method="post" action="{{ route('admin.class-slots.destroy', $classSlot) }}" class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4"
              onsubmit="return confirm('Remove or deactivate this slot?');">
            @csrf
            @method('delete')
            <p class="text-sm text-red-800">If schedules exist, the slot will be deactivated instead of deleted.</p>
            <button type="submit" class="mt-2 rounded-md bg-red-700 px-3 py-1.5 text-sm text-white hover:bg-red-800">Remove / deactivate</button>
        </form>
    @endcan
@endsection
