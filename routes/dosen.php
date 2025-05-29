<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Middleware\DosenMiddleware;
use App\Http\Controllers\Dosen\DataProyekController;

Route::middleware([DosenMiddleware::class])->prefix('dosen')->group(function () {
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
    Route::get('/data-proyek', [DataProyekController::class, 'getDataProyek'])->name('dosen.getDataProyek');
    Route::get('/data-proyek/{id}', [DataProyekController::class, 'getDataProyekById'])->name('dosen.detailProyek');
});