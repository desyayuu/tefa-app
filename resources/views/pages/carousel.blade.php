{{-- Section 4 - Proyek TEFA Carousel --}}
<section class="fourth-section py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title mb-3">Proyek TEFA</h2>
                <p class="section-subtitle text-muted">Showcase hasil karya dan inovasi dari kolaborasi mahasiswa, dosen, dan mitra industri</p>
            </div>
        </div>

        @if($proyekPoster && count($proyekPoster) > 0)
            <div class="carousel-container">
                <div id="proyekCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <!-- Carousel Items -->
                    <div class="carousel-inner" style="padding-left:20px; padding-right:20px;">
                        @php
                            // Flexible chunking based on screen size or available items
                            $itemsPerSlide = min(count($proyekPoster), 4); // Max 4 items per slide, or total if less
                            $chunks = $proyekPoster->chunk($itemsPerSlide);
                        @endphp
                        
                        @foreach($chunks as $index => $chunk)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">
                                    @foreach($chunk as $proyek)
                                        <div class="col">
                                            <div class="poster-card text-center">
                                                {{-- Wrap the entire poster in a clickable link --}}
                                                <a href="{{ route('detail-portofolio-proyek', $proyek->proyek_id) }}" 
                                                   class="poster-link" 
                                                   style="text-decoration: none; color: inherit;">
                                                    <div class="poster-image-wrapper">
                                                        <img src="{{ $proyek->poster_proyek }}" 
                                                             class="poster-image" 
                                                             alt="Poster {{ $proyek->nama_proyek }}"
                                                        >
                                                        <div class="poster-overlay">
                                                            <h5 class="poster-title">{{ $proyek->nama_proyek }}</h5>
                                                            <p class="poster-category">{{ $proyek->nama_jenis_proyek }}</p>
                                                            <div class="poster-actions">
                                                                {{-- Detail button --}}
                                                                <span class="btn btn-add btn-sm me-2">
                                                                     Lihat Detail
                                                                </span>
                                                                {{-- External link button (if available) --}}
                                                                @if($proyek->link_proyek)
                                                                    <a href="{{ $proyek->link_proyek }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-light btn-add"
                                                                       onclick="event.stopPropagation(); event.preventDefault(); window.open('{{ $proyek->link_proyek }}', '_blank');">
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#proyekCarousel" data-bs-slide="prev">
                        <div class="carousel-btn">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#proyekCarousel" data-bs-slide="next">
                        <div class="carousel-btn">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <span class="visually-hidden">Next</span>
                    </button>

                    <!-- Carousel Indicators (Dots) -->
                    <div class="carousel-indicators custom-indicators">
                        @foreach($chunks as $index => $chunk)
                            <button type="button" data-bs-target="#proyekCarousel" data-bs-slide-to="{{ $index }}" 
                                    class="{{ $index === 0 ? 'active' : '' }}" 
                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Belum Ada Poster Proyek</h4>
                    <p class="text-muted">Poster proyek akan ditampilkan di sini ketika sudah tersedia.</p>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to animate counter
    function animateCounter(element, start, end, duration) {
        let startTimestamp = null;
        
        // Add counting animation class
        element.classList.add('counting-animation');
        
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            
            // Easing function for smooth animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(easeOutQuart * (end - start) + start);
            
            element.textContent = current.toLocaleString('id-ID');
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                // Remove animation class when done
                element.classList.remove('counting-animation');
                element.textContent = end.toLocaleString('id-ID');
            }
        };
        
        window.requestAnimationFrame(step);
    }

    // Intersection Observer to trigger animation when visible
    const observerOptions = {
        threshold: 0.5, // Trigger when 50% of element is visible
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('.counter');
                
                counters.forEach((counter, index) => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    
                    // Add delay based on index for staggered animation
                    setTimeout(() => {
                        animateCounter(counter, 0, target, 2000); // 2 seconds duration
                    }, index * 200); // 200ms delay between each counter
                });
                
                // Stop observing after animation starts
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Start observing the stats container
    const statsContainer = document.getElementById('stats-container');
    if (statsContainer) {
        observer.observe(statsContainer);
    }

    // Fallback: If Intersection Observer is not supported or stats are immediately visible
    setTimeout(() => {
        const counters = document.querySelectorAll('.counter');
        counters.forEach((counter, index) => {
            const target = parseInt(counter.getAttribute('data-target'));
            if (counter.textContent === '0' || counter.textContent === target.toString()) {
                setTimeout(() => {
                    animateCounter(counter, 0, target, 2000);
                }, index * 200);
            }
        });
    }, 500);

    // Enhanced Carousel functionality
    const carousel = document.getElementById('proyekCarousel');
    if (carousel) {
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: 5000, // 5 seconds
            ride: 'carousel',
            pause: 'hover', // Pause on hover
            wrap: true // Loop back to first slide
        });

        // Add smooth transition effect
        carousel.addEventListener('slide.bs.carousel', function () {
            // Add any custom animations here if needed
        });
        
        // Restart auto-slide when carousel becomes visible
        const carouselObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    carouselInstance.cycle();
                } else {
                    carouselInstance.pause();
                }
            });
        });
        
        carouselObserver.observe(carousel);
    }
});

// Add number formatting for Indonesian locale
Number.prototype.toLocaleString = Number.prototype.toLocaleString || function() {
    return this.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};
</script>
@endpush