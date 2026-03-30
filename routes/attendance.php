<?php

use App\Http\Controllers\Attendance\KioskController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendance')->name('attendance.')->group(function (): void {
    Route::get('/', [KioskController::class, 'identify'])->name('identify');
    Route::post('/establish', [KioskController::class, 'establish'])->middleware('throttle:12,1');

    Route::middleware('attendance.kiosk')->group(function (): void {
        Route::get('/panel', [KioskController::class, 'panel'])->name('panel');
        Route::post('/sign-in', [KioskController::class, 'signIn'])->middleware('throttle:30,1')->name('sign-in');
        Route::post('/sign-out', [KioskController::class, 'signOut'])->middleware('throttle:30,1')->name('sign-out');
        Route::post('/leave', [KioskController::class, 'leave'])->name('leave');
    });
});
