@extends('layouts.app')

@section('title', 'TEFA | Dashboard Mahasiswa')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Mahasiswa.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Mahasiswa.header')
        
        <!-- Content Area -->
        <div class="content">

        </div>
    </div>
</div>
@endsection