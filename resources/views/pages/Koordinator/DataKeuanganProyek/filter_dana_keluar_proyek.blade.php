<!-- ✅ UPDATE: Ganti struktur filter dalam paste-1.txt dengan yang ini -->
<div class="filter-data-keuangan-tefa-container flex-grow-1 mt-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="title-filter d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Filter Data</h5>
            </div>
            
            <div id="filterContainer">
                <form id="formFilterKeuanganTefa">
                    <div class="row">
                        <!-- Tanggal Mulai -->
                        <div class="col-md-3 mb-3">
                            <label for="filter_tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="filter_tanggal_mulai" name="filter_tanggal_mulai">
                        </div>
                        
                        <!-- Tanggal Akhir -->
                        <div class="col-md-3 mb-3">
                            <label for="filter_tanggal_akhir" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="filter_tanggal_akhir" name="filter_tanggal_akhir">
                            <div class="invalid-feedback" id="filter_tanggal_akhir_error"></div>
                        </div>
                        
                        <!-- Nama Transaksi -->
                        <div class="col-md-3 mb-3">
                            <label for="filter_nama_transaksi" class="form-label">Nama Transaksi</label>
                            <input type="text" class="form-control" id="filter_nama_transaksi" name="filter_nama_transaksi" placeholder="Cari nama transaksi...">
                        </div>
                        
                        <div class="col-md-3 mb-3" id="filterKategoriTransaksiContainer" style="display: none;">
                            <label for="filter_jenis_keuangan" class="form-label">Kategori Pengeluaran</label>
                            <select class="form-select" id="filter_jenis_keuangan" name="filter_jenis_keuangan">
                                <option value="">Semua Kategori</option>
                            </select>
                            <div class="invalid-feedback" id="filter_jenis_keuangan_error"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3" id="filterKategoriTransaksiContainer" style="display: none;">
                            <label for="filter_sub_jenis_transaksi" class="form-label">Kategori Transaksi</label>
                            <select class="form-select" id="filter_sub_jenis_transaksi" name="filter_sub_jenis_transaksi">
                                <option value="">Semua</option>
                            </select>
                            <div class="invalid-feedback" id="filter_sub_jenis_transaksi_error"></div>
                        </div>

                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-tutup" id="btnResetFilter">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-add" id="btnApplyFilter">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>