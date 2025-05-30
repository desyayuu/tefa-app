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
                        <a href="{{ route('koordinator.dataMitra') }}" class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-project-diagram me-1"></i>
                            Data Mitra
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="">
                            <i class="fas fa-project-diagram me-1"></i>
                           
                        </a>
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
                <h4 class="m-0">Data Mitra</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataMitra') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari mitra..." value="{{$search ?? '' }}">
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
                    <a href="{{ route('koordinator.dataMitra') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <!-- Modal Tambah Data Mitra (Unified for Single dan Multiple) -->
                    <div class="modal fade" id="modalTambahData" aria-hidden="true" aria-labelledby="modalTambahDataLabel" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.tambahMultipleDataMitra') }}" method="POST" id="formTambahData">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahDataLabel">Tambah Data Mitra</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <div class="row mb-3">
                                            <!-- Nama Mitra -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nama_mitra" class="form-label ">Nama Mitra</label>
                                                <input type="text" class="form-control" id="nama_mitra" name="nama_mitra">
                                                <div class="invalid-feedback" id="nama_mitra_error"></div>
                                            </div>
                                            <!-- Email -->
                                            <div class="mb-2 col-md-6">
                                                <label for="email_mitra" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email_mitra" name="email_mitra">
                                                <div class="invalid-feedback" id="email_mitra_error"></div>
                                            </div>
                                            <!-- Telepon -->
                                            <div class="mb-2 col-md-6">
                                                <label for="telepon_mitra" class="form-label">Telepon</label>
                                                <input type="text" class="form-control" id="telepon_mitra" name="telepon_mitra">
                                                <div class="invalid-feedback" id="telepon_mitra_error"></div>
                                            </div>
                                            <!-- Alamat -->
                                            <div class="mb-2 col-md-6">
                                                <label for="alamat_mitra" class="form-label">Alamat</label>
                                                <textarea class="form-control" id="alamat_mitra" name="alamat_mitra" rows="1"></textarea>
                                                <div class="invalid-feedback" id="alamat_mitra_error"></div>
                                            </div>
                                            <div class="mt-2">
                                                <div class="col-12 text-end">
                                                    <button type="button" class="btn btn-add" id="btnTambahkanKeDaftar">Tambahkan ke Daftar</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Error message for form submit -->
                                        <div class="alert alert-danger d-none" id="form_error"></div>
                                        
                                        <div class="daftar-mitra-container mt-5">
                                            <h5>Daftar Mitra yang Akan Ditambahkan</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Mitra</th>
                                                            <th>Email</th>
                                                            <th>Telepon</th>
                                                            <th>Alamat</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="daftarMitra">
                                                        <tr id="emptyRow">
                                                            <td colspan="5" class="text-center">Belum ada mitra yang ditambahkan ke daftar</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- Hidden input untuk menyimpan data multiple mitra -->
                                            <input type="hidden" name="mitra_data" id="mitraJsonData" value="[]">
                                            <input type="hidden" name="is_single" id="isSingle" value="1">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-add" id="btnSimpan">Simpan Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-add" data-bs-target="#modalTambahData" data-bs-toggle="modal">Tambah Data</button>
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($mitra) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                    <!-- <th scope="col">#</th> -->
                    <th scope="col">Nama Mitra</th>
                    <th scope="col">Email</th>
                    <th scope="col">Telepon</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mitra as $m)
                    <tr>
                        <!-- <th scope="row">{{ $loop->iteration }}</th> -->
                        <td>{{ $m->nama_mitra }}</td>
                        <td>{{ $m->email_mitra }}</td>
                        <td>{{ $m->telepon_mitra }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalMitra{{ $m->mitra_proyek_id }}">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $m->mitra_proyek_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit and Detail Data -->
                    <div class="modal fade" id="modalMitra{{ $m->mitra_proyek_id }}" tabindex="-1" aria-labelledby="mitraLabel{{ $m->mitra_proyek_id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.updateDataMitra', $m->mitra_proyek_id) }}" method="POST" data-current-email="{{ $m->email_mitra }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="mitraLabel{{ $m->mitra_proyek_id }}">Edit Mitra</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="nama_mitra_{{ $m->mitra_proyek_id }}" class="form-label">Nama Mitra</label>
                                            <input type="text" class="form-control" id="nama_mitra_{{ $m->mitra_proyek_id }}" name="nama_mitra" value="{{ $m->nama_mitra }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_mitra_{{ $m->mitra_proyek_id }}" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email_mitra_{{ $m->mitra_proyek_id }}" name="email_mitra" value="{{ $m->email_mitra }}" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="telepon_mitra_{{ $m->mitra_proyek_id }}" class="form-label">Telepon</label>
                                            <input type="text" class="form-control" id="telepon_mitra_{{ $m->mitra_proyek_id }}" name="telepon_mitra" value="{{ $m->telepon_mitra }}">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="alamat_mitra_{{ $m->mitra_proyek_id }}" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="alamat_mitra_{{ $m->mitra_proyek_id }}" name="alamat_mitra" rows="2">{{ $m->alamat_mitra }}</textarea>
                                            <div class="invalid-feedback"></div>
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
                    <div class="modal fade" id="modalDelete{{ $m->mitra_proyek_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $m->mitra_proyek_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteDataMitra', $m->mitra_proyek_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $m->mitra_proyek_id }}">Konfirmasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah yakin ingin menghapus data <strong>{{ $m->nama_mitra }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-hapus">Hapus</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(isset($search) && $search)
                                Tidak ada hasil yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada data mitra
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <div class="showing-text">
                    Showing {{ $mitra->firstItem() }} to {{ $mitra->lastItem() }} of {{ $mitra->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $mitra->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
