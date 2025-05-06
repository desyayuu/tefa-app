<div class="detail-proyek-container flex-grow-1 me-3 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Detail Data Proyek</h5>
                <button type="button" class="btn btn-add" id="edit-button">Simpan Perubahan</button>
            </div>

            <form id="formProyek" style="font-size: 14px;" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Jenis Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="jenis_proyek" class="col-md-2 col-form-label">Jenis Proyek</label>
                        <div class="col-md-10">
                        <select class="form-select" id="jenis_proyek" name="jenis_proyek">
                            @foreach($jenisProyek as $jenis)
                                <option value="{{ $jenis->jenis_proyek_id }}" {{ $proyek->jenis_proyek_id == $jenis->jenis_proyek_id ? 'selected' : '' }}>
                                    {{ $jenis->nama_jenis_proyek }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label for="nama_proyek" class="col-md-2 col-form-label">Nama Proyek</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="nama_proyek" name="nama_proyek" value="{{ $proyek->nama_proyek }}">
                        </div>
                    </div>

                    <!-- Mitra Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="mitra_id" class="col-md-2 col-form-label">Mitra Proyek</label>
                        <div class="col-md-10">
                            <select class="form-select" id="mitra_id" name="mitra_id">
                                @foreach($daftarMitra as $mitra)
                                    <option value="{{ $mitra->mitra_proyek_id }}" {{ $proyek->mitra_proyek_id == $mitra->mitra_proyek_id ? 'selected' : '' }}>
                                        {{ $mitra->nama_mitra }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Status Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="status_proyek" class="col-md-2 col-form-label">Status Proyek</label>
                        <div class="col-md-10">
                        <select class="form-select" id="status_proyek" name="status_proyek">
                            <option value="Initiation" {{ $proyek->status_proyek == 'Initiation' ? 'selected' : '' }}>Initiation</option>
                            <option value="In Progress" {{ $proyek->status_proyek == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Done" {{ $proyek->status_proyek == 'Done' ? 'selected' : '' }}>Done</option>
                        </select>
                        </div>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="mb-3 row align-items-center">
                        <label for="tanggal_mulai" class="col-md-2 col-form-label">Tanggal Mulai</label>
                        <div class="col-md-10">
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ $proyek->tanggal_mulai }}">
                        </div>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="mb-3 row align-items-center">
                        <label for="tanggal_selesai" class="col-md-2 col-form-label">Tanggal Selesai</label>
                        <div class="col-md-10">
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ $proyek->tanggal_selesai }}">
                        </div>
                    </div>

                    <!-- Dana Pendanaan -->
                    <div class="mb-3 row align-items-center">
                        <label for="dana_pendanaan" class="col-md-2 col-form-label">Dana Pendanaan</label>
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control edit-only" id="dana_pendanaan" name="dana_pendanaan" value="{{ $proyek->dana_pendanaan }}" style="display: none;">
                                <input type="text" class="form-control view-only" value="{{ number_format($proyek->dana_pendanaan, 0, ',', '.') }}">
                            </div>
                        </div>    
                    </div>

                    <!-- Deskripsi Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="deskripsi_proyek" class="col-md-2 col-form-label">Deskripsi Proyek</label>
                        <div class="col-md-10">
                            <textarea class="form-control" id="deskripsi_proyek" name="deskripsi_proyek" rows="3">{{ $proyek->deskripsi_proyek }}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
