<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use App\Services\UniquePublicIdService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Student::class, 'student');
    }

    public function index(Request $request): View
    {
        $query = Student::query()->with('user')->orderByDesc('id');

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

        $students = $query->get();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request, UniquePublicIdService $idService): RedirectResponse
    {
        DB::transaction(function () use ($request, $idService): void {
            $isActive = $request->string('status')->toString() === 'active';

            $user = User::query()->create([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => Hash::make($request->string('password')->toString()),
                'is_active' => $isActive,
            ]);

            $user->assignRole('Student');

            Student::query()->create([
                'user_id' => $user->id,
                'public_id' => $idService->nextStudentPublicId(),
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'gender' => $request->filled('gender') ? $request->string('gender')->toString() : null,
                'status' => $request->string('status')->toString(),
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);
        });

        return redirect()->route('admin.students.index')->with('status', 'Student created successfully.');
    }

    public function show(Student $student): View
    {
        $student->load([
            'user',
            'parents',
            'classSchedules' => fn ($q) => $q->with(['teacher', 'classSlot'])->orderBy('day_of_week')->orderBy('start_date'),
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $student->load('user');

        return view('admin.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student): void {
            $isActive = $request->string('status')->toString() === 'active';

            $student->user->forceFill([
                'name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'is_active' => $isActive,
            ]);

            if ($request->filled('password')) {
                $student->user->password = Hash::make($request->string('password')->toString());
            }

            $student->user->save();

            $student->update([
                'full_name' => $request->string('full_name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'gender' => $request->filled('gender') ? $request->string('gender')->toString() : null,
                'status' => $request->string('status')->toString(),
                'country' => $request->filled('country') ? $request->string('country')->toString() : null,
                'timezone' => $request->filled('timezone') ? $request->string('timezone')->toString() : null,
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);
        });

        return redirect()->route('admin.students.show', $student)->with('status', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('delete', $student);

        DB::transaction(function () use ($student): void {
            $student->update(['status' => 'inactive']);
            $student->user->update(['is_active' => false]);
        });

        activity()
            ->performedOn($student)
            ->causedBy(request()->user())
            ->event('student.deactivated')
            ->log('Student record deactivated');

        return redirect()->route('admin.students.index')->with('status', 'Student deactivated.');
    }
}
