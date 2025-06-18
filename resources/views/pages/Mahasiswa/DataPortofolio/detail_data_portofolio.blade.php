

@extends('layouts.app')

@section('title', 'TEFA | Data Portofolio')

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
                        <a href="{{ route('mahasiswa.portofolio') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Portofolio Mahasiswa
                        </a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-profil-mahasiswa">
            @include('pages.Mahasiswa.DataPortofolio.data_bidang_keahlian')
        </div>
        <div>
            @include('pages.Mahasiswa.DataPortofolio.data_portofolio')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Mahasiswa/data_portofolio.js')  
@endpush