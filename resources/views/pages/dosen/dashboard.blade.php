@extends('layouts.app')

@section('title', 'Dashboard Dosen - TEFA JTI POLINEMA')

@section('content')
<div class="min-vh-100 d-flex flex-column">
    <div class="container mt-5 pt-5 flex-grow-1">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success mb-4">
                    <h4 class="mb-0">Dashboard Dosen</h4>
                    <p class="mb-0">Selamat datang, {{ session('user')['nama'] }}</p>
                </div>
                <div class="text-end mb-3">
                    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection