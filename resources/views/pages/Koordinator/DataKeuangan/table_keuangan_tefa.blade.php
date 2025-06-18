<div class="data-keuangan-tefa-container flex-grow-1">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="keuangan-tefa-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Data Keuangan TEFA</h5>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahKeuanganTefa">
                        Tambah Data
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="tableDataKeuanganTefa">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Transaksi</th>
                            <th>Jenis Keuangan</th>
                            <th>Nama Proyek</th>
                            <th>Nama Transaksi</th>
                            <th>Nominal</th>
                            <th>Saldo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
            <div id="emptyDataKeuanganTefaMessage" class="text-center py-3 d-none">
                <div class="py-4">
                    <!-- Pesan kosong akan dimuat melalui JavaScript -->
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- Info showing entries - akan diupdate via JavaScript -->
                <div id="keuanganTefaPaginationInfo" class="showing-text">
                    Showing 0 to 0 of 0 entries
                </div>
                
                <!-- Pagination links -->
                <div id="keuanganTefaPagination">
                    <!-- Pagination akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Data Keuangan TEFA -->
<div class="modal fade" id="modalTambahKeuanganTefa" aria-hidden="true" aria-labelledby="modalTambahKeuanganTefaLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formTambahDataKeuanganTefa"  novalidate enctype="multipart/form-data">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalTambahDataLabel">Tambah Data KeuanganTefa</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_keuangan_tefa_error"></div>
                    
                    <div class="row">
                        <!-- Tanggal Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                            <div class="invalid-feedback" id="tanggal_transaksi_error"></div>
                        </div>

                        <!-- Jenis Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_transaksi_id" name="jenis_transaksi_id" required>
                                <option value="" disabled selected>Pilih Jenis Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="jenis_transaksi_id_error"></div>
                        </div>

                        <!-- Keperluan Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="jenis_keuangan_tefa_id" class="form-label">Keperluan Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_keuangan_tefa_id" name="jenis_keuangan_tefa_id" required>
                                <option value="" disabled selected>Pilih Keperluan Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="jenis_keuangan_tefa_id_error"></div>
                        </div>

                        
                        <!-- Proyek -->
                        <div class="col-md-6 mb-3" id="proyekContainer" style="display: none;">
                            <label for="proyek_id_selected" class="form-label">Proyek</label>
                                <select class="form-select select2-dropdown" id="proyek_id_selected" name="proyek_id_selected">
                                    <option value="" disabled selected>Pilih Proyek</option>
                                </select>
                            <div class="invalid-feedback" id="proyek_id_selected_error"></div>
                        </div>

                        <div class="col-md-6 mb-3" id="kategoriTransaksiContainer" style="display: none;">
                            <label for="sub_jenis_transaksi_id" class="form-label">Kategori Transaksi</label>
                            <select class="form-select select2-dropdown" id="sub_jenis_transaksi_id" name="sub_jenis_transaksi_id">
                                <option value="" disabled selected>Pilih Kategori Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="sub_jenis_transaksi_id_error"></div>
                        </div>


                        <!-- Nama Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_transaksi" class="form-label">Nama Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_transaksi" name="nama_transaksi" required>
                            <div class="invalid-feedback" id="nama_transaksi_error"></div>
                        </div>

                        <!-- Nominal -->
                        <div class="col-md-6 mb-3">
                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nominal" name="nominal" required>
                            <div class="invalid-feedback" id="nominal_error"></div>
                        </div>

                        <!-- Dokumen Bukti -->
                        <div class="col-6 mb-3">
                            <label for="file_keuangan_tefa" class="form-label">Dokumen Bukti</label>
                            <input type="file" class="form-control form-selection" id="file_keuangan_tefa" name="file_keuangan_tefa" 
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <div class="invalid-feedback" id="file_keuangan_tefa_error"></div>
                            <small class="form-text text-muted">Format: pdf, doc, .docx, .ppt, .pptx, .xls, .xlsx, .jpg, .jpeg. png</small>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-6 mb-3">
                            <label for="deskripsi_transaksi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_transaksi" name="deskripsi_transaksi" rows="3"></textarea>
                            <div class="invalid-feedback" id="deskripsi_transaksi_error"></div>
                        </div>
                        <div class="mt-2">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarKeuanganTefa">Tambahkan ke Daftar</button>
                            </div>
                        </div>
                    </div>

                    
                    <div class="daftar-keuangan_tefa-container mt-5">
                        <h5>Daftar KeuanganTefa yang Akan Ditambahkan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Jenis Keperluan</th>
                                        <th>Nominal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftarKeuanganTefa">
                                    <tr id="emptyRowKeuanganTefa">
                                        <td colspan="6" class="text-center">Belum ada keuangan tefa yang ditambahkan ke daftar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Hidden input untuk menyimpan data multiple keuangan_tefa -->
                        <input type="hidden" name="keuangan_tefa_data" id="keuangan_tefa_JsonData" value="[]">
                        <input type="hidden" name="is_single" id="isSingleKeuanganTefa" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" id="btnCancelKeuanganTefa" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanKeuanganTefa">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Data Keuangan TEFA -->
