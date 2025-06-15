@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

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
                        <a href="{{ route('dosen.dataProyek') }}">
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
                @include('pages.Dosen.DataProyek.data_proyek')
            </div>
            <div style="display: flex; flex-direction: column;">
                @include('pages.Dosen.DataProyek.data_anggota_proyek')
            </div>
        </div>
        <div class="section-dokumen-penunjang">
            @include('pages.Dosen.DataProyek.data_dokumen_penunjang')
        </div>
        <div class="section-timeline-proyek">
            @include('pages.Dosen.DataProyek.data_timeline_proyek')
        </div>
        <div>
            @include('pages.Dosen.DataProyek.my_progres_proyek')
        </div>
        <div>
            @include('pages.Dosen.DataProyek.data_progres_proyek')
        </div>
        <div class="section-luaran-proyek">
            @include('pages.Dosen.DataProyek.data_luaran_proyek')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Dosen/data_proyek.js')
    @vite('resources/js/Dosen/detail_data_proyek.js')
    @vite('resources/js/Dosen/data_dokumen_penunjang.js')
    @vite('resources/js/Dosen/data_timeline.js')
    @vite('resources/js/Dosen/data_progres_proyek.js')
@endpush
