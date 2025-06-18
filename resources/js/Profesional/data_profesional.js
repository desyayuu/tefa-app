document.addEventListener('DOMContentLoaded', function() {
    autoCloseAlerts();
    editProfesional();
    setupModalCancelEvents();
});


function resetFormFieldsToOriginal() {
    const formInputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
    
    formInputs.forEach(input => {
        // Get the original value stored in data attribute
        const originalValue = input.getAttribute('data-original-value');
        if (originalValue !== undefined) {
            // Reset to original value
            input.value = originalValue;
        }
    });
    
    // Clear file inputs
    const fileInputs = form.querySelectorAll('input[type="file"]');
    fileInputs.forEach(fileInput => {
        fileInput.value = '';
    });
    
    // Clear password fields
    const passwordFields = form.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.value = '';
    });
}

function autoCloseAlerts() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000); 
    });
}

// Validate email format
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


// Check if email exists in database
function checkEmailExistsAsync(email) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('email_profesional', email);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/profesional/profil/check-email-nidn-exists', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            resolve(data.emailExists);
        })
        .catch(error => {
            reject(error);
        });
    });
}

// Clear empty date fields to prevent database errors
function clearEmptyDateFields(form) {
    const dateFields = form.querySelectorAll('input[type="date"]');
    dateFields.forEach(field => {
        if (field.value === '') {
            const originalName = field.getAttribute('name');
            field.setAttribute('data-original-name', originalName);
            field.removeAttribute('name');
        }
    });
    return true;
}

// Validate name field 
function validateNama(namaInput) {
    if (!namaInput.value.trim()) {
        namaInput.classList.add('is-invalid');
        const errorElement = namaInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Nama profesional wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Nama profesional wajib diisi';
            namaInput.parentNode.appendChild(errorDiv);
        }
        return false;
    } else {
        namaInput.classList.remove('is-invalid');
        const errorElement = namaInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
}


// Validate email format
function validateEmailFormat(emailInput) {
    const emailValue = emailInput.value.trim();
    
    // Validasi email tidak boleh kosong
    if (!emailValue) {
        emailInput.classList.add('is-invalid');
        const errorElement = emailInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Email wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Email wajib diisi';
            emailInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    // Validasi format email
    if (!isValidEmail(emailValue)) {
        emailInput.classList.add('is-invalid');
        const errorElement = emailInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Format email tidak valid';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Format email tidak valid';
            emailInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    emailInput.classList.remove('is-invalid');
    const errorElement = emailInput.nextElementSibling;
    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
        errorElement.textContent = '';
    }
    return true;
}

// Validate email with database check
async function validateEmail(emailInput, profesionalId, originalEmail) {
    const emailValue = emailInput.value.trim();
    
    // Skip validation if the value is unchanged
    if (emailValue === originalEmail) {
        emailInput.classList.remove('is-invalid');
        const errorElement = emailInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
    
    // Check email format first
    if (!validateEmailFormat(emailInput)) {
        return false;
    }
    
    try {
        const formData = new FormData();
        formData.append('email_profesional', emailValue);
        formData.append('profesional_id', profesionalId); // Tambahkan profesional_id ke request
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add loading indicator
        emailInput.classList.add('is-loading');
        
        const response = await fetch('/profesional/profil/check-email-nidn-exists', {
            method: 'POST',
            body: formData
        });
        
        // Remove loading indicator
        emailInput.classList.remove('is-loading');
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.emailExists) {
            emailInput.classList.add('is-invalid');
            const errorElement = emailInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = 'Email sudah terdaftar dalam sistem.';
            } else {
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.textContent = 'Email sudah terdaftar dalam sistem.';
                emailInput.parentNode.appendChild(errorDiv);
            }
            return false;
        } else {
            emailInput.classList.remove('is-invalid');
            const errorElement = emailInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = '';
            }
            return true;
        }
    } catch (error) {
        console.error('Error validating email:', error);
        
        // Remove loading indicator if there was an error
        emailInput.classList.remove('is-loading');
        
        // Show error message
        emailInput.classList.add('is-invalid');
        const errorElement = emailInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Gagal memeriksa email. Silakan coba lagi.';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Gagal memeriksa email. Silakan coba lagi.';
            emailInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
}

