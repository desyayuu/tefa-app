<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\JoinTefaController;


//Landing Page
// Route::get('/', function () {return view('pages.landing_page');})->name('beranda');
Route::get('/', [LandingPageController::class, 'landingPage'])->name('beranda');
Route::get('/layanan-kami', [LandingPageController::class, 'layananKami'])->name('layanan-kami');
Route::get('/register-dosen', [AuthController::class, 'showRegisterDosenForm'])->name('register-dosen');
Route::get('/register-profesional', [AuthController::class, 'showRegisterProfesionalForm'])->name('register-profesional');
Route::get('/login-landing-page', function () {return view('pages.login'); })->name('login-landing-page');

// Portofolio Proyek
Route::get('portofolio-proyek', [LandingPageController::class, 'getAllProyek'])->name('get-portofolio-proyek');
Route::get('portofolio-proyek/{id}', [LandingPageController::class, 'getProyekDetail'])->name('detail-portofolio-proyek');

// Pesan Pengunjung
Route::post('join-proyek', [JoinTefaController::class, 'tambahPesanPengunjung'])->name('join-proyek');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register-dosen', [AuthController::class, 'registerDosen'])->name('register-dosen.submit');
Route::post('/register-profesional', [AuthController::class, 'registerProfesional'])->name('register-profesional.submit');



require __DIR__.'/dosen.php';
require __DIR__.'/mahasiswa.php';
require __DIR__.'/koordinator.php';
require __DIR__.'/profesional.php';
