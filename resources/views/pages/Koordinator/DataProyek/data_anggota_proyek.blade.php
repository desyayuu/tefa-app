<div class="anggota-proyek-container flex-grow-1 me-3 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Anggota Proyek</h5>
            
            <!-- Project Leader Section -->
            <div class="mb-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Project Leader</h6>
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#addLeaderModal">Edit</button>
                </div>

                @if(isset($projectLeader) && $leaderInfo)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div class="text-secondary">
                        {{ $projectLeader->leader_type == 'dosen' ? 'Dosen' : 'Profesional' }} 
                        {{ $leaderInfo->nama }}
                    </div>
                    <button class="btn btn-danger rounded-circle btn-sm" onclick="removeLeader('{{ $projectLeader->project_leader_id }}')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                @else
                <p class="text-muted small">Belum ada project leader</p>
                @endif
            </div>
            
            <!-- Dosen Section -->
            <div class="mb-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Dosen</h6>
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#addDosenModal">
                        Tambah Data
                    </button>
                </div>

                @forelse($dosenAnggota ?? [] as $dosen)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div class="text-secondary">
                        {{ $dosen->nama_dosen }}
                    </div>
                    <button class="btn btn-danger rounded-circle btn-sm" onclick="removeAnggota('dosen', '{{ $dosen->anggota_id }}')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                @empty
                <p class="text-muted small">Belum ada dosen</p>
                @endforelse
            </div>
            
            <!-- Profesional Section -->
            <div class="mb-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Profesional</h6>
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#addProfesionalModal">
                        Tambah Data
                    </button>
                </div>

                @forelse($profesionalAnggota ?? [] as $profesional)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div class="text-secondary">
                        {{ $profesional->nama_profesional }}
                    </div>
                    <button class="btn btn-danger rounded-circle btn-sm" onclick="removeAnggota('profesional', '{{ $profesional->anggota_id }}')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                @empty
                <p class="text-muted small">Belum ada profesional</p>
                @endforelse
            </div>
            
            <!-- Mahasiswa Section -->
            <div class="mb-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Mahasiswa</h6>
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill"
                            data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                        Tambah Data
                    </button>
                </div>

                @forelse($mahasiswaAnggota ?? [] as $mahasiswa)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div class="text-secondary">
                        {{ $mahasiswa->nama_mahasiswa }}
                    </div>
                    <button class="btn btn-danger rounded-circle btn-sm" onclick="removeAnggota('mahasiswa', '{{ $mahasiswa->anggota_id }}')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                @empty
                <p class="text-muted small">Belum ada mahasiswa</p>
                @endforelse
            </div>
        </div>
    </div>
</div>