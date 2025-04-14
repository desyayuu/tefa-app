<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Landing Pages
Route::get('/', function () {
    return view('pages.landing_page.landing_page');
})->name('beranda');

Route::get('/layanan-kami', function () {
    return view('pages.landing_page.layanan_kami');
})->name('layanan-kami');

// Auth Routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth.custom'])->group(function () {
    // Admin Routes (Koordinator TEFA)
    Route::middleware(['role:1'])->group(function () {
        Route::get('/koordinator/dashboard', [DashboardController::class, 'koordinatorDashboard'])->name('koordinator.dashboard');
    });
    
    // Dosen Routes (Project Leader atau Project Member)
    Route::middleware(['role:2,3'])->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dosen.dashboard');
    });
    
    // Mahasiswa Routes
    Route::middleware(['role:4'])->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('mahasiswa.dashboard');
    });
});