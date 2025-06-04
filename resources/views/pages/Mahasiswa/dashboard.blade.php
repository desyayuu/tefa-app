@extends('layouts.app')

@section('title', 'TEFA | Dashboard Mahasiswa')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Mahasiswa.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Mahasiswa.header')
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dashboard') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Dashboard Mahasiswa
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="section-summary-data-proyek">
            @include('pages.Mahasiswa.summary_data_proyek')
        </div>
    </div>
</div>
@endsection