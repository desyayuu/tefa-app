@extends('layouts.app')

@section('title', 'TEFA | Dashboard Koordinator')

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
                        <a href="{{ route('koordinator.getPesanPengunjung') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-envelope me-1"></i>
                            Data Pesan Pengunjung
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                    </li>
                </ol>
            </nav>
        </div>
        <div class="content-table">
            <!-- Handling Error and Success -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Data Pesan Pengunjung</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.getPesanPengunjung') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari pesan..." value="{{ $search ?? '' }}">
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
                    <a href="{{ route('koordinator.getPesanPengunjung') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($pesanPengunjung) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama Pengirim</th>
                        <th scope="col">Perusahaan</th>
                        <th scope="col">Email</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pesanPengunjung as $pesan)
                    <tr>
                        <td>{{ $pesan->nama_pengirim }}</td>
                        <td>{{ $pesan->perusahaan_pengirim }}</td>
                        <td>{{ $pesan->email_pengirim }}</td>
                        <td>{{ $pesan->created_at ? $pesan->created_at->format('d/m/Y') : '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modalPesan{{ $pesan->pesan_id }}" title="Edit Pesan">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-sm p-1" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $pesan->pesan_id }}" title="Hapus Pesan">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit and Detail Data -->
                    <div class="modal fade" id="modalPesan{{ $pesan->pesan_id }}" tabindex="-1" aria-labelledby="pesanLabel{{ $pesan->pesan_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.updatePesanPengunjung', $pesan->pesan_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="pesanLabel{{ $pesan->pesan_id }}">Detail & Edit Pesan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="nama_pengirim_{{ $pesan->pesan_id }}" class="form-label">Nama Pengirim<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_pengirim_{{ $pesan->pesan_id }}" name="nama_pengirim" value="{{ $pesan->nama_pengirim }}" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="perusahaan_pengirim_{{ $pesan->pesan_id }}" class="form-label">Perusahaan Pengirim<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="perusahaan_pengirim_{{ $pesan->pesan_id }}" name="perusahaan_pengirim" value="{{ $pesan->perusahaan_pengirim }}" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email_pengirim_{{ $pesan->pesan_id }}" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email_pengirim_{{ $pesan->pesan_id }}" name="email_pengirim" value="{{ $pesan->email_pengirim }}" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="telepon_pengirim_{{ $pesan->pesan_id }}" class="form-label">Telepon</label>
                                                    <input type="text" class="form-control" id="telepon_pengirim_{{ $pesan->pesan_id }}" name="telepon_pengirim" value="{{ $pesan->telepon_pengirim }}">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="pesan_pengirim_{{ $pesan->pesan_id }}" class="form-label">Pesan <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="pesan_pengirim_{{ $pesan->pesan_id }}" name="pesan_pengirim" rows="8" required>{{ $pesan->pesan_pengirim }}</textarea>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-add">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal konfirmasi delete -->
                    <div class="modal fade" id="modalDelete{{ $pesan->pesan_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $pesan->pesan_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.hapusPesanPengunjung', $pesan->pesan_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $pesan->pesan_id }}">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                            <h6>Apakah Anda yakin ingin menghapus pesan ini?</h6>
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <strong>{{ $pesan->nama_pengirim }}</strong><br>
                                                <small class="text-muted">{{ $pesan->perusahaan_pengirim }}</small><br>
                                                <small class="text-muted">{{ $pesan->email_pengirim }}</small>
                                            </div>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-hapus">Ya, Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr id="emptyRow">
                        <td colspan="5" class="text-center py-4">
                            @if(isset($search) && $search)
                                <div class="d-flex flex-column align-items-center">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                        <path d="M21 21L15.5 15.5M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-0">Tidak ada hasil yang cocok dengan pencarian "<strong>{{ $search }}</strong>"</p>
                                    <a href="{{ route('koordinator.getPesanPengunjung') }}" class="btn btn-add btn-sm mt-2">Lihat Semua Pesan</a>
                                </div>
                            @else
                                <div class="d-flex flex-column align-items-center">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                        <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z" fill="#9CA3AF"/>
                                    </svg>
                                    <p class="mb-0">Belum ada pesan dari pengunjung</p>
                                    <small class="text-muted">Pesan yang dikirim melalui form website akan muncul di sini</small>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <div class="showing-text">
                    Showing {{ $pesanPengunjung->firstItem() }} to {{ $pesanPengunjung->lastItem() }} of {{ $pesanPengunjung->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $pesanPengunjung->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/Koordinator/data_pesan_pengunjung.js')
@endpush