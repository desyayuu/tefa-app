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
                            <a href="{{ route('koordinator.dataProyek') }}" class="breadcrumb-item active" aria-current="page">
                                <i class="fas fa-project-diagram me-1"></i>
                                Data Proyek
                            </a>
                        </li>
                    </ol>
            </nav>
        </div>

        <div class="content-table" style="font-size: 14px;">
            <!-- Handling Error and Success -->
            @include('components.handling_error')

            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Data Proyek</h5>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataProyek') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari proyek..." value="{{$search ?? '' }}">
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
                    <a href="{{ route('koordinator.dataProyek') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    <button class="btn btn-add" data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Tambah Data</button>
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($proyek) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Nama Proyek</th>
                    <th scope="col">Project Leader</th>
                    <th scope="col">Tanggal Berakhir</th>
                    <th scope="col">Status Proyek</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proyek as $dataProyek)
                    <tr>
                        <td>{{ $dataProyek->nama_proyek }}</td>
                        <td>{{ $dataProyek->nama_project_leader }}</td>
                        <td>{{ $dataProyek->tanggal_selesai }}</td>
                        @if ($dataProyek->status_proyek == 'Initiation')
                            <td><span class="badge bg-secondary">Inisiasi</span></td>
                        @elseif ($dataProyek->status_proyek == 'In Progress')
                            <td><span class="badge bg-primary">In Progres</span></td>
                        @elseif ($dataProyek->status_proyek == 'Done')
                            <td><span class="badge bg-success">Done</span></td>
                        @endif
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('koordinator.detailDataProyek', ['id' => $dataProyek->proyek_id]) }}">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                    <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                </svg>
                                </a>
                                <a href="" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $dataProyek->proyek_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- Modal Delete untuk Proyek -->
                    <div class="modal fade" id="modalDelete{{ $dataProyek->proyek_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $dataProyek->proyek_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteDataProyek', $dataProyek->proyek_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $dataProyek->proyek_id }}">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Apakah Anda yakin ingin menghapus proyek <strong>{{ $dataProyek->nama_proyek }}</strong>?</p>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Tindakan ini akan menghapus:
                                            <ul class="mb-0 mt-2">
                                                <li>Data proyek</li>
                                                <li>Project leader</li>
                                                <li>Semua anggota dosen</li>
                                                <li>Semua anggota mahasiswa</li>
                                                <li>Semua anggota profesional</li>
                                                <li>Data Timeline Proyek</li>
                                                <li>Data Progres Proyek</li>
                                                <li>Data Luaran Proyek</li>
                                                <li>Data Dokumen Penunjang</li>
                                                <li>Data Keuangan Proyek</li>
                                            </ul>
                                        </div>
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

<!-- Modal Tambah Data Proyek -->
<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('koordinator.tambahDataProyek') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Tambah Data Proyek</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <!-- Jenis Proyek -->
                        <div class="col-md-6 mb-3">
                            <label for="jenis_proyek" class="form-label label-form">Jenis Proyek <span class="text-danger">*</span></label>
                            <select class="form-select form-selection" id="jenis_proyek" name="jenis_proyek" required>
                                <option value="" disabled selected >Pilih Jenis Proyek</option>
                                @foreach($jenisProyek as $jenis)
                                    <option value="{{ $jenis->jenis_proyek_id }}" class="form-selection">{{ $jenis->nama_jenis_proyek }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Mitra Proyek -->
                        <div class="col-md-6 mb-3">
                            <label for="mitra_id" class="form-label label-form">Mitra Proyek <span class="text-danger">*</span></label>
                            <select class="form-select form-selection" id="mitra_id" name="mitra_id" required>
                                <option value="" disabled selected>Pilih Mitra</option>
                                @foreach($daftarMitra as $mitra)
                                    <option value="{{ $mitra->mitra_proyek_id }}" class="form-selection">{{ $mitra->nama_mitra }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 2: Project Leader -->
                    <div class="mb-3">
                        <label class="form-label label-form">Project Leader <span class="text-danger">*</span></label>
                        <select class="form-select form-selection mb-2" id="leader_type" name="leader_type" required>
                            <option value="" disabled selected>Pilih Tipe Project Leader</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Profesional">Profesional</option>
                        </select>

                        <!-- Dosen Leader Options -->
                        <div id="dosen_leader_section" style="display:none;">
                            <select class="form-select form-selection select2-dosen" id="dosen_leader_id" name="leader_id">
                                <option value="" disabled selected>Pilih Dosen <span class="text-danger">*</span></option>
                                @foreach($dataDosen as $dosen)
                                    <option value="{{ $dosen->dosen_id }}">{{ $dosen->nama_dosen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Profesional Leader Options -->
                        <div id="profesional_leader_section" style="display:none;">
                            <select class="form-select form-selection select2-profesional" id="profesional_leader_id" name="leader_id">
                                <option value="" disabled selected>Pilih Profesional <span class="text-danger">*</span></option>
                                @foreach($dataProfesional as $profesional)
                                    <option value="{{ $profesional->profesional_id }}">{{ $profesional->nama_profesional }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Nama Proyek -->
                    <div class="mb-3">
                        <label for="nama_proyek" class="form-label label-form">Nama Proyek <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-selection" id="nama_proyek" name="nama_proyek" required>
                    </div>

                    <!-- Baris 4: Status dan Dana -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status_proyek" class="form-label label-form">Status Proyek <span class="text-danger">*</span></label>
                            <select class="form-select form-selection" id="status_proyek" name="status_proyek" required>
                                <option value="Initiation" selected>Initiation</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Done">Done</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dana_pendanaan" class="form-label label-form">Dana Pendanaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control form-selection currency-format" id="dana_pendanaan" name="dana_pendanaan" required>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 5: Tanggal Mulai & Selesai -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label label-form">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-selection" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_selesai" class="form-label label-form">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-selection" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>

                    <!-- Baris 6: Deskripsi -->
                    <div class="mb-3">
                        <label for="deskripsi_proyek" class="form-label label-form">Deskripsi</label>
                        <textarea class="form-control form-selection" id="deskripsi_proyek" name="deskripsi_proyek" rows="3" placeholder="Tuliskan deskripsi proyek..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
@push('scripts')
    @vite('resources/js/Koordinator/data_proyek.js')
@endpush