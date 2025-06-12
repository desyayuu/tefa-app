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
use App\Http\Controllers\Dosen\DataKeluarKeuanganProyekDosenController;
use App\Http\Controllers\Dosen\DataDosenController;

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
    Route::prefix('progres-proyek')->group(function (){
        Route::get('/{id}/get', [DataProgresProyekDosenController::class, 'getProgresByProyek'])->name('dosen.getProgresByProyek');
        Route::get('/{id}/team-members', [DataProgresProyekDosenController::class, 'getTeamMembers'])->name('dosen.getTeamMembers');
        Route::get('/{id}/detail', [DataProgresProyekDosenController::class, 'getProgresDetail'])->name('dosen.getProgresDetail');
        Route::post('/store', [DataProgresProyekDosenController::class, 'storeProgresProyek'])->name('dosen.storeProgres');
        Route::put('/{id}/update', [DataProgresProyekDosenController::class, 'updateProgresProyek'])->name('dosen.updateProgres');
        Route::delete('{id}/delete', [DataProgresProyekDosenController::class, 'deleteDataProgresProyek'])->name('dosen.deleteDataProgres');
        //My Progres
        Route::get('{id}/current-user-info', [DataProgresProyekDosenController::class, 'getCurrentUserInfo'])->name('dosen.proyek.current-user-info');
        Route::get('{id}/my-progres/get', [DataProgresProyekDosenController::class, 'getMyProgresByProyek'])->name('dosen.getMyProgresByProyek');
        Route::post('/my-progres/store', [DataProgresProyekDosenController::class, 'storeMyProgres'])->name('dosen.storeMyProgres');
    });
    

    Route::prefix('data-keluar-keuangan-proyek')->group(function () {
        Route::get('/', [DataKeluarKeuanganProyekDosenController::class, 'getDataProyek'])->name('dosen.dataKeluarKeuanganProyek');
        Route::get('/get-kategori-pengeluaran', [DataKeluarKeuanganProyekDosenController::class, 'getKategoriPengeluaranForFilter'])->name('dosen.getKategoriPengeluaran');
        Route::get('/{proyekId}', [DataKeluarKeuanganProyekDosenController::class, 'getDataKeluarKeuanganProyek'])->name('dosen.detailDataKeluarKeuanganProyek');
        Route::get('/{proyekId}/transaksi', [DataKeluarKeuanganProyekDosenController::class, 'getDataTransaksiProyek'])->name('dosen.getDataTransaksiProyek');
        Route::get('/{proyekId}/transaksi/{transaksiId}/detail', [DataKeluarKeuanganProyekDosenController::class, 'getTransaksiDetailForEdit'])->name('transaksi.detail');
        Route::post('/tambah-transaksi', [DataKeluarKeuanganProyekDosenController::class, 'tambahTransaksiPengeluaran'])->name('tambah-transaksi');
        Route::post('/store-with-files', [DataKeluarKeuanganProyekDosenController::class, 'storeWithFiles'])->name('store-with-files');
        Route::put('/update-transaksi/{transaksiId}', [DataKeluarKeuanganProyekDosenController::class, 'updateTransaksiPengeluaran'])->name('update-transaksi');
        Route::delete('/hapus-transaksi/{transaksiId}', [DataKeluarKeuanganProyekDosenController::class, 'hapusTransaksi'])->name('hapus-transaksi');
        Route::get('/download/{fileName}', [DataKeluarKeuanganProyekDosenController::class, 'downloadBuktiTransaksi'])->name('download-bukti')->where('fileName', '.*');
        Route::get('/sub-jenis-transaksi', [DataKeluarKeuanganProyekDosenController::class, 'getSubJenisTransaksi'])->name('sub-jenis-transaksi');
        Route::get('/{proyekId}/summary', [DataKeluarKeuanganProyekDosenController::class, 'getSummary']);
    });

    Route::prefix('profil')->group(function () {
        Route::get('/', [DataDosenController::class, 'getProfil'])->name('dosen.getProfilDosen');
        Route::put('/update-profil-dosen', [DataDosenController::class, 'updateProfil'])->name('dosen.updateProfilDosen');
        Route::post('/check-email-nidn-exists', [DataDosenController::class, 'checkEmailNidnExists'])->name('dosen.checkEmailNidnExists');
    });

});