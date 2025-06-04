<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Middleware\DosenMiddleware;
use App\Http\Controllers\Dosen\DataProyekController;

Route::middleware([DosenMiddleware::class])->prefix('dosen')->group(function () {
    
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
        Route::get('/proyek-data', [DosenController::class, 'getProyekData'])->name('dosen.getProyekData');
        Route::get('/mitra-data', [DosenController::class, 'getMitraData'])->name('dosen.getMitraData');
    });


    
    Route::prefix('data-proyek')->group(function () {
        Route::get('/', [DataProyekController::class, 'getDataProyek'])->name('dosen.getDataProyek');
        Route::get('/{id}', [DataProyekController::class, 'getDataProyekById'])->name('dosen.detailProyek');
    });

});