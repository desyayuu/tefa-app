<div class="content-table" id="section-portofolio">
    @include('components.handling_error')
    
    <div class="title-table d-flex justify-content-between align-items-center mb-3" style="font-size: 14px;">
        <h5>Portofolio Mahasiswa</h5>
        <div class="d-flex gap-2 align-items-center">
            <div class="position-relative">
                <form action="{{ route('koordinator.detailDataMahasiswa', $mahasiswa->mahasiswa_id) }}" method="GET" id="searchPortofolioForm">
                    <input type="text" name="search_portofolio" class="form-control pe-5 form-search" 
                           placeholder="Cari portofolio..." value="{{ $searchPortofolio ?? '' }}">
                    <input type="hidden" name="scroll_to" value="portofolio">
                    <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
            </div>
            
            <!-- Button Clear Search-->
            @if(isset($searchPortofolio) && $searchPortofolio)
            <a href="{{ route('koordinator.detailDataMahasiswa', $mahasiswa->mahasiswa_id) }}?scroll_to=portofolio" class="btn btn-tutup btn-outline-secondary">
                Hapus Filter
            </a>
            @endif
            
            <button class="btn btn-add" data-bs-target="#modalTambahPortofolio" data-bs-toggle="modal">
                Tambah Data
            </button>
        </div>
    </div>

    @if(isset($searchPortofolio) && $searchPortofolio)
    <div class="alert alert-info">
        Menampilkan {{ $portofolioMahasiswa->count() }} hasil untuk pencarian "{{ $searchPortofolio }}"
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th scope="col" width="25%">Nama Kegiatan</th>
                    <th scope="col" width="15%">Jenis Kegiatan</th>
                    <th scope="col" width="20%">Penyelenggara</th>
                    <th scope="col" width="15%">Peran</th>
                    <th scope="col" width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($portofolioMahasiswa as $portofolio)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>{{ $portofolio->nama_kegiatan }}</div>
                            @if($portofolio->tingkat_kegiatan)
                                <span class="badge bg-secondary ms-2">{{ $portofolio->tingkat_kegiatan }}</span>
                            @endif
                        </div>
                                
                        @if($portofolio->link_kegiatan)
                            <small>
                                <a href="{{ $portofolio->link_kegiatan }}" target="_blank" class="text-primary d-inline-block mt-1">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    Link Kegiatan
                                </a>
                            </small>
                        @endif
                    </td>
                    <td>
                        @php
                        $jenisColors = [
                            'Magang' => 'primary',
                            'Pelatihan' => 'success',
                            'Lomba' => 'warning',
                            'Penelitian' => 'info',
                            'Pengabdian' => 'secondary',
                            'Lainnya' => 'dark'
                        ];
                        $color = $jenisColors[$portofolio->jenis_kegiatan] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ $portofolio->jenis_kegiatan }}</span>
                    </td>
                    <td>{{ $portofolio->penyelenggara ?: '-' }}</td>
                    <td>{{ $portofolio->peran_dalam_kegiatan ?: '-' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="#" 
                               class="btn-edit" 
                               data-id="{{ $portofolio->portofolio_id }}">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                    <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                </svg>
                            </a>
                            
                            <a href="#" 
                               class="btn-delete" 
                               data-id="{{ $portofolio->portofolio_id }}">
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
                        @if(isset($searchPortofolio) && $searchPortofolio)
                            Tidak ada hasil yang cocok dengan pencarian "{{ $searchPortofolio }}"
                        @else
                            Belum ada data portofolio
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center">
            <div class="showing-text">
                Showing {{ $portofolioMahasiswa->firstItem() ?? 0 }} to {{ $portofolioMahasiswa->lastItem() ?? 0 }} of {{ $portofolioMahasiswa->total() ?? 0 }} entries
            </div>
            <div class="pagination-links">
                {{ $portofolioMahasiswa->appends(['search_portofolio' => request('search_portofolio'), 'scroll_to' => 'portofolio'])->links('vendor.pagination.custom_master') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="modalTambahPortofolio" tabindex="-1" aria-labelledby="modalTambahPortofolioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahPortofolioLabel">Tambah Data Portofolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahPortofolio" method="POST" action="{{ route('koordinator.portofolio.tambah') }}">
                @csrf
                <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->mahasiswa_id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_kegiatan" class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_kegiatan" name="jenis_kegiatan" required>
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="Magang">Magang</option>
                                    <option value="Pelatihan">Pelatihan</option>
                                    <option value="Lomba">Lomba</option>
                                    <option value="Penelitian">Penelitian</option>
                                    <option value="Pengabdian">Pengabdian</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" class="form-control" id="penyelenggara" name="penyelenggara">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tingkat_kegiatan" class="form-label">Tingkat Kegiatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="tingkat_kegiatan" name="tingkat_kegiatan" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Internasional">Internasional</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Regional">Regional</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="peran_dalam_kegiatan" class="form-label">Peran dalam Kegiatan</label>
                                <input type="text" class="form-control" id="peran_dalam_kegiatan" name="peran_dalam_kegiatan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="link_kegiatan" class="form-label">Link Kegiatan</label>
                                <input type="url" class="form-control" id="link_kegiatan" name="link_kegiatan" placeholder="https://linkkegiatan.com">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" placeholder="Masukkan deskripsi kegiatan (opsional)"></textarea>
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
<div class="modal fade" id="modalEditPortofolio" tabindex="-1" aria-labelledby="modalEditPortofolioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPortofolioLabel">Edit Data Portofolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPortofolio" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nama_kegiatan" class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_kegiatan" name="nama_kegiatan" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_jenis_kegiatan" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_kegiatan" name="jenis_kegiatan" required>
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="Magang">Magang</option>
                                    <option value="Pelatihan">Pelatihan</option>
                                    <option value="Lomba">Lomba</option>
                                    <option value="Penelitian">Penelitian</option>
                                    <option value="Pengabdian">Pengabdian</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" class="form-control" id="edit_penyelenggara" name="penyelenggara">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tingkat_kegiatan" class="form-label">Tingkat Kegiatan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tingkat_kegiatan" name="tingkat_kegiatan" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="Internasional">Internasional</option>
                                    <option value="Nasional">Nasional</option>
                                    <option value="Regional">Regional</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_peran_dalam_kegiatan" class="form-label">Peran dalam Kegiatan</label>
                                <input type="text" class="form-control" id="edit_peran_dalam_kegiatan" name="peran_dalam_kegiatan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_link_kegiatan" class="form-label">Link Kegiatan</label>
                                <input type="url" class="form-control" id="edit_link_kegiatan" name="link_kegiatan" placeholder="https://linkkegiatan.com">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
                        <textarea class="form-control" id="edit_deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" placeholder="Masukkan deskripsi kegiatan (opsional)"></textarea>
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