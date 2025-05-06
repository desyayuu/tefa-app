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
        </div>
    </div>
    @include('pages.Koordinator.DataProyek.modal_anggota_proyek')
</div>