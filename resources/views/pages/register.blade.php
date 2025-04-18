@extends('layouts.app')

@section('title', 'TEFA | Registrasi Dosen')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <!-- Section Register -->
    <section class="py-5 card-auth">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 20px;">
                <h3 class="text-center mb-4">Register</h3>
                
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
                
                <form action="{{ route('register.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nidn" class="form-label">NIP/NIDN</label>
                        <input type="text" name="nidn" id="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn') }}" required>
                        @error('nidn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon') }}" required>
                        @error('telepon')
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
                    
                    <!-- Field yang diperlukan tapi disembunyikan -->
                    <input type="hidden" name="jenis_kelamin" value="Laki-Laki">
                    <input type="hidden" name="tanggal_lahir" value="{{ date('Y-m-d') }}">
                    <input type="hidden" name="password_confirmation" value="">

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