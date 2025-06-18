<div class="dashboard-container">
    <!-- Data Proyek Section -->
    <div class="data-section data-proyek">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body">
                <!-- Header -->
                <div class="keuangan-header">
                    <h5>Data Proyek</h5>
                </div>

                <!-- Cards Row -->
                <div class="row g-4">
                    <!-- Inisiasi -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card status-card card-inisiasi">
                            <div class="card-body">
                                <div class="status-content">
                                    <div class="status-number-col">
                                        <div class="status-number" id="proyekInisiasi">
                                            <span class="skeleton" style="width: 60px; height: 3rem; display: inline-block; border-radius: 50%;"></span>
                                        </div>
                                    </div>
                                    <div class="status-text-col">
                                        <div class="status-title">INISIASI</div>
                                        <div class="status-subtitle" id="proyekInisiasiDesc">
                                            <span class="skeleton" style="width: 140px; height: 1rem; display: inline-block; border-radius: 4px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card status-card card-progress">
                            <div class="card-body">
                                <div class="status-content">
                                    <div class="status-number-col">
                                        <div class="status-number" id="proyekInProgress">
                                            <span class="skeleton" style="width: 60px; height: 3rem; display: inline-block; border-radius: 50%;"></span>
                                        </div>
                                    </div>
                                    <div class="status-text-col">
                                        <div class="status-title">IN PROGRESS</div>
                                        <div class="status-subtitle" id="proyekInProgressDesc">
                                            <span class="skeleton" style="width: 140px; height: 1rem; display: inline-block; border-radius: 4px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Done -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card status-card card-done">
                            <div class="card-body">
                                <div class="status-content">
                                    <div class="status-number-col">
                                        <div class="status-number" id="proyekDone">
                                            <span class="skeleton" style="width: 60px; height: 3rem; display: inline-block; border-radius: 50%;"></span>
                                        </div>
                                    </div>
                                    <div class="status-text-col">
                                        <div class="status-title">DONE</div>
                                        <div class="status-subtitle" id="proyekDoneDesc">
                                            <span class="skeleton" style="width: 140px; height: 1rem; display: inline-block; border-radius: 4px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Mitra Section -->
    <div class="data-section data-mitra">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body">
                <!-- Header -->
                <div class="keuangan-header">
                    <h5>Data Mitra</h5>
                </div>

                <!-- Mitra Card -->
                <div class="">
                    <div class="">
                        <div class="card status-card card-mitra">
                            <div class="card-body">
                                <div class="status-content">
                                    <div class="status-number-col">
                                        <div class="status-number" id="totalMitraInProgress">
                                            <span class="skeleton" style="width: 60px; height: 3rem; display: inline-block; border-radius: 50%;"></span>
                                        </div>
                                    </div>
                                    <div class="status-text-col">
                                        <div class="status-title">MITRA</div>
                                        <div class="status-subtitle" id="totalMitraDesc">
                                            <span class="skeleton" style="width: 140px; height: 1rem; display: inline-block; border-radius: 4px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Counter animation
        function animateCountUp(elementId, finalValue) {
            const element = document.getElementById(elementId);
            const duration = 00; // 1.5 seconds
            const steps = 30;
            const stepValue = finalValue / steps;
            let currentValue = 0;
            let step = 0;

            element.classList.add('count-animate');

            const timer = setInterval(() => {
                if (step >= steps) {
                    element.innerHTML = finalValue;
                    clearInterval(timer);
                    return;
                }

                currentValue += stepValue;
                element.innerHTML = Math.floor(currentValue);
                step++;
            }, duration / steps);
        }

        // Load project data
        function loadProyekData() {
            fetch('{{ route("profesional.getProyekData") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update project cards
                    document.getElementById('proyekInisiasi').innerHTML = data.proyekInisiasi;
                    document.getElementById('proyekInProgress').innerHTML = data.proyekInProgress;
                    document.getElementById('proyekDone').innerHTML = data.proyekDone;

                    // Update descriptions
                    document.getElementById('proyekInisiasiDesc').innerHTML = `${data.proyekInisiasi} Project In Initiation`;
                    document.getElementById('proyekInProgressDesc').innerHTML = `${data.proyekInProgress} Project On Progress`;
                    document.getElementById('proyekDoneDesc').innerHTML = `${data.proyekDone} Projects Done`;

                    // Add counter animation
                    setTimeout(() => {
                        animateCountUp('proyekInisiasi', data.proyekInisiasi);
                        animateCountUp('proyekInProgress', data.proyekInProgress);
                        animateCountUp('proyekDone', data.proyekDone);
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Error loading project data:', error);
                // Fallback data
                document.getElementById('proyekInisiasi').innerHTML = '6';
                document.getElementById('proyekInProgress').innerHTML = '4';
                document.getElementById('proyekDone').innerHTML = '6';
                document.getElementById('proyekInisiasiDesc').innerHTML = '6 Project In Initiation';
                document.getElementById('proyekInProgressDesc').innerHTML = '4 Project On Progress';
                document.getElementById('proyekDoneDesc').innerHTML = '6 Projects Done';
            });
        }

        // Load partner data
        function loadMitraData() {
            fetch('{{ route("profesional.getMitraData") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update mitra card
                    document.getElementById('totalMitraInProgress').innerHTML = data.totalMitraInProgress;
                    document.getElementById('totalMitraDesc').innerHTML = `${data.totalMitraInProgress} Mitra Terlibat`;

                    // Add counter animation
                    setTimeout(() => {
                        animateCountUp('totalMitraInProgress', data.totalMitraInProgress);
                    }, 600);
                }
            })
            .catch(error => {
                console.error('Error loading partner data:', error);
                // Fallback data
                document.getElementById('totalMitraInProgress').innerHTML = '3';
                document.getElementById('totalMitraDesc').innerHTML = '3 Mitra Terlibat';
            });
        }

        // Load all data
        setTimeout(() => {
            loadProyekData();
            loadMitraData();
        }, 1000);

        // Add click handlers for cards (optional)
        document.querySelectorAll('.status-card').forEach(card => {
            card.addEventListener('click', function() {
                const cardClass = this.className;
                if (cardClass.includes('card-inisiasi')) {
                    console.log('Navigate to Inisiasi projects');
                } else if (cardClass.includes('card-progress')) {
                    console.log('Navigate to In Progress projects');
                } else if (cardClass.includes('card-done')) {
                    console.log('Navigate to Done projects');
                } else if (cardClass.includes('card-mitra')) {
                    console.log('Navigate to Mitra list');
                }
            });
        });
    });
</script>