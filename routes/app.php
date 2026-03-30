<?php

use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::get('/search', GlobalSearchController::class)->name('search');
