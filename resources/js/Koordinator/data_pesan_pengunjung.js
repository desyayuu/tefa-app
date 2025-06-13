document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); 
    });

    initPesanPengunjungFunctionality();
});

function initPesanPengunjungFunctionality() {
    // Handle form validation on modal edit forms
    const editForms = document.querySelectorAll('form[action*="update-pesan"]');
    
    editForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearFieldError(this);
            });

            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });

    // Handle search form
    const searchForm = document.querySelector('form[action*="getPesanPengunjung"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        
        // Auto-submit on Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });

        // Clear search functionality
        const clearButton = document.querySelector('a[href*="getPesanPengunjung"]:not([href*="search"])');
        if (clearButton) {
            clearButton.addEventListener('click', function(e) {
                if (searchInput.value.trim() === '') {
                    e.preventDefault();
                }
            });
        }
    }

    // Handle delete confirmation
    const deleteButtons = document.querySelectorAll('button[data-bs-target*="modalDelete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-bs-target');
            const modal = document.querySelector(modalId);
            
            if (modal) {
                // Focus on cancel button when modal opens
                setTimeout(() => {
                    const cancelButton = modal.querySelector('.btn-tutup');
                    if (cancelButton) {
                        cancelButton.focus();
                    }
                }, 300);
            }
        });
    });

    // Handle modal events
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            clearFormErrors(this);
        });
    });

    // Phone number formatting for Indonesian numbers
    const phoneInputs = document.querySelectorAll('input[name="telepon_pengirim"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            formatPhoneNumber(e.target);
        });
    });

    // Email validation on blur
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateEmailField(this);
        });
    });
}

// Form validation functions
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.getAttribute('name');
    let isValid = true;
    let errorMessage = '';

    // Required field validation
    if (field.hasAttribute('required') && value === '') {
        isValid = false;
        errorMessage = `${getFieldLabel(fieldName)} wajib diisi`;
    }
    
    // Email validation
    else if (field.type === 'email' && value !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Format email tidak valid';
        }
    }
    
    // Length validation
    else if (value !== '') {
        const maxLength = field.getAttribute('maxlength');
        if (maxLength && value.length > parseInt(maxLength)) {
            isValid = false;
            errorMessage = `${getFieldLabel(fieldName)} maksimal ${maxLength} karakter`;
        }
    }

    // Show/hide error
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }

    return isValid;
}

function validateEmailField(field) {
    const value = field.value.trim();
    
    if (value !== '') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Format email tidak valid');
            return false;
        }
    }
    
    clearFieldError(field);
    return true;
}

function getFieldLabel(fieldName) {
    const labels = {
        'nama_pengirim': 'Nama',
        'perusahaan_pengirim': 'Perusahaan',
        'email_pengirim': 'Email',
        'telepon_pengirim': 'Telepon',
        'pesan_pengirim': 'Pesan'
    };
    
    return labels[fieldName] || 'Field';
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
        feedback.style.display = 'block';
    }
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = '';
        feedback.style.display = 'none';
    }
}

function clearFormErrors(modal) {
    const inputs = modal.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
    
    const feedbacks = modal.querySelectorAll('.invalid-feedback');
    feedbacks.forEach(feedback => {
        feedback.textContent = '';
        feedback.style.display = 'none';
    });
}

function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, ''); // Remove non-digits
    
    // Add +62 prefix if not present and number starts with 8
    if (value.startsWith('8') && value.length > 1) {
        value = '62' + value;
    }
    
    // Format with +62
    if (value.startsWith('62')) {
        value = '+' + value;
    }
    
    input.value = value;
}

// Utility functions
function showAlert(type, message) {
    const alertContainer = document.querySelector('.content-table');
    if (!alertContainer) return;

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Search functionality
function handleSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchValue = searchInput ? searchInput.value.trim() : '';
    
    if (searchValue === '') {
        // If search is empty, redirect to clean URL
        window.location.href = window.location.pathname;
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const modal = bootstrap.Modal.getInstance(openModal);
            if (modal) {
                modal.hide();
            }
        }
    }
});