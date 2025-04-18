@extends('layouts.app')

@section('title', 'TEFA | Dashboard Koordinator')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Koordinator.header')
        
        <!-- Content Area -->
        <div class="content">

        </div>
    </div>
</div>
@endsection