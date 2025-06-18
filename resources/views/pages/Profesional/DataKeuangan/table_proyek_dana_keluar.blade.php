@extends('layouts.app')

@section('title', 'TEFA | Data Pengeluaran Keuangan Proyek')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Profesional.sidebar')
    
    <div class="main-content">
        @include('layouts.Profesional.header')            
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

            <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                Anda hanya dapat mengelola keuangan proyek yang Anda pimpin sebagai Project Leader.
            </div>
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Data Pengeluaran Keuangan Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('profesional.dataKeluarKeuanganProyek') }}" method="GET" id="searchForm">
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
                    <a href="{{ route('profesional.dataKeluarKeuanganProyek') }}" class="btn btn-tutup btn-outline-secondary" id="clearSearchBtn">
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
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                        <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
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
    @vite('resources/js/Profesional/data_keluar_keuangan_proyek.js')
@endpush