/**
 * Edit Profesional Functionality
 */
function editProfesional() {
    // Get all edit forms on the page
    const editForms = document.querySelectorAll('form[id^="form_edit_"]');
    
    editForms.forEach(form => {
        const profesionalId = form.id.replace('form_edit_', '');
        const formErrorContainer = document.getElementById(`edit_form_error_${profesionalId}`);
        
        // Store original values for fields when modal opens
        const modalEl = document.getElementById(`modalProfesional${profesionalId}`);
        if (modalEl) {
            modalEl.addEventListener('show.bs.modal', function() {
                console.log(`Modal ${modalEl.id} fully shown`);
                // Pastikan form diatur ulang ke nilai asli dari database
                const formInputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
                formInputs.forEach(input => {
                    input.setAttribute('data-original-value', input.value);
                });

            });

            modalEl.addEventListener('show.bs.modal', function() {
                console.log(`Modal ${modalEl.id} opening`);
                storeOriginalValues(form);
            });
        }
        
        // Set up field validations
        const namaInput = document.getElementById(`nama_profesional_${profesionalId}`);
        const emailInput = document.getElementById(`email_profesional_${profesionalId}`);
        
        if (namaInput) {
            namaInput.addEventListener('blur', function() {
                validateNama(namaInput);
            });
        }
        
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                validateEmailFormat(emailInput);
            });
        }
        
        // Handle form submission with AJAX
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Hide any previous error messages
            if (formErrorContainer) {
                formErrorContainer.classList.add('d-none');
                formErrorContainer.textContent = '';
            }
            
            // Reset validation errors
            resetFormValidation(form);
            
            // Disable submit button and show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            }
            
            // Client-side validation
            let isValid = true;
            
            // Name validation (required)
            if (namaInput) {
                const namaValid = validateNama(namaInput);
                isValid = isValid && namaValid;
            }
            
            // Email validation ONLY if changed
            if (emailInput && emailInput.value !== emailInput.getAttribute('data-original-value')) {
                let emailValid = validateEmailFormat(emailInput);
                
                // Check database only if format is valid and value has changed
                if (emailValid) {
                    emailValid = await validateEmail(emailInput, profesionalId, emailInput.getAttribute('data-original-value'));
                }
                
                isValid = isValid && emailValid;
            }
            
            // If client-side validation fails, show error
            if (!isValid) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan Perubahan';
                }
                
                // Show general error message
                if (formErrorContainer) {
                    formErrorContainer.textContent = "Mohon periksa kembali data yang dimasukkan.";
                    formErrorContainer.classList.remove('d-none');
                }
                
                // Scroll to first invalid input
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
                
                return;
            }
            
            // Clear empty date fields to prevent database errors
            clearEmptyDateFields(form);
            
            // Submit form with AJAX
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Success - reload the page
                    window.location.reload();
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        handleValidationErrors(form, result.errors, profesionalId);
                    } else {
                        // Show general error message
                        if (formErrorContainer) {
                            formErrorContainer.textContent = result.message || "Terjadi kesalahan saat menyimpan data.";
                            formErrorContainer.classList.remove('d-none');
                        }
                    }
                    
                    // Reset button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan Perubahan';
                    }
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                
                // Show error message
                if (formErrorContainer) {
                    formErrorContainer.textContent = "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.";
                    formErrorContainer.classList.remove('d-none');
                }
                
                // Reset button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan Perubahan';
                }
            }
        });
    });
}

/**
 * Reset form validation errors
 */
function resetFormValidation(form) {
    const invalidInputs = form.querySelectorAll('.is-invalid');
    invalidInputs.forEach(input => {
        input.classList.remove('is-invalid');
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
    });
}

/**
 * Handle validation errors from server
 */
