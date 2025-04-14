@extends('layouts.app')

@section('title', 'TEFA JTI POLINEMA')

@section('content')
    {{-- Navbar --}}
    @include('layouts.landing_page.navbar')

    <section class="first-section-layanan text-black py-5">
        <div class="container">
            <div class="title-layanan">Layanan Kami</div>
            <div class="font-description mt-2">
                Telusuri layanan yang kami tawarkan untuk mewujudkan ide proyek Anda!
            </div>
            <div class="mt-3">
                <a href="#layanan" class="btn btn-service">
                    Telusuri Layanan
                </a>
            </div>
        </div>
    </section>

    <section id="layanan" class="section-layanan py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 order-1 order-md-1 text-center mb-3 mb-md-0">
                    <img src="{{ asset('images/landingpage/section4-laptop.png') }}" class="img-fluid rounded img-layanan">
                </div>
                <div class="col-12 col-md-8 order-2 order-md-2 align-items-center ">
                    <div class="title-layanan text-center text-md-start">Pengembangan Software</div>
                    <div class="text-muted text-center text-md-start">
                        Kami menghadirkan solusi perangkat lunak yang inovatif dan sesuai kebutuhan bisnis Anda. Dari pembuatan website hingga aplikasi mobile, TEFA JTI Polinema siap membantu meningkatkan efisiensi dan produktivitas perusahaan Anda dengan teknologi terkini.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 order-1 order-md-2 text-center mb-3 mb-md-0">
                    <img src="{{ asset('images/landingpage/section4-operator.png') }}" class="img-fluid rounded img-layanan">
                </div>
                <div class="col-12 col-md-8 order-2 order-md-1">
                    <div class="title-layanan text-center text-md-start">Konsultan IT</div>
                    <div class="text-muted text-center text-md-start">
                        Butuh arahan strategis dalam transformasi digital? Tim ahli kami siap memberikan konsultasi dan solusi terbaik dalam perancangan serta implementasi teknologi untuk meningkatkan daya saing bisnis Anda. Kami siap membantu Anda! 
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="section-layanan py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 order-1 order-md-1 text-center mb-3 mb-md-0">
                    <img src="{{ asset('images/landingpage/section4-world.png') }}" class="img-fluid rounded img-layanan">
                </div>
                <div class="col-12 col-md-8 order-2 order-md-2 align-items-center ">
                    <div class="title-layanan text-center text-md-start">Instalasi Jaringan</div>
                    <div class="text-muted text-center text-md-start">
                    Koneksi yang stabil dan aman adalah kunci kelancaran operasional bisnis Anda. Kami menyediakan layanan instalasi dan konfigurasi jaringan yang optimal, baik untuk skala kecil maupun besar. 
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="layanan" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 order-1 order-md-2 text-center mb-3 mb-md-0">
                    <img src="{{ asset('images/landingpage/section4-iot.png') }}" class="img-fluid rounded img-layanan">
                </div>
                <div class="col-12 col-md-8 order-2 order-md-1">
                    <div class="title-layanan text-center text-md-start">Instalasi IOT</div>
                    <div class="text-muted text-center text-md-start">
                        Tingkatkan efisiensi operasional dengan teknologi Internet of Things (IoT)! TEFA JTI Polinema menyediakan layanan instalasi dan konfigurasi perangkat IoT, membantu industri dalam pemantauan otomatis, kontrol jarak jauh, dan analisis data real-time untuk berbagai kebutuhan bisnis.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="section-layanan py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 order-1 order-md-1 text-center mb-3 mb-md-0">
                    <img src="{{ asset('images/landingpage/section4-teaching.png') }}" class="img-fluid rounded img-layanan">
                </div>
                <div class="col-12 col-md-8 order-2 order-md-2 align-items-center ">
                    <div class="title-layanan text-center text-md-start">Pelatihan</div>
                    <div class="text-muted text-center text-md-start">
                    Kami tidak hanya mengembangkan teknologi, tetapi juga berbagi ilmu! TEFA JTI Polinema menawarkan pelatihan dan workshop dalam berbagai bidang IT. Dirancang untuk mahasiswa, profesional, maupun perusahaan yang ingin meningkatkan kompetensi digitalnya.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.landing_page.footer')
@endsection
