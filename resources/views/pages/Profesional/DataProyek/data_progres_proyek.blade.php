<div class="data-progres-container flex-grow-1 pb-3" id="dataProgresContainer">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="data-progres-proyek-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Progres Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form id="searchProgresForm">
                            <input type="text" name="search_progres_proyek" id="searchProgres" class="form-control pe-5 form-search" placeholder="Cari Progres..." value="{{ $searchProgres ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @if($isLeader)
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahProgresFromProfesional">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Data
                    </button>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="tableDataProgresProyek">
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
            <div id="emptyDataProgresProyekMessage" class="text-center py-3 d-none">
                <div class="py-4">
                    <!-- Pesan kosong akan dimuat melalui JavaScript -->
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- Info showing entries - akan diupdate via JavaScript -->
                <div id="progresProyekPaginationInfo" class="showing-text">
                    Showing 0 to 0 of 0 entries
                </div>
                
                <!-- Pagination links -->
                <div id="progresProyekPagination">
                    <!-- Pagination akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Progres -->
<div class="modal fade" id="modalTambahProgresFromProfesional" aria-hidden="true" aria-labelledby="modalTambahProgresLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahDataProgres" novalidate>
                @csrf
                <!-- Hidden input untuk proyek_id -->
                <input type="hidden" name="proyek_id" id="proyek_id" value="{{ $proyek->proyek_id ?? '' }}">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambaProgresLabel">Tambah Data Progres</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_progres_error"></div>
                    
                    <div class="row mb-3">
                        <!-- Nama Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="nama_progres" class="form-label">Nama Progres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_progres" name="nama_progres" required placeholder="Masukkan nama progres">
                            <div class="invalid-feedback" id="nama_progres_error"></div>
                        </div>
                        
                        <!-- Status Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="status_progres" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_progres" name="status_progres" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Done">Done</option>
                            </select>
                            <div class="invalid-feedback" id="status_progres_error"></div>
                        </div>
                        
                        <!-- Tanggal Mulai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="tanggal_mulai_progres_section">
                            <label for="tanggal_mulai_progres" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal_mulai_progres" name="tanggal_mulai_progres">
                            <div class="invalid-feedback" id="tanggal_mulai_progres_error"></div>
                            <small class="text-muted" id="tanggal_mulai_progres_hint"></small>
                        </div>

                        <!-- Tanggal Selesai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="tanggal_selesai_progres_section">
                            <label for="tanggal_selesai_progres" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal_selesai_progres" name="tanggal_selesai_progres">
                            <div class="invalid-feedback" id="tanggal_selesai_progres_error"></div>
                            <small class="text-muted" id="tanggal_selesai_progres_hint"></small>
                        </div>

                        <!-- Persentase Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="persentase_progres" class="form-label">Persentase Pengerjaan</label>
                            <div class="input-group">
                                <input type="number" min="0" max="100" class="form-control" id="persentase_progres" name="persentase_progres" required placeholder="0" value="0">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="invalid-feedback" id="persentase_progres_error"></div>
                        </div>
                        
                        <!-- Assigned To - Two Step -->
                        <div class="mb-3 col-md-6">
                            <label for="assigned_type" class="form-label">Ditugaskan Kepada <span class="text-danger">*</span></label>
                            <select class="form-select" id="assigned_type" name="assigned_type">
                                <option value="" selected>Pilih Tipe</option>
                                <option value="leader">Project Leader</option>
                                <option value="dosen">Anggota Dosen</option>
                                <option value="profesional">Anggota Profesional</option>
                                <option value="mahasiswa">Anggota Mahasiswa</option>
                            </select>
                            <div class="invalid-feedback" id="assigned_type_error"></div>
                        </div>
                        
                        <!-- Leader Section -->
                        <div class="mb-3 col-md-6 d-none" id="leader_section">
                            <label for="leader_assign_id" class="form-label">Pilih Project Leader <span class="text-danger">*</span></label>
                            <select class="form-select select2-assign-leader" id="leader_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="leader_assign_id_error"></div>
                        </div>
                        
                        <!-- Dosen Section -->
                        <div class="mb-3 col-md-6 d-none" id="dosen_section">
                            <label for="dosen_assign_id" class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select class="form-select select2-assign-dosen" id="dosen_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="dosen_assign_id_error"></div>
                        </div>
                        
                        <!-- Profesional Section -->
                        <div class="mb-3 col-md-6 d-none" id="profesional_section">
                            <label for="profesional_assign_id" class="form-label">Pilih Profesional <span class="text-danger">*</span></label>
                            <select class="form-select select2-assign-profesional" id="profesional_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="profesional_assign_id_error"></div>
                        </div>
                        
                        <!-- Mahasiswa Section -->
                        <div class="mb-3 col-md-6 d-none" id="mahasiswa_section">
                            <label for="mahasiswa_assign_id" class="form-label">Pilih Mahasiswa <span class="text-danger">*</span></label>
                            <select class="form-select select2-assign-mahasiswa" id="mahasiswa_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="mahasiswa_assign_id_error"></div>
                        </div>
                        
                        <!-- Hidden field for assigned_to -->
                        <input type="hidden" id="assigned_to" name="assigned_to" value="">
                        <input type="hidden" id="assigned_type_hidden" name="assigned_type" value="">
                        
                        <!-- Deskripsi -->
                        <div class="mb-3 col-12">
                            <label for="deskripsi_progres" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_progres" name="deskripsi_progres" rows="3" placeholder="Masukkan deskripsi progres"></textarea>
                            <div class="invalid-feedback" id="deskripsi_progres_error"></div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarProgres">
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
                                <tbody id="daftarProgres">
                                    <tr id="emptyRowProgres">
                                        <td colspan="7" class="text-center">Belum ada progres yang ditambahkan ke daftar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Hidden input untuk menyimpan data multiple progres -->
                        <input type="hidden" name="progres_data" id="progresJsonData" value="[]">
                        <input type="hidden" name="is_single" id="isSingleProgres" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" id="btnCancelProgres" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanProgres">
                        <i class="bi bi-save me-1"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Progres -->
