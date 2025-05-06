@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        {{-- Baris pertama: Data Proyek dan Data Anggota Proyek --}}
        <div style="display: flex; align-items: stretch;">
            <div style="flex: 9;">
                @include('pages.Koordinator.DataProyek.data_proyek')
            </div>
            <div style="flex: 3;">
                @include('pages.Koordinator.DataProyek.data_anggota_proyek')
            </div>
        </div>
    </div>
</div>
@endsection
