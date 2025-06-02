/**
 * Bidang Keahlian Manager
 * File: public/js/bidang-keahlian-manager.js
 * 
 * Manages bidang keahlian selection and form submission
 */

class BidangKeahlianManager {
    constructor(config = {}) {
        // Configuration
        this.config = {
            containerSelector: '#formBidangKeahlian',
            submitUrl: config.submitUrl || '',
            mahasiswaId: config.mahasiswaId || '',
            csrfToken: config.csrfToken || '',
            ...config
        };

        // Data
        this.bidangKeahlianData = config.bidangKeahlianData || [];
        this.selectedBidangKeahlian = [];
        this.filteredData = [...this.bidangKeahlianData];

        // DOM Elements
        this.elements = {};
        
        // Initialize
        this.init();
    }

    init() {
        if (!this.initializeElements()) {
            console.error('BidangKeahlianManager: Required elements not found');
            return;
        }

        this.setupEventListeners();
        this.loadInitialData();
        this.renderDropdownItems();
        this.updateSelectedDisplay();
        this.updateHiddenInputs();
    }

    initializeElements() {
        const selectors = {
            container: this.config.containerSelector,
            dropdownBidangKeahlian: '#dropdownBidangKeahlian',
            dropdownMenuBidang: '#dropdownMenuBidang',
            searchInput: '#searchBidangKeahlian',
            selectedContainer: '#selectedBidangContainer',
            bidangCounter: '#bidangCounter',
            dropdownIcon: '#dropdownIcon',
            hiddenInputsContainer: '#hiddenBidangKeahlianInputs',
            formBidangKeahlian: '#formBidangKeahlian',
            btnSimpan: '#btnSimpanBidangKeahlian',
            loadingSpinner: '#loadingSpinner',
            btnText: '#btnText',
            alertContainer: '#alertContainer'
        };

        for (const [key, selector] of Object.entries(selectors)) {
            this.elements[key] = document.querySelector(selector);
            if (!this.elements[key] && ['container', 'formBidangKeahlian'].includes(key)) {
                return false;
            }
        }

        return true;
    }

