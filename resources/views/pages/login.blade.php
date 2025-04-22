@extends('layouts.app')

@section('title', 'TEFA | Login')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <!-- Section Login -->
    <section class="py-5 card-auth">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 20px;">
                <h3 class="text-center mb-4">Login</h3>
                
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
                
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
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
                        <button type="submit" class="btn btn-auth btn-block">Login</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Belum punya akun? <a href="{{ route('register-landing-page') }}">Register</a></p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.footer')
@endsection