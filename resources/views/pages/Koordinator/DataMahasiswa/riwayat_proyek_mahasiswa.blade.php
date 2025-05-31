<div class="content-table mt-4">
    <div class="card-data-dosen" style="font-size: 14px;">
        <div class="card-body">
            <div class="title-table d-flex justify-content-between align-items-center mb-3">
                <h5>Riwayat Proyek</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 35%">Nama Proyek</th>
                            <th scope="col" style="width: 25%">Periode Proyek</th>
                            <th scope="col" style="width: 20%">Peran</th>
                            <th scope="col" style="width: 15%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayatProyek as $proyek)
                        <tr>
                            <td>{{ $proyek->nama_proyek }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('M Y') }} - 
                                {{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('M Y') }}
                            </td>
                            <td>
                                <span class="badge {{ $proyek->peran == 'Project Leader' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $proyek->peran }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ $proyek->status_proyek }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada riwayat proyek yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="showing-text">
                    Showing {{ $riwayatProyek->firstItem() }} to {{ $riwayatProyek->lastItem() }} 
                    of {{ $riwayatProyek->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $riwayatProyek->links('vendor.pagination.custom_master') }}
                </div>
            </div>
        </div>
    </div>
</div>
<style>