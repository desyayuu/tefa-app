<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {return view('pages.landing_page');})->name('beranda');
Route::get('/layanan-kami', function () {return view('pages.layanan_kami');})->name('layanan-kami');


