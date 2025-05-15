@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        <div style="display: flex; align-items: stretch;">
            <div style="flex: 9;">
                @include('pages.Koordinator.DataProyek.data_proyek')
            </div>
            <div style="flex: 3;">
                @include('pages.Koordinator.DataProyek.data_anggota_proyek')
            </div>
        </div>
        <div class="section-dokumen-penunjang">
            @include('pages.Koordinator.DataProyek.data_dokumen_penunjang')
        </div>
        <div class="section-timeline-proyek">
            @include('pages.Koordinator.DataProyek.data_timeline_proyek')
        </div>
        <div class="section-progres-proyek">
            @include('pages.Koordinator.DataProyek.data_progres_proyek')
        </div>
        <div class="section-progres-proyek">
            @include('pages.Koordinator.DataProyek.data_luaran_proyek')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_proyek.js')
    @vite('resources/js/Koordinator/data_dokumen_penunjang.js')
    @vite('resources/js/Koordinator/data_timeline.js')
    @vite('resources/js/Koordinator/data_luaran.js')
    @vite('resources/js/Koordinator/data_progres_proyek.js')
@endpush
