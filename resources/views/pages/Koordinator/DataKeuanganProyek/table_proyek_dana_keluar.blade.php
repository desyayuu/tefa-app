@extends('layouts.app')

@section('title', 'TEFA | Data Pengeluaran Keuangan Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')            
        <div class="breadcrumb-container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="">
                                <i class="fas fa-project-diagram me-1"></i>
                                Keuangan Proyek
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-info-circle me-1"></i>
                            Dana Pengeluaran Proyek
                        </li>
                    </ol>
                </nav>
            </div>

        <div class="content-table">
            <!-- Handling Error and Success -->
            @include('components.handling_error')
            

            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Data Pengeluaran Keuangan Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataKeluarKeuanganProyek') }}" method="GET" id="searchForm">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari proyek..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Button Clear Search-->
                    @if(isset($search) && $search)
                    <a href="{{ route('koordinator.dataKeluarKeuanganProyek') }}" class="btn btn-tutup btn-outline-secondary" id="clearSearchBtn">
                        Hapus Filter
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Handling Hasil Search - This will be updated via JavaScript -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($proyek) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <!-- Tabel List Proyek -->
            <div id="proyekTableContainer">
                <table class="table" id="tableProyek">
                    <thead>
                        <tr>
                            <th scope="col">Nama Proyek</th>
                            <th scope="col">Project Leader</th>
                            <th scope="col">Dana Pendanaan</th>
                            <th scope="col">Status Proyek</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="proyekTableBody">
                        @forelse ($proyek as $item)
                        <tr>
                            <td>{{ $item->nama_proyek }}</td>
                            <td>{{ $item->nama_project_leader ?? '-' }}</td>
                            <td>{{ 'Rp ' . number_format($item->dana_pendanaan, 0, ',', '.') }}</td>
                            @if ($item->status_proyek == 'Initiation')
                                <td><span class="badge bg-secondary">Inisiasi</span></td>
                            @elseif ($item->status_proyek == 'In Progress')
                                <td><span class="badge bg-primary">In Progres</span></td>
                            @elseif ($item->status_proyek == 'Done')
                                <td><span class="badge bg-success">Done</span></td>
                            @endif
                            <td>
                                <button type="button" class="btn view-project-btn" data-proyek-id="{{ $item->id }}">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                @if(isset($search) && $search)
                                    Tidak ada hasil yang cocok dengan pencarian "{{ $search }}"
                                @else
                                    Belum ada data proyek
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="showing-text">
                        Showing {{ $proyek->firstItem() }} to {{ $proyek->lastItem() }} of {{ $proyek->total() }} entries
                    </div>
                    <div class="pagination-links">
                        {{ $proyek->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
    @vite('resources/js/Koordinator/data_keluar_keuangan_proyek.js')
@endpush