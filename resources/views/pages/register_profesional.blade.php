@extends('layouts.app')

@section('title', 'TEFA | Registrasi Profesional')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <!-- Section Register -->
    <section class="py-5 card-auth">
        <div class="breadcrumb-container-rg">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('beranda') }}">
                                Beranda
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Registrasi Profesional
                        </li>
                    </ol>
                </nav>
        </div>
        <div class="container d-flex justify-content-center align-items-center">
            
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 20px;">
                <h3 class="text-center mb-4">Registrasi Profesional</h3>
                
                @if(session('success'))
                    <div class="alert alert-success mb-3">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger mb-3">
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('register-profesional.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_profesional" class="form-label">Nama</label>
                        <input type="text" name="nama_profesional" id="nama_profesional" class="form-control @error('nama_profesional') is-invalid @enderror" value="{{ old('nama_profesional') }}" required>
                        @error('nama_profesional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telepon_profesional" class="form-label">Telepon</label>
                        <input type="text" name="telepon_profesional" id="telepon_profesional" class="form-control @error('telepon_profesional') is-invalid @enderror" value="{{ old('telepon_profesional') }}">
                        @error('telepon_profesional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-auth btn-block">Register</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Sudah punya akun? <a href="{{ route('login-landing-page') }}">Login</a></p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.footer')
@endsection