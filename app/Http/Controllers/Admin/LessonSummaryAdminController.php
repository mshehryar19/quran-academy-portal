<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OverrideLessonSummaryRequest;
use App\Models\LessonSummary;
use App\Models\LessonSummaryOverride;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LessonSummaryAdminController extends Controller
{
    public function show(LessonSummary $lessonSummary): View
    {
        $this->authorize('view', $lessonSummary);

        $lessonSummary->load([
            'classSession.classSchedule.student',
            'classSession.classSchedule.classSlot',
            'student',
            'teacher',
            'overrides.adminUser',
        ]);

        return view('admin.lesson-summaries.show', compact('lessonSummary'));
    }

    public function update(OverrideLessonSummaryRequest $request, LessonSummary $lessonSummary): RedirectResponse
    {
        $this->authorize('override', $lessonSummary);

        DB::transaction(function () use ($request, $lessonSummary): void {
            $before = $lessonSummary->only([
                'lesson_topic', 'surah_or_lesson', 'memorization_progress', 'performance_notes', 'homework_assigned',
            ]);

            $lessonSummary->update($request->only([
                'lesson_topic', 'surah_or_lesson', 'memorization_progress', 'performance_notes', 'homework_assigned',
            ]));

            $after = $lessonSummary->fresh()->only([
                'lesson_topic', 'surah_or_lesson', 'memorization_progress', 'performance_notes', 'homework_assigned',
            ]);

            LessonSummaryOverride::query()->create([
                'lesson_summary_id' => $lessonSummary->id,
                'admin_user_id' => $request->user()->id,
                'previous_values' => $before,
                'new_values' => $after,
                'reason' => $request->input('reason'),
                'created_at' => now(),
            ]);

            activity()
                ->performedOn($lessonSummary)
                ->causedBy($request->user())
                ->event('lesson_summary.admin_override')
                ->withProperties(['before' => $before, 'after' => $after])
                ->log('Admin overrode lesson summary');
        });

        return redirect()->route('admin.lesson-summaries.show', $lessonSummary)->with('status', __('Lesson summary corrected by admin.'));
    }
}
