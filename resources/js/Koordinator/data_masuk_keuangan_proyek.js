// data_masuk_keuangan_proyek.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('Data Masuk Keuangan Proyek JS loaded');
    
    // Initialize currency formatting for input fields
    initializeCurrencyInput();
    
    // Handle view project button clicks with debugging
    initializeViewProjectButtons();
    
    // Check if we're on the detail page and load transaction data
    if (document.getElementById('tableKeuangan')) {
        console.log('Detail page detected, loading transaction data');
        loadTransaksiData();
        initializeAddTransactionForm();
        initializeSearchForm();
    }
});

function initializeSearchForm() {
    const searchForm = document.getElementById('searchDataMasukKeuanganProyekForm');
    if (!searchForm) {
        console.warn('Search form not found');
        return;
    }
    
    console.log('Initializing search form');
    
    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchValue = document.getElementById('searchDataMasukKeuanganProyek').value;
        
        // Update URL parameters
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('search', searchValue);
        currentUrl.hash = 'data-masuk-keuangan-proyek-section';
        
        window.history.pushState({}, '', currentUrl.toString());
        
        // Show "searching" message
        const tableBody = document.querySelector('#tableKeuangan tbody');
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2 text-muted small">Mencari data...</p>
                </td>
            </tr>
        `;
        
        // Load data with search filter
        loadTransaksiData(searchValue);
        
        // Scroll to results
        scrollToDataSection();
        
        // Show clear search button if needed
        const btnClearSearch = document.getElementById('btnClearSearch');
        if (btnClearSearch && searchValue) {
            btnClearSearch.classList.remove('d-none');
        }
    });
    
    // Handle clear search button
    const btnClearSearch = document.getElementById('btnClearSearch');
    if (btnClearSearch) {
        btnClearSearch.addEventListener('click', function() {
            document.getElementById('searchDataMasukKeuanganProyek').value = '';
            
            // Update URL parameters
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            window.history.pushState({}, '', currentUrl.toString());
            
            // Reload data without search filter
            loadTransaksiData();
            
            // Hide clear search button
            this.classList.add('d-none');
        });
    }
    
    // Check for existing search parameter when page loads
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        document.getElementById('searchDataMasukKeuanganProyek').value = searchParam;
        if (btnClearSearch) {
            btnClearSearch.classList.remove('d-none');
        }
    }
}

/**
 * Scroll to data section
 */
function scrollToDataSection() {
    const dataSection = document.getElementById('data-masuk-keuangan-proyek-section');
    if (dataSection) {
        window.scrollTo({
            top: dataSection.offsetTop - 80,
            behavior: 'smooth'
        });
    }
}

/**
 * Initialize currency input formatting
 */
function initializeCurrencyInput() {
    const currencyInputs = document.querySelectorAll('.currency-input');
    console.log('Currency inputs found:', currencyInputs.length);
    
    currencyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Remove non-digit characters
            let value = this.value.replace(/\D/g, '');
            
            // Format the number with thousand separators
            if (value) {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            
            this.value = value;
        });
    });
}

/**
 * Initialize view project buttons (eye icons)
 */
function initializeViewProjectButtons() {
    const viewButtons = document.querySelectorAll('.view-project-btn');
    console.log('View project buttons found:', viewButtons.length);
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const proyekId = this.dataset.proyekId;
            console.log('Button clicked for proyek ID:', proyekId);
            
            if (!proyekId) {
                console.error('No proyek ID found in clicked element');
                return;
            }
            
            showLoadingIndicator();
            console.log('Navigating to detail page for proyek ID:', proyekId);
            
            // Navigate to project detail page
            window.location.href = `/koordinator/data-masuk-keuangan-proyek/${proyekId}`;
        });
    });
    
    // Add global click delegation for eye icons (in case they're loaded dynamically)
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.view-project-btn');
        if (target) {
            e.preventDefault();
            const proyekId = target.dataset.proyekId;
            console.log('Delegated click handler triggered for proyek ID:', proyekId);
            
            if (proyekId) {
                showLoadingIndicator();
                window.location.href = `/koordinator/data-masuk-keuangan-proyek/${proyekId}`;
            }
        }
    });
}

/**
 * Show loading indicator
 */
function showLoadingIndicator() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('d-none');
        console.log('Loading indicator shown');
    } else {
        console.log('Loading indicator element not found');
    }
}

/**
 * Hide loading indicator
 */
function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.classList.add('d-none');
    }
}

/**
 * Load transaction data via AJAX
 */
function loadTransaksiData(searchFilter = '') {
    const tableBody = document.querySelector('#tableKeuangan tbody');
    if (!tableBody) {
        console.error('Table body element not found');
        return;
    }
    
    const proyekIdInput = document.querySelector('input[name="proyek_id"]');
    if (!proyekIdInput) {
        console.error('Could not find proyek_id input field');
        return;
    }
    
    const proyekId = proyekIdInput.value;
    console.log('Loading transaction data for proyek ID:', proyekId);
    
    // Show loading state
    tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted small">Memuat data...</p>
            </td>
        </tr>
    `;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout');
    }
    
    // Prepare search parameters
    const params = new URLSearchParams();
    if (searchFilter) {
        params.append('search', searchFilter);
    }
    
    // Fetch transaction data
    fetch(`/koordinator/data-masuk-keuangan-proyek/${proyekId}/transaksi?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Transaction data loaded:', data);
        
        if (data.data && data.data.length > 0) {
            // Build table rows
            let html = '';
            data.data.forEach(item => {
                html += `
                <tr>
                    <td>${item.tanggal}</td>
                    <td>${item.keterangan}</td>
                    <td>Rp ${item.nominal}</td>
                    <td>${item.bukti}</td>
                    <td>${item.aksi}</td>
                </tr>
                `;
            });
            tableBody.innerHTML = html;
            
            // Initialize delete buttons
            initializeDeleteButtons();
            
            // If this was a search, show results message
            if (searchFilter) {
                showSearchResultMessage(searchFilter, data.data.length);
            }
        } else {
            // Show empty state
            if (searchFilter) {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="d-flex justify-content-center flex-column align-items-center">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                <circle cx="11" cy="11" r="8" stroke="#8E8E8E" stroke-width="1.5"/>
                                <path d="M16.5 16.5L21 21" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M11 7V11" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M11 15H11.01" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <p class="text-muted mb-1">Tidak ada transaksi ditemukan dengan kata kunci: <strong>"${searchFilter}"</strong></p>
                        </div>
                    </td>
                </tr>
                `;
            } else {
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="d-flex justify-content-center flex-column align-items-center">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                <path d="M8 2V5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M16 2V5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M3.5 9.08984H20.5" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M19.21 15.7698L15.67 19.3098C15.53 19.4498 15.4 19.7098 15.37 19.8998L15.18 21.2498C15.11 21.7398 15.45 22.0798 15.94 22.0098L17.29 21.8198C17.48 21.7898 17.75 21.6598 17.88 21.5198L21.42 17.9798C22.03 17.3698 22.32 16.6598 21.42 15.7598C20.53 14.8698 19.82 15.1598 19.21 15.7698Z" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.7002 16.2798C19.0002 17.3598 19.8402 18.1998 20.9202 18.4998" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5V12" stroke="#8E8E8E" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.9955 13.7002H12.0045" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.29431 13.7002H8.30329" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.29431 16.7002H8.30329" stroke="#8E8E8E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="text-muted">Belum ada data transaksi pemasukan</p>
                        </div>
                    </td>
                </tr>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error loading transaction data:', error);
        tableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center text-danger py-4">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 9L9 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 9L15 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>Gagal memuat data transaksi</p>
                <p class="small text-muted">Silakan coba lagi nanti</p>
            </td>
        </tr>
        `;
    });
}

/**
 * Show search result message
 */
/**
 * Show search result message
 */
function showSearchResultMessage(searchFilter, resultCount) {
    // Create alert element for search results
    const existingAlert = document.querySelector('.alert.alert-info.search-result');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alertElement = document.createElement('div');
    alertElement.className = 'alert alert-info search-result';
    alertElement.innerHTML = `Menampilkan ${resultCount} hasil untuk pencarian "${searchFilter}"`;
    
    // Find a suitable container for the alert
    const container = document.querySelector('.section-data-keuangan-tefa') || 
                     document.querySelector('.card-body') || 
                     document.querySelector('.card-header');
    
    if (container) {
        // Safer insertion method - prepend to the container
        // This puts the alert at the beginning of the container
        container.insertBefore(alertElement, container.firstChild);
        
        // Alternative: you can put it after the title if that element exists
        const titleElement = container.querySelector('.title-table');
        if (titleElement && titleElement.parentNode === container) {
            container.insertBefore(alertElement, titleElement.nextSibling);
        }
    }
}

/**
 * Initialize add transaction form
 */
function initializeAddTransactionForm() {
    const form = document.getElementById('addTransactionForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Create FormData object
            const formData = new FormData(this);
            
            // Disable submit button
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Submit form via AJAX
            fetch('/koordinator/data-masuk-keuangan-proyek/tambah-transaksi', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Reset form
                form.reset();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addTransactionModal'));
                modal.hide();
                
                // Show success message
                showAlert(data.message, 'success');
                
                // Reload transaction data
                const searchInput = document.getElementById('searchDataMasukKeuanganProyek');
                loadTransaksiData(searchInput ? searchInput.value : '');
                
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';
            })
            .catch(error => {
                console.error('Error adding transaction:', error);
                showAlert('Gagal menambahkan transaksi. Silakan coba lagi.', 'danger');
                
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Simpan';
            });
        });
    }
}

/**
 * Initialize delete buttons for transactions
 */
function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete-transaction');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const transaksiId = this.dataset.id;
            
            if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                deleteTransaction(transaksiId);
            }
        });
    });
}

/**
 * Delete transaction via AJAX
 */
function deleteTransaction(transaksiId) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Submit delete request
    fetch(`/koordinator/data-masuk-keuangan-proyek/hapus-transaksi/${transaksiId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            // Reload transaction data
            const searchInput = document.getElementById('searchDataMasukKeuanganProyek');
            loadTransaksiData(searchInput ? searchInput.value : '');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error deleting transaction:', error);
        showAlert('Gagal menghapus transaksi. Silakan coba lagi.', 'danger');
    });
}

function showAlert(message, type) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Find container to show alert
    const container = document.querySelector('.section-data-keuangan-tefa') || document.querySelector('.card-header');
    
    // Insert alert before the container's first child
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
}