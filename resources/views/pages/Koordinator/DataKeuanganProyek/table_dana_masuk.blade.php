<div class="data-masuk-keuangan-proyek-container flex-grow-1">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="data-masuk-keuangan-proyek-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Rincian Dana Pemasukan</h5>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                        Tambah Data
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="tableKeuangan">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically via AJAX -->
                    </tbody>
                </table>
                
                <!-- Pagination Info and Controls -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div id="keuanganTefaPaginationInfo" class="showing-text">
                        Showing 0 to 0 from 0 entries
                    </div>
                    <div id="keuanganTefaPagination">
                        <!-- Pagination akan dimuat via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransactionModalLabel">Tambah Pemasukan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTransactionForm" enctype="multipart/form-data">
                    <input type="hidden" name="proyek_id" value="{{ $proyek->proyek_id }}">
                    
                    <!-- Pass subkategori data and availability flag to JavaScript -->
                    <script type="application/json" id="subkategoriData">
                        {!! json_encode($subkategoriPemasukan) !!}
                    </script>
                    
                    <!-- Pass hasSubkategoriPemasukan flag to JavaScript -->
                    <script>
                        window.hasSubkategoriPemasukan = {{ json_encode($hasSubkategoriPemasukan ?? false) }};
                        console.log('HasSubkategoriPemasukan flag:', window.hasSubkategoriPemasukan);
                    </script>
                    
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="form_pemasukan_error" role="alert"></div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                            <div class="invalid-feedback" id="tanggal_transaksi_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_transaksi_id" name="jenis_transaksi_id" required disabled>
                                @foreach($jenisTransaksi as $jenis)
                                    <option value="{{ $jenis->jenis_transaksi_id }}" selected>{{ $jenis->nama_jenis_transaksi }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="jenis_transaksi_id" id="hidden_jenis_transaksi_id">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jenis_keuangan_tefa_id" class="form-label">Keperluan Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_keuangan_tefa_id" required disabled>
                                @foreach($jenisKeuanganTefa as $jenis)
                                    @if($jenis->nama_jenis_keuangan_tefa == 'Proyek')
                                        <option value="{{ $jenis->jenis_keuangan_tefa_id }}" selected>{{ $jenis->nama_jenis_keuangan_tefa }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <input type="hidden" name="jenis_keuangan_tefa_id" id="hidden_jenis_keuangan_tefa_id">
                        </div>
                        <div class="col-md-6">
                            <label for="nama_proyek" class="form-label">Proyek</label>
                            <input type="text" class="form-control" id="nama_proyek" value="{{ $proyek->nama_proyek }}" disabled>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6" id="subkategoriContainer" style="display: none;">
                            <label for="subkategori_pemasukan_id" class="form-label">
                                Kategori Pemasukan 
                                <span class="text-danger">*</span>
                            </label>
                            <div class="invalid-feedback" id=sub_kategori_error></div>
                            <select class="form-select" id="subkategori_pemasukan_id" name="subkategori_pemasukan_id">
                                <option value="">Pilih Kategori Pemasukan</option>
                                @if(isset($subkategoriPemasukan) && $subkategoriPemasukan->count() > 0)
                                    @foreach($subkategoriPemasukan as $subkategori)
                                        <option value="{{ $subkategori->sub_jenis_transaksi_id }}">
                                            {{ $subkategori->nama_sub_jenis_transaksi }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" id="subkategori_pemasukan_id_error"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="nominal" name="nominal">
                            </div>
                            <div class="invalid-feedback" id="nominal_error"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_transaksi" class="form-label">Nama Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_transaksi" name="nama_transaksi">
                            <div class="invalid-feedback" id="nama_transaksi_error"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bukti_transaksi" class="form-label">Dokumen Bukti</label>
                            <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Format: pdf, doc, docx, ppt, pptx, xls, xlsx, jpg, jpeg, png (Max: 10MB)</small>
                            <div class="invalid-feedback" id="bukti_transaksi_error"></div>
                            <div id="file_preview_container"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="deskripsi_transaksi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_transaksi" name="deskripsi_transaksi" rows="3"></textarea>
                            <div class="invalid-feedback" id="deskripsi_transaksi_error"></div>
                        </div>
                    </div> 

                    <div class="mt-2">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-add" id="btnTambahkanKeDaftarPemasukan">Tambahkan ke Daftar</button>
                        </div>
                    </div>

                    <!-- Enhanced List Display -->
                    <div class="daftar-pemasukan-container mt-5">
                        <h5>Daftar Pemasukan Keuangan Proyek yang Akan ditambahkan</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama Transaksi</th>
                                        <th>Tanggal Transaksi</th>
                                        <th>Nominal</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="daftarPemasukan">
                                    <tr id="emptyRowPemasukan">
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="bi bi-inbox me-2"></i>
                                            Belum ada pemasukan yang ditambahkan ke daftar
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="pemasukan_data" id="pemasukan_JsonData" value="[]">
                        <input type="hidden" name="is_single" id="isSinglePemasukan" value="1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addTransactionForm" class="btn btn-add" id="btnSimpanPemasukan">Simpan</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Edit - Fixed with proper default values -->
<div class="modal fade" id="modalEditKeuanganProyek" aria-hidden="true" aria-labelledby="modalEditKeuanganProyekLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditDataKeuanganProyek" novalidate enctype="multipart/form-data">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEditKeuanganProyekLabel">Edit Data Pemasukan Keuangan Proyek</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body label-form">
                    <!-- Alert untuk error keseluruhan form -->
                    <div class="alert alert-danger d-none" id="form_keuangan_tefa_edit_error"></div>
                    
                    <!-- Loading State -->
                    <div id="editLoadingState" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data transaksi...</p>
                    </div>
                    
                    <!-- Form Content -->
                    <div id="editFormContent">
                        <div class="row">
                            <!-- Tanggal Transaksi -->
                            <div class="col-md-6 mb-3">
                                <label for="edit_tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_tanggal_transaksi" name="edit_tanggal_transaksi" required>
                                <div class="invalid-feedback" id="edit_tanggal_transaksi_error"></div>
                            </div>
                            
                            <!-- Jenis Transaksi - Fixed with proper value -->
                            <div class="col-md-6 mb-3">
                                <label for="edit_jenis_transaksi_id" class="form-label">Jenis Transaksi</label>
                                <select class="form-select" id="edit_jenis_transaksi_id" name="edit_jenis_transaksi_id" disabled>
                                    @foreach($jenisTransaksi as $jenis)
                                        @if($jenis->nama_jenis_transaksi == 'Pemasukan')
                                            <option value="{{ $jenis->jenis_transaksi_id }}" selected>{{ $jenis->nama_jenis_transaksi }}</option>
                                        @else
                                            <option value="{{ $jenis->jenis_transaksi_id }}">{{ $jenis->nama_jenis_transaksi }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Jenis transaksi tidak dapat diubah untuk pemasukan proyek</small>
                            </div>
                            
                            <!-- Keperluan Transaksi - Fixed with proper value -->
                            <div class="col-md-6 mb-3">
                                <label for="edit_jenis_keuangan_tefa_id" class="form-label">Keperluan Transaksi</label>
                                <select class="form-select" id="edit_jenis_keuangan_tefa_id" name="edit_jenis_keuangan_tefa_id" disabled>
                                    @foreach($jenisKeuanganTefa as $jenis)
                                        @if($jenis->nama_jenis_keuangan_tefa == 'Proyek')
                                            <option value="{{ $jenis->jenis_keuangan_tefa_id }}" selected>{{ $jenis->nama_jenis_keuangan_tefa }}</option>
                                        @else
                                            <option value="{{ $jenis->jenis_keuangan_tefa_id }}">{{ $jenis->nama_jenis_keuangan_tefa }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Keperluan transaksi tidak dapat diubah untuk pemasukan proyek</small>
                            </div>
                            
                            <!-- Proyek - Display Only -->
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_proyek" class="form-label">Proyek</label>
                                <input type="text" class="form-control" id="edit_nama_proyek" value="{{ $proyek->nama_proyek }}" disabled>
                                <input type="hidden" name="proyek_id" value="{{ $proyek->proyek_id }}">
                            </div>

                            <!-- ✅ KOREKSI: Modal Edit dengan logic conditional required yang benar -->
                            <div class="col-md-6 mb-3" id="edit_kategoriTransaksiContainer" style="display: none;">
                                <label for="edit_sub_jenis_transaksi_id" class="form-label">
                                    Kategori Pemasukan
                                    <!-- ✅ Required indicator akan ditambah via JavaScript jika diperlukan -->
                                </label>
                                <select class="form-select" id="edit_sub_jenis_transaksi_id" name="edit_sub_jenis_transaksi_id">
                                    <option value="">Pilih Kategori Pemasukan</option>
                                    @if(isset($subkategoriPemasukan) && $subkategoriPemasukan->count() > 0)
                                        @foreach($subkategoriPemasukan as $subkategori)
                                            <option value="{{ $subkategori->sub_jenis_transaksi_id }}">
                                                {{ $subkategori->nama_sub_jenis_transaksi }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback" id="edit_sub_jenis_transaksi_id_error"></div>
                                <!-- Help text yang akan diupdate via JavaScript -->
                                <small class="form-text text-muted" id="edit_subkategori_help_text">
                                    Kategori pemasukan untuk membantu kategorisasi transaksi.
                                </small>
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
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control currency-input" id="edit_nominal" name="edit_nominal" required>
                                </div>
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
                                <small class="form-text text-muted">Format: pdf, doc, docx, ppt, pptx, xls, xlsx, jpg, jpeg, png.</small>
                                
                                <div id="edit_file_preview_container"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Only keep the transaction ID -->
                    <input type="hidden" name="keuangan_tefa_id" id="keuangan_tefa_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" id="btnCancelEditKeuanganProyek" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add" id="btnSimpanEditKeuanganProyek">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
