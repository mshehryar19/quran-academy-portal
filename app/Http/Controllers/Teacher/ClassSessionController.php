<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreHomeworkTaskRequest;
use App\Http\Requests\Teacher\StoreLessonSummaryRequest;
use App\Http\Requests\Teacher\StoreProgressNoteRequest;
use App\Http\Requests\Teacher\StoreStudentAttendanceRequest;
use App\Http\Requests\Teacher\UpdateHomeworkTaskRequest;
use App\Models\ClassSession;
use App\Models\HomeworkTask;
use App\Models\LessonSummary;
use App\Models\StudentClassAttendance;
use App\Models\StudentProgressNote;
use App\Services\SystemNotificationDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassSessionController extends Controller
{
    public function show(Request $request, ClassSession $classSession): View
    {
        $this->authorize('view', $classSession);

        $classSession->load([
            'classSchedule.student',
            'classSchedule.classSlot',
            'studentAttendance.markedBy',
            'lessonSummary',
            'homeworkTasks.student',
            'progressNotes',
        ]);

        return view('teacher.sessions.show', compact('classSession'));
    }

    public function storeStudentAttendance(StoreStudentAttendanceRequest $request, ClassSession $classSession, SystemNotificationDispatcher $notifications): RedirectResponse
    {
        $this->authorize('manageAsTeacher', $classSession);

        $isAbsent = false;
        $teacherOffered = false;

        DB::transaction(function () use ($request, $classSession, &$isAbsent, &$teacherOffered): void {
            $isAbsent = $request->string('status')->toString() === 'absent';
            $teacherOffered = $isAbsent && $request->boolean('teacher_available_for_reassignment');

            StudentClassAttendance::query()->updateOrCreate(
                ['class_session_id' => $classSession->id],
                [
                    'status' => $request->string('status')->toString(),
                    'marked_by_user_id' => $request->user()->id,
                    'marked_at' => now(),
                    'teacher_available_for_reassignment' => $teacherOffered,
                ]
            );

            activity()
                ->performedOn($classSession)
                ->causedBy($request->user())
                ->event('student_attendance.marked')
                ->withProperties([
                    'status' => $request->string('status')->toString(),
                    'teacher_available_for_reassignment' => $request->boolean('teacher_available_for_reassignment'),
                ])
                ->log('Student attendance marked for class session');
        });

        if ($isAbsent) {
            $classSession->load('classSchedule.student.parents.user');
            $student = $classSession->classSchedule?->student;
            if ($student) {
                $notifications->studentAbsentInClass($classSession, $student, $teacherOffered);
            }
        }

        return redirect()->route('teacher.sessions.show', $classSession)->with('status', __('Attendance saved.'));
    }

    public function storeLessonSummary(StoreLessonSummaryRequest $request, ClassSession $classSession): RedirectResponse
    {
        $this->authorize('manageAsTeacher', $classSession);

        $teacher = $request->user()->teacher;
        abort_if(! $teacher, 403);

        $classSession->load('classSchedule');

        if (LessonSummary::query()->where('class_session_id', $classSession->id)->exists()) {
            return back()->withErrors(['lesson' => __('Lesson summary already submitted for this class.')]);
        }

        DB::transaction(function () use ($request, $classSession, $teacher): void {
            $now = now();
            LessonSummary::query()->create([
                'class_session_id' => $classSession->id,
                'teacher_id' => $teacher->id,
                'student_id' => $classSession->classSchedule->student_id,
                'lesson_topic' => $request->input('lesson_topic'),
                'surah_or_lesson' => $request->input('surah_or_lesson'),
                'memorization_progress' => $request->input('memorization_progress'),
                'performance_notes' => $request->input('performance_notes'),
                'homework_assigned' => $request->input('homework_assigned'),
                'submitted_at' => $now,
                'locked_at' => $now,
            ]);

            $classSession->update(['status' => 'completed']);

            activity()
                ->performedOn($classSession)
                ->causedBy($request->user())
                ->event('lesson_summary.submitted')
                ->log('Lesson summary submitted');
        });

        return redirect()->route('teacher.sessions.show', $classSession)->with('status', __('Lesson summary submitted.'));
    }

    public function storeHomework(StoreHomeworkTaskRequest $request, ClassSession $classSession): RedirectResponse
    {
        $this->authorize('manageAsTeacher', $classSession);

        $teacher = $request->user()->teacher;
        abort_if(! $teacher, 403);

        $classSession->load(['classSchedule', 'lessonSummary']);

        $task = HomeworkTask::query()->create([
            'class_session_id' => $classSession->id,
            'lesson_summary_id' => $classSession->lessonSummary?->id,
            'teacher_id' => $teacher->id,
            'student_id' => $classSession->classSchedule->student_id,
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'assigned_date' => $request->date('assigned_date'),
            'due_date' => $request->filled('due_date') ? $request->date('due_date') : null,
            'status' => 'pending',
        ]);

        activity()
            ->performedOn($task)
            ->causedBy($request->user())
            ->event('homework.created')
            ->log('Homework task assigned');

        return redirect()->route('teacher.sessions.show', $classSession)->with('status', __('Homework task added.'));
    }

    public function updateHomework(UpdateHomeworkTaskRequest $request, HomeworkTask $homeworkTask): RedirectResponse
    {
        $this->authorize('manageAsTeacher', $homeworkTask->classSession);

        $homeworkTask->update([
            'status' => $request->string('status')->toString(),
            'completion_marked_at' => now(),
        ]);

        activity()
            ->performedOn($homeworkTask)
            ->causedBy($request->user())
            ->event('homework.completion_marked')
            ->withProperties(['status' => $request->string('status')->toString()])
            ->log('Homework completion status updated');

        return redirect()->route('teacher.sessions.show', $homeworkTask->classSession)->with('status', __('Task status updated.'));
    }

    public function storeProgressNote(StoreProgressNoteRequest $request, ClassSession $classSession): RedirectResponse
    {
        $this->authorize('manageAsTeacher', $classSession);

        $teacher = $request->user()->teacher;
        abort_if(! $teacher, 403);

        $classSession->load('classSchedule');

        $note = StudentProgressNote::query()->create([
            'student_id' => $classSession->classSchedule->student_id,
            'teacher_id' => $teacher->id,
            'class_session_id' => $classSession->id,
            'body' => $request->string('body')->toString(),
            'recorded_at' => now(),
        ]);

        activity()
            ->performedOn($note)
            ->causedBy($request->user())
            ->event('progress_note.created')
            ->log('Student progress note recorded');

        return redirect()->route('teacher.sessions.show', $classSession)->with('status', __('Progress note saved.'));
    }
}
