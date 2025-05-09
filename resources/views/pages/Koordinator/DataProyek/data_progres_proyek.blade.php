<div class="data-timeline-container flex-grow-1 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div id="timeline-section" class="title-table d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Progres Proyek</h4>
                <div class="d-flex gap-2 align-items-center">
                    <div class="position-relative">
                        <form action="" method="GET">
                            <input type="text" name="search_timeline" class="form-control pe-5 form-search" placeholder="Cari Progres...">
                            <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y pe-2 py-2 border-0 bg-transparent">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11.7664" cy="11.7669" r="8.98856" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18.0181 18.4854L21.5421 22.0002" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    @if(isset($searchTimeline) && $searchTimeline)
                    <a href="" class="btn btn-tutup btn-outline-secondary">
                        Hapus Filter
                    </a>
                    @endif

                    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahProgresFromKoor">
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
 </div>