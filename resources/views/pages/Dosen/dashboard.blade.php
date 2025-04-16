@extends('layouts.app')

@section('title', 'Dashboard Dosen')

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
                    <div class="card-body">
                        <h3>Selamat Datang, {{ session('nama') }}!</h3>
                        <p>Anda telah login sebagai Dosen.</p>
                        
                        <!-- Konten Dashboard Dosen -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Proyek TEFA</h5>
                                        <p class="card-text">Kelola proyek TEFA yang Anda ampu</p>
                                        <a href="#" class="btn btn-primary">Kelola Proyek</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title">Bimbingan Mahasiswa</h5>
                                        <p class="card-text">Kelola mahasiswa yang Anda bimbing</p>
                                        <a href="#" class="btn btn-primary">Lihat Mahasiswa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    @include('layouts.footer')
@endsection