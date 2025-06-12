

@extends('layouts.app')

@section('title', 'TEFA | Profil Profesional')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Profesional.sidebar')
    
    <div class="main-content">
        @include('layouts.Profesional.header')

        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('profesional.getProfilProfesional') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Profil Akun
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-profil-profesional">
            @include('pages.Profesional.Pengaturan.data_profesional')
        </div>
        <div class="section-riwayat-proyek-profesional">
            @include('pages.Profesional.Pengaturan.riwayat_proyek_profesional')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Profesional/data_profesional.js')  
@endpush