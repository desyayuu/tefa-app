@extends('layouts.app')

@section('title', 'TEFA | Portfolio Proyek')

@section('content')
    {{-- Navbar --}}
    @include('layouts.navbar')

    <section class="detail-proyek text-black py-2">
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
                            Portofolio Proyek
                        </li>
                    </ol>
                </nav>
            </div>
            {{-- Header Section --}}
            <div class="title-table d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0 fw-bold">Proyek TEFA JTI Polinema</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('get-portofolio-proyek') }}" method="GET" class="d-flex">
                            {{-- Preserve other filters --}}
                            @if(request('jenis_proyek'))
                                <input type="hidden" name="jenis_proyek" value="{{ request('jenis_proyek') }}">
                            @endif
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            
                            <input type="text" 
                                   name="search" 
                                   class="form-control pe-5 form-search" 
                                   placeholder="Cari proyek..." 
                                   value="{{ $search ?? '' }}"
                                   >
                            <button type="submit" 
                                    class="btn position-absolute top-50 end-0 translate-middle-y pe-3 py-2 border-0 bg-transparent">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#6c757d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#6c757d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    {{-- Clear Search Button --}}
                    @if(isset($search) && $search)
                        <a href="{{ route('get-portofolio-proyek') }}" class="btn btn-outline-secondary">
                            Hapus Filter
                        </a>
                    @endif
                    
                    <div class="d-flex gap-2 align-items-center">
                        {{-- Filter Section --}}
                        <div class="col-md-12">
                            <div class="filter-section d-flex gap- align-items-center flex-wrap">
                                {{-- Jenis Proyek Filter --}}
                                <div class="filter-item">
                                    <select name="jenis_proyek" class="form-select" onchange="applyFilter()">
                                        <option value="">Semua Jenis Proyek</option>
                                        @foreach($jenisProyek as $jenis)
                                            <option value="{{ $jenis->jenis_proyek_id }}" 
                                                    {{ request('jenis_proyek') == $jenis->jenis_proyek_id ? 'selected' : '' }}>
                                                {{ $jenis->nama_jenis_proyek }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                </div>
            </div>


            {{-- Search Results Info --}}
            @if(isset($search) && $search)
                <div class="alert alert-info mb-4">
                    <i class="fas fa-search me-2"></i>
                    Menampilkan {{ $proyekList->total() }} hasil untuk pencarian "<strong>{{ $search }}</strong>"
                </div>
            @endif

            {{-- Projects Grid --}}
            @if($proyekList->count() > 0)
                <div class="row">
                    @foreach($proyekList as $proyek)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="project-card h-100">
                                <a href="{{ route('detail-portofolio-proyek', $proyek->proyek_id) }}" class="text-decoration-none">
                                    <div class="card h-100 shadow-sm border-0" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                        {{-- Project Poster --}}
                                        <div class="poster-container position-relative overflow-hidden">
                                            <img src="{{ asset($proyek->display_image) }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $proyek->image_source === 'documentation' ? 'Dokumentasi' : 'Poster' }} {{ $proyek->nama_proyek }}"
                                                 style="height: 180px; object-fit: cover; transition: transform 0.3s ease;"
                                                >
                                            {{-- Overlay on hover --}}
                                            <div class="card-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                 style="background: rgba(0,0,0,0.6); opacity: 0; transition: opacity 0.3s ease;">
                                                <span class="text-white">
                                                    <i class="fas fa-eye me-2"></i>Lihat Detail
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            {{-- Project Title --}}
                                            <h6 class="card-title fw-bold mb-2 text-dark" style="line-height: 1.4;">
                                                {{ $proyek->nama_proyek }}
                                            </h6>

                                            {{-- Project Type --}}
                                            @if($proyek->nama_jenis_proyek)
                                                <small class="text-tefa mb-2 d-block">
                                                    <i class="fas fa-tag me-1"></i>{{ $proyek->nama_jenis_proyek }}
                                                </small>
                                            @endif

                                            {{-- Project Description --}}
                                            <p class="card-text text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">
                                                {{ $proyek->short_description }}
                                            </p>

                                            {{-- Project Info --}}
                                            <div class="project-info mt-auto">
                                                @if($proyek->nama_mitra)
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="fas fa-handshake me-1"></i>{{ $proyek->nama_mitra }}
                                                    </small>
                                                @endif
                                                
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $proyek->formatted_start_date }}
                                                    @if($proyek->tanggal_selesai)
                                                        - {{ $proyek->formatted_end_date }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Section --}}
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <div class="showing-info">
                        @if($proyekList->total() > 0)
                            <span class="text-muted">
                                Showing {{ $proyekList->firstItem() }} to {{ $proyekList->lastItem() }} 
                                of {{ $proyekList->total() }} results
                            </span>
                        @else
                            <span class="text-muted">Tidak ada data</span>
                        @endif
                    </div>
                    <div class="pagination-wrapper">
                        {{ $proyekList->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class=" text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search" style="font-size: 3rem; color: #ddd;"></i>
                    </div>
                    <h8 class="text-muted">Tidak Ada Proyek Ditemukan</h8>
                    <p class="text-muted mb-4">
                        @if(isset($search) && $search)
                            Tidak ada proyek yang sesuai dengan pencarian "{{ $search }}"
                        @else
                            Belum ada proyek yang tersedia saat ini.
                        @endif
                    </p>
                    @if(isset($search) && $search)
                        <a href="{{ route('get-portofolio-proyek') }}" class="btn btn-add">
                            <i class="fas fa-arrow-left me-2"></i>Lihat Semua Proyek
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    @include('layouts.footer')

    {{-- Custom CSS --}}
    <style>
        .project-card:hover .card {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }

        .project-card:hover .card-overlay {
            opacity: 1 !important;
        }

        .project-card:hover .card-img-top {
            transform: scale(1.05);
        }

        .form-search:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }


        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .poster-container {
            border-radius: 15px 15px 0 0;
        }

        .pagination-wrapper .pagination {
            margin: 0;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #ddd;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>

    {{-- JavaScript --}}
    <script>
        function applyFilter() {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("get-portofolio-proyek") }}';

            // Get current search value
            const searchValue = '{{ $search ?? "" }}';
            if (searchValue) {
                const searchInput = document.createElement('input');
                searchInput.type = 'hidden';
                searchInput.name = 'search';
                searchInput.value = searchValue;
                form.appendChild(searchInput);
            }

            // Get selected jenis proyek
            const jenisProyek = document.querySelector('select[name="jenis_proyek"]').value;
            if (jenisProyek) {
                const jenisInput = document.createElement('input');
                jenisInput.type = 'hidden';
                jenisInput.name = 'jenis_proyek';
                jenisInput.value = jenisProyek;
                form.appendChild(jenisInput);
            }

            document.body.appendChild(form);
            form.submit();
        }

        // Add loading state for cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.project-card a');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    // Add loading state if needed
                    console.log('Navigating to project detail...');
                });
            });
        });
    </script>
@endsection