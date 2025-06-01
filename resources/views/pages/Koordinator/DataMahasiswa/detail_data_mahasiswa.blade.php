

@extends('layouts.app')

@section('title', 'TEFA | Data Mahasiswa')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dataMahasiswa') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Mahasiswa
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-info-circle me-1"></i>
                        Detail Data Mahasiswa
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-data-mahasiswa">
            @include('pages.Koordinator.DataMahasiswa.data_mahasiswa')
        </div>
        <div class="section-bidang-keahlian">
            @include('pages.Koordinator.DataMahasiswa.data_bidang_keahlian')
        </div>
        <div class="section-riwayat-proyek">
            @include('pages.Koordinator.DataMahasiswa.riwayat_proyek_mahasiswa')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_mahasiswa.js')
@endpush