<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassSlotRequest;
use App\Http\Requests\Admin\UpdateClassSlotRequest;
use App\Models\ClassSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassSlotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ClassSlot::class, 'class_slot');
    }

    public function index(Request $request): View
    {
        $query = ClassSlot::query()->orderBy('start_time');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $slots = $query->get();

        return view('admin.class-slots.index', compact('slots'));
    }

    public function create(): View
    {
        return view('admin.class-slots.create');
    }

    public function store(StoreClassSlotRequest $request): RedirectResponse
    {
        $start = Carbon::createFromFormat('H:i', $request->string('start_time')->toString())->format('H:i:s');
        $end = Carbon::createFromFormat('H:i', $request->string('end_time')->toString())->format('H:i:s');

        ClassSlot::query()->create([
            'name' => $request->filled('name') ? $request->string('name')->toString() : null,
            'start_time' => $start,
            'end_time' => $end,
            'duration_minutes' => 30,
            'status' => $request->string('status')->toString(),
            'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
        ]);

        return redirect()->route('admin.class-slots.index')->with('status', __('Class slot created.'));
    }

    public function show(ClassSlot $classSlot): View
    {
        $classSlot->loadCount('classSchedules');

        return view('admin.class-slots.show', compact('classSlot'));
    }

    public function edit(ClassSlot $classSlot): View
    {
        return view('admin.class-slots.edit', compact('classSlot'));
    }

    public function update(UpdateClassSlotRequest $request, ClassSlot $classSlot): RedirectResponse
    {
        $start = Carbon::createFromFormat('H:i', $request->string('start_time')->toString())->format('H:i:s');
        $end = Carbon::createFromFormat('H:i', $request->string('end_time')->toString())->format('H:i:s');

        $classSlot->update([
            'name' => $request->filled('name') ? $request->string('name')->toString() : null,
            'start_time' => $start,
            'end_time' => $end,
            'duration_minutes' => 30,
            'status' => $request->string('status')->toString(),
            'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
        ]);

        return redirect()->route('admin.class-slots.show', $classSlot)->with('status', __('Class slot updated.'));
    }

    public function destroy(ClassSlot $classSlot): RedirectResponse
    {
        $this->authorize('delete', $classSlot);

        if ($classSlot->classSchedules()->exists()) {
            $classSlot->update(['status' => 'inactive']);

            activity()
                ->performedOn($classSlot)
                ->causedBy(request()->user())
                ->event('class_slot.deactivated')
                ->log('Class slot deactivated (referenced by schedules)');

            return redirect()->route('admin.class-slots.index')->with('status', __('Slot deactivated (existing schedules still reference it).'));
        }

        $classSlot->delete();

        return redirect()->route('admin.class-slots.index')->with('status', __('Class slot removed.'));
    }
}
