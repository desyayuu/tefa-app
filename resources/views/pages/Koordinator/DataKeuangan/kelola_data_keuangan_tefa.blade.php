@extends('layouts.app')

@section('title', 'TEFA | Data Keuangan TEFA')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')
        
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





