<div class="content-table">
    @include('components.handling_error')
    <div class="title-table d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0">Data Profesional</h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="position-relative">                    
                    <form action="" method="GET">
                        <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari profesional..." value="{{$search ?? '' }}">
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
                    <a href="{{ route('koordinator.dataProfesional') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <!-- Modal Tambah Data Profesional (Unified for Single dan Multiple) -->
                    <div class="modal fade" id="modalTambahDataProfesional" aria-hidden="true" aria-labelledby="modalTambahDataLabel" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.tambahDataProfesional') }}" method="POST" id="formTambahProfesional" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahProfesionalLabel">Tambah Data Profesional</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <div class="row mb-3">
                                            <!-- Nama Profesional -->
                                            <div class="mb-2 col-md-4">
                                                <label for="nama_profesional" class="form-label">Nama</label>
                                                <input type="text" class="form-control" id="nama_profesional" name="nama_profesional">
                                                <div class="invalid-feedback" id="nama_error"></div>
                                            </div>
                                            <div class="mb-2 col-md-4">
                                                <label for="jenis_kelamin_profesional" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select" id="jenis_kelamin_profesional" name="jenis_kelamin_profesional">
                                                    <option value="" selected>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-Laki">Laki-Laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                                <div class="invalid-feedback" id="jenis_kelamin_error"></div>
                                            </div>
                                            <div class="mb-2 col-md-4">
                                                <label for="tanggal_lahir_profesional" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control" id="tanggal_lahir_profesional" name="tanggal_lahir_profesional">
                                                <div class="invalid-feedback" id="tanggal_lahir_error"></div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="mb-2 col-md-4">
                                                <label for="status_akun_profesional" class="form-label">Status Akun</label>
                                                <select class="form-select" id="status_akun_profesional" name="status_akun_profesional">
                                                    <option value="Active" selected>Active</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Rejected">Rejected</option>
                                                    <option value="Disabled">Disabled</option>
                                                </select>
                                                <div class="invalid-feedback" id="status_akun_error"></div>
                                            </div>
                                            <div class="mb-2 col-md-4">
                                                <label for="email_profesional" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email_profesional" name="email_profesional">
                                                <div class="invalid-feedback" id="email_error"></div>
                                            </div>
                                            <div class="mb-2 col-md-4">
                                                <label for="password_profesional" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password_profesional" name="password_profesional">
                                                <div class="invalid-feedback" id="password_error"></div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="mb-2 col-md-6">
                                                <label for="telepon_profesional" class="form-label">Telepon</label>
                                                <input type="text" class="form-control" id="telepon_profesional" name="telepon_profesional">
                                                <div class="invalid-feedback" id="telepon_error"></div>
                                            </div>
                                            <div class="mb-2 col-md-6">
                                                <label for="profile_img_profesional" class="form-label">Foto Profil</label>
                                                <input type="file" class="form-control" id="profile_img_profesional" name="profile_img_profesional" accept="image/*">
                                                <div class="invalid-feedback" id="profile_img_error"></div>
                                                <small class="text-muted">Format gambar: jpeg, png, jpg, gif. Maksimal 2MB.</small>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-end">
                                            <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarProfesional">Tambahkan ke Daftar</button>
                                        </div>
                                        
                                        <!-- Error message for form submit -->
                                        <div class="alert alert-danger d-none" id="form_error"></div>
                                        
                                        <div class="daftar-profesional-container mt-5">
                                            <h5>Daftar Profesional yang Akan Ditambahkan</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Dosen</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                            <th>Foto</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="daftarProfesional">
                                                        <tr id="emptyRow">
                                                            <td colspan="5" class="text-center">Belum ada Profesional yang ditambahkan ke daftar</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <!-- Hidden input untuk menyimpan data multiple profesional-->
                                            <input type="hidden" name="profesional_data" id="profesionalJsonData" value="[]">
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
                    <button class="btn btn-add" data-bs-target="#modalTambahDataProfesional" data-bs-toggle="modal">Tambah Data</button>
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($profesional) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                    <!-- <th scope="col">#</th> -->
                    <th scope="col">Nama Profesional</th>
                    <th scope="col">Email</th>
                    <th scope="col">Telepon</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($profesional as $p)
                    <tr>
                        <!-- <th scope="row">{{ $loop->iteration }}</th> -->
                        <td>{{ $p->nama_profesional }}</td>
                        <td>{{ $p->email }}</td>
                        <td>{{ $p->telepon_profesional }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('koordinator.detailDataProfesional', ['id' => $p->profesional_id]) }}" class="{{ request()->routeIs('koordinator.detailDataProfesional') ? 'active' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                        <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $p->profesional_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit and Detail Data -->
                    <div class="modal fade" id="modalProfesional{{ $p->profesional_id }}" tabindex="-1" aria-labelledby="mitraLabel{{ $p->profesional_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.updateDataProfesional', $p->profesional_id) }}" method="POST" data-current-email="{{ $p->email }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="mitraLabel{{ $p->profesional_id }}">Edit Data Profesional</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <!-- Baris 1: Nama, Jenis Kelamin, Tanggal Lahir -->
                                        <div class="row mb-3">
                                            <!-- Nama Profesional -->
                                            <div class="mb-2 col-md-4">
                                                <label for="nama_profesional_{{ $p->profesional_id }}" class="form-label">Nama Profesional</label>
                                                <input type="text" class="form-control @error('nama_profesional') is-invalid @enderror" id="nama_profesional_{{ $p->profesional_id }}" name="nama_profesional" value="{{ old('nama_profesional', $p->nama_profesional) }}">
                                                @error('nama_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- Jenis Kelamin -->
                                            <div class="mb-2 col-md-4">
                                                <label for="jenis_kelamin_profesional_{{ $p->profesional_id }}" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select @error('jenis_kelamin_profesional') is-invalid @enderror" 
                                                    id="jenis_kelamin_profesional_{{ $p->profesional_id }}" name="jenis_kelamin_profesional">
                                                    <option value="" {{ old('jenis_kelamin_profesional', $p->jenis_kelamin_profesional) == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-Laki" {{ old('jenis_kelamin_profesional', $p->jenis_kelamin_profesional) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                    <option value="Perempuan" {{ old('jenis_kelamin_profesional', $p->jenis_kelamin_profesional) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                                @error('jenis_kelamin_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- Tanggal Lahir -->
                                            <div class="mb-2 col-md-4">
                                                <label for="tanggal_lahir_profesional_{{ $p->profesional_id }}" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir_profesional') is-invalid @enderror" 
                                                    id="tanggal_lahir_profesional_{{ $p->profesional_id }}" name="tanggal_lahir_profesional" 
                                                    value="{{ old('tanggal_lahir_profesional', $p->tanggal_lahir_profesional) }}">
                                                @error('tanggal_lahir_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Baris 2: Status Akun, Email, Password -->
                                        <div class="row mb-3">
                                            <!-- Status Akun -->
                                            <div class="mb-2 col-md-4">
                                                <label for="status_akun_profesional_{{ $p->profesional_id }}" class="form-label">Status Akun</label>
                                                <select class="form-select @error('status_akun_profesional') is-invalid @enderror" 
                                                    id="status_akun_profesional_{{ $p->profesional_id }}" name="status_akun_profesional">
                                                    <option value="Active" {{ old('status_akun_profesional', $p->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Pending" {{ old('status_akun_profesional', $p->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="Rejected" {{ old('status_akun_profesional', $p->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                    <option value="Disabled" {{ old('status_akun_profesional', $p->status) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                                </select>
                                                @error('status_akun_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- Email -->
                                            <div class="mb-2 col-md-4">
                                                <label for="email_profesional_{{ $p->profesional_id }}" class="form-label">Email</label>
                                                <input type="email" class="form-control @error('email_profesional') is-invalid @enderror" 
                                                    id="email_profesional_{{ $p->profesional_id }}" name="email_profesional" value="{{ old('email_profesional', $p->email) }}"
                                                    data-original="{{ $p->email }}">
                                                @error('email_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- Password -->
                                            <div class="mb-2 col-md-4">
                                                <label for="password_profesional_{{ $p->profesional_id }}" class="form-label">Password</label>
                                                <input type="password" class="form-control @error('password_profesional') is-invalid @enderror" 
                                                    id="password_profesional_{{ $p->profesional_id }}" name="password_profesional">
                                                @error('password_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Kosongi jika tidak ingin mengubah password</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Baris 3: Telepon, Foto Profil -->
                                        <div class="row mb-3">
                                            <!-- Telepon -->
                                            <div class="mb-2 col-md-6">
                                                <label for="telepon_profesional_{{ $p->profesional_id }}" class="form-label">Telepon</label>
                                                <input type="text" class="form-control @error('telepon_profesional') is-invalid @enderror" 
                                                    id="telepon_profesional_{{ $p->profesional_id }}" name="telepon_profesional" 
                                                    value="{{ old('telepon_profesional', $p->telepon_profesional) }}">
                                                @error('telepon_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <!-- Foto Profil -->
                                            <div class="mb-2 col-md-6">
                                                <label for="profile_img_profesional_{{ $p->profesional_id }}" class="form-label">Foto Profil</label>
                                                @if ($p->profile_img_profesional)
                                                    <div class="mb-2">
                                                        <img src="{{ asset($p->profile_img_profesional) }}" alt="Profile Image" class="img-thumbnail" style="max-height: 100px;">
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control @error('profile_img_profesional') is-invalid @enderror" 
                                                    id="profile_img_profesional_{{ $p->profesional_id }}" name="profile_img_profesional" accept="image/*">
                                                @error('profile_img_profesional')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                    <div class="modal fade" id="modalDelete{{ $p->profesional_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $p->profesional_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteDataProfesional', $p->profesional_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $p->profesional_id }}">Konfirmasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah yakin ingin menghapus data <strong>{{ $p->nama_profesional}}</strong>?
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
                    Showing {{ $profesional->firstItem() }} to {{ $profesional->lastItem() }} of {{ $profesional->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $profesional->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>
