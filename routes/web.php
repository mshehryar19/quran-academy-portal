<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function (): void {
    require __DIR__.'/app.php';
    require __DIR__.'/admin.php';
});
