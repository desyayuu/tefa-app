@extends('layouts.app')

@section('title', 'Dashboard Koordinator')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Dashboard Dosen</h5>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm">Logout</button>
                        </form>
                    </div>
                    <div class="card-header bg-dark text-white">
                        <h5 class="m-0">Dashboard Koordinator</h5>
                    </div>
                    <div class="card-body">
                        <h3>Selamat Datang, {{ session('nama') }}!</h3>
                        <p>Anda telah login sebagai Koordinator.</p>
                        
                        <!-- Konten Dashboard Koordinator -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Manajemen Proyek</h5>
                                        <p class="card-text">Kelola semua proyek TEFA</p>
                                        <a href="#" class="btn btn-dark">Kelola Proyek</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Manajemen Dosen</h5>
                                        <p class="card-text">Kelola data dosen</p>
                                        <a href="#" class="btn btn-dark">Kelola Dosen</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Manajemen Mahasiswa</h5>
                                        <p class="card-text">Kelola data mahasiswa</p>
                                        <a href="#" class="btn btn-dark">Kelola Mahasiswa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Laporan & Statistik</h5>
                                        <p class="card-text">Lihat laporan dan statistik proyek TEFA</p>
                                        <a href="#" class="btn btn-dark">Lihat Laporan</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Pengaturan Sistem</h5>
                                        <p class="card-text">Kelola pengaturan sistem</p>
                                        <a href="#" class="btn btn-dark">Pengaturan</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    @include('layouts.footer')
@endsection