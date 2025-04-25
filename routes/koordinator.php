<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoordinatorController;
use App\Http\Middleware\KoordinatorMiddleware;

Route::middleware([KoordinatorMiddleware::class])->prefix('koordinator')->group(function () {
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');

    //Data Mitra
    Route::get('/data-mitra', [KoordinatorController::class, 'getDataMitra'])->name('koordinator.dataMitra');
    Route::post('/data-mitra', [KoordinatorController::class, 'storeDataMitra'])->name('koordinator.storeDataMitra');
    Route::post('/tambah-multiple-data-mitra', [KoordinatorController::class, 'tambahMultipleDataMitra'])->name('koordinator.tambahMultipleDataMitra');
    Route::put('/mitra/{id}', [KoordinatorController::class, 'updateDataMitra'])->name('koordinator.updateDataMitra');
    Route::delete('/mitra/{id}', [KoordinatorController::class, 'deleteDataMitra'])->name('koordinator.deleteDataMitra');
    Route::post('/check-email-exists', [KoordinatorController::class, 'checkEmailExists'])->name('koordinator.checkEmailExists');

    //Data Proyek
    Route::get('/data-proyek', [KoordinatorController::class, 'getDataProyek'])->name('koordinator.dataProyek');
    Route::post('/data-proyek', [KoordinatorController::class, 'tambahDataProyek'])->name('proyek.tambahDataProyek');
    Route::get('/data-proyek/{id}', [KoordinatorController::class, 'getDataProyekById'])->name('proyek.detailDataProyek');

    //Data Dosen
    Route::get('/data-dosen', [KoordinatorController::class, 'getDataDosen'])->name('koordinator.dataDosen');

    //Data User
    Route::get('/data-user', [KoordinatorController::class, 'getDataUser'])->name('koordinator.dataUser');
    Route::put('/koordinator/user/{id}/update-status', [KoordinatorController::class, 'updateStatusUser'])->name('koordinator.updateStatusUser');
    Route::delete('/koordinator/user/{id}', [KoordinatorController::class, 'deleteDataUser'])->name('koordinator.deleteUser');

});