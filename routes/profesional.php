<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfesionalController;
use App\Http\Middleware\ProfesionalMiddleware;

Route::middleware([ProfesionalMiddleware::class])->prefix('profesional')->group(function () {
    Route::get('/dashboard', [ProfesionalController::class, 'dashboard'])->name('profesional.dashboard');
});