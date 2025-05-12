<!-- Form Dokumen Penunjang -->
<div id="dokumen-penunjang-section" class="detail-dokumen-penunjang flex-grow-1 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-0">Dokumen Penunjang</h5>
            <div>
                <div class="mt-3">
                    <form id="formDokumenPenunjang">
                        <input type="hidden" name="proyek_id" value="{{ $proyek->proyek_id }}">
                        @csrf
                        <div class="mb-3 row align-items-center">
                            <label for="nama_dokumen_penunjang" class="col-md-2 col-form-label">Nama Dokumen<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control form-selection" id="nama_dokumen_penunjang" name="nama_dokumen_penunjang" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="jenis_dokumen_penunjang_id" class="col-md-2 col-form-label">Jenis Dokumen<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="form-select form-selection" id="jenis_dokumen_penunjang_id" name="jenis_dokumen_penunjang_id" required>
                                    <option value="" disabled selected>Pilih Jenis Dokumen Penunjang</option>
                                    @foreach($jenisDokumenPenunjang as $jenis)
                                        <option value="{{ $jenis->jenis_dokumen_penunjang_id }}" data-nama="{{ $jenis->nama_jenis_dokumen_penunjang }}" class="form-selection">
                                            {{ $jenis->nama_jenis_dokumen_penunjang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="file_dokumen_penunjang" class="col-md-2 col-form-label">File Dokumen<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="file" class="form-control form-selection" id="file_dokumen_penunjang" name="file_dokumen_penunjang" accept=".pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx" required>
                                <small class="text-muted">Format: pdf, doc, docx, ppt, pptx, xls, xlsx</small>
                            </div>
                        </div>
                        <!-- Button Tambah ke Daftar -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-add" id="btnTambahDokumen">Tambahkan ke Daftar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Document Preview Table Section -->
            <div id="previewDokumenSection" class="mt-4 d-none">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Dokumen Yang Akan Disimpan</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="previewDokumenTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dokumen</th>
                                        <th>Jenis Dokumen</th>
                                        <th>File</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Preview documents will be added here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-tutup me-2" id="btnBatalPreview">Batal</button>
                            <button type="button" class="btn btn-add" id="btnSimpanDokumen">Simpan Semua Dokumen</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Dokumen Penunjang -->
            <div class="title-table d-flex justify-content-between align-items-center mb-3 mt-5 border-top pt-3">
                <h4 class="m-0">Daftar Dokumen Penunjang</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="{{ route('koordinator.detailDataProyek', ['id' => $proyek->proyek_id]) }}#dokumen-penunjang-section" method="GET" id="searchDokumenForm">
                            <input type="text" name="searchDokumenPenunjang" class="form-control pe-5 form-search" id="searchDokumenPenunjang" placeholder="Cari Dokumen..." value="{{ request('searchDokumenPenunjang') ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped" id="tableDokumenPenunjang">
                    <thead>
                        <tr>
                            <th>Nama Dokumen</th>
                            <th>Jenis Dokumen</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>

            <div id="emptyDokumenMessage" class="text-center py-3 d-none">
                <div class="py-4">
                    <!-- Pesan kosong akan dimuat melalui JavaScript -->
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <!-- Info showing entries - akan diupdate via JavaScript -->
                <div id="dokumenPaginationInfo" class="showing-text">
                    Showing 0 to 0 of 0 entries
                </div>
                
                <!-- Pagination links -->
                <div id="dokumenPagination">
                    <!-- Pagination akan dimuat via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>