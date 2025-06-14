@extends('layouts.app')

@section('title', 'TEFA | Layanan Kami')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <section class="first-section-layanan text-black py-5">
        <div class="container">
            <div class="breadcrumb-container-lp">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('beranda') }}">
                                Beranda
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Layanan Kami
                        </li>
                    </ol>
                </nav>
            </div>
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

    @foreach($jenisProyek as $layanan)
        @php
            $sectionId = 'layanan-' . $layanan->jenis_proyek_id;
        @endphp

            @if($loop->iteration % 2 != 0)
                <section id="{{ $sectionId }}" class="section-layanan py-5">
            @else
                <section id="{{ $sectionId }}" class="py-5">
            @endif
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-4 {{ $loop->even ? 'order-1 order-md-2' : 'order-1 order-md-1' }} text-center mb-3 mb-md-0">
                            <img src="{{ asset('storage/jenis_proyek/' . $layanan->img_jenis_proyek) }}" class="img-fluid rounded img-layanan" alt="{{ $layanan->nama_jenis_proyek }}">
                        </div>
                        <div class="col-12 col-md-8 {{ $loop->even ? 'order-2 order-md-1' : 'order-2 order-md-2' }}">
                            <div class="title-layanan text-center text-md-start">{{ $layanan->nama_jenis_proyek }}</div>
                            <div class="text-muted text-center text-md-start">{{ $layanan->deskripsi_jenis_proyek }}</div>
                        </div>
                    </div>
                </div>
            </section>
    @endforeach

    {{-- Footer --}}
    @include('layouts.footer')
@endsection