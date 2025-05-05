<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoordinatorController;
use App\Http\Middleware\KoordinatorMiddleware;
use App\Http\Controllers\Koordinator\DataProfesionalController;
use App\Http\Controllers\Koordinator\DataDosenController;
use App\Http\Controllers\Koordinator\DataUserController;
use App\Http\Controllers\Koordinator\DataMitraController;
use App\Http\Controllers\Koordinator\DataMahasiswaController;

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
    Route::get('/data-proyek', [KoordinatorController::class, 'getDataProyek'])->name('koordinator.dataProyek');
    Route::post('/data-proyek', [KoordinatorController::class, 'tambahDataProyek'])->name('proyek.tambahDataProyek');
    Route::get('/data-proyek/{id}', [KoordinatorController::class, 'getDataProyekById'])->name('proyek.detailDataProyek');

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
});