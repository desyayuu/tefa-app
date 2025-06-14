<div class="anggota-proyek-container flex-grow-1 me-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Anggota Proyek</h5>
            
            {{-- Hanya tampilkan handling_error di bagian anggota proyek --}}
            @include('components.handling_error', ['section' => 'anggota_proyek'])
            
            <!-- Project Leader Section -->
            <div class="card-project-leader">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-subtitle mb-0">Project Leader</div>
                </div>
                
                @if(isset($projectLeader) && $leaderInfo)
                <div class="d-flex align-items-center justify-content-between py-2">
                    <div class="text-anggota">
                        {{ $leaderInfo->nama }}
                    </div>
                </div>
                @else
                <p class="text-muted small">Belum ada project leader</p>
                @endif
            </div>

            <!-- Anggota Dosen Section -->
            <div class="card-project-leader mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Dosen</h6>
                </div>
                
                @if(isset($anggotaDosen) && count($anggotaDosen) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaDosen as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_dosen }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small">Belum ada anggota dosen</p>
                @endif
            </div>

            <!-- Anggota Profesional Section -->
            <div class="card-project-leader mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Profesional</h6>
                </div>
                
                @if(isset($anggotaProfesional) && count($anggotaProfesional) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaProfesional as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_profesional }}</div>
                                </div>
                                <div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small">Belum ada anggota profesional</p>
                @endif
            </div>

            <!-- Anggota Mahasiswa Section -->
            <div class="card-project-leader mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-subtitle mb-0">Mahasiswa</h6>
                </div>
                
                @if(isset($anggotaMahasiswa) && count($anggotaMahasiswa) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaMahasiswa as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_mahasiswa }}</div>
                                </div>
                                <div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small">Belum ada anggota mahasiswa</p>
                @endif
            </div>
        </div>
    </div>
</div>