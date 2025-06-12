<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\MahasiswaMiddleware;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\Mahasiswa\DataProyekController;
use App\Http\Controllers\Mahasiswa\DataDokumenPenunjangMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataTimelineMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataProgresProyekMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataMahasiswaController;
use App\Http\Controllers\Mahasiswa\DataBidangKeahlianController;

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

    Route::prefix('profil')->group(function () {
        Route::get('/', [DataMahasiswaController::class, 'getProfil'])->name('mahasiswa.getProfilMahasiswa');
        Route::put('/update-profil-mahasiswa', [DataMahasiswaController::class, 'updateProfil'])->name('mahasiswa.updateProfilMahasiswa');
        Route::post('/check-email-nim-exists', [DataMahasiswaController::class, 'checkEmailNimExists'])->name('mahasiswa.checkEmailNimExists');
    });

    Route::prefix('bidang-keahlian')->group(function () {
        Route::get('/', [DataBidangKeahlianController::class, 'index'])->name('mahasiswa.portofolio');
        Route::put('/update', [DataBidangKeahlianController::class, 'updateBidangKeahlianMahasiswa'])->name('mahasiswa.updateBidangKeahlian');
        Route::get('/get', [DataBidangKeahlianController::class, 'getBidangKeahlianMahasiswa'])->name('mahasiswa.getBidangKeahlian');
        Route::get('/all', [DataBidangKeahlianController::class, 'getAllBidangKeahlian'])->name('mahasiswa.getAllBidangKeahlian');
    });

    Route::prefix('portofolio')->group(function () {
        Route::post('/store', [DataBidangKeahlianController::class, 'tambahPortofolioMahasiswa'])->name('mahasiswa.portofolio.tambah');
        Route::put('/update/{id}', [DataBidangKeahlianController::class, 'updatePortofolioMahasiswa'])->name('mahasiswa.portofolio.update');
        Route::delete('/delete/{id}', [DataBidangKeahlianController::class, 'deletePortofolioMahasiswa'])->name('mahasiswa.portofolio.delete');
        Route::get('/detail/{id}', [DataBidangKeahlianController::class, 'getPortofolioMahasiswaById'])->name('mahasiswa.portofolio.detail');
    });
});