<div class="modal fade" id="modalEditKeuanganTefa" aria-hidden="true" aria-labelledby="modalEditKeuanganTefaLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditDataKeuanganTefa" novalidate enctype="multipart/form-data">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditKeuanganTefaLabel">Edit Data Keuangan TEFA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_keuangan_tefa_edit_error"></div>
                    
                    <div class="row">
                        <!-- Tanggal Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_transaksi" name="edit_tanggal_transaksi" required>
                            <div class="invalid-feedback" id="edit_tanggal_transaksi_error"></div>
                        </div>
                        <!-- Jenis Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis_transaksi_id" name="edit_jenis_transaksi_id" required>
                                <option value="" disabled selected>Pilih Jenis Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="edit_jenis_transaksi_id_error"></div>
                        </div>
                        <!-- Keperluan Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_jenis_keuangan_tefa_id" class="form-label">Keperluan Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis_keuangan_tefa_id" name="edit_jenis_keuangan_tefa_id" required>
                                <option value="" disabled selected>Pilih Keperluan Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="edit_jenis_keuangan_tefa_id_error"></div>
                        </div>
                        <!-- Proyek -->
                        <div class="col-md-6 mb-3" id="edit_proyekContainer" style="display: none;">
                            <label for="edit_proyek_id_selected" class="form-label">Proyek</label>
                            <select class="form-select select2-dropdown" id="edit_proyek_id_selected" name="edit_proyek_id_selected">
                                <option value="" disabled selected>Pilih Proyek</option>
                            </select>
                            <div class="invalid-feedback" id="edit_proyek_id_selected_error"></div>
                        </div>

                        <!-- Sub Jeis kATEGORI -->
                        <div class="col-md-6 mb-3" id="edit_kategoriTransaksiContainer" style="display: none;">
                            <label for="edit_sub_jenis_transaksi_id" class="form-label">Kategori Transaksi</label>
                            <select class="form-select select2-dropdown" id="edit_sub_jenis_transaksi_id" name="edit_sub_jenis_transaksi_id">
                                <option value="" disabled selected>Pilih Kategori Transaksi</option>
                            </select>
                            <div class="invalid-feedback" id="edit_sub_jenis_transaksi_id_error"></div>
                        </div>

                        <!-- Nama Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama_transaksi" class="form-label">Nama Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_transaksi" name="edit_nama_transaksi" required>
                            <div class="invalid-feedback" id="edit_nama_transaksi_error"></div>
                        </div>
                        <!-- Nominal -->
                        <div class="col-md-6 mb-3">
                            <label for="edit_nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nominal" name="edit_nominal" required>
                            <div class="invalid-feedback" id="edit_nominal_error"></div>
                        </div>
                        <!-- Deskripsi -->
                        <div class="col-6 mb-3">
                            <label for="edit_deskripsi_transaksi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi_transaksi" name="edit_deskripsi_transaksi" rows="3"></textarea>
                            <div class="invalid-feedback" id="edit_deskripsi_transaksi_error"></div>
                        </div>
                        <!-- Dokumen Bukti -->
                        <div class="col-6 mb-3">
                            <label for="edit_file_keuangan_tefa" class="form-label">Dokumen Bukti</label>
                            <input type="file" class="form-control form-selection" id="edit_file_keuangan_tefa" name="edit_file_keuangan_tefa" 
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <div class="invalid-feedback" id="edit_file_keuangan_tefa_error"></div>
                            <small class="form-text text-muted">Format: pdf, doc, .docx, .ppt, .pptx, .xls, .xlsx, .jpg, .jpeg. png</small>
                        </div>
                    </div>
                    <input type="hidden" name="keuangan_tefa_id" id="keuangan_tefa_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" id="btnCancelEditKeuanganTefa" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanEditKeuanganTefa">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>