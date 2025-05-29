@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Dosen.sidebar')
    
    <div class="main-content">
        @include('layouts.Dosen.header')

        <div style="display: flex; align-items: stretch;">
            <div style="flex: 9;">
                @include('pages.Dosen.DataProyek.data_proyek')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_proyek.js')
@endpush
