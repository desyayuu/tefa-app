

@extends('layouts.app')

@section('title', 'TEFA | Data profesional')

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
                        <a href="{{ route('koordinator.dataProfesional') }}">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data profesional
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-info-circle me-1"></i>
                        Detail Data profesional
                    </li>
                </ol>
            </nav>
        </div>

        <div class="section-data-profesional">
            @include('pages.Koordinator.DataProfesional.data_profesional')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_profesional.js')
@endpush