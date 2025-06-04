<div class="data-total-keuangan-tefa-container flex-grow-1">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
                <!-- Header -->
                <div class="keuangan-header">
                    <h5>Data Keuangan TEFA</h5>
                </div>

                <!-- Cards Row -->
                <div class="row g-4">
                    <!-- Saldo Saat Ini -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card keuangan-card card-saldo">
                            <i class="fas fa-wallet keuangan-icon"></i>
                            <div class="card-body">
                                <div class="keuangan-label">Saldo Saat Ini</div>
                                <h3 class="keuangan-amount" id="saldoSaatIni">
                                    <span class="skeleton" style="width: 200px; height: 1.8rem; display: inline-block; border-radius: 4px;"></span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pemasukan -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card keuangan-card card-pemasukan">
                            <i class="fas fa-arrow-trend-up keuangan-icon"></i>
                            <div class="card-body">
                                <div class="keuangan-label">Total Pemasukan</div>
                                <h3 class="keuangan-amount" id="totalPemasukan">
                                    <span class="skeleton" style="width: 200px; height: 1.8rem; display: inline-block; border-radius: 4px;"></span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pengeluaran -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card keuangan-card card-pengeluaran">
                            <i class="fas fa-arrow-trend-down keuangan-icon"></i>
                            <div class="card-body">
                                <div class="keuangan-label">Total Pengeluaran</div>
                                <h3 class="keuangan-amount" id="totalPengeluaran">
                                    <span class="skeleton" style="width: 200px; height: 1.8rem; display: inline-block; border-radius: 4px;"></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format currency function
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Counter animation
        function animateCountUp(elementId, finalValue) {
            const element = document.getElementById(elementId);
            const duration = 2000; // 2 seconds
            const steps = 60;
            const stepValue = finalValue / steps;
            let currentValue = 0;
            let step = 0;

            const timer = setInterval(() => {
                if (step >= steps) {
                    element.innerHTML = formatCurrency(finalValue);
                    clearInterval(timer);
                    return;
                }

                currentValue += stepValue;
                element.innerHTML = formatCurrency(Math.floor(currentValue));
                step++;
            }, duration / steps);
        }

        // Load financial data
        function loadKeuanganData() {
            // You can replace this with actual AJAX call to your Laravel controller
            fetch('{{ route("koordinator.getKeuanganData") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update the cards with actual data
                    document.getElementById('saldoSaatIni').innerHTML = formatCurrency(data.saldoSaatIni);
                    document.getElementById('totalPemasukan').innerHTML = formatCurrency(data.totalPemasukan);
                    document.getElementById('totalPengeluaran').innerHTML = formatCurrency(data.totalPengeluaran);

                    // Add counter animation
                    setTimeout(() => {
                        animateCountUp('saldoSaatIni', data.saldoSaatIni);
                        animateCountUp('totalPemasukan', data.totalPemasukan);
                        animateCountUp('totalPengeluaran', data.totalPengeluaran);
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error loading financial data:', error);
                // Fallback to static data if API fails
                const fallbackData = {
                    saldoSaatIni: 100000000,
                    totalPemasukan: 100000000,
                    totalPengeluaran: 100000000
                };
                
                document.getElementById('saldoSaatIni').innerHTML = formatCurrency(fallbackData.saldoSaatIni);
                document.getElementById('totalPemasukan').innerHTML = formatCurrency(fallbackData.totalPemasukan);
                document.getElementById('totalPengeluaran').innerHTML = formatCurrency(fallbackData.totalPengeluaran);
            });
        }

        // Simulate loading delay and then load data
        setTimeout(() => {
            loadKeuanganData();
        }, 1000);
    });
</script>