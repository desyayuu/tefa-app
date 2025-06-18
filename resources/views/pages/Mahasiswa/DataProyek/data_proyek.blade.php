<div class="detail-proyek-container flex-grow-1 me-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Detail Data Proyek</h4>
            </div>

            @include('components.handling_error', ['section' => 'detail_proyek'])
            
            <form id="formProyek" style="font-size: 14px;" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Jenis Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="jenis_proyek" class="col-md-3 col-form-label">Jenis Proyek <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-select select2-basic" id="jenis_proyek" name="jenis_proyek" 
                                    disabled required>
                                @foreach($jenisProyek as $jenis)
                                    <option value="{{ $jenis->jenis_proyek_id }}" {{ $proyek->jenis_proyek_id == $jenis->jenis_proyek_id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis_proyek }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Nama Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="nama_proyek" class="col-md-3 col-form-label">Nama Proyek <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control form-selection" id="nama_proyek" name="nama_proyek" 
                                   value="{{ $proyek->nama_proyek }}" disabled required>
                        </div>
                    </div>

                    <!-- Mitra Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="mitra_id" class="col-md-3 col-form-label">Mitra Proyek <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-select select2-basic" id="mitra_id" name="mitra_id" 
                                    disabled required>
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
                        <label for="status_proyek" class="col-md-3 col-form-label">Status Proyek <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-select form-selection" id="status_proyek" name="status_proyek" 
                                    disabled required>
                                <option value="Initiation" {{ $proyek->status_proyek == 'Initiation' ? 'selected' : '' }}>Initiation</option>
                                <option value="In Progress" {{ $proyek->status_proyek == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Done" {{ $proyek->status_proyek == 'Done' ? 'selected' : '' }}>Done</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="mb-3 row align-items-center">
                        <label for="tanggal_mulai" class="col-md-3 col-form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="date" class="form-control form-selection" id="tanggal_mulai" name="tanggal_mulai" 
                                   value="{{ $proyek->tanggal_mulai }}" disabled required>
                        </div>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="mb-3 row align-items-center">
                        <label for="tanggal_selesai" class="col-md-3 col-form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="date" class="form-control form-selection" id="tanggal_selesai" name="tanggal_selesai" 
                                   value="{{ $proyek->tanggal_selesai }}" disabled required>
                        </div>
                    </div>

                    <!-- Deskripsi Proyek -->
                    <div class="mb-3 row align-items-center">
                        <label for="deskripsi_proyek" class="col-md-3 col-form-label">Deskripsi Proyek</label>
                        <div class="col-md-9">
                            <textarea class="form-control form-selection" id="deskripsi_proyek" name="deskripsi_proyek" 
                                      rows="3" disabled>{{ $proyek->deskripsi_proyek }}</textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
