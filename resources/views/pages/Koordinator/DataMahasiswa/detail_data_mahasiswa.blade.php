@extends('layouts.app')

@section('title', 'TEFA | Data Mahasiswa')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')
        @include('pages.Koordinator.DataMahasiswa.data_mahasiswa')
    </div>
</div>