<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MahasiswaMiddleware;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\Mahasiswa\DataProyekController;
use App\Http\Controllers\Mahasiswa\DataDokumenPenunjangMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataTimelineMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataProgresProyekMahasiswaController;

Route::middleware([MahasiswaMiddleware::class])->prefix('mahasiswa')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
        Route::get('/proyek-data', [MahasiswaController::class, 'getProyekData'])->name('mahasiswa.getProyekData');
        Route::get('/mitra-data', [MahasiswaController::class, 'getMitraData'])->name('mahasiswa.getMitraData');
    });
    
    Route::prefix('data-proyek')->group(function () {
        Route::get('/', [DataProyekController::class, 'getDataProyek'])->name('mahasiswa.dataProyek');
        Route::get('/{id}', [DataProyekController::class, 'detailProyek'])->name('mahasiswa.detailProyek');
        Route::put('/{id}', [DataProyekController::class, 'updateDataProyek'])->name('mahasiswa.updateDataProyek');
    });

    //Dokumen Penunjang Proyek 
    Route::post('/proyek/dokumen-penunjang/', [DataDokumenPenunjangMahasiswaController::class, 'addDokumenPenunjang'])->name('mahasiswa.addDokumenPenunjang');
    Route::get('/proyek/{id}/dokumen-penunjang', [DataDokumenPenunjangMahasiswaController::class, 'getDokumenPenunjang'])->name('mahasiswa.getDokumenPenunjang');
    Route::delete('/proyek/dokumen-penunjang/{id}', [DataDokumenPenunjangMahasiswaController::class, 'deleteDokumenPenunjang'])->name('mahasiswa.deleteDokumenPenunjang');
    Route::get('/proyek/dokumen-penunjang/download/{id}', [DataDokumenPenunjangMahasiswaController::class, 'downloadDokumenPenunjang'])->name('mahasiswa.downloadDokumenPenunjang');

    // Data Timeline Proyek
    Route::get('/proyek/{id}/timeline', [DataTimelineMahasiswaController::class, 'getDataTimeline'])->name('mahasiswa.dataTimeline');
    Route::get('/proyek/timeline/{id}', [DataTimelineMahasiswaController::class, 'detailDataTimeline'])->name('mahasiswa.detailDataTimeline');

    Route::prefix('progres-proyek')->group(function () {
        Route::get('/{id}/team-members', [DataProgresProyekMahasiswaController::class, 'getTeamMembers'])->name('mahasiswa.getTeamMembers');
        Route::get('/{id}/get', [DataProgresProyekMahasiswaController::class, 'getProgresByProyek'])->name('mahasiswa.getProgresByProyek');
        Route::get('/{id}/detail', [DataProgresProyekMahasiswaController::class, 'getProgresDetail'])->name('mahasiswa.getProgresDetail');
        Route::put('/{id}/update', [DataProgresProyekMahasiswaController::class, 'updateProgresProyek'])->name('mahasiswa.updateProgresProyek');
        Route::delete('/{id}/delete', [DataProgresProyekMahasiswaController::class, 'deleteProgresProyek'])->name('mahasiswa.deleteDataProgres');
        //My Progres
        Route::get('/{id}/current-user-info', [DataProgresProyekMahasiswaController::class, 'getCurrentUserInfo'])->name('mahasiswa.proyek.current-user-info');//for auto assigned
        Route::get('/{id}/my-progres/get', [DataProgresProyekMahasiswaController::class, 'getMyProgresByProyek'])->name('mahasiswa.getMyProgresByProyek');
        Route::post('/my-progres/store', [DataProgresProyekMahasiswaController::class, 'storeMyProgres'])->name('mahasiswa.storeMyProgres');

    });

});