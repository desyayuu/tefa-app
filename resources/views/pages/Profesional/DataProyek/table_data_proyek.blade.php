<!-- File: resources/views/pages/Profesional/DataProyek/table_data_proyek.blade.php -->
@extends('layouts.app')

@section('title', 'TEFA | Data Proyek')

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
                            <a href="{{ route('profesional.dataProyek') }}" class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-project-diagram me-1"></i>
                                Data Proyek
                            </a>
                        </li>
                    </ol>
            </nav>
        </div>
        
        <div class="content-table">
            @include('components.handling_error')
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">My Project</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('profesional.dataProyek') }}" method="GET">
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
                    <a href="{{ route('profesional.dataProyek') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ $data->total() }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif
            

            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Proyek</th>
                        <th>Jenis Proyek</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td>{{ $item->nama_proyek }}</td>
                        <td>{{ $item->nama_jenis_proyek }}</td>
                        <td>
                            @if($item->peran == 'Project Leader')
                                <span class="badge bg-warning">{{ $item->peran }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $item->peran }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $statusClass = match($item->status_proyek) {
                                'Done' => 'bg-success',    
                                'In Progress' => 'bg-primary', 
                                'Initiation' => 'bg-secondary',
                                default => 'bg-info'    
                            };
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ $item->status_proyek }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('profesional.detailProyek', $item->proyek_id) }}" class="{{ request()->routeIs('profesional.dataProyek') ? 'active' : '' }}">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </a>
                        </td>
                            </tr>
                        @empty
                            <tr>
                        <td colspan="9" class="text-center">Tidak ada data proyek</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($data->total() > 0)
                        <span>Showing {{ $data->firstItem() }} to {{ $data->lastItem() }} of {{ $data->total() }} entries</span>
                    @else
                        <span>Tidak ada data</span>
                    @endif
                </div>
                <div>
                    {{ $data->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection