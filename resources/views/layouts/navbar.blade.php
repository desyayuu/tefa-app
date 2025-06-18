<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold tefa" href="#">TEFA JTI POLINEMA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('beranda') ? 'active' : '' }}" href="{{ route('beranda') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('layanan-kami') ? 'active' : '' }}" href="{{ route('layanan-kami') }}">Layanan Kami</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('get-portofolio-proyek') || request()->routeIs('detail-portofolio-proyek') ? 'active' : '' }}" href="{{ route('get-portofolio-proyek') }}">Portofolio</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('register-landing-page*') ? 'active' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Registrasi
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('register-dosen') }}">Registrasi Dosen</a></li>
                        <li><a class="dropdown-item" href="{{ route('register-profesional') }}">Registrasi Profesional</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('login-landing-page') ? 'active' : '' }}" href="{{ route('login-landing-page') }}">Masuk</a></li>
            </ul>
        </div>
    </div>
</nav>
