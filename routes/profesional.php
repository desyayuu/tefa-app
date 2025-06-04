<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfesionalController;
use App\Http\Middleware\ProfesionalMiddleware;

Route::middleware([ProfesionalMiddleware::class])->prefix('profesional')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ProfesionalController::class, 'dashboard'])->name('profesional.dashboard');
        Route::get('/proyek-data', [ProfesionalController::class, 'getProyekData'])->name('profesional.getProyekData');
        Route::get('/mitra-data', [ProfesionalController::class, 'getMitraData'])->name('profesional.getMitraData');
    });

});