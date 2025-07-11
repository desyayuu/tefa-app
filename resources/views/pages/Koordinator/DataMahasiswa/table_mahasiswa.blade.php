<div class="content-table">
            <!-- Handling Error and Success -->
            @include('components.handling_error')
            
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Data Mahasiswa</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.dataMahasiswa') }}" method="GET">
                            <input type="text" name="search" class="form-control pe-5 form-search" placeholder="Cari Mahasiswa..." value="{{$search ?? '' }}">
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
                    <a href="{{ route('koordinator.dataMahasiswa') }}" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif
                    
                    <!-- Modal Tambah Data Mahasiswa -->
                    <div class="modal fade" id="modalTambahMahasiswa" aria-hidden="true" aria-labelledby="modalTambahMahasiswaLabel" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{route ('koordinator.tambahDataMahasiswa') }}" method="POST" id="formTambahMahasiswa" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="modalTambahMahasiswaLabel">Tambah Data Mahasiswa</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body label-form">
                                        <div class="row mb-3">
                                            <!-- Nama Mahasiswa -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nama_mahasiswa" class="form-label">Nama Mahasiswa</label>
                                                <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" >
                                                <div class="invalid-feedback" id="nama_mahasiswa_error"></div>
                                            </div>
                                            <!-- NIM -->
                                            <div class="mb-2 col-md-6">
                                                <label for="nim_mahasiswa" class="form-label">NIM</label>
                                                <input type="text" class="form-control" id="nim_mahasiswa" name="nim_mahasiswa" 
                                                    maxlength="10" pattern="[0-9]+" title="NIM harus berupa angka dan maksimal 10 digit">
                                                <div class="invalid-feedback" id="nim_error"></div>
                                                <small class="text-muted">NIM harus berupa angka dan maksimal 10 digit</small>
                                            </div>
                                            <!-- Status Akun -->
                                            <div class="mb-2 col-md-4">
                                                <label for="status_akun_mahasiswa" class="form-label">Status Akun</label>
                                                <select class="form-select" id="status_akun_mahasiswa" name="status_akun_mahasiswa">
                                                    <option value="Active" selected>Active</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Rejected">Rejected</option>
                                                    <option value="Disabled">Disabled</option>
                                                </select>
                                                <div class="invalid-feedback" id="status_akun_error"></div>
                                            </div>
                                            <!-- Email -->
                                            <div class="mb-2 col-md-4">
                                                <label for="email_mahasiswa" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email_mahasiswa" name="email_mahasiswa">
                                                <div class="invalid-feedback" id="email_error"></div>
                                            </div>
                                            <!-- Password -->
                                            <div class="mb-2 col-md-4">
                                                <label for="password_mahasiswa" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="password_mahasiswa" name="password_mahasiswa">
                                                <div class="invalid-feedback" id="password_error"></div>
                                                <small class="text-muted">Password akan otomatis menggunakan jika field ini kosong</small>
                                            </div>
                                            <!-- Tanggal Lahir -->
                                            <div class="mb-2 col-md-4">
                                                <label for="tanggal_lahir_mahasiswa" class="form-label">Tanggal Lahir</label>
                                                <input type="date" class="form-control" id="tanggal_lahir_mahasiswa" name="tanggal_lahir_mahasiswa">
                                                <div class="invalid-feedback" id="tanggal_lahir_error"></div>
                                            </div>
                                            <!-- Jenis Kelamin -->
                                            <div class="mb-2 col-md-4">
                                                <label for="jenis_kelamin_mahasiswa" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select" id="jenis_kelamin_mahasiswa" name="jenis_kelamin_mahasiswa">
                                                    <option value="" selected>Pilih Jenis Kelamin</option>
                                                    <option value="Laki-Laki">Laki-Laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                                <div class="invalid-feedback" id="jenis_kelamin_error"></div>
                                            </div>
                                            <!-- Telepon -->
                                            <div class="mb-2 col-md-4">
                                                <label for="telepon_mahasiswa" class="form-label">Telepon</label>
                                                <input type="text" class="form-control" id="telepon_mahasiswa" name="telepon_mahasiswa">
                                                <div class="invalid-feedback" id="telepon_error"></div>
                                            </div>
                                            <!-- Foto Profil -->
                                            <div class="mb-2 col-md-6">
                                                <label for="profile_img_mahasiswa" class="form-label">Foto Profil</label>
                                                <input type="file" class="form-control" id="profile_img_mahasiswa" name="profile_img_mahasiswa" accept="image/*">
                                                <div class="invalid-feedback" id="profile_img_error"></div>
                                                <small class="text-muted">Format gambar: jpeg, png, jpg, gif. Maksimal 2MB.</small>
                                            </div>
                                            <div class="mt-2">
                                                <div class="col-12 text-end">
                                                    <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarMahasiswa">Tambahkan ke Daftar</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Error message for form submit -->
                                        <div class="alert alert-danger d-none" id="form_error"></div>
                                        
                                        <div class="daftar-mahasiswa-container mt-5">
                                            <h5>Daftar Mahasiswa yang Akan Ditambahkan</h5>
                                            <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Mahasiswa</th>
                                                        <th>NIM</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Foto</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="daftarMahasiswa">
                                                    <tr id="emptyRow">
                                                        <td colspan="6" class="text-center">Belum ada mahasiswa yang ditambahkan ke daftar</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>
                                            
                                            <!-- Hidden input untuk menyimpan data multiple mahasiswa -->
                                            <input type="hidden" name="mahasiswa_data" id="mahasiswaJsonData" value="[]">
                                            <input type="hidden" name="is_single" id="isSingle" value="1">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-add" id="btnSimpanMahasiswa">Simpan Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-add" data-bs-target="#modalTambahMahasiswa" data-bs-toggle="modal">Tambah Data</button>
                </div>
            </div>
            
            <!-- Handling Hasil Search -->
            @if(isset($search) && $search)
            <div class="alert alert-info">
                Menampilkan {{ count($mahasiswa) }} hasil untuk pencarian "{{ $search }}"
            </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                    <!-- <th scope="col">#</th> -->
                    <th scope="col" style="width: 30%">Nama Mahasiswa</th>
                    <th scope="col" style="width: 15%">NIM</th>
                    <th scope="col" style="width: 40%">Bidang Keahlian</th>
                    <th scope="col" style="width: 15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mahasiswa as $m)
                    <tr>
                        <td>{{ $m->nama_mahasiswa}}</td>
                        <td>{{ $m->nim_mahasiswa }}</td>
                        <td>
                            @if(isset($bidangKeahlianMahasiswa[$m->mahasiswa_id]) && $bidangKeahlianMahasiswa[$m->mahasiswa_id]->isNotEmpty())
                                @php
                                    $bidangKeahlianList = $bidangKeahlianMahasiswa[$m->mahasiswa_id];
                                    $totalBidang = $bidangKeahlianList->count();
                                @endphp
                                
                                @if($totalBidang <= 2)
                                    <!-- Tampilkan semua jika <= 2 -->
                                    @foreach($bidangKeahlianList as $bidang)
                                        <span class="badge bg-primary me-1 mb-1">{{ $bidang->nama_bidang_keahlian }}</span>
                                    @endforeach
                                @else
                                    <!-- Tampilkan 2 pertama + counter jika > 2 -->
                                    @foreach($bidangKeahlianList->take(2) as $bidang)
                                        <span class="badge bg-primary me-1 mb-1">{{ $bidang->nama_bidang_keahlian }}</span>
                                    @endforeach
                                    <span class="badge bg-secondary">+{{ $totalBidang - 2 }} lainnya</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('koordinator.detailDataMahasiswa', ['id' => $m->mahasiswa_id]) }}" class="{{ request()->routeIs('koordinator.detailDataMahasiswa') ? 'active' : '' }}">
                                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                        <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $m->mahasiswa_id }}">
                                    <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- Modal konfirmasi delete -->
                    <div class="modal fade" id="modalDelete{{ $m->mahasiswa_id }}" tabindex="-1" aria-labelledby="deleteLabel{{ $m->mahasiswa_id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('koordinator.deleteDataMahasiswa', $m->mahasiswa_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteLabel{{ $m->mahasiswa_id }}">Konfirmasi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah yakin ingin menghapus data <strong>{{ $m->nama_mahasiswa}}</strong>?
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
                    Showing {{ $mahasiswa->firstItem() }} to {{ $mahasiswa->lastItem() }} of {{ $mahasiswa->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $mahasiswa->appends(['search' => request('search')])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>