@extends('layouts.app')

@section('title', 'TEFA | Dashboard Dosen')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Dosen.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Dosen.header')
        
        <!-- Content Area -->
        <div class="content">

        </div>
    </div>
    </div>
</div>
@endsection
