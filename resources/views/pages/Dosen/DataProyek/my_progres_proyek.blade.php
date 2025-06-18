<div class="data-progres-container flex-grow-1 pb-3" id="dataMyProgresContainer">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="my-progres-proyek-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">
                    Progres Saya
                </h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form id="searchMyProgresForm">
                            <input type="text" name="search_my_progres_proyek" id="searchMyProgres" class="form-control pe-5 form-search" placeholder="Cari Progres Saya..." value="">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahProgresFromDosenSelf">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Data
                    </button>
                </div>
            </div>

            <!-- Info Section untuk Progres Saya -->
            <div class="alert alert-info alert-sm mb-3" id="myProgresInfo">
                <small>
                    Progres Saya menampilkan progres yang Anda buat atau yang ditugaskan kepada Anda di proyek <strong>{{ $proyek->nama_proyek ?? 'Proyek' }}</strong>.
                </small>
            </div>

            <div class="table-responsive">
                <table class="table" id="tableMyProgresProyek">
                    <thead>
                        <tr>
                            <th>Nama Progres</th>
                            <th>Status</th>
                            <th>Persentase</th>
                            <th>Ditugaskan Ke</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
            <div id="emptyMyProgresMessage" class="text-center py-3 d-none">
                <div class="py-4">
                    <!-- Pesan kosong akan dimuat melalui JavaScript -->
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- Info showing entries - akan diupdate via JavaScript -->
                <div id="myProgresProyekPaginationInfo" class="showing-text">
                    Showing 0 to 0 of 0 entries
                </div>
                
                <!-- Pagination links -->
                <div id="myProgresPagination">
                    <!-- Pagination akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>
 </div>


<!-- Modal Tambah Progres Saya - UPDATED VERSION WITH DATE FIELDS -->
<div class="modal fade" id="modalTambahProgresFromDosenSelf" aria-hidden="true" aria-labelledby="modalTambahMyProgresLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahMyProgres" novalidate>
                @csrf
                <!-- Hidden input untuk proyek_id -->
                <input type="hidden" name="proyek_id" id="my_proyek_id" value="{{ $proyek->proyek_id ?? '' }}">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahMyProgresLabel">
                        Tambah Progres Saya
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="my_form_progres_error"></div>
                    
                    <!-- Info untuk Progres Saya -->
                    <div class="alert alert-info alert-sm mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>
                            <strong>Progres Saya:</strong> Progres yang Anda buat akan otomatis ditugaskan kepada Anda sesuai dengan posisi Anda di proyek ini.
                        </small>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Nama Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="my_nama_progres" class="form-label">Nama Progres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="my_nama_progres" name="nama_progres" required placeholder="Masukkan nama progres">
                            <div class="invalid-feedback" id="my_nama_progres_error"></div>
                        </div>
                        
                        <!-- Status Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="my_status_progres" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="my_status_progres" name="status_progres" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Done">Done</option>
                            </select>
                            <div class="invalid-feedback" id="my_status_progres_error"></div>
                        </div>

                        <!-- Tanggal Mulai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="my_tanggal_mulai_progres_section">
                            <label for="my_tanggal_mulai_progres" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="my_tanggal_mulai_progres" name="tanggal_mulai_progres">
                            <div class="invalid-feedback" id="my_tanggal_mulai_progres_error"></div>
                            <small class="text-muted" id="my_tanggal_mulai_progres_hint"></small>
                        </div>

                        <!-- Tanggal Selesai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="my_tanggal_selesai_progres_section">
                            <label for="my_tanggal_selesai_progres" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="my_tanggal_selesai_progres" name="tanggal_selesai_progres">
                            <div class="invalid-feedback" id="my_tanggal_selesai_progres_error"></div>
                            <small class="text-muted" id="my_tanggal_selesai_progres_hint"></small>
                        </div>
                        
                        <!-- Persentase Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="my_persentase_progres" class="form-label">Persentase Pengerjaan</label>
                            <div class="input-group">
                                <input type="number" min="0" max="100" class="form-control" id="my_persentase_progres" name="persentase_progres" required placeholder="0" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="invalid-feedback" id="my_persentase_progres_error"></div>
                        </div>
                        
                        <!-- Auto Assignment Display - Read Only -->
                        <div class="mb-3 col-md-6">
                            <label for="my_assignment_display" class="form-label">Ditugaskan Kepada</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="my_assignment_display" disabled value="{{ $dosenInfo->nama_dosen ?? 'Nama tidak ditemukan' }}">
                            </div>
                            <small class="form-text text-muted">Otomatis ditugaskan kepada Anda</small>
                        </div>
                        
                        <!-- Hidden Assignment Fields -->
                        <input type="hidden" id="my_assigned_to" name="assigned_to" value="">
                        <input type="hidden" id="my_assigned_type_hidden" name="assigned_type" value="">
                        
                        <!-- Deskripsi -->
                        <div class="mb-3 col-12">
                            <label for="my_deskripsi_progres" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="my_deskripsi_progres" name="deskripsi_progres" rows="3" placeholder="Masukkan deskripsi progres"></textarea>
                            <div class="invalid-feedback" id="my_deskripsi_progres_error"></div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarMyProgres">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Tambahkan ke Daftar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="daftar-progres-container mt-5">
                        <h5>Daftar Progres yang Akan Ditambahkan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Progres</th>
                                        <th>Status</th>
                                        <th>Persentase</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Ditugaskan Kepada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftarMyProgres">
                                    <tr id="emptyRowMyProgres">
                                        <td colspan="7" class="text-center">Belum ada progres yang ditambahkan ke daftar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Hidden input untuk menyimpan data multiple progres -->
                        <input type="hidden" name="progres_data" id="myProgresJsonData" value="[]">
                        <input type="hidden" name="is_single" id="myIsSingleProgres" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" id="btnCancelMyProgres" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanMyProgres">
                        <i class="bi bi-save me-1"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>