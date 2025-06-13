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
use App\Http\Controllers\Koordinator\DataProgresProyekController;
use App\Http\Controllers\Koordinator\DataKeuangan\DataKeuanganTefaController;
use App\Http\Controllers\Koordinator\DataKeuangan\DataMasukKeuanganProyekController;
use App\Http\Controllers\Koordinator\DataKeuangan\DataKeluarKeuanganProyekController;
use App\Http\Controllers\Koordinator\DataSubJenisKategoriTransaksiController;
use App\Http\Controllers\Koordinator\DataBidangKeahlianMahasiswaController;
use App\Http\Controllers\JoinTefaController;

Route::middleware([KoordinatorMiddleware::class])->prefix('koordinator')->group(function () {
    Route::get('/', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard'])->name('koordinator.dashboard');
    Route::get('/keuangan-data', [KoordinatorController::class, 'getKeuanganData'])->name('koordinator.getKeuanganData');
    Route::get('/data-proyek', [KoordinatorController::class, 'getProyekData'])->name('koordinator.getProyekData');
    Route::get('/keuangan-summary', [KoordinatorController::class, 'getKeuanganSummary'])->name('getKeuanganSummary');
    Route::get('/koordinator/proyek-data', [KoordinatorController::class, 'getProyekData'])->name('koordinator.getProyekData');
    Route::get('/koordinator/mitra-data', [KoordinatorController::class, 'getMitraData'])->name('koordinator.getMitraData');
    Route::get('/koordinator/dashboard-data', [KoordinatorController::class, 'getDashboardData'])->name('koordinator.getDashboardData');


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
    Route::get('/data-dosen/{id}', [DataDosenController::class, 'getDataDosenById'])->name('koordinator.detailDataDosen');

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
    Route::get('/data-profesional/{id}', [DataProfesionalController::class, 'getDataProfesionalById'])->name('koordinator.detailDataProfesional');

    //Data Mahasiswa 
    Route::get('/data-mahasiswa', [DataMahasiswaController::class, 'getDataMahasiswa'])->name('koordinator.dataMahasiswa');
    Route::post('/data-mahasiswa', [DataMahasiswaController::class, 'tambahDataMahasiswa'])->name('koordinator.tambahDataMahasiswa');
    Route::post('/check-email-nim-exists', [DataMahasiswaController::class, 'checkEmailNimExists'])->name('koordinator.checkEmailExists');
    Route::put('/mahasiswa/{id}', [DataMahasiswaController::class, 'updateDataMahasiswa'])->name('koordinator.updateDataMahasiswa');
    Route::delete('/mahasiswa/{id}', [DataMahasiswaController::class, 'deleteDataMahasiswa'])->name('koordinator.deleteDataMahasiswa');
    Route::get('/data-mahasiswa/{id}', [DataMahasiswaController::class, 'getDataMahasiswaById'])->name('koordinator.detailDataMahasiswa');
    Route::put('/mahasiswa/{id}/bidang-keahlian', [DataMahasiswaController::class, 'updateBidangKeahlianMahasiswa'])->name('koordinator.updateBidangKeahlianMahasiswa');
    Route::get('/mahasiswa/{id}/bidang-keahlian', [DataMahasiswaController::class, 'getBidangKeahlianMahasiswa'])->name('koordinator.getBidangKeahlianMahasiswa');
    Route::get('/data-mahasiswa/portofolio/detail/{id}', [DataMahasiswaController::class, 'getPortofolioMahasiswaById'])->name('koordinator.portofolio.detail');
    Route::post('/data-mahasiswa/portofolio/tambah', [DataMahasiswaController::class, 'tambahPortofolioMahasiswa'])->name('koordinator.portofolio.tambah');
    Route::put('/data-mahasiswa/portofolio/update/{id}', [DataMahasiswaController::class, 'updatePortofolioMahasiswa'])->name('koordinator.portofolio.update');
    Route::delete('/data-mahasiswa/portofolio/delete/{id}', [DataMahasiswaController::class, 'deletePortofolioMahasiswa'])->name('koordinator.portofolio.delete');
    
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
    Route::get('/proyek/{id}/luaran', [DataLuaranController::class, 'getDataLuaranDokumentasi'])->name('koordinator.getDataLuaranDokumentasi');
    Route::post('/proyek/luaran', [DataLuaranController::class, 'saveLuaranProyek'])->name('koordinator.updateDataLuaran');
    Route::post('/proyek/dokumentasi', [DataLuaranController::class, 'uploadDokumentasi'])->name('koordinator.addDokumentasi');
    Route::delete('/proyek/dokumentasi/{id}', [DataLuaranController::class, 'deleteDokumentasi'])->name('koordinator.deleteDokumentasi');

    //Data Progres Proyek
    Route::get('/proyek/{id}/progres-proyek', [DataProgresProyekController::class, 'getProgresByProyek'])->name('koordinator.getProgresByProyek');
    Route::get('/proyek/{id}/team-members', [DataProgresProyekController::class, 'getTeamMembers'])->name('getTeamMembers');
    Route::get('/proyek/progres-proyek/{id}', [DataProgresProyekController::class, 'getProgresDetail'])->name('getProgresDetail');
    Route::post('/proyek/progres-proyek', [DataProgresProyekController::class, 'store'])->name('addProgres');
    Route::get('/proyek/progres-proyek/{id}', [DataProgresProyekController::class, 'getProgresDetail'])->name('getProgresDetail');
    Route::put('/proyek/progres-proyek/{id}', [DataProgresProyekController::class, 'update'])->name('updateProgres');
    Route::delete('/proyek/progres-proyek/{id}', [DataProgresProyekController::class, 'deleteDataProgresProyek'])->name('koordinator.deleteDataProgres');

    //Data Keuangan TEFA 
    Route::get('/data-keuangan-tefa', [DataKeuanganTefaController::class, 'getDataKeuanganTefa'])->name('koordinator.dataKeuanganTefa');
    Route::post('/keuangan-tefa/store', [DataKeuanganTefaController::class, 'store'])->name('koordinator.storeKeuanganTefa');
    Route::post('/keuangan-tefa/store-with-files', [DataKeuanganTefaController::class, 'storeWithFiles']);
    Route::get('/keuangan-tefa/data-proyek', [DataKeuanganTefaController::class, 'getProyek'])->name('koordinator.getProyekKeuanganTefa');
    Route::get('/keuangan-tefa/jenis-keuangan-tefa', [DataKeuanganTefaController::class, 'getJenisKeuanganTefa'])->name('koordinator.getJenisKeuanganTefa');
    Route::get('/keuangan-tefa/get-sub-jenis-transaksi', [DataKeuanganTefaController::class, 'getSubJenisTransaksi'])->name('koordinator.getSubJenisTransaksi');
    Route::get('/keuangan-tefa/jenis-transaksi', [DataKeuanganTefaController::class, 'getJenisTransaksi'])->name('koordinator.getJenisTransaksi');
    Route::get('/keuangan-tefa/get-summary', [DataKeuanganTefaController::class, 'getSummary']);
    Route::get('/keuangan-tefa/{id}', [DataKeuanganTefaController::class, 'getKeuanganTefaById']);
    Route::post('/keuangan-tefa/update/{id}', [DataKeuanganTefaController::class, 'update']);
    Route::delete('/keuangan-tefa/delete/{id}', [DataKeuanganTefaController::class, 'destroy']);

    Route::prefix('data-masuk-keuangan-proyek')->group(function () {
        Route::get('/', [DataMasukKeuanganProyekController::class, 'getDataProyek'])->name('koordinator.dataMasukKeuanganProyek');
        Route::get('/get-kategori-pemasukan', [DataMasukKeuanganProyekController::class, 'getKategoriPemasukanForFilter'])->name('koordinator.getKategoriPemasukan');
        Route::get('/{proyekId}', [DataMasukKeuanganProyekController::class, 'getDataMasukKeuanganProyek'])->name('koordinator.detailDataMasukKeuanganProyek');
        Route::get('/{proyekId}/transaksi', [DataMasukKeuanganProyekController::class, 'getDataTransaksiProyek'])->name('koordinator.getDataTransaksiProyek');
        Route::get('/{proyekId}/transaksi/{transaksiId}/detail', [DataMasukKeuanganProyekController::class, 'getTransaksiDetailForEdit'])->name('transaksi.detail');
        Route::post('/tambah-transaksi', [DataMasukKeuanganProyekController::class, 'tambahTransaksiPemasukan'])->name('tambah-transaksi');
        Route::post('/store-with-files', [DataMasukKeuanganProyekController::class, 'storeWithFiles'])->name('store-with-files');
        Route::put('/update-transaksi/{transaksiId}', [DataMasukKeuanganProyekController::class, 'updateTransaksiPemasukan'])->name('update-transaksi');
        Route::delete('/hapus-transaksi/{transaksiId}', [DataMasukKeuanganProyekController::class, 'hapusTransaksi'])->name('hapus-transaksi');
        Route::get('/download/{fileName}', [DataMasukKeuanganProyekController::class, 'downloadBuktiTransaksi'])->name('download-bukti')->where('fileName', '.*');
        Route::get('/sub-jenis-transaksi', [DataMasukKeuanganProyekController::class, 'getSubJenisTransaksi'])->name('sub-jenis-transaksi');
        Route::get('/{proyekId}/summary', [DataMasukKeuanganProyekController::class, 'getSummary']);
    });


    // Data Keluar Keuangan Proyek
    Route::prefix('data-keluar-keuangan-proyek')->group(function () {
        Route::get('/', [DataKeluarKeuanganProyekController::class, 'getDataProyek'])->name('koordinator.dataKeluarKeuanganProyek');
        Route::get('/get-kategori-pengeluaran', [DataKeluarKeuanganProyekController::class, 'getKategoriPengeluaranForFilter'])->name('koordinator.getKategoriPengeluaran');
        Route::get('/{proyekId}', [DataKeluarKeuanganProyekController::class, 'getDataKeluarKeuanganProyek'])->name('koordinator.detailDataKeluarKeuanganProyek');
        Route::get('/{proyekId}/transaksi', [DataKeluarKeuanganProyekController::class, 'getDataTransaksiProyek'])->name('koordinator.getDataTransaksiProyek');
        Route::get('/{proyekId}/transaksi/{transaksiId}/detail', [DataKeluarKeuanganProyekController::class, 'getTransaksiDetailForEdit'])->name('transaksi.detail');
        Route::post('/tambah-transaksi', [DataKeluarKeuanganProyekController::class, 'tambahTransaksiPengeluaran'])->name('tambah-transaksi');
        Route::post('/store-with-files', [DataKeluarKeuanganProyekController::class, 'storeWithFiles'])->name('store-with-files');
        Route::put('/update-transaksi/{transaksiId}', [DataKeluarKeuanganProyekController::class, 'updateTransaksiPengeluaran'])->name('update-transaksi');
        Route::delete('/hapus-transaksi/{transaksiId}', [DataKeluarKeuanganProyekController::class, 'hapusTransaksi'])->name('hapus-transaksi');
        Route::get('/download/{fileName}', [DataKeluarKeuanganProyekController::class, 'downloadBuktiTransaksi'])->name('download-bukti')->where('fileName', '.*');
        Route::get('/sub-jenis-transaksi', [DataKeluarKeuanganProyekController::class, 'getSubJenisTransaksi'])->name('sub-jenis-transaksi');
        Route::get('/{proyekId}/summary', [DataKeluarKeuanganProyekController::class, 'getSummary']);
    });

    Route::prefix('data-sub-kategori-transaksi')->group(function () {
        Route::get('/', [DataSubJenisKategoriTransaksiController::class, 'getDataSubJenisKategoriTransaksi'])->name('koordinator.getSubKategoriTransaksi');
        Route::get('/data-sub-kategori-transaksi/search', [DataSubJenisKategoriTransaksiController::class, 'getDataSubJenisKategoriTransaksi'])->name('getSubKategoriTransaksi');
        Route::post('/store-sub-kategori-transaksi', [DataSubJenisKategoriTransaksiController::class, 'storeDataSubJenisTransaksi'])->name('koordinator.storeDataSubJenisKategori');
        Route::get('/get-sub-jenis-kategori/{id}', [DataSubJenisKategoriTransaksiController::class, 'detailDataSubJenisTransaksi'])->name('koordinator.getDetailSubJenisKategori');
        Route::put('/update-sub-jenis-kategori/{id}', [DataSubJenisKategoriTransaksiController::class, 'updateDataSubJenisTransaksi'])->name('koordinator.updateSubJenisKategori');
        Route::delete('/delete-sub-jenis-kategori/{id}', [DataSubJenisKategoriTransaksiController::class, 'deleteDataSubJenisTransaksi'])->name('koordinator.deleteSubJenisKategori');
        Route::get('/get-jenis-transaksi', [DataSubJenisKategoriTransaksiController::class, 'getJenisTransaksi'])->name('koordinator.getJenisTransaksi');
        Route::get('/get-jenis-keuangan-tefa', [DataSubJenisKategoriTransaksiController::class, 'getJenisKeuanganTefa'])->name('koordinator.getJenisKeuanganTefa');
    });

    Route::prefix('pesan-pengujung')->group(function () {
        Route::get('/', [JoinTefaController::class, 'getPesanPengunjung'])->name('koordinator.getPesanPengunjung');
        Route::post('/tambah-pesan', [JoinTefaController::class, 'tambahPesanPengunjung'])->name('koordinator.tambahPesanPengunjung');
        Route::put('/update-pesan/{id}', [JoinTefaController::class, 'updatePesanPengunjung'])->name('koordinator.updatePesanPengunjung');
        Route::delete('/hapus-pesan/{id}', [JoinTefaController::class, 'hapusPesanPengunjung'])->name('koordinator.hapusPesanPengunjung');
    });

});