<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MahasiswaMiddleware;
use App\Http\Controllers\MahasiswaController;

Route::middleware([MahasiswaMiddleware::class])->prefix('mahasiswa')->group(function () {
    Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
});