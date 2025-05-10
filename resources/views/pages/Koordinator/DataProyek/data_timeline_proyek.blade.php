{{-- timeline-section.blade.php --}}

<div class="data-timeline-container flex-grow-1 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="timeline-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Timeline Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form id="searchTimelineForm">
                            <input type="text" name="search_timeline" id="searchTimeline" class="form-control pe-5 form-search" placeholder="Cari Timeline..." value="{{ $searchTimeline ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahTimeline">
                        Tambah Data
                    </button>
                </div>
            </div>

            <!-- Timeline Table -->
            <div class="table-responsive">
                <table class="table table-striped" id="tableDataTimeline">
                    <thead>
                        <tr>
                            <th>Nama Timeline</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
            <div id="emptyDataTimelineMessage" class="text-center py-3 d-none">
                <div class="py-4">
                    <!-- Pesan kosong akan dimuat melalui JavaScript -->
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- Info showing entries - akan diupdate via JavaScript -->
                <div id="timelinePaginationInfo" class="showing-text">
                    Showing 0 to 0 of 0 entries
                </div>
                
                <!-- Pagination links -->
                <div id="timelinePagination">
                    <!-- Pagination akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Timeline -->
<div class="modal fade" id="modalTambahTimeline" aria-hidden="true" aria-labelledby="modalTambahTimelineLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahDataTimeline" novalidate>
                @csrf
                <!-- Hidden input untuk proyek_id -->
                <input type="hidden" name="proyek_id" id="proyek_id" value="{{ $proyek->proyek_id ?? '' }}">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahDataLabel">Tambah Data Timeline</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
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
                            <label for="deskripsi_timeline" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_timeline" name="deskripsi_timeline" rows="3"></textarea>
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
                    <button type="button" class="btn btn-tutup" id="btnCancelTimeline" data-bs-dismiss="modal">Batal</button>
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
            <form id="formEditTimeline" novalidate>
                @csrf
                <input type="hidden" name="_method" value="PUT">
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