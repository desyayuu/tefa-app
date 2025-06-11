<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfesionalController;
use App\Http\Middleware\ProfesionalMiddleware;
use App\Http\Controllers\Profesional\DataProyekController;
use App\Http\Controllers\Koordinator\DataAnggotaProyek;
use App\Http\Controllers\Koordinator\DataAnggotaProyekController;
use App\Http\Controllers\Profesional\DataDokumenPenunjangProfesionalController;
use App\Http\Controllers\Profesional\DataTimelineProfesionalController;
use App\Http\Controllers\Profesional\DataProgresProyekProfesionalController;

Route::middleware([ProfesionalMiddleware::class])->prefix('profesional')->group(function () {
    
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ProfesionalController::class, 'dashboard'])->name('profesional.dashboard');
        Route::get('/proyek-data', [ProfesionalController::class, 'getProyekData'])->name('profesional.getProyekData');
        Route::get('/mitra-data', [ProfesionalController::class, 'getMitraData'])->name('profesional.getMitraData');
    });
    
    Route::prefix('data-proyek')->group(function () {
        Route::get('/', [DataProyekController::class, 'getDataProyek'])->name('profesional.dataProyek');
        Route::get('/{id}', [DataProyekController::class, 'detailProyek'])->name('profesional.detailProyek');
        Route::put('/{id}', [DataProyekController::class, 'updateDataProyek'])->name('profesional.updateDataProyek');
    });

    Route::put('/proyek/{id}/project-leader', [DataAnggotaProyekController::class, 'updateProjectLeader'])->name('profesional.updateProjectLeader');
    Route::post('/proyek/{id}/project-member-dosen', [DataAnggotaProyekController::class, 'tambahAnggotaDosen'])->name('profesional.tambahAnggotaDosen');
    Route::delete('/proyek/{proyekId}/hapus-anggota-dosen/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaDosen'])->name('profesional.hapusAnggotaDosen');
    Route::post('/proyek/{id}/project-member-mahasiswa', [DataAnggotaProyekController::class, 'tambahAnggotaMahasiswa'])->name('profesional.tambahAnggotaMahasiswa');
    Route::delete('/proyek/{proyekId}/hapus-anggota-mahasiswa/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaMahasiswa'])->name('profesional.hapusAnggotaMahasiswa');
    Route::post('/proyek/{id}/project-member-profesional', [DataAnggotaProyekController::class, 'tambahAnggotaProfesional'])->name('profesional.tambahAnggotaProfesional');
    Route::delete('/proyek/{proyekId}/hapus-anggota-profesional/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaProfesional'])->name('profesional.hapusAnggotaProfesional');

    //Dokumen Penunjang Proyek 
    Route::post('/proyek/dokumen-penunjang/', [DataDokumenPenunjangProfesionalController::class, 'addDokumenPenunjang'])->name('profesional.addDokumenPenunjang');
    Route::get('/proyek/{id}/dokumen-penunjang', [DataDokumenPenunjangProfesionalController::class, 'getDokumenPenunjang'])->name('profesional.getDokumenPenunjang');
    Route::delete('/proyek/dokumen-penunjang/{id}', [DataDokumenPenunjangProfesionalController::class, 'deleteDokumenPenunjang'])->name('profesional.deleteDokumenPenunjang');
    Route::get('/proyek/dokumen-penunjang/download/{id}', [DataDokumenPenunjangProfesionalController::class, 'downloadDokumenPenunjang'])->name('profesional.downloadDokumenPenunjang');

    // Data Timeline Proyek
    Route::get('/proyek/{id}/timeline', [DataTimelineProfesionalController::class, 'getDataTimeline'])->name('profesional.dataTimeline');
    Route::post('/proyek/timeline', [DataTimelineProfesionalController::class, 'addDataTimeline'])->name('profesional.tambahDataTimeline');
    Route::get('/proyek/timeline/{id}', [DataTimelineProfesionalController::class, 'detailDataTimeline'])->name('profesional.detailDataTimeline');
    Route::put('/proyek/timeline/{id}', [DataTimelineProfesionalController::class, 'updateDataTimeline'])->name('profesional.updateDataTimeline');
    Route::delete('/proyek/timeline/{id}', [DataTimelineProfesionalController::class, 'deleteDataTimeline'])->name('profesional.deleteDataTimeline');

    //Data Progres Proyek
    Route::prefix('progres-proyek')->group(function (){
        Route::get('/{id}/get', [DataProgresProyekProfesionalController::class, 'getProgresByProyek'])->name('profesional.getProgresByProyek');
        Route::get('/{id}/team-members', [DataProgresProyekProfesionalController::class, 'getTeamMembers'])->name('profesional.getTeamMembers');
        Route::get('/{id}/detail', [DataProgresProyekProfesionalController::class, 'getProgresDetail'])->name('profesional.getProgresDetail');
        Route::post('/store', [DataProgresProyekProfesionalController::class, 'storeProgresproyek'])->name('profesional.storeProgres');
        Route::put('/{id}/update', [DataProgresProyekProfesionalController::class, 'updateProgresProyek'])->name('profesional.updateProgres');
        Route::delete('/{id/delete', [DataProgresProyekProfesionalController::class, 'deleteProgresProyek'])->name('profesional.deleteDataProgres');
        //My Progres
        Route::get('/{id}/current-user-info', [DataProgresProyekProfesionalController::class, 'getCurrentUserInfo'])->name('profesional.proyek.current-user-info');
        Route::get('/{id}/my-progres/get', [DataProgresProyekProfesionalController::class, 'getMyProgresByProyek'])->name('profesional.getMyProgresByProyek');
        Route::post('/my-progres/store', [DataProgresProyekProfesionalController::class, 'storeMyProgres'])->name('profesional.storeMyProgres');

    });

});