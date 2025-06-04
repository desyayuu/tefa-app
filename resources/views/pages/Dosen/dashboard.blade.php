@extends('layouts.app')

@section('title', 'TEFA | Dashboard Dosen')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Dosen.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Dosen.header')
        <div class="breadcrumb-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('koordinator.dashboard') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Dashboard Dosen
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="section-summary-data-proyek">
            @include('pages.Dosen.summary_data_proyek')
        </div>
    </div>
</div>
@endsection