    setupEventListeners() {
        // Form submission
        if (this.elements.formBidangKeahlian) {
            this.elements.formBidangKeahlian.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitBidangKeahlian();
            });
        }

        // Dropdown interactions
        if (this.elements.dropdownBidangKeahlian) {
            this.elements.dropdownBidangKeahlian.addEventListener('click', (e) => {
                if (e.target !== this.elements.searchInput) {
                    this.toggleDropdown();
                }
            });
        }

        // Search input
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('input', (e) => {
                this.filterBidangKeahlian(e.target.value);
            });

            this.elements.searchInput.addEventListener('focus', () => {
                this.showDropdown();
            });

            this.elements.searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.hideDropdown();
                }
            });
        }

        // Click outside to close dropdown
        document.addEventListener('click', (e) => {
            if (this.elements.dropdownBidangKeahlian && 
                this.elements.dropdownMenuBidang &&
                !this.elements.dropdownBidangKeahlian.contains(e.target) && 
                !this.elements.dropdownMenuBidang.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }

    loadInitialData() {
        // Load initial selected bidang keahlian from config
        if (this.config.initialSelectedBidangKeahlian && this.config.initialSelectedBidangKeahlian.length > 0) {
            this.selectedBidangKeahlian = this.config.initialSelectedBidangKeahlian.map(item => ({
                id: item.bidang_keahlian_id,
                nama: item.nama_bidang_keahlian
            }));
        }
    }

    toggleDropdown() {
        if (this.elements.dropdownMenuBidang.style.display === 'none') {
            this.showDropdown();
        } else {
            this.hideDropdown();
        }
    }

    showDropdown() {
        if (!this.elements.dropdownMenuBidang || !this.elements.dropdownBidangKeahlian || !this.elements.dropdownIcon) return;

        this.elements.dropdownMenuBidang.style.display = 'block';
        this.elements.dropdownBidangKeahlian.classList.add('active');
        this.elements.dropdownIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
    }

    hideDropdown() {
        if (!this.elements.dropdownMenuBidang || !this.elements.dropdownBidangKeahlian || !this.elements.dropdownIcon) return;

        this.elements.dropdownMenuBidang.style.display = 'none';
        this.elements.dropdownBidangKeahlian.classList.remove('active');
        this.elements.dropdownIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        
        if (this.elements.searchInput) {
            this.elements.searchInput.value = '';
        }
        
        this.filteredData = [...this.bidangKeahlianData];
        this.renderDropdownItems();
    }

    filterBidangKeahlian(searchTerm) {
        this.filteredData = this.bidangKeahlianData.filter(item => 
            item.nama_bidang_keahlian.toLowerCase().includes(searchTerm.toLowerCase()) &&
            !this.selectedBidangKeahlian.some(selected => selected.id === item.bidang_keahlian_id)
        );
        this.renderDropdownItems();
        this.showDropdown();
    }

    renderDropdownItems() {
        if (!this.elements.dropdownMenuBidang) return;

        const availableItems = this.filteredData.filter(item => 
            !this.selectedBidangKeahlian.some(selected => selected.id === item.bidang_keahlian_id)
        );

        if (availableItems.length === 0) {
            this.elements.dropdownMenuBidang.innerHTML = `
                <div class="dropdown-item-bidang disabled">
                    <i class="fas fa-search me-2"></i>
                    Tidak ada bidang keahlian yang tersedia
                </div>
            `;
            return;
        }

        this.elements.dropdownMenuBidang.innerHTML = availableItems.map(item => `
            <button class="dropdown-item-bidang" 
                    data-bidang-id="${item.bidang_keahlian_id}" 
                    data-bidang-nama="${this.escapeHtml(item.nama_bidang_keahlian)}">
                <i class="fas fa-plus-circle me-2 text-success"></i>
                ${this.escapeHtml(item.nama_bidang_keahlian)}
            </button>
        `).join('');

        // Add event listeners for dropdown items
        this.elements.dropdownMenuBidang.querySelectorAll('.dropdown-item-bidang:not(.disabled)').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const id = button.getAttribute('data-bidang-id');
                const nama = button.getAttribute('data-bidang-nama');
                this.addBidangKeahlian(id, nama);
            });
        });
    }

    addBidangKeahlian(id, nama) {
        if (!this.selectedBidangKeahlian.some(item => item.id === id)) {
            this.selectedBidangKeahlian.push({ id, nama });
            this.updateSelectedDisplay();
            this.updateHiddenInputs();
            this.renderDropdownItems();
            
            if (this.elements.searchInput) {
                this.elements.searchInput.value = '';
                this.elements.searchInput.focus();
            }

            // Trigger custom event
            this.triggerEvent('bidangKeahlianAdded', { id, nama });
        }
    }

    removeBidangKeahlian(id) {
        const removedItem = this.selectedBidangKeahlian.find(item => item.id === id);
        this.selectedBidangKeahlian = this.selectedBidangKeahlian.filter(item => item.id !== id);
        this.updateSelectedDisplay();
        this.updateHiddenInputs();
        this.renderDropdownItems();

        // Trigger custom event
        if (removedItem) {
            this.triggerEvent('bidangKeahlianRemoved', removedItem);
        }
    }

    updateSelectedDisplay() {
        if (!this.elements.selectedContainer) return;

        if (this.selectedBidangKeahlian.length === 0) {
            this.elements.selectedContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-plus-circle me-2"></i>
                    Belum ada bidang keahlian yang dipilih
                </div>
            `;
            this.elements.selectedContainer.classList.remove('has-items');
        } else {
            this.elements.selectedContainer.innerHTML = this.selectedBidangKeahlian.map(item => `
                <span class="bidang-keahlian-tag">
                    <i class="fas fa-code me-2"></i>
                    ${this.escapeHtml(item.nama)}
                    <button class="remove-tag" 
                            data-bidang-id="${item.id}" 
                            title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `).join('');
            this.elements.selectedContainer.classList.add('has-items');

            // Add event listeners for remove buttons
            this.elements.selectedContainer.querySelectorAll('.remove-tag').forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    const id = button.getAttribute('data-bidang-id');
                    this.removeBidangKeahlian(id);
                });
            });
        }
        
        if (this.elements.bidangCounter) {
            this.elements.bidangCounter.textContent = this.selectedBidangKeahlian.length;
        }
    }

    updateHiddenInputs() {
        if (!this.elements.hiddenInputsContainer) return;

        // Clear existing hidden inputs
        this.elements.hiddenInputsContainer.innerHTML = '';
        
        // Add hidden input for each selected bidang keahlian
        this.selectedBidangKeahlian.forEach(item => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'bidang_keahlian[]';
            hiddenInput.value = item.id;
            this.elements.hiddenInputsContainer.appendChild(hiddenInput);
        });
    }

    async submitBidangKeahlian() {
        if (!this.config.mahasiswaId) {
            this.showAlert('error', 'ID Mahasiswa tidak ditemukan');
            return;
        }

        // Set loading state
        this.setLoadingState(true);
        this.clearAlerts();

        try {
            // Prepare form data
            const formData = new FormData();
            
            // Add CSRF token
            if (this.config.csrfToken) {
                formData.append('_token', this.config.csrfToken);
            }

            // Add method
            formData.append('_method', 'PUT');

            // Add bidang keahlian
            this.selectedBidangKeahlian.forEach(item => {
                formData.append('bidang_keahlian[]', item.id);
            });

            // Submit via AJAX
            const response = await fetch(this.config.submitUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.status === 'success') {
                this.showAlert('success', data.message);
                this.triggerEvent('bidangKeahlianSaved', { 
                    data: data.data, 
                    selectedItems: [...this.selectedBidangKeahlian] 
                });
            } else {
                this.showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan data');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            this.showAlert('error', 'Terjadi kesalahan saat menghubungi server');
        } finally {
            this.setLoadingState(false);
        }
    }

    setLoadingState(isLoading) {
        if (!this.elements.btnSimpan || !this.elements.loadingSpinner || !this.elements.btnText) return;

        this.elements.btnSimpan.disabled = isLoading;
        
        if (isLoading) {
            this.elements.loadingSpinner.classList.remove('d-none');
            this.elements.btnText.textContent = 'Menyimpan...';
        } else {
            this.elements.loadingSpinner.classList.add('d-none');
            this.elements.btnText.textContent = 'Simpan Perubahan';
        }
    }

    showAlert(type, message) {
        if (!this.elements.alertContainer) return;

        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'warning' ? 'alert-warning' : 'alert-danger';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${this.escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        this.elements.alertContainer.innerHTML = alertHtml;

        // Auto hide after 5 seconds
        setTimeout(() => {
            const alert = this.elements.alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => {
                    this.elements.alertContainer.innerHTML = '';
                }, 150);
            }
        }, 5000);
    }

    clearAlerts() {
        if (this.elements.alertContainer) {
            this.elements.alertContainer.innerHTML = '';
        }
    }

    // Public API methods
    reset() {
        this.selectedBidangKeahlian = [];
        this.updateSelectedDisplay();
        this.updateHiddenInputs();
        this.renderDropdownItems();
        this.clearAlerts();
        this.triggerEvent('bidangKeahlianReset');
    }

    setBidangKeahlian(bidangKeahlianIds) {
        this.selectedBidangKeahlian = [];
        bidangKeahlianIds.forEach(id => {
            const item = this.bidangKeahlianData.find(bk => bk.bidang_keahlian_id === id);
            if (item) {
                this.selectedBidangKeahlian.push({
                    id: item.bidang_keahlian_id,
                    nama: item.nama_bidang_keahlian
                });
            }
        });
        this.updateSelectedDisplay();
        this.updateHiddenInputs();
        this.renderDropdownItems();
        this.triggerEvent('bidangKeahlianSet', { ids: bidangKeahlianIds });
    }

    getSelectedBidangKeahlian() {
        return [...this.selectedBidangKeahlian];
    }

    updateData(newBidangKeahlianData) {
        this.bidangKeahlianData = newBidangKeahlianData || [];
        this.filteredData = [...this.bidangKeahlianData];
        this.renderDropdownItems();
    }

    // Utility methods
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    triggerEvent(eventName, detail = {}) {
        if (this.elements.container) {
            const event = new CustomEvent(eventName, { 
                detail,
                bubbles: true,
                cancelable: true 
            });
            this.elements.container.dispatchEvent(event);
        }
    }

    // Cleanup method
    destroy() {
        // Remove event listeners and cleanup
        if (this.elements.container) {
            this.elements.container.innerHTML = '';
        }
        this.elements = {};
        this.selectedBidangKeahlian = [];
        this.bidangKeahlianData = [];
        this.filteredData = [];
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BidangKeahlianManager;
} else if (typeof window !== 'undefined') {
    window.BidangKeahlianManager = BidangKeahlianManager;
}