@extends('layouts.app')

@section('title', 'TEFA | Data Dosen')

@section('content')
<div class="main-layout">
    <!-- Sidebar -->
    @include('layouts.Koordinator.sidebar')
    
    <div class="main-content">
        @include('layouts.Koordinator.header')

        <div class="content-table">
            @include('components.handling_error')
            
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
                    
                    <!-- Modal Tambah Data Dosen (Single dan Multiple) -->
                    <div class="modal fade" id="modalTambahDosen" aria-hidden="true" aria-labelledby="modalTambahDosenLabel" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{route ('koordinator.tambahDataDosen') }}" method="POST" id="formTambahDosen" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahDosenLabel">Tambah Data Dosen</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <div class="row mb-3">
                                            <!-- Nama Dosen -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nama_dosen" class="form-label">Nama Dosen</label>
                                                <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" >
                                                <div class="invalid-feedback" id="nama_error"></div>
                                            </div>
                                            <!-- NIDN -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nidn_dosen" class="form-label">NIDN/NIP</label>
                                                <input type="text" class="form-control" id="nidn_dosen" name="nidn_dosen" 
                                                    maxlength="10" pattern="[0-9]+" title="NIDN harus berupa angka dan maksimal 10 digit">
                                                <div class="invalid-feedback" id="nidn_error"></div>
                                                <small class="text-muted">NIDN harus berupa angka dan maksimal 10 digit</small>
                                            </div>
                                            <!-- Status Akun -->
                                            <div class="mb-2 col-md-4">
                                                <label for="status_akun_dosen" class="form-label">Status Akun</label>
                                                <select class="form-select" id="status_akun_dosen" name="status_akun_dosen">
                                                    <option value="Active" selected>Active</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Rejected">Rejected</option>
                                                    <option value="Disabled">Disabled</option>
                                                </select>
                                                <div class="invalid-feedback" id="status_akun_error"></div>
                                            </div>
                                            <!-- Email -->
                                            <div class="mb-2 col-md-4">
                                                <label for="email_dosen" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email_dosen" name="email_dosen">
                                                <div class="invalid-feedback" id="email_error"></div>
                                            </div>
                                            <!-- Password -->
                                            <div class="mb-2 col-md-4">
                                                <label for="password_dosen" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password_dosen" name="password_dosen">
                                                <div class="invalid-feedback" id="password_error"></div>
                                                <small class="text-muted">Password akan otomatis menggunakan NIDN jika field ini kosong</small>
                                            </div>
                                            <!-- Tanggal Lahir -->
                                            <div class="mb-2 col-md-4">
                                                <label for="tanggal_lahir_dosen" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control" id="tanggal_lahir_dosen" name="tanggal_lahir_dosen">
                                                <div class="invalid-feedback" id="tanggal_lahir_error"></div>
                                            </div>
                                            <!-- Jenis Kelamin -->
                                            <div class="mb-2 col-md-4">
                                                <label for="jenis_kelamin_dosen" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select" id="jenis_kelamin_dosen" name="jenis_kelamin_dosen">
                                                    <option value="" selected>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-Laki">Laki-Laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                                <div class="invalid-feedback" id="jenis_kelamin_error"></div>
                                            </div>
                                            <!-- Telepon -->
                                            <div class="mb-2 col-md-4">
                                                <label for="telepon_dosen" class="form-label">Telepon</label>
                                                <input type="text" class="form-control" id="telepon_dosen" name="telepon_dosen">
                                                <div class="invalid-feedback" id="telepon_error"></div>
                                            </div>
                                            <!-- Foto Profil -->
                                            <div class="mb-2 col-md-6">
                                                <label for="profile_img_dosen" class="form-label">Foto Profil</label>
                                                <input type="file" class="form-control" id="profile_img_dosen" name="profile_img_dosen" accept="image/*">
                                                <div class="invalid-feedback" id="profile_img_error"></div>
                                                <small class="text-muted">Format gambar: jpeg, png, jpg, gif. Maksimal 2MB.</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="col-12 text-end">
                                                    <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarDosen">Tambahkan ke Daftar</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Error message for form submit -->
                                        <div class="alert alert-danger d-none" id="form_error"></div>
                                        
                                        <div class="daftar-dosen-container mt-5">
                                            <h5>Daftar Dosen yang Akan Ditambahkan</h5>
                                            <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Dosen</th>
                                                        <th>NIDN/NIP</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Foto</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="daftarDosen">
                                                    <tr id="emptyRow">
                                                        <td colspan="6" class="text-center">Belum ada dosen yang ditambahkan ke daftar</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>
                                            
                                            <!-- Hidden input untuk menyimpan data multiple dosen -->
                                            <input type="hidden" name="dosen_data" id="dosenJsonData" value="[]">
                                            <input type="hidden" name="is_single" id="isSingle" value="1">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-add" id="btnSimpanDosen">Simpan Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-add" data-bs-target="#modalTambahDosen" data-bs-toggle="modal">Tambah Data</button>
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
                    <th scope="col">Nama Dosen</th>
                    <th scope="col">NIP/NIDN</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dosen as $d)
                    <tr>
                        <td>{{ $d->nama_dosen}}</td>
                        <td>{{ $d->nidn_dosen}}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="" data-bs-target="#modalMahasiswa{{ $d->dosen_id }}">
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
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.updateDataDosen', $d->dosen_id) }}" method="POST" enctype="multipart/form-data" id="form_edit_{{ $d->dosen_id }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="dosenLabel{{ $d->dosen_id }}">Edit Dosen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <!-- Alert untuk error saat submit -->
                                        <div class="alert alert-danger d-none" id="edit_form_error_{{ $d->dosen_id }}"></div>
                                        
                                        <div class="row mb-3">
                                            <!-- Nama Dosen -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nama_dosen_{{ $d->dosen_id }}" class="form-label">Nama Dosen</label>
                                                <input type="text" class="form-control @error('nama_dosen') is-invalid @enderror" 
                                                    id="nama_dosen_{{ $d->dosen_id }}" name="nama_dosen" 
                                                    value="{{ old('nama_dosen', $d->nama_dosen) }}" required>
                                                <div class="invalid-feedback" id="nama_error_{{ $d->dosen_id }}">
                                                    @error('nama_dosen') {{ $message }} @else Nama dosen wajib diisi @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- NIDN -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nidn_dosen_{{ $d->dosen_id }}" class="form-label">NIDN/NIP</label>
                                                <input type="text" class="form-control @error('nidn_dosen') is-invalid @enderror" 
                                                    id="nidn_dosen_{{ $d->dosen_id }}" name="nidn_dosen" 
                                                    value="{{ old('nidn_dosen', $d->nidn_dosen) }}" 
                                                    maxlength="10" pattern="[0-9]{10}" 
                                                    title="NIDN harus berupa angka dan tepat 10 digit" required>
                                                <div class="invalid-feedback" id="nidn_error_{{ $d->dosen_id }}">
                                                    @error('nidn_dosen') {{ $message }} @else NIDN harus berupa angka dan tepat 10 digit @enderror
                                                </div>
                                                <small class="text-muted">NIDN harus berupa angka dan tepat 10 digit</small>
                                            </div>
                                            
                                            <!-- Status Akun -->
                                            <div class="mb-2 col-md-4">
                                                <label for="status_akun_dosen_{{ $d->dosen_id }}" class="form-label">Status Akun</label>
                                                <select class="form-select @error('status_akun_dosen') is-invalid @enderror" 
                                                    id="status_akun_dosen_{{ $d->dosen_id }}" name="status_akun_dosen" required>
                                                    <option value="Active" {{ old('status_akun_dosen', $d->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Pending" {{ old('status_akun_dosen', $d->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Rejected" {{ old('status_akun_dosen', $d->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="Disabled" {{ old('status_akun_dosen', $d->status) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                                </select>
                                                <div class="invalid-feedback" id="status_akun_error_{{ $d->dosen_id }}">
                                                    @error('status_akun_dosen') {{ $message }} @else Status akun harus dipilih @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- Email -->
                                            <div class="mb-2 col-md-4">
                                                <label for="email_dosen_{{ $d->dosen_id }}" class="form-label">Email</label>
                                                <input type="email" class="form-control @error('email_dosen') is-invalid @enderror" 
                                                    id="email_dosen_{{ $d->dosen_id }}" name="email_dosen" 
                                                    value="{{ old('email_dosen', $d->email) }}" 
                                                    data-original="{{ $d->email }}" required>
                                                <div class="invalid-feedback" id="email_error_{{ $d->dosen_id }}">
                                                    @error('email_dosen') {{ $message }} @else Format email tidak valid @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- Password -->
                                            <div class="mb-2 col-md-4">
                                                <label for="password_dosen_{{ $d->dosen_id }}" class="form-label">Password</label>
                                                <input type="password" class="form-control @error('password_dosen') is-invalid @enderror" 
                                                    id="password_dosen_{{ $d->dosen_id }}" name="password_dosen">
                                                <div class="invalid-feedback" id="password_error_{{ $d->dosen_id }}">
                                                    @error('password_dosen') {{ $message }} @enderror
                                                </div>
                                                <small class="text-muted">Password akan diupdate otomatis jika NIDN diubah</small>
                                            </div>
                                            
                                            <!-- Tanggal Lahir -->
                                            <div class="mb-2 col-md-4">
                                                <label for="tanggal_lahir_dosen_{{ $d->dosen_id }}" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir_dosen') is-invalid @enderror" 
                                                    id="tanggal_lahir_dosen_{{ $d->dosen_id }}" name="tanggal_lahir_dosen" 
                                                    value="{{ old('tanggal_lahir_dosen', $d->tanggal_lahir_dosen) }}">
                                                <div class="invalid-feedback" id="tanggal_lahir_error_{{ $d->dosen_id }}">
                                                    @error('tanggal_lahir_dosen') {{ $message }} @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- Jenis Kelamin -->
                                            <div class="mb-2 col-md-4">
                                                <label for="jenis_kelamin_dosen_{{ $d->dosen_id }}" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select @error('jenis_kelamin_dosen') is-invalid @enderror" 
                                                    id="jenis_kelamin_dosen_{{ $d->dosen_id }}" name="jenis_kelamin_dosen">
                                                    <option value="" {{ old('jenis_kelamin_dosen', $d->jenis_kelamin_dosen) == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-Laki" {{ old('jenis_kelamin_dosen', $d->jenis_kelamin_dosen) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                    <option value="Perempuan" {{ old('jenis_kelamin_dosen', $d->jenis_kelamin_dosen) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                                <div class="invalid-feedback" id="jenis_kelamin_error_{{ $d->dosen_id }}">
                                                    @error('jenis_kelamin_dosen') {{ $message }} @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- Telepon -->
                                            <div class="mb-2 col-md-4">
                                                <label for="telepon_dosen_{{ $d->dosen_id }}" class="form-label">Telepon</label>
                                                <input type="text" class="form-control @error('telepon_dosen') is-invalid @enderror" 
                                                    id="telepon_dosen_{{ $d->dosen_id }}" name="telepon_dosen" 
                                                    value="{{ old('telepon_dosen', $d->telepon_dosen) }}">
                                                <div class="invalid-feedback" id="telepon_error_{{ $d->dosen_id }}">
                                                    @error('telepon_dosen') {{ $message }} @enderror
                                                </div>
                                            </div>
                                            
                                            <!-- Foto Profil -->
                                            <div class="mb-2 col-md-6">
                                                <label for="profile_img_dosen_{{ $d->dosen_id }}" class="form-label">Foto Profil</label>
                                                @if ($d->profile_img_dosen)
                                                    <div class="mb-2">
                                                        <img src="{{ asset($d->profile_img_dosen) }}" alt="Profile Image" class="img-thumbnail" style="max-height: 100px;">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('profile_img_dosen') is-invalid @enderror" 
                                                    id="profile_img_dosen_{{ $d->dosen_id }}" name="profile_img_dosen" accept="image/*">
                                                <div class="invalid-feedback" id="profile_img_error_{{ $d->dosen_id }}">
                                                    @error('profile_img_dosen') {{ $message }} @else Format gambar tidak valid. Gunakan jpeg, png, jpg, gif. Maksimal 2MB. @enderror
                                                </div>
                                                <small class="text-muted">Format gambar: jpeg, png, jpg, gif. Maksimal 2MB.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batalkan</button>
                                        <button type="submit" class="btn btn-add">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal konfirmasi delete -->
                    <div class="modal fade" id="modalDelete{{ $d->dosen_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $d->dosen_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteDataDosen', $d->dosen_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $d->dosen_id }}">Konfirmasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah yakin ingin menghapus data <strong>{{ $d-> nama_dosen}}</strong>?
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
<script src="{{ asset('js/Koordiantor/data_dosen.js') }}"></script>
@endsection