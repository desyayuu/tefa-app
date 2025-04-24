@extends('layouts.app')

@section('title', 'TEFA | Data Dosen')

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
                <h4 class="m-0">Data Dosen</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari dosen..." value="{{$search ?? '' }}">
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
                    <a href="{{ route('koordinator.dataDosen') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <!-- Modal Tambah Data Dosen -->
                    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Tambah Data Dosen</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Nama Dosen -->
                                            <div class="mb-3 col-md-6">
                                                <label for="nama_dosen" class="form-label label-form">Nama Dosen</label>
                                                <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" required>
                                            </div>
                                            <!-- NIDN -->
                                            <div class="mb-3 col-md-6">
                                                <label for="nidn" class="form-label label-form">NIDN</label>
                                                <input type="text" class="form-control" id="nidn" name="nidn" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Tanggal Lahir -->
                                            <div class="mb-3 col-md-6">
                                                <label for="tanggal_lahir" class="form-label label-form">Tanggal Lahir</label>
                                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                            </div>
                                            <!-- Jenis Kelamin -->
                                            <div class="mb-3 col-md-6">
                                                <label for="jenis_kelamin" class="form-label label-form">Jenis Kelamin</label>
                                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Telepon -->
                                            <div class="mb-3 col-md-6">
                                                <label for="telepon_dosen" class="form-label label-form">Telepon</label>
                                                <input type="text" class="form-control" id="telepon_dosen" name="telepon_dosen" required>
                                            </div>
                                            <!-- Email -->
                                            <div class="mb-3 col-md-6">
                                                <label for="email_dosen" class="form-label label-form">Email</label>
                                                <input type="email" class="form-control" id="email_dosen" name="email_dosen" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Alamat -->
                                            <div class="mb-3 col-md-6">
                                                <label for="alamat_dosen" class="form-label label-form">Alamat</label>
                                                <textarea class="form-control" id="alamat_dosen" name="alamat_dosen" rows="2" required></textarea>
                                            </div>
                                            <!-- Foto Profil -->
                                            <div class="mb-3 col-md-6">
                                                <label for="profile_img" class="form-label label-form">Foto Profil</label>
                                                <input type="file" class="form-control" id="profile_img" name="profile_img" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- Password -->
                                            <div class="mb-3 col-md-6">
                                                <label for="password" class="form-label label-form">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                            </div>
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
                Menampilkan {{ count($dosen) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama Dosen</th>
                    <th scope="col">NIP/NIDN</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dosen as $d)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $d->nama_dosen}}</td>
                        <td>{{ $d->nidn_dosen}}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDosen{{ $d->dosen_id }}">
                                    <svg width="20" height="20" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                        <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $d->dosen_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit and Detail Data -->
                    <div class="modal fade" id="modalDosen{{ $d->dosen_id }}" tabindex="-1" aria-labelledby="dosenLabel{{ $d->dosen_id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="dosenLabel{{ $d->dosen_id }}">Edit Dosen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="nama_dosen" class="form-label">Nama Dosen</label>
                                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" value="{{ $d->nama_dosen}}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_dosen" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email_dosen" name="email_dosen" value="{{ $d->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="telepon_dosen" class="form-label">Telepon</label>
                                            <input type="text" class="form-control" id="telepon_dosen" name="telepon_dosen" value="{{ $d->telepon_dosen }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="profile_img_dosen" class="form-label">Foto Profil</label>
                                            <input type="file" class="form-control" id="profile_img" name="profile_img_dosen" accept="image/*">
                                            @if($d->profile_img_dosen)
                                                <img src="{{ asset('storage/' . $d->profile_img) }}" alt="Foto Profil" class="img-thumbnail mt-2" style="max-width: 100px;">
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password">
                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal_lahir_dosen" class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" id="tanggal_lahir_dosen" name="tanggal_lahir_dosen" value="{{ $d->tanggal_lahir_dosen }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="jenis_kelamin_dosen" class="form-label">Jenis Kelamin</label>
                                            <select class="form-select" id="jenis_kelamin_dosen" name="jenis_kelamin_dosen">
                                                <option value="Laki-laki" {{ $d->jenis_kelamin_dosen == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="Perempuan" {{ $d->jenis_kelamin_dosen == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
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
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @if(isset($search) && $search)
                                Tidak ada hasil yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada data dosen
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        
            <div class="d-flex justify-content-between align-items-center">
                <div class="showing-text">
                    Showing {{ $dosen->firstItem() }} to {{ $dosen->lastItem() }} of {{ $dosen->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $dosen->appends(['search' => request('search')])->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 3000); 
        });
    });
</script>
@endsection