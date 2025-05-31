@extends('layouts.app')

@section('title', 'TEFA | Data Profesional')

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
                        <a href="{{ route('koordinator.dataProfesional') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Profesional
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
        <div class="section-current-profesional" style="font-size: 14px;">
            @include('pages.Koordinator.DataProfesional.current_profesional')
        </div>
        <div class="section-table-profesional" style="font-size: 14px;">
            @include('pages.Koordinator.DataProfesional.table_profesional')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_profesional.js')
@endpush