<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademyParentRequest;
use App\Http\Requests\Admin\UpdateAcademyParentRequest;
use App\Models\AcademyParent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademyParentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyParent::class, 'parent');
    }

    public function index(Request $request): View
    {
        $query = AcademyParent::query()->with('user')->orderByDesc('id');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $parents = $query->paginate(40)->withQueryString();

        return view('admin.parents.index', compact('parents'));
    }

    public function create(): View
    {
        $students = Student::query()->where('status', 'active')->orderBy('full_name')->get();

        return view('admin.parents.create', compact('students'));
    }

    public function store(StoreAcademyParentRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $isActive = $request->string('status')->toString() === 'active';

            $user = User::query()->create([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'is_active' => $isActive,
            ]);

            $user->assignRole('Parent');

            $parent = AcademyParent::query()->create([
                'user_id' => $user->id,
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'status' => $request->string('status')->toString(),
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);

            $ids = array_unique($request->input('student_ids', []));
            $parent->students()->sync($ids);

            activity()
                ->performedOn($parent)
                ->causedBy(request()->user())
                ->withProperties(['student_ids' => $ids])
                ->event('parent.students_synced')
                ->log('Parent linked to students');
        });

        return redirect()->route('admin.parents.index')->with('status', 'Parent created successfully.');
    }

    public function show(AcademyParent $parent): View
    {
        $parent->load(['user', 'students']);

        return view('admin.parents.show', compact('parent'));
    }

    public function edit(AcademyParent $parent): View
    {
        $parent->load(['user', 'students']);
        $students = Student::query()->where('status', 'active')->orderBy('full_name')->get();

        return view('admin.parents.edit', compact('parent', 'students'));
    }

    public function update(UpdateAcademyParentRequest $request, AcademyParent $parent): RedirectResponse
    {
        DB::transaction(function () use ($request, $parent): void {
            $isActive = $request->string('status')->toString() === 'active';

            $parent->user->forceFill([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'is_active' => $isActive,
            ]);

            if ($request->filled('password')) {
                $parent->user->password = $request->string('password')->toString();
            }

            $parent->user->save();

            $parent->update([
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'status' => $request->string('status')->toString(),
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);

            $ids = array_unique($request->input('student_ids', []));
            $parent->students()->sync($ids);

            activity()
                ->performedOn($parent)
                ->causedBy(request()->user())
                ->withProperties(['student_ids' => $ids])
                ->event('parent.students_synced')
                ->log('Parent student links updated');
        });

        return redirect()->route('admin.parents.show', $parent)->with('status', 'Parent updated successfully.');
    }

    public function destroy(AcademyParent $parent): RedirectResponse
    {
        $this->authorize('delete', $parent);

        DB::transaction(function () use ($parent): void {
            $parent->update(['status' => 'inactive']);
            $parent->user->update(['is_active' => false]);
        });

        activity()
            ->performedOn($parent)
            ->causedBy(request()->user())
            ->event('parent.deactivated')
            ->log('Parent record deactivated');

        return redirect()->route('admin.parents.index')->with('status', 'Parent deactivated.');
    }
}