<div class="modal fade" id="modalEditProgres" aria-hidden="true" aria-labelledby="modalEditProgresLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditProgres" novalidate>
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="edit_progres_id" id="edit_progres_id">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditProgresLabel">Edit Data Progres</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error -->
                    <div class="alert alert-danger d-none" id="edit_form_error"></div>
                    
                    <div class="row mb-3">
                        <!-- Nama Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="edit_nama_progres" class="form-label">Nama Progres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_progres" name="nama_progres" required>
                            <div class="invalid-feedback" id="edit_nama_progres_error"></div>
                        </div>
                        
                        <!-- Status Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="edit_status_progres" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status_progres" name="status_progres" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="To Do">To Do</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Done">Done</option>
                            </select>
                            <div class="invalid-feedback" id="edit_status_progres_error"></div>
                        </div>
                        
                        <!-- Tanggal Mulai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="edit_tanggal_mulai_progres_section">
                            <label for="edit_tanggal_mulai_progres" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="edit_tanggal_mulai_progres" name="tanggal_mulai_progres">
                            <div class="invalid-feedback" id="edit_tanggal_mulai_progres_error"></div>
                            <small class="text-muted" id="edit_tanggal_mulai_progres_hint"></small>
                        </div>

                        <!-- Tanggal Selesai - Hidden by default -->
                        <div class="mb-3 col-md-6 d-none" id="edit_tanggal_selesai_progres_section">
                            <label for="edit_tanggal_selesai_progres" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="edit_tanggal_selesai_progres" name="tanggal_selesai_progres">
                            <div class="invalid-feedback" id="edit_tanggal_selesai_progres_error"></div>
                            <small class="text-muted" id="edit_tanggal_selesai_progres_hint"></small>
                        </div>

                        <!-- Persentase Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="edit_persentase_progres" class="form-label">Persentase Pengerjaan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="0" max="100" class="form-control" id="edit_persentase_progres" name="persentase_progres" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="invalid-feedback" id="edit_persentase_progres_error"></div>
                        </div>
                        
                        <!-- Assigned To - Two Step -->
                        <div class="mb-3 col-md-6">
                            <label for="edit_assigned_type" class="form-label">Ditugaskan Kepada <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_assigned_type" name="assigned_type">
                                <option value="" selected>Pilih Tipe</option>
                                <option value="leader">Project Leader</option>
                                <option value="dosen">Anggota Dosen</option>
                                <option value="profesional">Anggota Profesional</option>
                                <option value="mahasiswa">Anggota Mahasiswa</option>
                            </select>
                            <div class="invalid-feedback" id="edit_assigned_type_error"></div>
                        </div>
                        
                        <!-- Leader Section -->
                        <div class="mb-3 col-md-6 d-none" id="edit_leader_section">
                            <label for="edit_leader_assign_id" class="form-label">Pilih Project Leader <span class="text-danger">*</span></label>
                            <select class="form-select select2-edit-leader" id="edit_leader_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="edit_leader_assign_id_error"></div>
                        </div>
                        
                        <!-- Dosen Section -->
                        <div class="mb-3 col-md-6 d-none" id="edit_dosen_section">
                            <label for="edit_dosen_assign_id" class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                            <select class="form-select select2-edit-dosen" id="edit_dosen_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="edit_dosen_assign_id_error"></div>
                        </div>
                        
                        <!-- Profesional Section -->
                        <div class="mb-3 col-md-6 d-none" id="edit_profesional_section">
                            <label for="edit_profesional_assign_id" class="form-label">Pilih Profesional <span class="text-danger">*</span></label>
                            <select class="form-select select2-edit-profesional" id="edit_profesional_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="edit_profesional_assign_id_error"></div>
                        </div>
                        
                        <!-- Mahasiswa Section -->
                        <div class="mb-3 col-md-6 d-none" id="edit_mahasiswa_section">
                            <label for="edit_mahasiswa_assign_id" class="form-label">Pilih Mahasiswa <span class="text-danger">*</span></label>
                            <select class="form-select select2-edit-mahasiswa" id="edit_mahasiswa_assign_id">
                                <option value="">Cari nama...</option>
                            </select>
                            <div class="invalid-feedback" id="edit_mahasiswa_assign_id_error"></div>
                        </div>
                        
                        <!-- Hidden field for assigned_to -->
                        <input type="hidden" id="edit_assigned_to" name="assigned_to" value="">
                        <input type="hidden" id="edit_assigned_type_hidden" name="assigned_type" value="">
                        
                        <!-- Deskripsi -->
                        <div class="mb-3 col-12">
                            <label for="edit_deskripsi_progres" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi_progres" name="deskripsi_progres" rows="3"></textarea>
                            <div class="invalid-feedback" id="edit_deskripsi_progres_error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnUpdateProgres">
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>