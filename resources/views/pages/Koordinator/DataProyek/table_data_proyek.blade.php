@extends('layouts.app')

@section('title', 'TEFA | Dashboard Koordinator')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

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
                <h4 class="m-0">Data Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataProyek') }}" method="GET">
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
                    <a href="{{ route('koordinator.dataProyek') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <!-- Modal Tambah Data Proyek -->
                    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.tambahDataProyek') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Tambah Data Proyek</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Jenis Proyek -->
                                        <div class="mb-3">
                                            <label for="jenis_proyek" class="form-label label-form">Jenis Proyek</label>
                                            <select class="form-select" id="jenis_proyek" name="jenis_proyek" required>
                                                <option value="" disabled selected>Pilih Jenis Proyek</option>
                                                @foreach($jenisProyek as $jenis)
                                                    <option value="{{ $jenis->jenis_proyek_id }}">{{ $jenis->nama_jenis_proyek }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Mitra Proyek -->
                                        <div class="mb-3">
                                            <label for="mitra_id" class="form-label label-form">Mitra Proyek</label>
                                            <select class="form-select" id="mitra_id" name="mitra_id" required>
                                                <option value="" disabled selected>Pilih Mitra</option>
                                                @foreach($daftarMitra as $mitra)
                                                    <option value="{{ $mitra->mitra_proyek_id }}">{{ $mitra->nama_mitra }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Project Leader Selection -->
                                        <div class="mb-3">
                                            <label class="form-label label-form">Project Leader</label>
                                            <select class="form-select mb-2" id="leader_type" name="leader_type" required>
                                                <option value="" disabled selected>Pilih Tipe Project Leader</option>
                                                <option value="Dosen">Dosen</option>
                                                <option value="Profesional">Profesional</option>
                                            </select>
                                            
                                <!-- Dosen Leader Options -->
                                <div id="dosen_leader_section" style="display:none;">
                                    <select class="form-select" id="dosen_leader_id" name="leader_id" style="width: 100%;">
                                        <option value="" disabled selected>Pilih Dosen</option>
                                        @foreach($dataDosen as $dosen)
                                            <option value="{{ $dosen->dosen_id }}">{{ $dosen->nama_dosen }}</option>
                                        @endforeach
                                    </select>

                                    
                                </div>

                                <!-- Profesional Leader Options -->
                                <div id="profesional_leader_section" style="display:none;">
                                    <select class="form-select" id="profesional_leader_id" name="leader_id" style="width: 100%;">
                                        <option value="" disabled selected>Pilih Profesional</option>
                                        @foreach($dataProfesional as $profesional)
                                            <option value="{{ $profesional->profesional_id }}">{{ $profesional->nama_profesional }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                        </div>
                                        
                                        <!-- Nama Proyek -->
                                        <div class="mb-3">
                                            <label for="nama_proyek" class="form-label label-form">Nama Proyek</label>
                                            <input type="text" class="form-control" id="nama_proyek" name="nama_proyek" required>
                                        </div>

                                        <!-- Status Proyek -->
                                        <div class="mb-3">
                                            <label for="status_proyek" class="form-label label-form">Status Proyek</label>
                                            <select class="form-select" id="status_proyek" name="status_proyek" required>
                                                <option value="Initiation" selected>Initiation</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Done">Done</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <!-- Tanggal Mulai -->
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal_mulai" class="form-label label-form">Tanggal Mulai</label>
                                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                                            </div>

                                            <!-- Tanggal Selesai -->
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal_selesai" class="form-label label-form">Tanggal Selesai</label>
                                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                                            </div>
                                        </div>

                                        <!-- Dana Pendanaan -->
                                        <div class="mb-3">
                                            <label for="dana_pendanaan" class="form-label label-form">Dana Pendanaan</label>
                                            <input type="number" class="form-control" id="dana_pendanaan" name="dana_pendanaan" required>
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
                    <th scope="col">#</th>
                    <th scope="col">Nama Proyek</th>
                    <th scope="col">Project Leader</th>
                    <th scope="col">Tanggal Berakhir</th>
                    <th scope="col">Status Proyek</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proyek as $proyek)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $proyek->nama_proyek }}</td>
                        <td>{{ $proyek->nama_project_leader }}</td>
                        <td>{{ $proyek->tanggal_selesai }}</td>
                        <td>{{ $proyek->status_proyek}}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('koordinator.detailDataProyek', ['id' => $proyek->proyek_id]) }}">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/Koordinator/data_proyek.js') }}"></script>
@endsection