@extends('layouts.app')

@section('title', 'TEFA | Detail Portofolio Proyek')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <section class="detail-proyek text-black py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0" style="display: flex; justify-content: center; align-items: center;">
                    {{-- Poster Proyek --}}
                    <img src="{{ $proyek->poster_proyek }}" 
                        class="poster-image" 
                        alt="Poster {{ $proyek->nama_proyek }}"
                        style="width: 80%; height: auto; border-radius: 10px;"
                    >
                </div>

                <div class="col-lg-8">
                    <h3 class="mb-3 fw-bold">{{ $proyek->nama_proyek }}</h3>
                    <p class="text-muted mb-4">{{ $proyek->deskripsi_luaran }}</p>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <h6>Mitra Industri</h6>
                            <div class="text-muted mb-4">
                                @if ($proyek->nama_mitra)
                                    {{ $proyek->nama_mitra }}
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>Dosen</h6>
                            <div class="text-muted mb-4">
                                @if ($dosen->count() > 0)
                                    @foreach ($dosen as $ds)
                                        <div class="mb-2">{{$ds->nama_dosen}}</div>
                                    @endforeach
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>Mahasiswa</h6>
                            <div class="text-muted mb-4">
                                @if ($mahasiswa->count() > 0)
                                    @foreach ($mahasiswa as $mhs)
                                        <div class="mb-2">{{$mhs->nama_mahasiswa}}</div>
                                    @endforeach
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h6>Profesional</h6>
                            <div class="text-muted mb-4">
                                @if ($profesional->count() > 0)
                                    @foreach ($profesional as $prof)
                                        <div class="mb-2">{{$prof->nama_profesional}}</div>
                                    @endforeach
                                @else
                                    <span class="text-danger">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Dokumentasi Proyek Section --}}
    <section class="dokumentasi-proyek text-black py-5 bg-light">
        <div class="container">
            <h3 class="mb-4 fw-bold">Dokumentasi Proyek</h3>
            
            @if($dokumentasi && $dokumentasi->count() > 0)
                <div class="row">
                    @foreach ($dokumentasi as $index => $doc)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="dokumentasi-item-porto">
                                <div class="position-relative overflow-hidden">
                                    <img src="{{ $doc->path_file }}" 
                                        alt="Dokumentasi {{ $proyek->nama_proyek }} - {{ $index + 1 }}"
                                    >
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-camera" style="font-size: 4rem; color: #ddd;"></i>
                    </div>
                    <h5 class="text-muted">Belum Ada Dokumentasi</h5>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.footer')

@endsection
