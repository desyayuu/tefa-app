<div class="anggota-proyek-container flex-grow-1 me-3 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Anggota Proyek</h5>
            
            {{-- Hanya tampilkan handling_error di bagian anggota proyek --}}
            @include('components.handling_error', ['section' => 'anggota_proyek'])
            
            <!-- Project Leader Section -->
            <div class="card-project-leader">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-subtitle mb-0">Project Leader</div>
                    <!-- Di tempat tombol Edit -->
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#addLeaderModal">Edit</button>
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
                    <!-- Tombol Tambah Anggota Dosen -->
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#addDosenModal">
                        Tambah
                    </button>
                </div>
                
                @if(isset($anggotaDosen) && count($anggotaDosen) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaDosen as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_dosen }}</div>
                                </div>
                                <div>
                                    <form action="{{ route('koordinator.hapusAnggotaDosen', ['proyekId' => $proyek->proyek_id, 'memberId' => $anggota->project_member_dosen_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus anggota dosen ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm border-0 bg-transparent p-0">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7627 13.2734C21.7627 18.677 17.6296 23.0575 12.5312 23.0575C7.43286 23.0575 3.2998 18.677 3.2998 13.2734C3.2998 7.86976 7.43286 3.48926 12.5312 3.48926C17.6296 3.48926 21.7627 7.86976 21.7627 13.2734ZM15.9478 8.19757L16.0215 8.12654C16.4027 7.79716 16.965 7.82103 17.3203 8.19757C17.6756 8.57412 17.6981 9.17013 17.3873 9.57414L17.3203 9.65228L13.9038 13.2734L17.3204 16.8945C17.6994 17.2963 17.6994 17.9475 17.3204 18.3492C16.9413 18.751 16.3268 18.751 15.9478 18.3492L12.5312 14.7281L9.11464 18.3492C8.73563 18.751 8.12112 18.751 7.74211 18.3492C7.3631 17.9475 7.3631 17.2963 7.74211 16.8945L11.1587 13.2734L7.74214 9.65227L7.67513 9.57414C7.36436 9.17012 7.38687 8.57411 7.74214 8.19757C8.09742 7.82102 8.65976 7.79716 9.04095 8.12654L9.11467 8.19757L12.5312 11.8187L15.9478 8.19757Z" fill="#E56F8C"/>
                                            </svg>
                                        </button>
                                    </form>
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
                    <!-- Tombol Tambah Anggota Profesional -->
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#addProfesionalModal">
                        Tambah
                    </button>
                </div>
                
                @if(isset($anggotaProfesional) && count($anggotaProfesional) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaProfesional as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_profesional }}</div>
                                </div>
                                <div>
                                    <form action="{{ route('koordinator.hapusAnggotaProfesional', ['proyekId' => $proyek->proyek_id, 'memberId' => $anggota->project_member_profesional_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus anggota profesional ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm border-0 bg-transparent p-0">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7627 13.2734C21.7627 18.677 17.6296 23.0575 12.5312 23.0575C7.43286 23.0575 3.2998 18.677 3.2998 13.2734C3.2998 7.86976 7.43286 3.48926 12.5312 3.48926C17.6296 3.48926 21.7627 7.86976 21.7627 13.2734ZM15.9478 8.19757L16.0215 8.12654C16.4027 7.79716 16.965 7.82103 17.3203 8.19757C17.6756 8.57412 17.6981 9.17013 17.3873 9.57414L17.3203 9.65228L13.9038 13.2734L17.3204 16.8945C17.6994 17.2963 17.6994 17.9475 17.3204 18.3492C16.9413 18.751 16.3268 18.751 15.9478 18.3492L12.5312 14.7281L9.11464 18.3492C8.73563 18.751 8.12112 18.751 7.74211 18.3492C7.3631 17.9475 7.3631 17.2963 7.74211 16.8945L11.1587 13.2734L7.74214 9.65227L7.67513 9.57414C7.36436 9.17012 7.38687 8.57411 7.74214 8.19757C8.09742 7.82102 8.65976 7.79716 9.04095 8.12654L9.11467 8.19757L12.5312 11.8187L15.9478 8.19757Z" fill="#E56F8C"/>
                                            </svg>
                                        </button>
                                    </form>
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
                    <!-- Tombol Tambah Anggota Mahasiswa -->
                    <button type="button" class="btn btn-add btn-sm px-3 py-1 rounded-pill" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                        Tambah
                    </button>
                </div>
                
                @if(isset($anggotaMahasiswa) && count($anggotaMahasiswa) > 0)
                    <div class="list-anggota">
                        @foreach($anggotaMahasiswa as $anggota)
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div>
                                    <div class="text-anggota">{{ $anggota->nama_mahasiswa }}</div>
                                </div>
                                <div>
                                    <form action="{{ route('koordinator.hapusAnggotaMahasiswa', ['proyekId' => $proyek->proyek_id, 'memberId' => $anggota->project_member_mahasiswa_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus anggota mahasiswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm border-0 bg-transparent p-0">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7627 13.2734C21.7627 18.677 17.6296 23.0575 12.5312 23.0575C7.43286 23.0575 3.2998 18.677 3.2998 13.2734C3.2998 7.86976 7.43286 3.48926 12.5312 3.48926C17.6296 3.48926 21.7627 7.86976 21.7627 13.2734ZM15.9478 8.19757L16.0215 8.12654C16.4027 7.79716 16.965 7.82103 17.3203 8.19757C17.6756 8.57412 17.6981 9.17013 17.3873 9.57414L17.3203 9.65228L13.9038 13.2734L17.3204 16.8945C17.6994 17.2963 17.6994 17.9475 17.3204 18.3492C16.9413 18.751 16.3268 18.751 15.9478 18.3492L12.5312 14.7281L9.11464 18.3492C8.73563 18.751 8.12112 18.751 7.74211 18.3492C7.3631 17.9475 7.3631 17.2963 7.74211 16.8945L11.1587 13.2734L7.74214 9.65227L7.67513 9.57414C7.36436 9.17012 7.38687 8.57411 7.74214 8.19757C8.09742 7.82102 8.65976 7.79716 9.04095 8.12654L9.11467 8.19757L12.5312 11.8187L15.9478 8.19757Z" fill="#E56F8C"/>
                                            </svg>
                                        </button>
                                    </form>
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
    @include('pages.Koordinator.DataProyek.modal_anggota_proyek')
</div>