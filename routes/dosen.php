<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Middleware\DosenMiddleware;

Route::middleware([DosenMiddleware::class])->prefix('dosen')->group(function () {
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
});