@extends('layouts.app')
@section('title', 'TEFA | Data Sub Jenis Kategori Transaksi')

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
                        <a href="{{ route('koordinator.getSubKategoriTransaksi') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Sub Jenis Kategori Transaksi
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>
        
        <div class="content-table">
            @include('components.handling_error')
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3" style="font-size: 14px;">
                <h5>Data Sub Jenis Kategori Transaksi</h5>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <!-- PERBAIKAN: Gunakan route yang sama untuk search -->
                        <form action="{{ route('koordinator.getSubKategoriTransaksi') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" 
                                placeholder="Cari data..." value="{{ $search ?? '' }}">
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
                    <a href="{{ route('koordinator.getSubKategoriTransaksi') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <button class="btn btn-add" data-bs-target="#modalTambahData" data-bs-toggle="modal">
                        Tambah Data
                    </button>
                </div>
            </div>

            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($subJenis) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" width="25%">Jenis Transaksi</th>
                            <th scope="col" width="25%">Kategori Transaksi</th>
                            <th scope="col" width="30%">Nama Sub Jenis Kategori</th>
                            <th scope="col" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subJenis as $s)
                        <tr>
                            <td>{{ $s->nama_jenis_transaksi ?? '-' }}</td>
                            <td>{{ $s->nama_jenis_keuangan_tefa ?? '-' }}</td>
                            <td>{{ $s->nama_sub_jenis_transaksi ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- SOLUSI: Hilangkan data-bs-toggle dan data-bs-target untuk menghindari konflik -->
                                    <a href="#" 
                                    class="btn-edit" 
                                    data-id="{{ $s->sub_jenis_transaksi_id }}">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                            <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                        </svg>
                                    </a>
                                    
                                    <a href="#" 
                                    class="btn-delete" 
                                    data-id="{{ $s->sub_jenis_transaksi_id }}">
                                        <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                @if(isset($search) && $search)
                                    Tidak ada hasil yang cocok dengan pencarian "{{ $search }}"
                                @else
                                    Belum ada data sub jenis kategori transaksi
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="showing-text">
                        Showing {{ $subJenis->firstItem() }} to {{ $subJenis->lastItem() }} of {{ $subJenis->total() }} entries
                    </div>
                    <div class="pagination-links">
                        {{ $subJenis->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambahData" tabindex="-1" aria-labelledby="modalTambahDataLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataLabel">Tambah Sub Jenis Kategori Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahData" method="POST" action="{{ route('koordinator.storeDataSubJenisKategori') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                        <select class="form-select" id="jenis_transaksi_id" name="jenis_transaksi_id" required>
                            <option value="">Pilih Jenis Transaksi</option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_keuangan_tefa_id" class="form-label">Kategori Transaksi <span class="text-danger">*</span></label>
                        <select class="form-select" id="jenis_keuangan_tefa_id" name="jenis_keuangan_tefa_id" required>
                            <option value="">Pilih Kategori Transaksi</option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nama_sub_jenis_transaksi" class="form-label">Nama Sub Jenis Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_sub_jenis_transaksi" name="nama_sub_jenis_transaksi" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data -->
<div class="modal fade" id="modalEditData" tabindex="-1" aria-labelledby="modalEditDataLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditDataLabel">Edit Sub Jenis Kategori Transaksi <span class="text-danger">*</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditData" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_jenis_transaksi_id" name="jenis_transaksi_id" required>
                            <option value="">Pilih Jenis Transaksi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jenis_keuangan_tefa_id" class="form-label">Kategori Transaksi <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_jenis_keuangan_tefa_id" name="jenis_keuangan_tefa_id" required>
                            <option value="">Pilih Kategori Transaksi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_sub_jenis_transaksi" class="form-label">Nama Sub Jenis Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_sub_jenis_transaksi" name="nama_sub_jenis_transaksi" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_sub_jenis_kategori_transaksi.js')
@endpush