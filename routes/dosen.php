<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Middleware\DosenMiddleware;
use App\Http\Controllers\Dosen\DataProyekController;
use App\Http\Controllers\Koordinator\DataAnggotaProyek;
use App\Http\Controllers\Koordinator\DataAnggotaProyekController;
use App\Http\Controllers\Dosen\DataDokumenPenunjangDosenController;
use App\Http\Controllers\Dosen\DataTimelineDosenController;
use App\Http\Controllers\Dosen\DataProgresProyekDosenController;

Route::middleware([DosenMiddleware::class])->prefix('dosen')->group(function () {
    
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
        Route::get('/proyek-data', [DosenController::class, 'getProyekData'])->name('dosen.getProyekData');
        Route::get('/mitra-data', [DosenController::class, 'getMitraData'])->name('dosen.getMitraData');
    });
    
    Route::prefix('data-proyek')->group(function () {
        Route::get('/', [DataProyekController::class, 'getDataProyek'])->name('dosen.dataProyek');
        Route::get('/{id}', [DataProyekController::class, 'detailProyek'])->name('dosen.detailProyek');
        Route::put('/{id}', [DataProyekController::class, 'updateDataProyek'])->name('dosen.updateDataProyek');
    });

    Route::put('/proyek/{id}/project-leader', [DataAnggotaProyekController::class, 'updateProjectLeader'])->name('dosen.updateProjectLeader');
    Route::post('/proyek/{id}/project-member-dosen', [DataAnggotaProyekController::class, 'tambahAnggotaDosen'])->name('dosen.tambahAnggotaDosen');
    Route::delete('/proyek/{proyekId}/hapus-anggota-dosen/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaDosen'])->name('dosen.hapusAnggotaDosen');
    Route::post('/proyek/{id}/project-member-mahasiswa', [DataAnggotaProyekController::class, 'tambahAnggotaMahasiswa'])->name('dosen.tambahAnggotaMahasiswa');
    Route::delete('/proyek/{proyekId}/hapus-anggota-mahasiswa/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaMahasiswa'])->name('dosen.hapusAnggotaMahasiswa');
    Route::post('/proyek/{id}/project-member-profesional', [DataAnggotaProyekController::class, 'tambahAnggotaProfesional'])->name('dosen.tambahAnggotaProfesional');
    Route::delete('/proyek/{proyekId}/hapus-anggota-profesional/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaProfesional'])->name('dosen.hapusAnggotaProfesional');

    //Dokumen Penunjang Proyek 
    Route::post('/proyek/dokumen-penunjang/', [DataDokumenPenunjangDosenController::class, 'addDokumenPenunjang'])->name('dosen.addDokumenPenunjang');
    Route::get('/proyek/{id}/dokumen-penunjang', [DataDokumenPenunjangDosenController::class, 'getDokumenPenunjang'])->name('dosen.getDokumenPenunjang');
    Route::delete('/proyek/dokumen-penunjang/{id}', [DataDokumenPenunjangDosenController::class, 'deleteDokumenPenunjang'])->name('dosen.deleteDokumenPenunjang');
    Route::get('/proyek/dokumen-penunjang/download/{id}', [DataDokumenPenunjangDosenController::class, 'downloadDokumenPenunjang'])->name('dosen.downloadDokumenPenunjang');

    Route::get('/proyek/{id}/timeline', [DataTimelineDosenController::class, 'getDataTimeline'])->name('dosen.dataTimeline');
    Route::post('/proyek/timeline', [DataTimelineDosenController::class, 'addDataTimeline'])->name('dosen.tambahDataTimeline');
    Route::get('/proyek/timeline/{id}', [DataTimelineDosenController::class, 'detailDataTimeline'])->name('dosen.detailDataTimeline');
    Route::put('/proyek/timeline/{id}', [DataTimelineDosenController::class, 'updateDataTimeline'])->name('dosen.updateDataTimeline');
    Route::delete('/proyek/timeline/{id}', [DataTimelineDosenController::class, 'deleteDataTimeline'])->name('dosen.deleteDataTimeline');

    //Data Progres Proyek
    Route::get('/proyek/{id}/progres-proyek', [DataProgresProyekDosenController::class, 'getProgresByProyek'])->name('dosen.getProgresByProyek');
    Route::get('/proyek/{id}/team-members', [DataProgresProyekDosenController::class, 'getTeamMembers'])->name('getTeamMembers');
    Route::get('/proyek/progres-proyek/{id}', [DataProgresProyekDosenController::class, 'getProgresDetail'])->name('getProgresDetail');
    Route::post('/proyek/progres-proyek', [DataProgresProyekDosenController::class, 'store'])->name('addProgres');
    Route::get('/proyek/progres-proyek/{id}', [DataProgresProyekDosenController::class, 'getProgresDetail'])->name('getProgresDetail');
    Route::put('/proyek/progres-proyek/{id}', [DataProgresProyekDosenController::class, 'update'])->name('updateProgres');
    Route::delete('/proyek/progres-proyek/{id}', [DataProgresProyekDosenController::class, 'deleteDataProgresProyek'])->name('dosen.deleteDataProgres');
    Route::get('/proyek/{id}/current-user-info', [DataProgresProyekDosenController::class, 'getCurrentUserInfo'])->name('dosen.proyek.current-user-info');
    Route::get('/proyek/{id}/my-progres', [DataProgresProyekDosenController::class, 'getMyProgresByProyek'])->name('dosen.getMyProgresByProyek');
    Route::post('/proyek/my-progres', [DataProgresProyekDosenController::class, 'storeMyProgres'])->name('dosen.storeMyProgres');

});