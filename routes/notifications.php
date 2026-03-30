<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Portal\StaffNoticeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth', 'role:Admin|HR|Supervisor|Teacher|Accountant'])->group(function (): void {
    Route::get('/staff-notices', [StaffNoticeController::class, 'index'])->name('staff-notices.index');
    Route::get('/staff-notices/{staff_notice}', [StaffNoticeController::class, 'show'])->name('staff-notices.show');
});
