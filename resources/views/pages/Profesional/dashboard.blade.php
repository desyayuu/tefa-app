@extends('layouts.app')

@section('title', 'TEFA | Dashboard Profesional')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Profesional.sidebar')
    
    <div class="main-content">
        <!-- Header -->
        @include('layouts.Profesional.header')
        
        <!-- Content Area -->
        <div class="content">

        </div>
    </div>
</div>
@endsection