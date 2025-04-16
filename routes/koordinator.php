<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoordinatorController;
use App\Http\Middleware\KoordinatorMiddleware;

Route::middleware([KoordinatorMiddleware::class])->prefix('koordinator')->group(function () {
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');
});