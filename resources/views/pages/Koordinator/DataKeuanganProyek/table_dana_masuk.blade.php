<div class="data-masuk-keuangan-proyek-container flex-grow-1">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="data-masuk-keuangan-proyek-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Rincian Dana Pemasukan</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form id="searchDataMasukKeuanganProyekForm">
                            <input type="text" name="search_data_masuk_keuangan_proyek" id="searchDataMasukKeuanganProyek" class="form-control pe-5 form-search" placeholder="Cari Data..." value="{{ $searchDataMasukKeuanganProyek ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>

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
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah  -->
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
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_transaksi" class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_transaksi_id" class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_transaksi_id" name="jenis_transaksi_id" required>
                                @foreach($jenisTransaksi as $jenis)
                                    <option value="{{ $jenis->jenis_transaksi_id }}">{{ $jenis->nama_jenis_transaksi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jenis_keuangan_tefa_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_keuangan_tefa_id" name="jenis_keuangan_tefa_id" required>
                                @foreach($jenisKeuanganTefa as $jenis)
                                    <option value="{{ $jenis->jenis_keuangan_tefa_id }}">{{ $jenis->nama_jenis_keuangan_tefa }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control currency-input" id="nominal" name="nominal" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nama_transaksi" class="form-label">Nama Transaksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_transaksi" name="nama_transaksi" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi_transaksi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi_transaksi" name="deskripsi_transaksi" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bukti_transaksi" class="form-label">Bukti Transaksi (PDF/JPG/PNG, maks 2MB)</label>
                        <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>