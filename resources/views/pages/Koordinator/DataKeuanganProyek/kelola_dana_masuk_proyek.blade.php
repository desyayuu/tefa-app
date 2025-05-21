@extends('layouts.app')

@section('title', 'TEFA | Detail Pemasukan Keuangan Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')
        <div class="section-data-keuangan-tefa">
            @include('pages.Koordinator.DataKeuanganProyek.table_dana_masuk')
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_masuk_keuangan_proyek.js')
@endpush





