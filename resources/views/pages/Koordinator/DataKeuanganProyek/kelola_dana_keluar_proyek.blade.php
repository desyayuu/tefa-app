@extends('layouts.app')

@section('title', 'TEFA | Detail Pengeluaran Keuangan Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        <div class="section-filter-data-keuangan">
            @include('pages.Koordinator.DataKeuanganProyek.filter_dana_keluar_proyek')
        </div>
        <div class="section-data-keuangan-tefa">
            @include('pages.Koordinator.DataKeuanganProyek.table_dana_keluar')
        </div>
        <div>
            @include('pages.Koordinator.DataKeuanganProyek.total_dana_keluar_proyek')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_keluar_keuangan_proyek.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush





