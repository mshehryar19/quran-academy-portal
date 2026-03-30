<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

require __DIR__.'/auth.php';

require __DIR__.'/attendance.php';

Route::middleware('auth')->group(function (): void {
    require __DIR__.'/app.php';
    require __DIR__.'/billing.php';
    require __DIR__.'/payroll.php';
    require __DIR__.'/teacher.php';
    require __DIR__.'/notifications.php';
    require __DIR__.'/admin.php';
});
