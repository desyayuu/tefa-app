@extends('layouts.app')

@section('title', 'TEFA | Detail Pengeluaran Keuangan Proyek')

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
                        <a href="">
                            <i class="fas fa-project-diagram me-1"></i>
                            Keuangan Proyek
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('dosen.dataKeluarKeuanganProyek')}}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Dana Pengeluaran Proyek
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-info-circle me-1"></i>
                            Detail Dana Pengeluaran Proyek
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-filter-data-keuangan">
            @include('pages.Dosen.DataKeuangan.filter_dana_keluar_proyek')
        </div>
        <div class="section-data-keuangan-tefa">
            @include('pages.Dosen.DataKeuangan.table_dana_keluar')
        </div>
        <div>
            @include('pages.Dosen.DataKeuangan.total_dana_keluar_proyek')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Dosen/data_keluar_keuangan_proyek.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush





