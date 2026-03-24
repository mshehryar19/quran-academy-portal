<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClassScheduleRequest;
use App\Http\Requests\Admin\UpdateClassScheduleRequest;
use App\Models\ClassSchedule;
use App\Models\ClassSlot;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\ScheduleChangeLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ClassSchedule::class, 'class_schedule');
    }

    public function index(Request $request): View
    {
        $query = ClassSchedule::query()
            ->with(['teacher', 'student', 'classSlot'])
            ->orderByDesc('id');

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->integer('teacher_id'));
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->integer('student_id'));
        }
        if ($request->filled('class_slot_id')) {
            $query->where('class_slot_id', $request->integer('class_slot_id'));
        }
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->integer('day_of_week'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn ($t) => $t->where('full_name', 'like', "%{$search}%")
                    ->orWhere('public_id', 'like', "%{$search}%"))
                    ->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', "%{$search}%")
                        ->orWhere('public_id', 'like', "%{$search}%"));
            });
        }

        $schedules = $query->get();

        $teachers = Teacher::query()->where('status', 'active')->orderBy('full_name')->get();
        $students = Student::query()->where('status', 'active')->orderBy('full_name')->get();
        $slots = ClassSlot::query()->orderBy('start_time')->get();

        return view('admin.class-schedules.index', compact('schedules', 'teachers', 'students', 'slots'));
    }

    public function create(): View
    {
        $teachers = Teacher::query()->where('status', 'active')->with('user')->orderBy('full_name')->get();
        $students = Student::query()->where('status', 'active')->with('user')->orderBy('full_name')->get();
        $slots = ClassSlot::query()->where('status', 'active')->orderBy('start_time')->get();

        return view('admin.class-schedules.create', compact('teachers', 'students', 'slots'));
    }

    public function store(StoreClassScheduleRequest $request): RedirectResponse
    {
        $schedule = DB::transaction(function () use ($request) {
            $schedule = ClassSchedule::query()->create([
                'teacher_id' => $request->integer('teacher_id'),
                'student_id' => $request->integer('student_id'),
                'class_slot_id' => $request->integer('class_slot_id'),
                'day_of_week' => $request->integer('day_of_week'),
                'start_date' => $request->date('start_date'),
                'end_date' => $request->filled('end_date') ? $request->date('end_date') : null,
                'status' => $request->string('status')->toString(),
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);

            ScheduleChangeLogger::log($schedule, 'created', [
                'teacher_id' => $schedule->teacher_id,
                'student_id' => $schedule->student_id,
                'class_slot_id' => $schedule->class_slot_id,
                'day_of_week' => $schedule->day_of_week,
                'start_date' => $schedule->start_date->toDateString(),
                'end_date' => $schedule->end_date?->toDateString(),
                'status' => $schedule->status,
            ]);

            return $schedule;
        });

        return redirect()->route('admin.class-schedules.show', $schedule)->with('status', __('Schedule created.'));
    }

    public function show(ClassSchedule $classSchedule): View
    {
        $classSchedule->load(['teacher.user', 'student.user', 'classSlot', 'changeLogs.user']);

        return view('admin.class-schedules.show', compact('classSchedule'));
    }

    public function edit(ClassSchedule $classSchedule): View
    {
        $classSchedule->load(['teacher', 'student', 'classSlot']);
        $teachers = Teacher::query()->where('status', 'active')->with('user')->orderBy('full_name')->get();
        $students = Student::query()->where('status', 'active')->with('user')->orderBy('full_name')->get();
        $slots = ClassSlot::query()->orderBy('start_time')->get();

        return view('admin.class-schedules.edit', compact('classSchedule', 'teachers', 'students', 'slots'));
    }

    public function update(UpdateClassScheduleRequest $request, ClassSchedule $classSchedule): RedirectResponse
    {
        DB::transaction(function () use ($request, $classSchedule): void {
            $before = $classSchedule->only([
                'teacher_id', 'student_id', 'class_slot_id', 'day_of_week',
                'start_date', 'end_date', 'status', 'notes',
            ]);

            $oldTeacherId = $classSchedule->teacher_id;

            $classSchedule->update([
                'teacher_id' => $request->integer('teacher_id'),
                'student_id' => $request->integer('student_id'),
                'class_slot_id' => $request->integer('class_slot_id'),
                'day_of_week' => $request->integer('day_of_week'),
                'start_date' => $request->date('start_date'),
                'end_date' => $request->filled('end_date') ? $request->date('end_date') : null,
                'status' => $request->string('status')->toString(),
                'notes' => $request->filled('notes') ? $request->string('notes')->toString() : null,
            ]);

            $classSchedule->refresh();

            $after = $classSchedule->only([
                'teacher_id', 'student_id', 'class_slot_id', 'day_of_week',
                'start_date', 'end_date', 'status', 'notes',
            ]);

            ScheduleChangeLogger::log($classSchedule, 'updated', [
                'before' => $before,
                'after' => $after,
            ]);

            if ($oldTeacherId !== $classSchedule->teacher_id) {
                activity()
                    ->performedOn($classSchedule)
                    ->causedBy($request->user())
                    ->event('schedule.teacher_reassigned')
                    ->withProperties([
                        'from_teacher_id' => $oldTeacherId,
                        'to_teacher_id' => $classSchedule->teacher_id,
                    ])
                    ->log('Teacher assignment changed on schedule');
            }
        });

        return redirect()->route('admin.class-schedules.show', $classSchedule)->with('status', __('Schedule updated.'));
    }

    public function destroy(ClassSchedule $classSchedule): RedirectResponse
    {
        $this->authorize('delete', $classSchedule);

        DB::transaction(function () use ($classSchedule): void {
            ScheduleChangeLogger::log($classSchedule, 'deactivated', [
                'previous_status' => $classSchedule->status,
            ]);
            $classSchedule->update(['status' => 'inactive']);
        });

        activity()
            ->performedOn($classSchedule)
            ->causedBy(request()->user())
            ->event('schedule.deactivated')
            ->log('Class schedule deactivated');

        return redirect()->route('admin.class-schedules.index')->with('status', __('Schedule deactivated.'));
    }
}
