<?php

use App\Http\Controllers\Employee\AdvanceSalaryRequestController;
use App\Http\Controllers\Employee\LeaveRequestController;
use App\Http\Controllers\Employee\MySalaryController;
use App\Http\Controllers\Hr\LeaveMonitoringController;
use App\Http\Controllers\Supervisor\SupervisorLeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:leave.request'])->prefix('my-leave')->name('employee.leaves.')->group(function (): void {
    Route::get('/', [LeaveRequestController::class, 'index'])->name('index');
    Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
    Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
    Route::get('/{leave}/attachment', [LeaveRequestController::class, 'downloadAttachment'])->name('attachment');
    Route::get('/{leave}', [LeaveRequestController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'permission:salary.view'])->prefix('my-salary')->name('employee.salary.')->group(function (): void {
    Route::get('/', [MySalaryController::class, 'index'])->name('index');
    Route::get('/record/{monthly_salary_record}', [MySalaryController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'role:Teacher|HR|Supervisor|Admin'])->prefix('my-advances')->name('employee.advances.')->group(function (): void {
    Route::get('/', [AdvanceSalaryRequestController::class, 'index'])->name('index');
    Route::get('/create', [AdvanceSalaryRequestController::class, 'create'])->name('create');
    Route::post('/', [AdvanceSalaryRequestController::class, 'store'])->name('store');
});

Route::middleware(['auth', 'role:Supervisor', 'permission:leave.review'])->prefix('supervisor/leaves')->name('supervisor.leaves.')->group(function (): void {
    Route::get('/', [SupervisorLeaveController::class, 'index'])->name('index');
    Route::get('/{leave}', [SupervisorLeaveController::class, 'show'])->name('show');
    Route::post('/{leave}/decision', [SupervisorLeaveController::class, 'decide'])->name('decide');
});

Route::middleware(['auth', 'role:HR', 'permission:leave.monitor'])->prefix('hr/leaves')->name('hr.leaves.')->group(function (): void {
    Route::get('/', [LeaveMonitoringController::class, 'index'])->name('index');
    Route::get('/{leave}', [LeaveMonitoringController::class, 'show'])->name('show');
});
