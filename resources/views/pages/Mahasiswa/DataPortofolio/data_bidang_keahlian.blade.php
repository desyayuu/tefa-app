<div class="content-table">
    <div class="card-data-dosen" style="font-size: 14px;">
        <div class="card-body">
            <div id="alertContainer"></div>
            <form id="formKeahlianBahasaTools" method="POST">
                @csrf
                @method('PUT')
                <div class="title-table d-flex justify-content-between align-items-center">
                    <h5>
                        Keahlian, Bahasa Pemrograman & Tools
                    </h5>
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-add" id="btnSimpanKeahlianBahasaTools">
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
                    <strong>Informasi:</strong> Pilih bidang keahlian, bahasa pemrograman, dan tools yang sesuai dengan kemampuan dan minat Anda. 
                </div>

                <!-- Bidang Keahlian Section -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Bidang Keahlian:</label>
                    <div class="dropdown position-relative mb-3">
                        <div class="dropdown-bidang-keahlian" id="dropdownBidangKeahlian">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchBidangKeahlian"
                                    placeholder="Cari atau pilih bidang keahlian...">
                                <i class="fas fa-chevron-down" id="dropdownIconBidang"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-bidang position-absolute w-100 mt-1" id="dropdownMenuBidang" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>

                    <!-- Area untuk menampilkan bidang keahlian yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Bidang Keahlian Terpilih:</label>
                        <div class="selected-bidang-container" id="selectedBidangContainer">
                            <div class="empty-state" id="emptyStateBidang">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada bidang keahlian yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter bidang keahlian -->
                    <div class="text-end mb-2">
                        <small class="text-muted">
                            <span id="bidangCounter">0</span> bidang keahlian dipilih
                        </small>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Bahasa Pemrograman Section -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Bahasa Pemrograman:</label>
                    <div class="dropdown position-relative mb-3">
                        <div class="dropdown-bahasa-pemrograman" id="dropdownBahasaPemrograman">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchBahasaPemrograman"
                                    placeholder="Cari atau pilih bahasa pemrograman...">
                                <i class="fas fa-chevron-down" id="dropdownIconBahasa"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-bahasa position-absolute w-100 mt-1" id="dropdownMenuBahasa" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>

                    <!-- Area untuk menampilkan bahasa pemrograman yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Bahasa Pemrograman Terpilih:</label>
                        <div class="selected-bahasa-container" id="selectedBahasaContainer">
                            <div class="empty-state" id="emptyStateBahasa">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada bahasa pemrograman yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter bahasa pemrograman -->
                    <div class="text-end mb-2">
                        <small class="text-muted">
                            <span id="bahasaCounter">0</span> bahasa pemrograman dipilih
                        </small>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Tools Section -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Tools & Teknologi:</label>
                    <div class="dropdown position-relative mb-3">
                        <div class="dropdown-tools" id="dropdownTools">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchTools"
                                    placeholder="Cari atau tambah tools...">
                                <i class="fas fa-chevron-down" id="dropdownIconTools"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-tools position-absolute w-100 mt-1" id="dropdownMenuTools" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>

                    <!-- Area untuk menampilkan tools yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Tools & Teknologi Terpilih:</label>
                        <div class="selected-tools-container" id="selectedToolsContainer">
                            <div class="empty-state" id="emptyStateTools">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada tools yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter tools -->
                    <div class="text-end mb-2">
                        <small class="text-muted">
                            <span id="toolsCounter">0</span> tools dipilih
                        </small>
                    </div>
                </div>

                <!-- Hidden inputs untuk menyimpan data yang dipilih -->
                <div id="hiddenBidangKeahlianInputs">
                    <!-- Hidden inputs bidang keahlian akan ditambahkan di sini via JavaScript -->
                </div>
                <div id="hiddenBahasaPemrogramanInputs">
                    <!-- Hidden inputs bahasa pemrograman akan ditambahkan di sini via JavaScript -->
                </div>
                <div id="hiddenToolsInputs">
                    <!-- Hidden inputs tools akan ditambahkan di sini via JavaScript -->
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Data dari database
    const bidangKeahlianData = @json($bidangKeahlian ?? []);
    const bahasaPemrogramanData = @json($bahasaPemrograman ?? []);
    const toolsData = @json($tools ?? []);
    
    // Data yang sudah dipilih (untuk mode edit)
    const initialSelectedBidangKeahlian = @json($selectedBidangKeahlian ?? []);
    const initialSelectedBahasaPemrograman = @json($selectedBahasaPemrograman ?? []);
    const initialSelectedTools = @json($selectedTools ?? []);

    // Mahasiswa ID untuk form submission
    const mahasiswaId = '{{ $mahasiswa->mahasiswa_id ?? '' }}';

    // State variables
    let selectedBidangKeahlian = [];
    let selectedBahasaPemrograman = [];
    let selectedTools = [];
    let filteredBidangData = [...bidangKeahlianData];
    let filteredBahasaData = [...bahasaPemrogramanData];
    let filteredToolsData = [...toolsData];

    // DOM Elements
    const formKeahlianBahasaTools = document.getElementById('formKeahlianBahasaTools');
    const btnSimpan = document.getElementById('btnSimpanKeahlianBahasaTools');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const btnText = document.getElementById('btnText');
    const alertContainer = document.getElementById('alertContainer');

    // Bidang Keahlian Elements
    const dropdownBidangKeahlian = document.getElementById('dropdownBidangKeahlian');
    const dropdownMenuBidang = document.getElementById('dropdownMenuBidang');
    const searchBidangInput = document.getElementById('searchBidangKeahlian');
    const selectedBidangContainer = document.getElementById('selectedBidangContainer');
    const bidangCounter = document.getElementById('bidangCounter');
    const dropdownIconBidang = document.getElementById('dropdownIconBidang');
    const hiddenBidangInputsContainer = document.getElementById('hiddenBidangKeahlianInputs');

    // Bahasa Pemrograman Elements
    const dropdownBahasaPemrograman = document.getElementById('dropdownBahasaPemrograman');
    const dropdownMenuBahasa = document.getElementById('dropdownMenuBahasa');
    const searchBahasaInput = document.getElementById('searchBahasaPemrograman');
    const selectedBahasaContainer = document.getElementById('selectedBahasaContainer');
    const bahasaCounter = document.getElementById('bahasaCounter');
    const dropdownIconBahasa = document.getElementById('dropdownIconBahasa');
    const hiddenBahasaInputsContainer = document.getElementById('hiddenBahasaPemrogramanInputs');

    // Tools Elements
    const dropdownTools = document.getElementById('dropdownTools');
    const dropdownMenuTools = document.getElementById('dropdownMenuTools');
    const searchToolsInput = document.getElementById('searchTools');
    const selectedToolsContainer = document.getElementById('selectedToolsContainer');
    const toolsCounter = document.getElementById('toolsCounter');
    const dropdownIconTools = document.getElementById('dropdownIconTools');
    const hiddenToolsInputsContainer = document.getElementById('hiddenToolsInputs');

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeKeahlianBahasaTools();
        setupEventListeners();
    });

    function initializeKeahlianBahasaTools() {
        // Load bidang keahlian yang sudah dipilih
        if (initialSelectedBidangKeahlian && initialSelectedBidangKeahlian.length > 0) {
            selectedBidangKeahlian = initialSelectedBidangKeahlian.map(item => ({
                id: item.bidang_keahlian_id,
                nama: item.nama_bidang_keahlian
            }));
        }
        
        // Load bahasa pemrograman yang sudah dipilih
        if (initialSelectedBahasaPemrograman && initialSelectedBahasaPemrograman.length > 0) {
            selectedBahasaPemrograman = initialSelectedBahasaPemrograman.map(item => ({
                id: item.bahasa_pemrograman_id,
                nama: item.nama_bahasa_pemrograman
            }));
        }

        // Load tools yang sudah dipilih
        if (initialSelectedTools && initialSelectedTools.length > 0) {
            selectedTools = initialSelectedTools.map(item => ({
                id: item.tool_id || 'custom_' + Date.now(),
                nama: item.nama_tool || item.custom_nama_tool,
                isCustom: item.is_custom || false,
                deskripsi: item.custom_deskripsi_tool || ''
            }));
        }
        
        renderDropdownItems();
        updateAllDisplays();
        updateAllHiddenInputs();
    }

    function setupEventListeners() {
        // Form submission handler
        formKeahlianBahasaTools.addEventListener('submit', function(e) {
            e.preventDefault();
            submitKeahlianBahasaTools();
        });

        // Event Listeners untuk Bidang Keahlian
        dropdownBidangKeahlian.addEventListener('click', function(e) {
            if (e.target !== searchBidangInput) {
                toggleDropdown('bidang');
            }
        });

        searchBidangInput.addEventListener('input', function() {
            filterBidangKeahlian(this.value);
        });

        searchBidangInput.addEventListener('focus', function() {
            showDropdown('bidang');
        });

        // Event Listeners untuk Bahasa Pemrograman
        dropdownBahasaPemrograman.addEventListener('click', function(e) {
            if (e.target !== searchBahasaInput) {
                toggleDropdown('bahasa');
            }
        });

        searchBahasaInput.addEventListener('input', function() {
            filterBahasaPemrograman(this.value);
        });

        searchBahasaInput.addEventListener('focus', function() {
            showDropdown('bahasa');
        });

        // Event Listeners untuk Tools
        dropdownTools.addEventListener('click', function(e) {
            if (e.target !== searchToolsInput) {
                toggleDropdown('tools');
            }
        });

        searchToolsInput.addEventListener('input', function() {
            filterTools(this.value);
        });

        searchToolsInput.addEventListener('focus', function() {
            showDropdown('tools');
        });

        searchToolsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = this.value.trim();
                if (searchTerm && !toolExists(searchTerm)) {
                    showAddCustomToolModal(searchTerm);
                }
            }
        });

        // Click outside to close dropdowns
        document.addEventListener('click', function(e) {
            if (!dropdownBidangKeahlian.contains(e.target) && !dropdownMenuBidang.contains(e.target)) {
                hideDropdown('bidang');
            }
            if (!dropdownBahasaPemrograman.contains(e.target) && !dropdownMenuBahasa.contains(e.target)) {
                hideDropdown('bahasa');
            }
            if (!dropdownTools.contains(e.target) && !dropdownMenuTools.contains(e.target)) {
                hideDropdown('tools');
            }
        });
    }

    function submitKeahlianBahasaTools() {
        if (!mahasiswaId) {
            showAlert('error', 'Data mahasiswa tidak ditemukan');
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

        // Add bahasa pemrograman
        selectedBahasaPemrograman.forEach(item => {
            formData.append('bahasa_pemrograman[]', item.id);
        });

        // Add tools
        selectedTools.forEach(item => {
            if (item.isCustom) {
                // Custom tool - gunakan custom fields
                formData.append('custom_tools[]', JSON.stringify({
                    nama: item.nama,
                    deskripsi: item.deskripsi
                }));
            } else {
                // Existing tool - gunakan tool_id
                formData.append('tools[]', item.id);
            }
        });

        // Submit via AJAX - Updated URL untuk mahasiswa
        fetch(`{{ route('mahasiswa.updateKeahlianBahasaTools') }}`, {
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
                console.log('Data keahlian, bahasa, dan tools berhasil disimpan:', data);
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

    // === DROPDOWN FUNCTIONS ===

    function toggleDropdown(type) {
        if (type === 'bidang') {
            if (dropdownMenuBidang.style.display === 'none') {
                showDropdown('bidang');
            } else {
                hideDropdown('bidang');
            }
        } else if (type === 'bahasa') {
            if (dropdownMenuBahasa.style.display === 'none') {
                showDropdown('bahasa');
            } else {
                hideDropdown('bahasa');
            }
        } else if (type === 'tools') {
            if (dropdownMenuTools.style.display === 'none') {
                showDropdown('tools');
            } else {
                hideDropdown('tools');
            }
        }
    }

    function showDropdown(type) {
        if (type === 'bidang') {
            dropdownMenuBidang.style.display = 'block';
            dropdownBidangKeahlian.classList.add('active');
            dropdownIconBidang.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else if (type === 'bahasa') {
            dropdownMenuBahasa.style.display = 'block';
            dropdownBahasaPemrograman.classList.add('active');
            dropdownIconBahasa.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else if (type === 'tools') {
            dropdownMenuTools.style.display = 'block';
            dropdownTools.classList.add('active');
            dropdownIconTools.classList.replace('fa-chevron-down', 'fa-chevron-up');
        }
    }

    function hideDropdown(type) {
        if (type === 'bidang') {
            dropdownMenuBidang.style.display = 'none';
            dropdownBidangKeahlian.classList.remove('active');
            dropdownIconBidang.classList.replace('fa-chevron-up', 'fa-chevron-down');
            searchBidangInput.value = '';
            filteredBidangData = [...bidangKeahlianData];
            renderBidangDropdownItems();
        } else if (type === 'bahasa') {
            dropdownMenuBahasa.style.display = 'none';
            dropdownBahasaPemrograman.classList.remove('active');
            dropdownIconBahasa.classList.replace('fa-chevron-up', 'fa-chevron-down');
            searchBahasaInput.value = '';
            filteredBahasaData = [...bahasaPemrogramanData];
            renderBahasaDropdownItems();
        } else if (type === 'tools') {
            dropdownMenuTools.style.display = 'none';
            dropdownTools.classList.remove('active');
            dropdownIconTools.classList.replace('fa-chevron-up', 'fa-chevron-down');
            searchToolsInput.value = '';
            filteredToolsData = [...toolsData];
            renderToolsDropdownItems();
        }
    }

    // === FILTER FUNCTIONS ===

    function filterBidangKeahlian(searchTerm) {
        filteredBidangData = bidangKeahlianData.filter(item => 
            item.nama_bidang_keahlian.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !selectedBidangKeahlian.some(selected => selected.id === item.bidang_keahlian_id)
        );
        renderBidangDropdownItems();
        showDropdown('bidang');
    }

    function filterBahasaPemrograman(searchTerm) {
        filteredBahasaData = bahasaPemrogramanData.filter(item => 
            item.nama_bahasa_pemrograman.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !selectedBahasaPemrograman.some(selected => selected.id === item.bahasa_pemrograman_id)
        );
        renderBahasaDropdownItems();
        showDropdown('bahasa');
    }

    function filterTools(searchTerm) {
        filteredToolsData = toolsData.filter(item => 
            item.nama_tool.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !selectedTools.some(selected => selected.id === item.tool_id)
        );
        renderToolsDropdownItems();
        showDropdown('tools');
    }

    function toolExists(toolName) {
        return toolsData.some(tool => tool.nama_tool.toLowerCase() === toolName.toLowerCase()) ||
            selectedTools.some(tool => tool.nama.toLowerCase() === toolName.toLowerCase());
    }

    // === RENDER FUNCTIONS ===

    function renderDropdownItems() {
        renderBidangDropdownItems();
        renderBahasaDropdownItems();
        renderToolsDropdownItems();
    }

    function renderBidangDropdownItems() {
        const availableItems = filteredBidangData.filter(item => 
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

    function renderBahasaDropdownItems() {
        const availableItems = filteredBahasaData.filter(item => 
            !selectedBahasaPemrograman.some(selected => selected.id === item.bahasa_pemrograman_id)
        );

        if (availableItems.length === 0) {
            dropdownMenuBahasa.innerHTML = `
                <div class="dropdown-item-bahasa disabled">
                    <i class="fas fa-search me-2"></i>
                    Tidak ada bahasa pemrograman yang tersedia
                </div>
            `;
            return;
        }

        dropdownMenuBahasa.innerHTML = availableItems.map(item => `
            <button class="dropdown-item-bahasa" onclick="addBahasaPemrograman('${item.bahasa_pemrograman_id}', '${item.nama_bahasa_pemrograman.replace(/'/g, "\\'")}')">
                <i class="fas fa-plus-circle me-2 text-success"></i>
                ${item.nama_bahasa_pemrograman}
            </button>
        `).join('');
    }

    function renderToolsDropdownItems() {
        const availableItems = filteredToolsData.filter(item => 
            !selectedTools.some(selected => selected.id === item.tool_id)
        );

        let dropdownContent = '';

        // Selalu tampilkan opsi untuk menambah tool baru di bagian atas
        dropdownContent += `
            <button class="dropdown-item-tools add-custom-general" onclick="showAddCustomToolModal('')">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                <strong>Tambah Tool </strong>
            </button>
            <div class="dropdown-divider"></div>
        `;

        // Tambahkan opsi untuk menambah custom tool berdasarkan search term jika ada
        const searchTerm = searchToolsInput.value.trim();
        if (searchTerm && !toolExists(searchTerm)) {
            dropdownContent += `
                <button class="dropdown-item-tools add-custom" onclick="showAddCustomToolModal('${searchTerm.replace(/'/g, "\\'")}')">
                    <i class="fas fa-plus me-2 text-success"></i>
                    Tambahkan "${searchTerm}" sebagai tool
                </button>
                <div class="dropdown-divider"></div>
            `;
        }

        // Tampilkan available tools
        if (availableItems.length === 0 && !searchTerm) {
            dropdownContent += `
                <div class="dropdown-item-tools disabled">
                    <i class="fas fa-info-circle me-2"></i>
                    Tidak ada tools lain yang tersedia
                </div>
            `;
        } else if (availableItems.length === 0 && searchTerm) {
            dropdownContent += `
                <div class="dropdown-item-tools disabled">
                    <i class="fas fa-search me-2"></i>
                    Tidak ditemukan tools yang cocok
                </div>
            `;
        } else {
            if (availableItems.length > 0) {
                dropdownContent += `
                    <div class="tools-section-header">
                        <small class="text-muted fw-bold px-3">Tools Tersedia:</small>
                    </div>
                `;
            }
            
            dropdownContent += availableItems.map(item => `
                <button class="dropdown-item-tools" onclick="addTool('${item.tool_id}', '${item.nama_tool.replace(/'/g, "\\'")}', false)">
                    <i class="fas fa-tools me-2 text-success"></i>
                    ${item.nama_tool}
                </button>
            `).join('');
        }

        dropdownMenuTools.innerHTML = dropdownContent;
    }

    // === ADD/REMOVE FUNCTIONS ===

    function addBidangKeahlian(id, nama) {
        if (!selectedBidangKeahlian.some(item => item.id === id)) {
            selectedBidangKeahlian.push({ id, nama });
            updateAllDisplays();
            updateAllHiddenInputs();
            renderBidangDropdownItems();
            searchBidangInput.value = '';
            searchBidangInput.focus();
        }
    }

    function removeBidangKeahlian(id) {
        selectedBidangKeahlian = selectedBidangKeahlian.filter(item => item.id !== id);
        updateAllDisplays();
        updateAllHiddenInputs();
        renderBidangDropdownItems();
    }

    function addBahasaPemrograman(id, nama) {
        if (!selectedBahasaPemrograman.some(item => item.id === id)) {
            selectedBahasaPemrograman.push({ id, nama });
            updateAllDisplays();
            updateAllHiddenInputs();
            renderBahasaDropdownItems();
            searchBahasaInput.value = '';
            searchBahasaInput.focus();
        }
    }

    function removeBahasaPemrograman(id) {
        selectedBahasaPemrograman = selectedBahasaPemrograman.filter(item => item.id !== id);
        updateAllDisplays();
        updateAllHiddenInputs();
        renderBahasaDropdownItems();
    }

    function addTool(id, nama, isCustom = false, deskripsi = '') {
        if (!selectedTools.some(item => item.id === id)) {
            selectedTools.push({ 
                id, 
                nama, 
                isCustom, 
                deskripsi: deskripsi || '' 
            });
            updateAllDisplays();
            updateAllHiddenInputs();
            renderToolsDropdownItems();
            searchToolsInput.value = '';
            searchToolsInput.focus();
        }
    }

    function removeTool(id) {
        selectedTools = selectedTools.filter(item => item.id !== id);
        updateAllDisplays();
        updateAllHiddenInputs();
        renderToolsDropdownItems();
    }

    // === UPDATE DISPLAY FUNCTIONS ===

    function updateAllDisplays() {
        updateBidangDisplay();
        updateBahasaDisplay();
        updateToolsDisplay();
    }

    function updateBidangDisplay() {
        if (selectedBidangKeahlian.length === 0) {
            selectedBidangContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-plus-circle me-2"></i>
                    Belum ada bidang keahlian yang dipilih
                </div>
            `;
            selectedBidangContainer.classList.remove('has-items');
        } else {
            selectedBidangContainer.innerHTML = selectedBidangKeahlian.map(item => `
                <span class="bidang-keahlian-tag">
                    <i class="fas fa-code me-2"></i>
                    ${item.nama}
                    <button class="remove-tag" onclick="removeBidangKeahlian('${item.id}')" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            selectedBidangContainer.classList.add('has-items');
        }
        
        bidangCounter.textContent = selectedBidangKeahlian.length;
    }

    function updateBahasaDisplay() {
        if (selectedBahasaPemrograman.length === 0) {
            selectedBahasaContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-plus-circle me-2"></i>
                    Belum ada bahasa pemrograman yang dipilih
                </div>
            `;
            selectedBahasaContainer.classList.remove('has-items');
        } else {
            selectedBahasaContainer.innerHTML = selectedBahasaPemrograman.map(item => `
                <span class="bahasa-pemrograman-tag">
                    <i class="fas fa-laptop-code me-2"></i>
                    ${item.nama}
                    <button class="remove-tag" onclick="removeBahasaPemrograman('${item.id}')" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            selectedBahasaContainer.classList.add('has-items');
        }
        
        bahasaCounter.textContent = selectedBahasaPemrograman.length;
    }

    function updateToolsDisplay() {
        if (selectedTools.length === 0) {
            selectedToolsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-plus-circle me-2"></i>
                    Belum ada tools yang dipilih
                </div>
            `;
            selectedToolsContainer.classList.remove('has-items');
        } else {
            selectedToolsContainer.innerHTML = selectedTools.map(item => `
                <span class="tools-tag ${item.isCustom ? 'custom-tool' : ''}">
                    <i class="fas fa-tools me-2"></i>
                    ${item.nama}
                    ${item.isCustom ? '<i title="Custom tool"></i>' : ''}
                    <button class="remove-tag" onclick="removeTool('${item.id}')" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            selectedToolsContainer.classList.add('has-items');
        }
        
        toolsCounter.textContent = selectedTools.length;
    }

    // === HIDDEN INPUTS FUNCTIONS ===

    function updateAllHiddenInputs() {
        updateBidangHiddenInputs();
        updateBahasaHiddenInputs();
        updateToolsHiddenInputs();
    }

    function updateBidangHiddenInputs() {
        hiddenBidangInputsContainer.innerHTML = '';
        selectedBidangKeahlian.forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'bidang_keahlian[]';
            hiddenInput.value = item.id;
            hiddenBidangInputsContainer.appendChild(hiddenInput);
        });
    }

    function updateBahasaHiddenInputs() {
        hiddenBahasaInputsContainer.innerHTML = '';
        selectedBahasaPemrograman.forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'bahasa_pemrograman[]';
            hiddenInput.value = item.id;
            hiddenBahasaInputsContainer.appendChild(hiddenInput);
        });
    }

    function updateToolsHiddenInputs() {
        hiddenToolsInputsContainer.innerHTML = '';
        selectedTools.forEach(item => {
            if (item.isCustom) {
                // Custom tool
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'custom_tools[]';
                hiddenInput.value = JSON.stringify({
                    nama: item.nama,
                    deskripsi: item.deskripsi
                });
                hiddenToolsInputsContainer.appendChild(hiddenInput);
            } else {
                // Existing tool
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'tools[]';
                hiddenInput.value = item.id;
                hiddenToolsInputsContainer.appendChild(hiddenInput);
            }
        });
    }

    // === CUSTOM TOOL MODAL ===

    function showAddCustomToolModal(toolName) {
        const modalHtml = `
            <div class="modal fade" id="addCustomToolModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Tool Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="customToolForm">
                                <div class="mb-3">
                                    <label class="form-label">Nama Tool <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customToolName" value="${toolName}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Tool</label>
                                    <textarea class="form-control" id="customToolDescription" rows="3"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="addCustomTool()">Tambahkan</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('addCustomToolModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addCustomToolModal'));
        modal.show();

        // Focus on name input
        document.getElementById('customToolName').focus();
    }

    function addCustomTool() {
        const nameInput = document.getElementById('customToolName');
        const descriptionInput = document.getElementById('customToolDescription');
        
        const toolName = nameInput.value.trim();
        const toolDescription = descriptionInput.value.trim();

        if (!toolName) {
            alert('Nama tool harus diisi!');
            nameInput.focus();
            return;
        }

        if (toolExists(toolName)) {
            alert('Tool dengan nama ini sudah ada!');
            nameInput.focus();
            return;
        }

        // Generate unique ID for custom tool
        const customId = 'custom_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

        // Add to selected tools
        addTool(customId, toolName, true, toolDescription);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomToolModal'));
        modal.hide();

        // Clear search input and hide dropdown
        searchToolsInput.value = '';
        hideDropdown('tools');
    }

    // === ALERT FUNCTION ===

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 
                        type === 'warning' ? 'alert-warning' : 'alert-danger';

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
</script>
@endpush
