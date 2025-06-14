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
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                        <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
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