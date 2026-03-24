<?php

use App\Http\Controllers\Admin\AcademyParentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\ClassSlotController;
use App\Http\Controllers\Admin\StaffUserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'role:Admin|HR|Supervisor'])
    ->as('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::middleware('role:Admin')->group(function (): void {
            Route::get('/staff', [StaffUserController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [StaffUserController::class, 'create'])->name('staff.create');
            Route::post('/staff', [StaffUserController::class, 'store'])->name('staff.store');
        });

        Route::resource('teachers', TeacherController::class);
        Route::resource('students', StudentController::class);
        Route::resource('parents', AcademyParentController::class)->parameters([
            'parents' => 'parent',
        ]);

        Route::middleware('role:Admin|Supervisor')->group(function (): void {
            Route::resource('class-slots', ClassSlotController::class)->parameters([
                'class-slots' => 'class_slot',
            ]);
            Route::resource('class-schedules', ClassScheduleController::class);
        });
    });
