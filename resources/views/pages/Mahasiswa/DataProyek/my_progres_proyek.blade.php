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
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahProgresFromMahasiswaSelf">
                        Tambah Data
                    </button>
                </div>
            </div>

            <!-- Info Section untuk Progres Saya -->
            <div class="alert alert-info alert-sm mb-3" id="myProgresInfo">
                <small>
                    Progres Saya menampilkan progres yang Anda buat atau yang ditugaskan kepada Anda di proyek <strong>{{$proyek->nama_proyek}}</strong>
                </small>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="tableMyProgresProyek">
                    <thead>
                        <tr>
                            <th width="25%">Nama Progres</th>
                            <th width="15%">Status</th>
                            <th width="25%">Persentase Pengerjaan</th>
                            <th width="25%">Ditugaskan Ke</th>
                            <th with="10%">Aksi</th>
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


 <!-- Modal Tambah Progres Saya -->
<div class="modal fade" id="modalTambahProgresFromMahasiswaSelf" aria-hidden="true" aria-labelledby="modalTambahMyProgresLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahDataProgres" novalidate>
                @csrf
                <!-- Hidden input untuk proyek_id -->
                <input type="hidden" name="proyek_id" id="proyek_id" value="{{ $proyek->proyek_id ?? '' }}">
                
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahMyProgresLabel">
                        Tambah Progres Saya - {{ $proyek->nama_proyek ?? 'Proyek' }}
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_progres_error"></div>
                    
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
                            <label for="nama_progres" class="form-label">Nama Progres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_progres" name="nama_progres" required placeholder="Masukkan nama progres">
                            <div class="invalid-feedback" id="nama_progres_error"></div>
                        </div>
                        
                        <!-- Status Progres -->
                        <div class="mb-3 col-md-6">
                            <label for="status_progres" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_progres" name="status_progres" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="Inisiasi">Inisiasi</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Done">Done</option>
                            </select>
                            <div class="invalid-feedback" id="status_progres_error"></div>
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
                        
                        <!-- Auto Assignment Display - Read Only -->
                        <div class="mb-3 col-md-6">
                            <label for="my_assignment_display" class="form-label">Ditugaskan Kepada</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="my_assignment_display" disabled value="{{ $mahasiswaInfo->nama_mahasiswa ?? 'Nama tidak ditemukan' }}">
                            </div>
                            <small class="form-text text-muted">Otomatis ditugasakan kepada Anda</small>
                        </div>
                        
                        <!-- Hidden Assignment Fields -->
                        <input type="hidden" id="assigned_to" name="assigned_to" value="">
                        <input type="hidden" id="assigned_type_hidden" name="assigned_type" value="">
                        
                        <!-- Deskripsi -->
                        <div class="mb-3 col-12">
                            <label for="deskripsi_progres" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_progres" name="deskripsi_progres" rows="3" placeholder="Masukkan deskripsi progres (opsional)"></textarea>
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
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Progres</th>
                                        <th>Status</th>
                                        <th>Persentase</th>
                                        <th>Ditugaskan Kepada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftarProgres">
                                    <tr id="emptyRowProgres">
                                        <td colspan="5" class="text-center">Belum ada progres yang ditambahkan ke daftar</td>
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