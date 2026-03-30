<?php

use App\Http\Controllers\Admin\AcademicSessionReportController;
use App\Http\Controllers\Admin\AcademyParentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdvanceSalaryAdminController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\ClassSlotController;
use App\Http\Controllers\Admin\EmployeeAttendanceReportController;
use App\Http\Controllers\Admin\FinancialOverviewReportController;
use App\Http\Controllers\Admin\LeaveFinalController;
use App\Http\Controllers\Admin\LessonSummaryAdminController;
use App\Http\Controllers\Admin\MonthlySalaryRecordController;
use App\Http\Controllers\Admin\PayrollSummaryReportController;
use App\Http\Controllers\Admin\ReportHubController;
use App\Http\Controllers\Admin\SalaryProfileController;
use App\Http\Controllers\Admin\StaffNoticeController;
use App\Http\Controllers\Admin\StaffUserController;
use App\Http\Controllers\Admin\StudentAttendanceReportController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'role:Admin|HR|Supervisor|Accountant'])
    ->as('admin.')
    ->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        Route::get('/reports', ReportHubController::class)->name('reports.hub');

        Route::middleware(['permission:reports.view', 'role:Admin|Supervisor|HR'])->group(function (): void {
            Route::get('/reports/employee-attendance', [EmployeeAttendanceReportController::class, 'index'])->name('reports.employee-attendance');
            Route::get('/reports/employee-attendance/export.xlsx', [EmployeeAttendanceReportController::class, 'exportExcel'])->name('reports.employee-attendance.export.excel');
            Route::get('/reports/employee-attendance/export.pdf', [EmployeeAttendanceReportController::class, 'exportPdf'])->name('reports.employee-attendance.export.pdf');

            Route::get('/reports/class-sessions', [AcademicSessionReportController::class, 'index'])->name('reports.class-sessions');
            Route::get('/reports/class-sessions/export.xlsx', [AcademicSessionReportController::class, 'exportExcel'])->name('reports.class-sessions.export.excel');
            Route::get('/reports/class-sessions/export.pdf', [AcademicSessionReportController::class, 'exportPdf'])->name('reports.class-sessions.export.pdf');

            Route::get('/reports/student-attendance', [StudentAttendanceReportController::class, 'index'])->name('reports.student-attendance');
            Route::get('/reports/student-attendance/export.xlsx', [StudentAttendanceReportController::class, 'exportExcel'])->name('reports.student-attendance.export.excel');
            Route::get('/reports/student-attendance/export.pdf', [StudentAttendanceReportController::class, 'exportPdf'])->name('reports.student-attendance.export.pdf');
        });

        Route::middleware(['permission:reports.view', 'role:Admin|Accountant'])->group(function (): void {
            Route::get('/reports/financial', [FinancialOverviewReportController::class, 'index'])->name('reports.financial');
            Route::get('/reports/financial/export.xlsx', [FinancialOverviewReportController::class, 'exportExcel'])->name('reports.financial.export.excel');
            Route::get('/reports/financial/export.pdf', [FinancialOverviewReportController::class, 'exportPdf'])->name('reports.financial.export.pdf');
        });

        Route::middleware(['permission:reports.view', 'can:salary.manage'])->group(function (): void {
            Route::get('/reports/payroll', [PayrollSummaryReportController::class, 'index'])->name('reports.payroll');
            Route::get('/reports/payroll/export.xlsx', [PayrollSummaryReportController::class, 'exportExcel'])->name('reports.payroll.export.excel');
            Route::get('/reports/payroll/export.pdf', [PayrollSummaryReportController::class, 'exportPdf'])->name('reports.payroll.export.pdf');
        });

        Route::middleware(['role:Admin', 'permission:settings.manage'])->group(function (): void {
            Route::get('/settings', [SystemSettingsController::class, 'edit'])->name('settings.edit');
            Route::put('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');
        });

        Route::middleware('role:Admin')->group(function (): void {
            Route::get('/staff', [StaffUserController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [StaffUserController::class, 'create'])->name('staff.create');
            Route::post('/staff', [StaffUserController::class, 'store'])->name('staff.store');
        });

        Route::middleware(['permission:notifications.manage', 'role:Admin'])->group(function (): void {
            Route::get('/staff-notices', [StaffNoticeController::class, 'index'])->name('staff-notices.index');
            Route::get('/staff-notices/create', [StaffNoticeController::class, 'create'])->name('staff-notices.create');
            Route::post('/staff-notices', [StaffNoticeController::class, 'store'])->name('staff-notices.store');
            Route::get('/staff-notices/{staff_notice}', [StaffNoticeController::class, 'show'])->name('staff-notices.show');
            Route::delete('/staff-notices/{staff_notice}', [StaffNoticeController::class, 'destroy'])->name('staff-notices.destroy');
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

            Route::get('/lesson-summaries/{lesson_summary}', [LessonSummaryAdminController::class, 'show'])->name('lesson-summaries.show');
        });

        Route::middleware('role:Admin')->group(function (): void {
            Route::put('/lesson-summaries/{lesson_summary}', [LessonSummaryAdminController::class, 'update'])->name('lesson-summaries.update');

            Route::prefix('leaves/final')->name('leaves.final.')->group(function (): void {
                Route::get('/', [LeaveFinalController::class, 'index'])->name('index');
                Route::get('/{leave}', [LeaveFinalController::class, 'show'])->name('show');
                Route::post('/{leave}/decision', [LeaveFinalController::class, 'decide'])->name('decide');
            });

            Route::resource('salary-profiles', SalaryProfileController::class)->except(['show', 'destroy']);

            Route::get('/monthly-salary-records', [MonthlySalaryRecordController::class, 'index'])->name('monthly-salary-records.index');
            Route::get('/monthly-salary-records/{monthly_salary_record}', [MonthlySalaryRecordController::class, 'show'])->name('monthly-salary-records.show');
            Route::post('/monthly-salary-records/recompute', [MonthlySalaryRecordController::class, 'recompute'])->name('monthly-salary-records.recompute');
            Route::post('/monthly-salary-records/{monthly_salary_record}/finalize', [MonthlySalaryRecordController::class, 'finalize'])->name('monthly-salary-records.finalize');

            Route::get('/advances', [AdvanceSalaryAdminController::class, 'index'])->name('advances.index');
            Route::get('/advances/{advance_salary_request}', [AdvanceSalaryAdminController::class, 'show'])->name('advances.show');
            Route::post('/advances/{advance_salary_request}/decision', [AdvanceSalaryAdminController::class, 'decide'])->name('advances.decide');
        });
    });
