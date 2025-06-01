<div class="content-table mt-4">
    <div class="card-data-dosen" style="font-size: 14px;">
        <div class="card-body">
            <div id="alertContainer" class="mt-3"></div>
            <form id="formBidangKeahlian" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="title-table d-flex justify-content-between align-items-center mb-3">
                        <h5>Bidang Keahlian</h5>
                        <div class="row mb-4">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-add" id="btnSimpanBidangKeahlian">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="loadingSpinner"></span>
                                    <span id="btnText">Simpan Perubahan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown untuk memilih bidang keahlian -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Bidang Keahlian:</label>
                        <div class="dropdown position-relative">
                            <div class="dropdown-bidang-keahlian" id="dropdownBidangKeahlian">
                                <div class="d-flex align-items-center justify-content-between">
                                    <input type="text" 
                                        class="search-input" 
                                        id="searchBidangKeahlian"
                                        placeholder="Cari atau pilih bidang keahlian...">
                                    <i class="fas fa-chevron-down" id="dropdownIcon"></i>
                                </div>
                            </div>
                            
                            <div class="dropdown-menu-bidang position-absolute w-100 mt-1" id="dropdownMenuBidang" style="display: none; z-index: 1000;">
                                <!-- Dynamic content akan diisi di sini -->
                            </div>
                        </div>
                    </div>

                    <!-- Area untuk menampilkan bidang keahlian yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Bidang Keahlian Terpilih:</label>
                        <div class="selected-bidang-container" id="selectedBidangContainer">
                            <div class="empty-state" id="emptyState">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada bidang keahlian yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter -->
                    <div class="text-end">
                        <small class="text-muted">
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

<script>
    // Data bidang keahlian dari database
    const bidangKeahlianData = @json($bidangKeahlian ?? []);
    
    // Data bidang keahlian yang sudah dipilih (untuk mode edit)
    const initialSelectedBidangKeahlian = @json($selectedBidangKeahlian ?? []);
    
    // Mahasiswa ID untuk form submission
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
    initializeBidangKeahlian();

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
    }

    // Form submission handler
    formBidangKeahlian.addEventListener('submit', function(e) {
        e.preventDefault();
        submitBidangKeahlian();
    });

    function submitBidangKeahlian() {
        if (!mahasiswaId) {
            showAlert('error', 'ID Mahasiswa tidak ditemukan');
            return;
        }

        // Disable button dan show loading
        btnSimpan.disabled = true;
        loadingSpinner.classList.remove('d-none');
        btnText.textContent = 'Menyimpan...';

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

        // Submit via AJAX
        fetch(`/koordinator/mahasiswa/${mahasiswaId}/bidang-keahlian`, {
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
            } else {
                showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan data');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
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
            btnText.textContent = 'Simpan Perubahan';
        });
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-danger';
        const icon = type === 'success' ? 'fas fa-check-circle' : 
                    type === 'warning' ? 'fas fa-exclamation-triangle' : 'fas fa-times-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
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
        dropdownIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    }

    function hideDropdown() {
        dropdownMenuBidang.style.display = 'none';
        dropdownBidangKeahlian.classList.remove('active');
        dropdownIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
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
        }
    }

    function removeBidangKeahlian(id) {
        selectedBidangKeahlian = selectedBidangKeahlian.filter(item => item.id !== id);
        updateSelectedDisplay();
        updateHiddenInputs();
        renderDropdownItems();
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
                    <button class="remove-tag" onclick="removeBidangKeahlian('${item.id}')" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            selectedContainer.classList.add('has-items');
        }
        
        bidangCounter.textContent = selectedBidangKeahlian.length;
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

    // Keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideDropdown();
        }
    });

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
</script>