function handleValidationErrors(form, errors, profesionalId) {
    for (const field in errors) {
        const inputId = `${field}_${profesionalId}`;
        const input = document.getElementById(inputId);
        if (input) {
            input.classList.add('is-invalid');
            
            // Display error message
            const errorElement = input.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            } else {
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                input.parentNode.appendChild(errorDiv);
            }
        }
    }
    
    // Show general error message
    const formErrorContainer = document.getElementById(`edit_form_error_${profesionalId}`);
    if (formErrorContainer) {
        formErrorContainer.textContent = "Terdapat kesalahan pada form. Mohon periksa kembali data yang dimasukkan.";
        formErrorContainer.classList.remove('d-none');
    }
    
    // Scroll to first error
    const firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus();
    }
}

/**
 * Setup modal cancel events
 */
function setupModalCancelEvents() {
    const editModals = document.querySelectorAll('.modal[id^="modalProfesional"]');
    
    editModals.forEach(modal => {
        const profesionalId = modal.id.replace('modalProfesional', '');
        const form = modal.querySelector('form');
        
        // Simpan nilai asli saat modal dibuka
        modal.addEventListener('show.bs.modal', function() {
            console.log(`Modal ${modal.id} opened - storing original values`);
            storeOriginalValues(form);
        });
        
        // Reset form saat tombol "Batalkan" diklik
        const cancelBtn = modal.querySelector('button.btn-tutup');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                console.log(`Cancel button clicked in modal ${modal.id}`);
                resetFormToOriginal(form);
            });
        }
        
        // Reset form saat tombol "X" (close) diklik
        const closeBtn = modal.querySelector('button.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                console.log(`Close button clicked in modal ${modal.id}`);
                resetFormToOriginal(form);
            });
        }
        
        // Reset form saat modal ditutup dengan cara apapun
        modal.addEventListener('hidden.bs.modal', function() {
            console.log(`Modal ${modal.id} hidden`);
            resetFormToOriginal(form);
        });
    });
}

/**
 * Store original form values
 */
function storeOriginalValues(form) {
    // Simpan nilai asli dalam objek pada form
    form._originalValues = {};
    
    const formInputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
    formInputs.forEach(input => {
        // Simpan nilai asli dalam objek
        const id = input.id;
        form._originalValues[id] = input.value;
        
        // Simpan juga sebagai atribut data
        input.setAttribute('data-original-value', input.value);
        
        console.log(`Stored original value for ${id}: ${input.value}`);
    });
}

/**
 * Reset form to original values with immediate effect
 */
function resetFormToOriginal(form) {
    console.log('Resetting form to original values');
    
    // Gunakan nilai yang disimpan dalam objek form
    if (form._originalValues) {
        const formInputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
        
        formInputs.forEach(input => {
            const id = input.id;
            if (form._originalValues[id] !== undefined) {
                // Set nilai langsung
                input.value = form._originalValues[id];
                console.log(`Reset ${id} to ${form._originalValues[id]}`);
            } else {
                // Fallback ke atribut data
                const originalValue = input.getAttribute('data-original-value');
                if (originalValue !== null && originalValue !== undefined) {
                    input.value = originalValue;
                    console.log(`Reset ${id} to ${originalValue} (from attribute)`);
                }
            }
        });
    } else {
        // Fallback ke atribut data jika objek tidak ada
        const formInputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
        
        formInputs.forEach(input => {
            const originalValue = input.getAttribute('data-original-value');
            if (originalValue !== null && originalValue !== undefined) {
                input.value = originalValue;
                console.log(`Reset ${input.id} to ${originalValue} (from attribute)`);
            }
        });
    }
    
    // Bersihkan input file
    const fileInputs = form.querySelectorAll('input[type="file"]');
    fileInputs.forEach(fileInput => {
        fileInput.value = '';
    });
    
    // Bersihkan input password
    const passwordFields = form.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.value = '';
    });
    
    // Hapus pesan error validasi
    resetFormValidation(form);
    
    // Sembunyikan pesan error umum
    const formError = form.querySelector('.alert-danger');
    if (formError) {
        formError.classList.add('d-none');
        formError.textContent = '';
    }
}

