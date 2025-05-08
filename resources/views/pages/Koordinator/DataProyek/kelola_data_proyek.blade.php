@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        <div style="display: flex; align-items: stretch;">
            <div style="flex: 9;">
                @include('pages.Koordinator.DataProyek.data_proyek')
            </div>
            <div style="flex: 3;">
                @include('pages.Koordinator.DataProyek.data_anggota_proyek')
            </div>
        </div>
        <div class="section-dokumen-penunjang">
            @include('pages.Koordinator.DataProyek.data_dokumen_penunjang')
        </div>
    </div>
</div>
@endsection
