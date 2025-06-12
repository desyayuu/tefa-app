<div class="content-table">
    <div class="card-data-dosen" style="font-size: 14px;">
        <div class="card-body">
            <div id="alertContainer"></div>
            <form id="formBidangKeahlian" method="POST">
                @csrf
                @method('PUT')
                <div class="title-table d-flex justify-content-between align-items-center">
                    <h5>
                        Bidang Keahlian Saya
                    </h5>
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-add" id="btnSimpanBidangKeahlian">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="loadingSpinner"></span>
                                <span id="btnText">
                                    Simpan Perubahan
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="alert alert-info mb-3">
                    <strong>Informasi:</strong> Pilih bidang keahlian yang sesuai dengan kemampuan dan minat Anda. 
                </div>

                <!-- Dropdown untuk memilih bidang keahlian -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Bidang Keahlian:</label>
                    <div class="dropdown position-relative">
                        <div class="dropdown-bidang-keahlian" id="dropdownBidangKeahlian">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchBidangKeahlian"
                                    placeholder="Cari atau pilih bidang keahlian...">
                                <i class="fas fa-chevron-down transition-icon" id="dropdownIcon"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-bidang position-absolute w-100 mt-1" id="dropdownMenuBidang" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>
                </div>

                <!-- Area untuk menampilkan bidang keahlian yang dipilih -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Bidang Keahlian Terpilih:</label>
                    <div class="selected-bidang-container" id="selectedBidangContainer">
                        <div class="empty-state" id="emptyState">
                            <i class="fas fa-plus-circle me-2"></i>
                            Belum ada bidang keahlian yang dipilih
                        </div>
                    </div>
                </div>
                
                <!-- Info counter dan statistik -->
                <div class="d-flex justify-content-right align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-chart-bar me-1"></i>
                        <span id="bidangCounter">0</span> bidang keahlian dipilih
                    </small>
                </div>

                <!-- Hidden inputs untuk menyimpan bidang keahlian yang dipilih -->
                <div id="hiddenBidangKeahlianInputs">
                    <!-- Hidden inputs akan ditambahkan di sini via JavaScript -->
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Data bidang keahlian dari database
    const bidangKeahlianData = @json($bidangKeahlian ?? []);
    
    // Data bidang keahlian yang sudah dipilih (untuk mode edit)
    const initialSelectedBidangKeahlian = @json($selectedBidangKeahlian ?? []);
    
    // Mahasiswa ID untuk form submission (ambil dari session, tidak perlu ID eksternal)
    const mahasiswaId = '{{ $mahasiswa->mahasiswa_id ?? '' }}';

    let selectedBidangKeahlian = [];
    let filteredData = [...bidangKeahlianData];

    // DOM Elements
    const dropdownBidangKeahlian = document.getElementById('dropdownBidangKeahlian');
    const dropdownMenuBidang = document.getElementById('dropdownMenuBidang');
    const searchInput = document.getElementById('searchBidangKeahlian');
    const selectedContainer = document.getElementById('selectedBidangContainer');
    const emptyState = document.getElementById('emptyState');
    const bidangCounter = document.getElementById('bidangCounter');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const hiddenInputsContainer = document.getElementById('hiddenBidangKeahlianInputs');
    const formBidangKeahlian = document.getElementById('formBidangKeahlian');
    const btnSimpan = document.getElementById('btnSimpanBidangKeahlian');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const btnText = document.getElementById('btnText');
    const alertContainer = document.getElementById('alertContainer');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeBidangKeahlian();
    });

    function initializeBidangKeahlian() {
        // Load bidang keahlian yang sudah dipilih (untuk mode edit)
        if (initialSelectedBidangKeahlian && initialSelectedBidangKeahlian.length > 0) {
            selectedBidangKeahlian = initialSelectedBidangKeahlian.map(item => ({
                id: item.bidang_keahlian_id,
                nama: item.nama_bidang_keahlian
            }));
        }
        
        renderDropdownItems();
        updateSelectedDisplay();
        updateHiddenInputs();
        
        // Setup event listeners
        setupEventListeners();
    }

    function setupEventListeners() {
        // Form submission handler
        formBidangKeahlian.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBidangKeahlian();
        });

        // Event Listeners untuk dropdown
        dropdownBidangKeahlian.addEventListener('click', function(e) {
            if (e.target !== searchInput) {
                toggleDropdown();
            }
        });

        searchInput.addEventListener('input', function() {
            filterBidangKeahlian(this.value);
        });

        searchInput.addEventListener('focus', function() {
            showDropdown();
        });

        // Click outside to close dropdown
        document.addEventListener('click', function(e) {
            if (!dropdownBidangKeahlian.contains(e.target) && !dropdownMenuBidang.contains(e.target)) {
                hideDropdown();
            }
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDropdown();
            }
        });
    }

    function submitBidangKeahlian() {
        // Disable button dan show loading
        btnSimpan.disabled = true;
        loadingSpinner.classList.remove('d-none');
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

        // Clear previous alerts
        alertContainer.innerHTML = '';

        // Prepare form data
        const formData = new FormData();
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value;
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        // Add method
        formData.append('_method', 'PUT');

        // Add bidang keahlian
        selectedBidangKeahlian.forEach(item => {
            formData.append('bidang_keahlian[]', item.id);
        });

        // Submit via AJAX - URL disesuaikan untuk mahasiswa
        fetch(`{{ route('mahasiswa.updateBidangKeahlian') }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('success', data.message);
                console.log('Bidang keahlian berhasil disimpan:', data);
                
                // Update display jika diperlukan
                if (data.data && data.data.changes) {
                    updateCounterWithAnimation();
                }
            } else {
                showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan data');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                    showValidationErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat menghubungi server');
        })
        .finally(() => {
            // Reset button state
            btnSimpan.disabled = false;
            loadingSpinner.classList.add('d-none');
            btnText.innerHTML = 'Simpan Perubahan';
        });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-danger';
        const icon = type === 'success' ? 'fas fa-check-circle' : 
                    type === 'warning' ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        alertContainer.innerHTML = alertHtml;

        // Auto hide after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 150);
            }
        }, 5000);
    }

    function showValidationErrors(errors) {
        const errorMessages = Object.values(errors).flat();
        const errorHtml = errorMessages.map(msg => `<li>${msg}</li>`).join('');
        
        showAlert('error', `
            <strong>Kesalahan Validasi:</strong>
            <ul class="mb-0 mt-2">
                ${errorHtml}
            </ul>
        `);
    }

    function toggleDropdown() {
        if (dropdownMenuBidang.style.display === 'none') {
            showDropdown();
        } else {
            hideDropdown();
        }
    }

    function showDropdown() {
        dropdownMenuBidang.style.display = 'block';
        dropdownBidangKeahlian.classList.add('active');
        dropdownIcon.style.transform = 'rotate(180deg)';
    }

    function hideDropdown() {
        dropdownMenuBidang.style.display = 'none';
        dropdownBidangKeahlian.classList.remove('active');
        dropdownIcon.style.transform = 'rotate(0deg)';
        searchInput.value = '';
        filteredData = [...bidangKeahlianData];
        renderDropdownItems();
    }

    function filterBidangKeahlian(searchTerm) {
        filteredData = bidangKeahlianData.filter(item => 
            item.nama_bidang_keahlian.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !selectedBidangKeahlian.some(selected => selected.id === item.bidang_keahlian_id)
        );
        renderDropdownItems();
        showDropdown();
    }

    function renderDropdownItems() {
        const availableItems = filteredData.filter(item => 
            !selectedBidangKeahlian.some(selected => selected.id === item.bidang_keahlian_id)
        );

        if (availableItems.length === 0) {
            dropdownMenuBidang.innerHTML = `
                <div class="dropdown-item-bidang disabled">
                    <i class="fas fa-search me-2"></i>
                    Tidak ada bidang keahlian yang tersedia
                </div>
            `;
            return;
        }

        dropdownMenuBidang.innerHTML = availableItems.map(item => `
            <button class="dropdown-item-bidang" onclick="addBidangKeahlian('${item.bidang_keahlian_id}', '${item.nama_bidang_keahlian.replace(/'/g, "\\'")}')">
                <i class="fas fa-plus-circle me-2 text-success"></i>
                ${item.nama_bidang_keahlian}
            </button>
        `).join('');
    }

    function addBidangKeahlian(id, nama) {
        if (!selectedBidangKeahlian.some(item => item.id === id)) {
            selectedBidangKeahlian.push({ id, nama });
            updateSelectedDisplay();
            updateHiddenInputs();
            renderDropdownItems();
            searchInput.value = '';
            searchInput.focus();
            
            // Animate counter
            updateCounterWithAnimation();
        }
    }

    function removeBidangKeahlian(id) {
        selectedBidangKeahlian = selectedBidangKeahlian.filter(item => item.id !== id);
        updateSelectedDisplay();
        updateHiddenInputs();
        renderDropdownItems();
        
        // Animate counter
        updateCounterWithAnimation();
    }

    function updateSelectedDisplay() {
        if (selectedBidangKeahlian.length === 0) {
            selectedContainer.innerHTML = `
                <div class="empty-state" id="emptyState">
                    <i class="fas fa-plus-circle me-2"></i>
                    Belum ada bidang keahlian yang dipilih
                </div>
            `;
            selectedContainer.classList.remove('has-items');
        } else {
            selectedContainer.innerHTML = selectedBidangKeahlian.map(item => `
                <span class="bidang-keahlian-tag">
                    <i class="fas fa-code me-2"></i>
                    ${item.nama}
                    <button class="remove-tag" onclick="removeBidangKeahlian('${item.id}')" title="Hapus ${item.nama}">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            selectedContainer.classList.add('has-items');
        }
        
        bidangCounter.textContent = selectedBidangKeahlian.length;
    }

    function updateCounterWithAnimation() {
        bidangCounter.style.transform = 'scale(1.2)';
        bidangCounter.style.color = '#007bff';
        
        setTimeout(() => {
            bidangCounter.style.transform = 'scale(1)';
            bidangCounter.style.color = '';
        }, 200);
    }

    function updateHiddenInputs() {
        // Hapus semua hidden input yang ada
        hiddenInputsContainer.innerHTML = '';
        
        // Tambahkan hidden input untuk setiap bidang keahlian yang dipilih
        selectedBidangKeahlian.forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'bidang_keahlian[]';
            hiddenInput.value = item.id;
            hiddenInputsContainer.appendChild(hiddenInput);
        });
    }

    // Public functions untuk keperluan lain
    window.resetBidangKeahlian = function() {
        selectedBidangKeahlian = [];
        updateSelectedDisplay();
        updateHiddenInputs();
        renderDropdownItems();
        alertContainer.innerHTML = '';
    };

    window.setBidangKeahlian = function(bidangKeahlianIds) {
        selectedBidangKeahlian = [];
        bidangKeahlianIds.forEach(id => {
            const item = bidangKeahlianData.find(bk => bk.bidang_keahlian_id === id);
            if (item) {
                selectedBidangKeahlian.push({
                    id: item.bidang_keahlian_id,
                    nama: item.nama_bidang_keahlian
                });
            }
        });
        updateSelectedDisplay();
        updateHiddenInputs();
        renderDropdownItems();
    };

    // Load bidang keahlian dari server jika diperlukan refresh
    window.refreshBidangKeahlian = function() {
        btnSimpan.disabled = true;
        loadingSpinner.classList.remove('d-none');
        
        fetch(`{{ route('mahasiswa.getBidangKeahlian') }}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                selectedBidangKeahlian = data.data.map(item => ({
                    id: item.bidang_keahlian_id,
                    nama: item.nama_bidang_keahlian
                }));
                updateSelectedDisplay();
                updateHiddenInputs();
                renderDropdownItems();
                showAlert('success', 'Data bidang keahlian berhasil dimuat ulang');
            } else {
                showAlert('error', 'Gagal memuat ulang data bidang keahlian');
            }
        })
        .catch(error => {
            console.error('Error refreshing bidang keahlian:', error);
            showAlert('error', 'Terjadi kesalahan saat memuat ulang data');
        })
        .finally(() => {
            btnSimpan.disabled = false;
            loadingSpinner.classList.add('d-none');
        });
    };
</script>
@endpush