@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

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
                        <a href="{{ route('profesional.dataProyek') }}">
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
                @include('pages.Profesional.DataProyek.data_proyek')
            </div>
            <div style="display: flex; flex-direction: column;">
                @include('pages.Profesional.DataProyek.data_anggota_proyek')
            </div>
        </div>
        <div class="section-dokumen-penunjang">
            @include('pages.Profesional.DataProyek.data_dokumen_penunjang')
        </div>
        <div class="section-timeline-proyek">
            @include('pages.Profesional.DataProyek.data_timeline_proyek')
        </div>
        <div>
            @include('pages.Profesional.DataProyek.my_progres_proyek')
        </div>
        <div>
            @include('pages.Profesional.DataProyek.data_progres_proyek')
        </div>
                <div class="section-luaran-proyek">
            @include('pages.Profesional.DataProyek.data_luaran_proyek')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Profesional/data_proyek.js')
    @vite('resources/js/Profesional/detail_data_proyek.js')
    @vite('resources/js/Profesional/data_dokumen_penunjang.js')
    @vite('resources/js/Profesional/data_timeline.js')
    @vite('resources/js/Profesional/data_progres_proyek.js')
@endpush
