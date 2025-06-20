<header class="header bg-white shadow-sm">
    <div class="container-fluid h-100">
        <div class="row align-items-center h-100">
            <div class="col-10">
                <h5 class="mb-0">{{ $titleSidebar }}</h5>
            </div>
            
            {{-- Kolom kanan: Info User --}}
            <div class="col-2">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="me-2">
                        @if(session('profile_img'))
                            <img src="{{ asset(session('profile_img')) }}" alt="Profile Image" class="rounded-circle" width="35" height="35">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                <span>{{ substr(session('nama') ?? 'K', 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-start">
                        <small class="d-block fw-bold" style="font-size: 13px;">{{ session('nama') ?? 'Profesional' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>