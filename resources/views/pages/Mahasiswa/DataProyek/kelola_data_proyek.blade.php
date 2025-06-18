@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

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
                        <a href="{{ route('mahasiswa.dataProyek') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Proyek
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-info-circle me-1"></i>
                        Detail Data Proyek
                    </li>
                </ol>
            </nav>
        </div>

        <div style="display: grid; grid-template-columns: 3fr 1fr; align-items: stretch;">
            <div style="display: flex; flex-direction: column;">
                @include('pages.Mahasiswa.DataProyek.data_proyek')
            </div>
            <div style="display: flex; flex-direction: column;">
                @include('pages.Mahasiswa.DataProyek.data_anggota_proyek')
            </div>
        </div>
        <div class="section-dokumen-penunjang">
            @include('pages.Mahasiswa.DataProyek.data_dokumen_penunjang')
        </div>
        <div class="section-timeline">
            @include('pages.Mahasiswa.DataProyek.data_timeline_proyek')
        </div>
        <div>
            @include('pages.Mahasiswa.DataProyek.my_progres_proyek')
        </div>
        <div>
            @include('pages.Mahasiswa.DataProyek.data_progres_proyek')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Mahasiswa/data_dokumen_penunjang.js')
    @vite('resources/js/Mahasiswa/data_timeline.js')
    @vite('resources/js/Mahasiswa/data_progres_proyek.js')
@endpush
