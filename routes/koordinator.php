<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoordinatorController;
use App\Http\Middleware\KoordinatorMiddleware;
use App\Http\Controllers\Koordinator\DataProfesionalController;
use App\Http\Controllers\Koordinator\DataDosenController;
use App\Http\Controllers\Koordinator\DataUserController;
use App\Http\Controllers\Koordinator\DataMitraController;
use App\Http\Controllers\Koordinator\DataMahasiswaController;
use App\Http\Controllers\Koordinator\DataProyekController;
use App\Http\Controllers\Koordinator\DataDokumenPenunjangController;
use App\Http\Controllers\Koordinator\DataTimelineController;
use App\Http\Controllers\Koordinator\DataAnggotaProyekController;
use App\Http\Controllers\Koordinator\DataLuaranController;


Route::middleware([KoordinatorMiddleware::class])->prefix('koordinator')->group(function () {
    Route::get('/', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');

    //Data Mitra
    Route::get('/data-mitra', [DataMitraController::class, 'getDataMitra'])->name('koordinator.dataMitra');
    Route::post('/data-mitra', [DataMitraController::class, 'storeDataMitra'])->name('koordinator.storeDataMitra');
    Route::post('/tambah-multiple-data-mitra', [DataMitraController::class, 'tambahMultipleDataMitra'])->name('koordinator.tambahMultipleDataMitra');
    Route::put('/mitra/{id}', [DataMitraController::class, 'updateDataMitra'])->name('koordinator.updateDataMitra');
    Route::delete('/mitra/{id}', [DataMitraController::class, 'deleteDataMitra'])->name('koordinator.deleteDataMitra');
    Route::post('/check-email-exists', [DataMitraController::class, 'checkEmailExists'])->name('koordinator.checkEmailExists');

    //Data Proyek
    Route::get('/data-proyek', [DataProyekController::class, 'getDataProyek'])->name('koordinator.dataProyek');
    Route::post('/data-proyek', [DataProyekController::class, 'addDataProyek'])->name('koordinator.tambahDataProyek');
    Route::get('/data-proyek/{id}', [DataProyekController::class, 'getDataProyekById'])->name('koordinator.detailDataProyek');
    Route::put('/data-proyek/{id}', [DataProyekController::class, 'updateDataProyek'])->name('koordinator.updateDataProyek');
    Route::delete('/data-proyek/{id}', [DataProyekController::class, 'deleteDataProyek'])->name('koordinator.deleteDataProyek');

    //Data Dosen
    Route::get('/data-dosen', [DataDosenController::class, 'getDataDosen'])->name('koordinator.dataDosen');
    Route::post('/data-dosen', [DataDosenController::class, 'tambahDataDosen'])->name('koordinator.tambahDataDosen');
    Route::post('/check-email-nidn-exists', [DataDosenController::class, 'checkEmailNidnExists'])->name('koordinator.checkEmailNidnExists');
    Route::put('/dosen/{id}', [DataDosenController::class, 'updateDataDosen'])->name('koordinator.updateDataDosen');
    Route::delete('/dosen/{id}', [DataDosenController::class, 'deleteDataDosen'])->name('koordinator.deleteDataDosen');

    //Data User
    Route::get('/data-user', [DataUserController::class, 'getDataUser'])->name('koordinator.dataUser');
    Route::put('/koordinator/user/{id}/update-status', [DataUserController::class, 'updateStatusUser'])->name('koordinator.updateStatusUser');
    Route::delete('/koordinator/user/{id}', [DataUserController::class, 'deleteDataUser'])->name('koordinator.deleteUser');

    // Display professionals
    Route::get('/data-profesional', [DataProfesionalController::class, 'getDataProfesional'])->name('koordinator.dataProfesional');
    Route::post('/data-profesional', [DataProfesionalController::class, 'tambahDataProfesional'])->name('koordinator.tambahDataProfesional');
    Route::post('/check-email-profesional-exists', [DataProfesionalController::class, 'checkEmailProfesionalExists'])->name('koordinator.checkEmailProfesionalExists');
    Route::put('/profesional/{id}', [DataProfesionalController::class, 'updateDataProfesional'])->name('koordinator.updateDataProfesional');
    Route::delete('/profesional/{id}', [DataProfesionalController::class, 'deleteDataProfesional'])->name('koordinator.deleteDataProfesional');

    //Data Mahasiswa 
    Route::get('/data-mahasiswa', [DataMahasiswaController::class, 'getDataMahasiswa'])->name('koordinator.dataMahasiswa');
    Route::post('/data-mahasiswa', [DataMahasiswaController::class, 'tambahDataMahasiswa'])->name('koordinator.tambahDataMahasiswa');
    Route::post('/check-email-nim-exists', [DataMahasiswaController::class, 'checkEmailNimExists'])->name('koordinator.checkEmailExists');
    Route::put('/mahasiswa/{id}', [DataMahasiswaController::class, 'updateDataMahasiswa'])->name('koordinator.updateDataMahasiswa');
    Route::delete('/mahasiswa/{id}', [DataMahasiswaController::class, 'deleteDataMahasiswa'])->name('koordinator.deleteDataMahasiswa');
    Route::get('/data-mahasiswa/{id}', [DataMahasiswaController::class, 'getDataMahasiswaById'])->name('koordinator.detailDataMahasiswa');

    // Project Leader and Members
    Route::put('/proyek/{id}/project-leader', [DataAnggotaProyekController::class, 'updateProjectLeader'])->name('koordinator.updateProjectLeader');
    Route::post('/proyek/{id}/project-member-dosen', [DataAnggotaProyekController::class, 'tambahAnggotaDosen'])->name('koordinator.tambahAnggotaDosen');
    Route::delete('/proyek/{proyekId}/hapus-anggota-dosen/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaDosen'])->name('koordinator.hapusAnggotaDosen');
    Route::post('/proyek/{id}/project-member-mahasiswa', [DataAnggotaProyekController::class, 'tambahAnggotaMahasiswa'])->name('koordinator.tambahAnggotaMahasiswa');
    Route::delete('/proyek/{proyekId}/hapus-anggota-mahasiswa/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaMahasiswa'])->name('koordinator.hapusAnggotaMahasiswa');
    Route::post('/proyek/{id}/project-member-profesional', [DataAnggotaProyekController::class, 'tambahAnggotaProfesional'])->name('koordinator.tambahAnggotaProfesional');
    Route::delete('/proyek/{proyekId}/hapus-anggota-profesional/{memberId}', [DataAnggotaProyekController::class, 'hapusAnggotaProfesional'])->name('koordinator.hapusAnggotaProfesional');

    //Dokumen Penunjang Proyek 
    Route::post('/proyek/dokumen-penunjang/', [DataDokumenPenunjangController::class, 'addDokumenPenunjang'])->name('koordinator.addDokumenPenunjang');
    Route::get('/proyek/{id}/dokumen-penunjang', [DataDokumenPenunjangController::class, 'getDokumenPenunjang'])->name('koordinator.getDokumenPenunjang');
    Route::delete('/proyek/dokumen-penunjang/{id}', [DataDokumenPenunjangController::class, 'deleteDokumenPenunjang'])->name('koordinator.deleteDokumenPenunjang');
    Route::get('/proyek/dokumen-penunjang/download/{id}', [DataDokumenPenunjangController::class, 'downloadDokumenPenunjang'])->name('koordinator.downloadDokumenPenunjang');

    //Data Timeline
    Route::get('/proyek/{id}/timeline', [DataTimelineController::class, 'getDataTimeline'])->name('koordinator.dataTimeline');
    Route::post('/proyek/timeline', [DataTimelineController::class, 'addDataTimeline'])->name('koordinator.tambahDataTimeline');
    Route::get('/proyek/timeline/{id}', [DataTimelineController::class, 'detailDataTimeline'])->name('koordinator.detailDataTimeline');
    Route::put('/proyek/timeline/{id}', [DataTimelineController::class, 'updateDataTimeline'])->name('koordinator.updateDataTimeline');
    Route::delete('/proyek/timeline/{id}', [DataTimelineController::class, 'deleteDataTimeline'])->name('koordinator.deleteDataTimeline');

    //Data Dokumentasi Proyek
    // Show luaran page
Route::get('/proyek/{id}/luaran', [DataLuaranController::class, 'getDataLuaranDokumentasi'])->name('koordinator.getDataLuaranDokumentasi');
Route::post('/proyek/luaran', [DataLuaranController::class, 'saveLuaranProyek'])->name('koordinator.updateDataLuaran');
Route::post('/proyek/dokumentasi', [DataLuaranController::class, 'uploadDokumentasi'])->name('koordinator.addDokumentasi');
Route::delete('/proyek/dokumentasi/{id}', [DataLuaranController::class, 'deleteDokumentasi'])->name('koordinator.deleteDokumentasi');

});