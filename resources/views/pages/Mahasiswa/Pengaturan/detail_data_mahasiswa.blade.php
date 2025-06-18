

@extends('layouts.app')

@section('title', 'TEFA | Profil Mahasiswa')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Mahasiswa.sidebar')
    
    <div class="main-content">
        @include('layouts.Mahasiswa.header')

        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('mahasiswa.getProfilMahasiswa') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Profil Akun
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-profil-mahasiswa">
            @include('pages.Mahasiswa.Pengaturan.data_mahasiswa')
        </div>
        <div class="section-riwayat-proyek-mahasiswa">
            @include('pages.Mahasiswa.Pengaturan.riwayat_proyek_mahasiswa')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Mahasiswa/data_mahasiswa.js')  
@endpush