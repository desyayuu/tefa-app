@extends('layouts.app')

@section('title', 'TEFA | Detail Pemasukan Keuangan Proyek')

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
                        <a href="">
                            <i class="fas fa-project-diagram me-1"></i>
                            Keuangan Proyek
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{route('koordinator.dataMasukKeuanganProyek')}}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Dana Pemasukan Proyek
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-info-circle me-1"></i>
                            Detail Dana Pemasukan Proyek
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="section-filter-data-keuangan">
            @include('pages.Koordinator.DataKeuanganProyek.filter_dana_masuk_proyek')
        </div>
        <div class="section-data-keuangan-tefa">
            @include('pages.Koordinator.DataKeuanganProyek.table_dana_masuk')
        </div>
        <div>
            @include('pages.Koordinator.DataKeuanganProyek.total_dana_masuk_proyek')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_masuk_keuangan_proyek.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush





