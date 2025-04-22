<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoordinatorController;
use App\Http\Middleware\KoordinatorMiddleware;

Route::middleware([KoordinatorMiddleware::class])->prefix('koordinator')->group(function () {
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');
    Route::get('/data-mitra', [KoordinatorController::class, 'getDataMitra'])->name('koordinator.dataMitra');
    Route::post('/data-mitra', [KoordinatorController::class, 'storeDataMitra'])->name('mitra.tambahDataMitra');
    Route::put('/mitra/{id}', [KoordinatorController::class, 'updateDataMitra'])->name('mitra.editDataMitra');
    Route::delete('/mitra/{id}', [KoordinatorController::class, 'deleteDataMitra'])->name('mitra.hapusDataMitra');

});