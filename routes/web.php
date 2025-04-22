<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;


//Landing Page
// Route::get('/', function () {return view('pages.landing_page');})->name('beranda');
Route::get('/', [LandingPageController::class, 'landingPage'])->name('beranda');
Route::get('/layanan-kami', [LandingPageController::class, 'layananKami'])->name('layanan-kami');
Route::get('/register-landing-page', function () {return view('pages.register'); })->name('register-landing-page');
Route::get('/login-landing-page', function () {return view('pages.login'); })->name('login-landing-page');


// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


require __DIR__.'/dosen.php';
require __DIR__.'/mahasiswa.php';
require __DIR__.'/koordinator.php';
