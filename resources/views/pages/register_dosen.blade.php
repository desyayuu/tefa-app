@extends('layouts.app')

@section('title', 'TEFA | Registrasi Dosen')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <!-- Section Register -->
    <section class="py-5 card-auth">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 20px;">
                <h3 class="text-center mb-4">Registrasi Dosen</h3>
                
                {{-- Handling Error --}}
                @include('components.handling_error')
                
                {{-- Form Register --}}
                <form action="{{ route('register-dosen.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_dosen" class="form-label">Nama</label>
                        <input type="text" name="nama_dosen" id="nama_dosen" class="form-control @error('nama_dosen') is-invalid @enderror" value="{{ old('nama_dosen') }}" required>
                        @error('nama_dosen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nidn_dosen" class="form-label">NIP/NIDN</label>
                        <input type="text" name="nidn_dosen" id="nidn_dosen" class="form-control @error('nidn_dosen') is-invalid @enderror" value="{{ old('nidn_dosen') }}" required>
                        @error('nidn_dosen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    

                    <div class="mb-3">
                        <label for="telepon_dosen" class="form-label">Telepon</label>
                        <input type="text" name="telepon_dosen" id="telepon_dosen" class="form-control @error('telepon_dosen') is-invalid @enderror" value="{{ old('telepon_dosen') }}">
                        @error('telepon_dosen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
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