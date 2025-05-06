<!-- Modal untuk Dosen -->
<div class="modal fade" id="addDosenModal" tabindex="-1" aria-labelledby="addDosenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDosenModalLabel">Tambah Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="tipe_anggota" value="dosen">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dosen_id" class="form-label">Pilih Dosen</label>
                        <select class="form-select" id="dosen_id" name="anggota_id" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dataDosen ?? [] as $dosen)
                                <option value="{{ $dosen->dosen_id }}">{{ $dosen->nama_dosen }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Profesional -->
<div class="modal fade" id="addProfesionalModal" tabindex="-1" aria-labelledby="addProfesionalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProfesionalModalLabel">Tambah Profesional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="tipe_anggota" value="profesional">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profesional_id" class="form-label">Pilih Profesional</label>
                        <select class="form-select" id="profesional_id" name="anggota_id" required>
                            <option value="">-- Pilih Profesional --</option>
                            @foreach($dataProfesional ?? [] as $profesional)
                                <option value="{{ $profesional->profesional_id }}">{{ $profesional->nama_profesional }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Mahasiswa -->
<div class="modal fade" id="addMahasiswaModal" tabindex="-1" aria-labelledby="addMahasiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMahasiswaModalLabel">Tambah Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="tipe_anggota" value="mahasiswa">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mahasiswa_id" class="form-label">Pilih Mahasiswa</label>
                        <select class="form-select" id="mahasiswa_id" name="anggota_id" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($dataMahasiswa ?? [] as $mahasiswa)
                                <option value="{{ $mahasiswa->mahasiswa_id }}">{{ $mahasiswa->nama_mahasiswa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>