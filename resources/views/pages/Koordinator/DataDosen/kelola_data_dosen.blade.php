@extends('layouts.app')

@section('title', 'TEFA | Data Dosen')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content" style="font-size: 14px;">
        @include('layouts.Koordinator.header')
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dataDosen') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Dosen
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="">
                            <i class="fas fa-project-diagram me-1"></i>
                           
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="section-current-dosen">
            @include('pages.Koordinator.DataDosen.current_dosen')
        </div>
        <div class="section-table-dosen">
            @include('pages.Koordinator.DataDosen.table_dosen')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_dosen.js')
@endpush