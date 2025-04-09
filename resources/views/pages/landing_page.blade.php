@extends('layouts.app')

@section('title', 'Landing Page TEFA')

@section('content')

    {{-- Navbar --}}
    @include('layouts.navbar')

    {{-- Section 1--}}
    <section class="first-section text-black py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="font-title">Teaching Factory</div>
                    <p class="font-description">Teaching Factory (TEFA) adalah sebuah program pembelajaran yang diadakan oleh Jurusan Teknologi Informasi Politeknik Negeri Malang untuk mempersiapkan mahasiswa dalam bekerja di industri.</p>
                </div>

                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="card shadow-sm h-100 card-info text-center">
                                <div class="card-body">
                                    <h5 class="card-title font-total">50</h5>
                                    <p class="card-text">Total Proyek</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card shadow-sm h-100 card-info text-center">
                                <div class="card-body">
                                    <h5 class="card-title font-total">100</h5>
                                    <p class="card-text">Total Partisipasi Mahasiswa</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card shadow-sm h-100 card-info text-center">
                                <div class="card-body">
                                    <h5 class="card-title font-total">9</h5>
                                    <p class="card-text">Total Mitra Industri</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2 -->
    <section class="second-section py-5">
        <div class="container">
            <div class="row gx-4 align-items-center text-center text-md-start">
                <div class="col-12 col-md-2 mb-3 mb-md-0">
                    <h5 class="font-title">Siapa Kita? </h5>
                </div>

                <div class="col-12 col-md-10 font-description">
                    <p>
                        TEFA JTI Polinema adalah media pembelajaran inovatif sekaligus unit bisnis yang menghubungkan dunia akademik dengan industri. JTI Polinema menerapkan metode pembelajaran berbasis proyek nyata, di mana mahasiswa tidak hanya belajar teori, tetapi juga terlibat langsung dalam pengembangan solusi teknologi yang digunakan oleh industri. Selain menjadi tempat pengembangan keterampilan mahasiswa, TEFA JTI Polinema juga berperan sebagai unit bisnis yang menyediakan layanan profesional di bidang pengembangan perangkat lunak, sistem IoT, website, dan solusi digital lainnya. Melalui TEFA JTI Polinema, kami menciptakan lulusan yang siap bersaing di dunia kerja sekaligus menghadirkan solusi IT bagi dunia industri.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3 -->
    <section class="third-section py-5">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center text-md-start">
                <div class="col-md-6 order-2 order-md-1 mt-4 mt-md-0">
                    <h2 class="title-section3">Pembelajaran Berbasis Proyek</h2>
                    <p class="text-muted">
                        Model pembelajaran di TEFA tidak hanya berbasis teori, tetapi mahasiswa akan terlibat langsung dalam proyek-proyek nyata yang sesuai dengan kondisi dunia kerja sesungguhnya.
                    </p>
                </div>
                <div class="col-md-6 text-center order-1 order-md-2 mb-4 mb-md-0">
                    <img src="{{ asset('images/landingpage/section3-first.png') }}" alt="first" class="img-section3">
                </div>
            </div>
        </div>

        <div class="container space-third-section">
            <div class="row align-items-center justify-content-center text-center text-md-start">
                <div class="col-md-6 text-center order-1 order-md-1 mb-4 mb-md-0">
                    <img src="{{ asset('images/landingpage/section3-second.png') }}" alt="second" class="img-section3">
                </div>
                <div class="col-md-6 order-2 order-md-2 mt-4 mt-md-0">
                    <h2 class="title-section3">Kolaborasi Industri</h2>
                    <p class="text-muted">
                        Teaching Factory JTI Polinema menjalin kemitraan dengan berbagai perusahaan dan industri teknologi untuk menciptakan lingkungan pembelajaran berbasis praktik nyata. Melalui kerja sama ini, mahasiswa tidak hanya mendapatkan pengalaman dalam proyek-proyek industri tetapi juga memahami standar dan etika kerja profesional.
                    </p>
                </div>
            </div>
        </div>

        <div class="container space-third-section">
            <div class="row align-items-center justify-content-center text-center text-md-start">
                <div class="col-md-6 order-2 order-md-1 mt-4 mt-md-0">
                    <h2 class="title-section3">Produk Inovasi Teknologi</h2>
                    <p class="text-muted">
                        Melalui TEFA, JTI Polinema menghasilkan produk dan solusi berbasis teknologi. Mahasiswa didorong untuk menciptakan solusi yang tidak hanya kreatif, tetapi juga aplikatif dan sesuai dengan kebutuhan industri.
                    </p>
                </div>

                <div class="col-md-6 text-center order-1 order-md-2 mb-4 mb-md-0">
                    <img src="{{ asset('images/landingpage/section3-thrid.png') }}" alt="third" class="img-section3">
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4 --}}
    <section class="container my-5">
        <h2 class="text-center mb-4">Layanan Kami</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <img src="{{ asset('images/landingpage/section4-laptop.png') }}" alt="pengembangan-software" class="img-section4 mb-3">
                    <h3 class="h5">Pengembangan Software</h3>
                    <p>Kami menghadirkan solusi perangkat lunak yang inovatif dan sesuai kebutuhan bisnis Anda.</p>

                    <!-- Baris tambahan -->
                    <div class="w-100 mt-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/landingpage/see-more.png') }}" alt="icon" width="20" style="margin-right: 4px;">
                            <a href="#" class="text-decoration-none ms-2 see-more">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <img img src="{{ asset('images/landingpage/section4-operator.png') }}" alt="konsultan" class="img-section4">
                    <h3 class="h5">Konsultan IT</h3>
                    <p>Butuh arahan strategis dalam transformasi digital? Kami siap memberikan konsultasi dan solusi terbaik</p>
                
                    <div class="w-100 mt-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/landingpage/see-more.png') }}" alt="icon" width="20" style="margin-right: 4px;">
                                <a href="#" class="text-decoration-none ms-2 see-more">Selengkapnya</a>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <img img src="{{ asset('images/landingpage/section4-world.png') }}" alt="jaringan" class="img-section4">
                    <h3 class="h5">Instalasi Jaringan</h3>
                    <p>Kami menyediakan layanan instalasi dan konfigurasi jaringan yang optimal, baik untuk skala kecil maupun besar. </p>
                    <div class="w-100 mt-3">
                        <div class="d-flex align-items-center">
                                <img src="{{ asset('images/landingpage/see-more.png') }}" alt="icon" width="20" style="margin-right: 4px;">
                                <a href="#" class="text-decoration-none ms-2 see-more">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <img img src="{{ asset('images/landingpage/section4-iot.png') }}" alt="iot" class="img-section4">
                    <h3 class="h5">Instalasi IOT</h3>
                    <p>TEFA JTI Polinema menyediakan layanan instalasi dan konfigurasi perangkat IoT untuk berbagai kebutuhan bisnis</p>
                
                    <div class="w-100 mt-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/landingpage/see-more.png') }}" alt="icon" width="20" style="margin-right: 4px;">
                            <a href="#" class="text-decoration-none ms-2 see-more">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
                    <img img src="{{ asset('images/landingpage/section4-teaching.png') }}" alt="pelatihan" class="img-section4">
                    <h3 class="h5">Pelatihan</h3>
                    <p>TEFA JTI Polinema menawarkan pelatihan dan workshop dalam berbagai bidang IT. </p>
                
                    <div class="w-100 mt-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/landingpage/see-more.png') }}" alt="icon" width="20" style="margin-right: 4px;">
                            <a href="#" class="text-decoration-none ms-2 see-more">Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- Footer --}}
    @include('layouts.footer')

@endsection
