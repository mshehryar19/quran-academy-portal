<?php

use App\Http\Controllers\Teacher\ClassSessionController;
use App\Http\Controllers\Teacher\MyReportsController;
use App\Http\Controllers\Teacher\MyScheduleController;
use Illuminate\Support\Facades\Route;

Route::prefix('my-classes')->name('teacher.')->middleware('role:Teacher')->group(function (): void {
    Route::get('/my-reports', MyReportsController::class)->name('reports.summary');
    Route::get('/', [MyScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/sessions/{class_session}', [ClassSessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{class_session}/student-attendance', [ClassSessionController::class, 'storeStudentAttendance'])->name('sessions.student-attendance');
    Route::post('/sessions/{class_session}/lesson-summary', [ClassSessionController::class, 'storeLessonSummary'])->name('sessions.lesson-summary');
    Route::post('/sessions/{class_session}/homework', [ClassSessionController::class, 'storeHomework'])->name('sessions.homework');
    Route::patch('/homework-tasks/{homework_task}', [ClassSessionController::class, 'updateHomework'])->name('homework.update');
    Route::post('/sessions/{class_session}/progress-notes', [ClassSessionController::class, 'storeProgressNote'])->name('sessions.progress-notes');
});
