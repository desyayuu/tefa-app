@extends('layouts.app')

@section('title', 'TEFA | Dashboard Koordinator')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Koordinator.header')
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dashboard') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Dashboard Koordinator
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="section-summary-data-proyek">
            @include('pages.Koordinator.summary_data_proyek')
        </div>
        <div class="section-summary-data-keuangan">
            @include('pages.Koordinator.summary_keuangan')
        </div>
    </div>
</div>
@endsection