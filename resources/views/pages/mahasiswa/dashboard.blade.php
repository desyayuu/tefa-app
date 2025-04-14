@extends('layouts.app')

@section('title', 'TEFA JTI POLINEMA')

@section('content')
<div class="min-vh-100 d-flex flex-column">
    
    <!-- {{-- Header (file terpisah) --}}
    @include('layouts.authenticated.header.mahasiswa_header') -->

    {{-- Sidebar dan Konten --}}
    <div class="d-flex flex-grow-1">
        {{-- Sidebar kiri --}}
        <div class="bg-white border-end" style="width: 250px;">
            @include('layouts.authenticated.sidebar.mahasiswa_sidebar')
        </div>

        {{-- Konten utama --}}
        <div class="flex-grow-1 p-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    Aktivitas Saya
                </div>
                <div class="card-body">
                    <p>Ini adalah dashboard khusus untuk mahasiswa.</p>
                    <p>Di halaman ini akan ditampilkan proyek-proyek TEFA yang Anda ikuti sebagai anggota tim.</p>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>
@endsection
