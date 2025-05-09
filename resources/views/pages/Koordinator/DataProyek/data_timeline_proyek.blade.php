<div class="data-timeline-container flex-grow-1 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
        <div id="timeline-section" class="title-table d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Timeline Proyek</h4>
            <div class="d-flex gap-2 align-items-center">
                <div class="position-relative">
                    <form action="{{ route('koordinator.detailDataProyek', ['id' => $proyek->proyek_id]) }}#timeline-section" method="GET">
                        <input type="text" name="search_timeline" class="form-control pe-5 form-search" placeholder="Cari Timeline..." value="{{ $searchTimeline ?? '' }}">
                        <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>

                @if(isset($searchTimeline) && $searchTimeline)
                <a href="{{ route('koordinator.detailDataProyek', ['id' => $proyek->proyek_id]) }}#timeline-section" class="btn btn-tutup btn-outline-secondary">
                    Hapus Filter
                </a>
                @endif

                <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahTimeline">
                    Tambah Data
                </button>
            </div>
        </div>

            <!-- Timeline Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Timeline</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($timelines) && count($timelines) > 0)
                            @foreach($timelines as $index => $timeline)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $timeline->nama_timeline_proyek }}</td>
                                    <td>{{ \Carbon\Carbon::parse($timeline->tanggal_mulai_timeline)->format('d F Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($timeline->tanggal_selesai_timeline)->format('d F Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-action-detail" data-id="{{ $timeline->timeline_proyek_id }}" data-bs-toggle="modal" data-bs-target="#modalEditTimeline">
                                                <svg width="15" height="15" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                                    <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                            </button>
                                            <button type="button" class="btn btn-action-delete btn-delete-timeline" data-id="{{ $timeline->timeline_proyek_id }}">
                                                <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data timeline</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Timeline -->
<div class="modal fade" id="modalTambahTimeline" aria-hidden="true" aria-labelledby="modalTambahTimelineLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('koordinator.tambahDataTimeline') }}" method="POST" id="formTambahDataTimeline" novalidate>
                @csrf
                <!-- Tambahkan hidden input untuk proyek_id -->
                <input type="hidden" name="proyek_id" id="proyek_id" value="{{ $proyek->proyek_id ?? '' }}">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahDataLabel">Tambah Data Timeline</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Tambahkan alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_timeline_error"></div>
                    
                    <div class="row mb-3">
                        <!-- Nama Timeline -->
                        <div class="mb-2 col-md-6">
                            <label for="nama_timeline" class="form-label">Nama Timeline</label>
                            <input type="text" class="form-control" id="nama_timeline" name="nama_timeline" required>
                            <div class="invalid-feedback" id="nama_timeline_error"></div>
                        </div>
                        <!-- Tanggal Mulai -->
                        <div class="mb-2 col-md-6">
                            <label for="tanggal_mulai_timeline" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai_timeline" name="tanggal_mulai_timeline" required>
                            <div class="invalid-feedback" id="tanggal_mulai_timeline_error"></div>
                        </div>
                        <!-- Tanggal Selesai -->
                        <div class="mb-2 col-md-6">
                            <label for="tanggal_selesai_timeline" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tanggal_selesai_timeline" name="tanggal_selesai_timeline" required>
                            <div class="invalid-feedback" id="tanggal_selesai_timeline_error"></div>
                        </div>
                        <!-- Deskripsi -->
                        <div class="mb-2 col-md-6">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi_timeline" rows="3"></textarea>
                            <div class="invalid-feedback" id="deskripsi_timeline_error"></div>
                        </div>
                        <div class="mt-2">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarTimeline">Tambahkan ke Daftar</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="daftar-timeline-container mt-5">
                        <h5>Daftar Timeline yang Akan Ditambahkan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Timeline</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftarTimeline">
                                    <tr id="emptyRowTimeline">
                                        <td colspan="4" class="text-center">Belum ada timeline yang ditambahkan ke daftar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Hidden input untuk menyimpan data multiple timeline -->
                        <input type="hidden" name="timeline_data" id="timelineJsonData" value="[]">
                        <input type="hidden" name="is_single" id="isSingleTimeline" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanTimeline">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Timeline -->
<div class="modal fade" id="modalEditTimeline" aria-hidden="true" aria-labelledby="modalEditTimelineLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="" method="POST" id="formEditTimeline" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_timeline_id" id="edit_timeline_id">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditTimelineLabel">Edit Data Timeline</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error -->
                    <div class="alert alert-danger d-none" id="edit_form_error"></div>
                    
                    <div class="mb-3">
                        <label for="edit_nama_timeline" class="form-label">Nama Timeline <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_timeline" name="nama_timeline" required>
                        <div class="invalid-feedback" id="edit_nama_timeline_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tanggal_mulai_timeline" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_tanggal_mulai_timeline" name="tanggal_mulai_timeline" required>
                        <div class="invalid-feedback" id="edit_tanggal_mulai_timeline_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tanggal_selesai_timeline" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="edit_tanggal_selesai_timeline" name="tanggal_selesai_timeline" required>
                        <div class="invalid-feedback" id="edit_tanggal_selesai_timeline_error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi_timeline" rows="3"></textarea>
                        <div class="invalid-feedback" id="edit_deskripsi_timeline_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnUpdateTimeline">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add CSRF Token Meta for JS -->
<meta name="csrf-token" content="{{ csrf_token() }}">