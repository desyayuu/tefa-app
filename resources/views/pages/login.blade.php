@extends('layouts.app')

@section('title', 'Register')

@section('content')

    {{-- Navbar --}}
    @include('layouts.navbar')

    <!-- Section Register -->
    <section class="py-5 card-auth">
        <div class="container d-flex justify-content-center align-items-center">
            <div class="card shadow-lg p-4" style="width: 100%; max-width: 500px; border-radius: 20px;">
                <h3 class="text-center mb-4">Login</h3>
                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-auth btn-block">Login</button>
                    </div>

                </form>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.footer')

@endsection
