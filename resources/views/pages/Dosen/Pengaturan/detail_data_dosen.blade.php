

@extends('layouts.app')

@section('title', 'TEFA | Profil Dosen')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Dosen.sidebar')
    
    <div class="main-content">
        @include('layouts.Dosen.header')

        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dosen.getProfilDosen') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Profil Akun
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-profil-dosen">
            @include('pages.Dosen.Pengaturan.data_dosen')
        </div>
        <div class="section-riwayat-proyek-dosen">
            @include('pages.Dosen.Pengaturan.riwayat_proyek_dosen')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Dosen/data_dosen.js')  
@endpush