<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MahasiswaMiddleware;
use App\Http\Controllers\MahasiswaController;

Route::middleware([MahasiswaMiddleware::class])->prefix('mahasiswa')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
        Route::get('/proyek-data', [MahasiswaController::class, 'getProyekData'])->name('mahasiswa.getProyekData');
        Route::get('/mitra-data', [MahasiswaController::class, 'getMitraData'])->name('mahasiswa.getMitraData');
    });
});