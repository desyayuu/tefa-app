<div class="current-data-container flex-grow-1 mt-3">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
            <div class="title-filter d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0">Partisipasi Profesional dalam Proyek</h5>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="" method="GET">
                            <!-- Hidden input untuk mempertahankan search utama -->
                            @if(isset($search) && $search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            
                            <input type="text" name="search_partisipasi" class="form-control pe-5 form-search" 
                                   placeholder="Cari partisipasi profesional..." value="{{ $searchPartisipasi ?? '' }}">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    @if(isset($searchPartisipasi) && $searchPartisipasi)
                        <a href="{{ route('koordinator.dataProfesional') }}{{ isset($search) && $search ? '?search=' . $search : '' }}" 
                           class="btn btn-tutup btn-outline-secondary">
                            Hapus Filter
                        </a>
                    @endif
                </div>
            </div>

            <!-- Handling Hasil Search Partisipasi -->
            @if(isset($searchPartisipasi) && $searchPartisipasi)
            <div class="alert alert-info">
                Menampilkan {{ $partisipasiProfesional->count() }} hasil partisipasi untuk pencarian "{{ $searchPartisipasi }}"
            </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Profesional</th>
                            <th scope="col">Nama Proyek</th>
                            <th scope="col">Peran</th>
                            <th scope="col">Status Proyek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partisipasiProfesional as $p)
                        <tr>
                            <td>{{ $p->nama_profesional }}</td>
                            <td>{{ $p->nama_proyek }}</td>
                            <td>
                                <span class="badge {{ $p->role_type == 'Project Leader' ? 'bg-warning' : 'bg-secondary' }}">
                                    {{ $p->role_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $p->status_proyek }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                @if(isset($searchPartisipasi) && $searchPartisipasi)
                                    <div class="text-muted">
                                        Tidak ada hasil yang cocok dengan pencarian "{{ $searchPartisipasi }}"
                                    </div>
                                @else
                                    <div class="text-muted">
                                        Belum ada profesional yang berpartisipasi dalam proyek yang sedang berjalan
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination untuk Partisipasi -->
            @if($partisipasiProfesional->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="showing-text">
                    Showing {{ $partisipasiProfesional->firstItem() }} to {{ $partisipasiProfesional->lastItem() }} 
                    of {{ $partisipasiProfesional->total() }} partisipasi
                </div>
                <div class="pagination-links">
                    {{ $partisipasiProfesional->appends([
                        'search' => request('search'),
                        'search_partisipasi' => request('search_partisipasi')
                    ])->links('vendor.pagination.custom_master') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>