

@extends('layouts.app')

@section('title', 'TEFA | Data Dosen')

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
                        <a href="{{ route('koordinator.dataDosen') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Dosen
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-info-circle me-1"></i>
                        Detail Data Dosen
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-data-dosen">
            @include('pages.Koordinator.DataDosen.data_dosen')
        </div>
        <div>
            @include('pages.Koordinator.DataDosen.riwayat_proyek_dosen')
        </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_dosen.js')
@endpush