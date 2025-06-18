@extends('layouts.app')

@section('title', 'TEFA | Data Keuangan TEFA')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')
        <div class="breadcrumb-container">
            <div class="breadcrumb-container">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('koordinator.dataKeuanganTefa') }}" class="breadcrumb-item active" aria-current="page">
                                    <i class="fas fa-project-diagram me-1"></i>
                                    Keuangan TEFA
                                </a>
                            </li>
                        </ol>
                </nav>
            </div>
        </div>
        <div class="section-filter-data-keuangan">
            @include('pages.Koordinator.DataKeuangan.filter_data_keuangan_tefa')
        </div>
        <div class="section-data-keuangan-tefa">
            @include('pages.Koordinator.DataKeuangan.table_keuangan_tefa')
        </div>
        <div class="section-total-data-keuangan-tefa">
            @include('pages.Koordinator.DataKeuangan.total_keuangan_tefa')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_keuangan_tefa.js')
@endpush





