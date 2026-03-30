<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeacherRequest;
use App\Http\Requests\Admin\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use App\Services\UniquePublicIdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Teacher::class, 'teacher');
    }

    public function index(Request $request): View
    {
        $query = Teacher::query()->with('user')->orderByDesc('id');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('public_id', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $teachers = $query->paginate(40)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request, UniquePublicIdService $idService): RedirectResponse
    {
        DB::transaction(function () use ($request, $idService): void {
            $isActive = $request->string('status')->toString() === 'active';

            $user = User::query()->create([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'is_active' => $isActive,
            ]);

            $user->assignRole('Teacher');

            Teacher::query()->create([
                'user_id' => $user->id,
                'public_id' => $idService->nextTeacherPublicId(),
                'attendance_digits' => $request->filled('attendance_digits')
                    ? preg_replace('/\D/', '', $request->string('attendance_digits')->toString())
                    : null,
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'gender' => $request->filled('gender') ? $request->string('gender')->toString() : null,
                'date_of_appointment' => $request->date('date_of_appointment'),
                'status' => $request->string('status')->toString(),
                'address_line' => $request->filled('address_line') ? $request->string('address_line')->toString() : null,
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('status', 'Teacher created successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load([
            'user',
            'classSchedules' => fn ($q) => $q->with(['student', 'classSlot'])->orderBy('day_of_week')->orderBy('start_date'),
        ]);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($request, $teacher): void {
            $isActive = $request->string('status')->toString() === 'active';

            $teacher->user->forceFill([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'is_active' => $isActive,
            ]);

            if ($request->filled('password')) {
                $teacher->user->password = $request->string('password')->toString();
            }

            $teacher->user->save();

            $teacher->update([
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'attendance_digits' => $request->filled('attendance_digits')
                    ? preg_replace('/\D/', '', $request->string('attendance_digits')->toString())
                    : null,
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'gender' => $request->filled('gender') ? $request->string('gender')->toString() : null,
                'date_of_appointment' => $request->date('date_of_appointment'),
                'status' => $request->string('status')->toString(),
                'address_line' => $request->filled('address_line') ? $request->string('address_line')->toString() : null,
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);
        });

        return redirect()->route('admin.teachers.show', $teacher)->with('status', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $this->authorize('delete', $teacher);

        DB::transaction(function () use ($teacher): void {
            $teacher->update(['status' => 'inactive']);
            $teacher->user->update(['is_active' => false]);
        });

        activity()
            ->performedOn($teacher)
            ->causedBy(request()->user())
            ->event('teacher.deactivated')
            ->log('Teacher record deactivated');

        return redirect()->route('admin.teachers.index')->with('status', 'Teacher deactivated.');
    }
}
