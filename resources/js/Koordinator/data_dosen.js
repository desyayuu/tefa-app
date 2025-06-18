document.addEventListener('DOMContentLoaded', function() {
    autoCloseAlerts();
    tambahDosen();
    editDosen();
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

// Validate NIDN format (10 digits)
function isValidNidn(nidn) {
    return /^\d{10}$/.test(nidn);
}

// Check if email exists in database
function checkEmailExistsAsync(email) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('email_dosen', email);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/koordinator/check-email-nidn-exists', {
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

// Check if NIDN exists in database
function checkNidnExistsAsync(nidn) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('nidn_dosen', nidn);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/koordinator/check-email-nidn-exists', {
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
            resolve(data.nidnExists);
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
            errorElement.textContent = 'Nama dosen wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Nama dosen wajib diisi';
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

// Validate NIDN format
function validateNidnFormat(nidnInput) {
    const nidnValue = nidnInput.value.trim();
    
    // Validasi NIDN tidak boleh kosong
    if (!nidnValue) {
        nidnInput.classList.add('is-invalid');
        const errorElement = nidnInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'NIDN/NIP wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'NIDN/NIP wajib diisi';
            nidnInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    // Validasi NIDN harus berupa angka dan tepat 10 digit
    if (!/^\d{10}$/.test(nidnValue)) {
        nidnInput.classList.add('is-invalid');
        const errorElement = nidnInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'NIDN harus berupa angka dan tepat 10 digit';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'NIDN harus berupa angka dan tepat 10 digit';
            nidnInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    nidnInput.classList.remove('is-invalid');
    const errorElement = nidnInput.nextElementSibling;
    if (errorElement && errorElement.classList.contains('invalid-feedback')) {
        errorElement.textContent = '';
    }
    return true;
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

// Validate NIDN with database check
async function validateNidn(nidnInput, dosenId, originalNidn) {
    const nidnValue = nidnInput.value.trim();
    
    // Skip validation if the value is unchanged
    if (nidnValue === originalNidn) {
        nidnInput.classList.remove('is-invalid');
        const errorElement = nidnInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
    
    // Check NIDN format first
    if (!validateNidnFormat(nidnInput)) {
        return false;
    }
    
    try {
        const formData = new FormData();
        formData.append('nidn_dosen', nidnValue);
        formData.append('dosen_id', dosenId); // Tambahkan dosen_id ke request
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add loading indicator
        nidnInput.classList.add('is-loading');
        
        const response = await fetch('/koordinator/check-email-nidn-exists', {
            method: 'POST',
            body: formData
        });
        
        // Remove loading indicator
        nidnInput.classList.remove('is-loading');
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.nidnExists) {
            nidnInput.classList.add('is-invalid');
            const errorElement = nidnInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = 'NIDN sudah terdaftar dalam sistem.';
            } else {
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.textContent = 'NIDN sudah terdaftar dalam sistem.';
                nidnInput.parentNode.appendChild(errorDiv);
            }
            return false;
        } else {
            nidnInput.classList.remove('is-invalid');
            const errorElement = nidnInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = '';
            }
            return true;
        }
    } catch (error) {
        console.error('Error validating NIDN:', error);
        
        // Remove loading indicator if there was an error
        nidnInput.classList.remove('is-loading');
        
        // Show error message
        nidnInput.classList.add('is-invalid');
        const errorElement = nidnInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Gagal memeriksa NIDN. Silakan coba lagi.';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Gagal memeriksa NIDN. Silakan coba lagi.';
            nidnInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
}

// Validate email with database check
async function validateEmail(emailInput, dosenId, originalEmail) {
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
        formData.append('email_dosen', emailValue);
        formData.append('dosen_id', dosenId); // Tambahkan dosen_id ke request
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add loading indicator
        emailInput.classList.add('is-loading');
        
        const response = await fetch('/koordinator/check-email-nidn-exists', {
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

function tambahDosen() {
    let dosenList = [];

    // DOM Elements
    const btnTambahkanKeDaftarDosen= document.getElementById('btnTambahkanKeDaftarDosen'); 
    const daftarDosen = document.getElementById('daftarDosen');
    const dosenJsonData = document.getElementById('dosenJsonData');
    const isSingle = document.getElementById('isSingle');
    const emptyRow = document.getElementById('emptyRow');
    const formTambahDosen = document.getElementById('formTambahDosen');
    const modalTambahDosen = document.getElementById('modalTambahDosen');
    const btnSimpan = document.getElementById('btnSimpan');
    const formError = document.getElementById('form_error');

    // Input fields
    const namaDosen = document.getElementById('nama_dosen');
    const nidnDosen = document.getElementById('nidn_dosen');
    const statusDosen = document.getElementById('status_akun_dosen');
    const emailDosen = document.getElementById('email_dosen');
    const passwordDosen = document.getElementById('password_dosen');
    const tanggalLahirDosen = document.getElementById('tanggal_lahir_dosen');
    const jenisKelaminDosen = document.getElementById('jenis_kelamin_dosen');
    const teleponDosen = document.getElementById('telepon_dosen');
    const profileImgDosen = document.getElementById('profile_img_dosen');

    // Error message elements 
    const namaError = document.getElementById('nama_error');
    const nidnError = document.getElementById('nidn_error');
    const statusError = document.getElementById('status_akun_error');
    const emailError = document.getElementById('email_error');
    const passwordError = document.getElementById('password_error');
    const tanggalLahirError = document.getElementById('tanggal_lahir_error');
    const jenisKelaminError = document.getElementById('jenis_kelamin_error');
    const teleponError = document.getElementById('telepon_error');
    const profileImgError = document.getElementById('profile_img_error');

    // Setup password toggle visibility if available
    setupPasswordToggle();

    // Field validations on blur
    setupFieldValidations();

    // Reset modal on close
    if (modalTambahDosen) {
        modalTambahDosen.addEventListener('hidden.bs.modal', resetModal);
    }

    // Button event listeners
    if (btnTambahkanKeDaftarDosen) {
        btnTambahkanKeDaftarDosen.addEventListener('click', handleAddToDaftarClick);
    }

    // Form submission
    if (formTambahDosen) {
        formTambahDosen.addEventListener('submit', handleFormSubmit);
    }

    // Password toggle setup
    function setupPasswordToggle() {
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword && passwordDosen) {
            togglePassword.addEventListener('click', function() {
                const type = passwordDosen.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordDosen.setAttribute('type', type);
                
                const eyeIcon = document.getElementById('eye-icon');
                if (eyeIcon) {
                    if (type === 'text') {
                        eyeIcon.classList.remove('bi-eye');
                        eyeIcon.classList.add('bi-eye-slash');
                    } else {
                        eyeIcon.classList.remove('bi-eye-slash');
                        eyeIcon.classList.add('bi-eye');
                    }
                }
            });
        }
    }

    // Set up field validations on blur
    function setupFieldValidations() {
        if (namaDosen) {
            namaDosen.addEventListener('blur', function() {
                validateNama(namaDosen);
            });
        }

        if (nidnDosen) {
            nidnDosen.addEventListener('blur', function() {
                validateNidnFormat(nidnDosen);
            });
        }
        
        if (emailDosen) {
            emailDosen.addEventListener('blur', function() {
                validateEmailFormat(emailDosen);
            });
        }
    }

    // Add dosen to the list
    function addDosenToList() {
        // Generate a unique index for this dosen
        const index = dosenList.length;
        
        // Create the dosen object without the image first
        const dosen = {
            nama_dosen: namaDosen.value.trim(),
            nidn_dosen: nidnDosen.value.trim(),
            status_akun_dosen: statusDosen.value,
            email_dosen: emailDosen.value.trim(),
            password_dosen: passwordDosen.value || nidnDosen.value.trim(), // Use NIDN as default password
            tanggal_lahir_dosen: tanggalLahirDosen.value,
            jenis_kelamin_dosen: jenisKelaminDosen.value || null,
            telepon_dosen: teleponDosen.value.trim(),
            has_profile_img: profileImgDosen.files.length > 0,
            profile_img_name: profileImgDosen.files.length > 0 ? profileImgDosen.files[0].name : ''
        };
        
        // Add to the list
        dosenList.push(dosen);
        dosenJsonData.value = JSON.stringify(dosenList);
        
        // Handle the image file if exists
        if (profileImgDosen.files.length > 0) {
            // Create a clone of the file input with a unique name for this dosen
            const fileClone = document.createElement('input');
            fileClone.type = 'file';
            fileClone.name = `profile_img_dosen_${index}`;
            fileClone.classList.add('d-none'); // Hide it
            fileClone.setAttribute('data-dosen-index', index);
            
            // Create a DataTransfer object to set the files
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(profileImgDosen.files[0]);
            fileClone.files = dataTransfer.files;
            
            // Add to the form
            formTambahDosen.appendChild(fileClone);
        }
        
        // Update the table
        updateDosenTable();
    }

    // Update the dosen table display
    function updateDosenTable() {
        if (emptyRow) {
            emptyRow.remove();
        }
        
        daftarDosen.innerHTML = '';
        
        dosenList.forEach((dosen, index) => {
            const row = document.createElement('tr');
            
            // Display the photo info if available
            const photoInfo = dosen.has_profile_img 
                ? `<i class="bi bi-image text-success"></i> ${dosen.profile_img_name}`
                : 'No image';
                
            row.innerHTML = `
                <td>${dosen.nama_dosen}</td>
                <td>${dosen.nidn_dosen}</td>
                <td>${dosen.email_dosen}</td>
                <td>${dosen.status_akun_dosen}</td>
                <td>${photoInfo}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" data-index="${index}">Hapus</button>
                </td>
            `;
            
            const deleteBtn = row.querySelector('button[data-index]');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    
                    // Also remove the file input if it exists
                    const fileInput = formTambahDosen.querySelector(`input[name="profile_img_dosen_${idx}"]`);
                    if (fileInput) {
                        fileInput.remove();
                    }
                    
                    // Remove from the list
                    dosenList.splice(idx, 1);
                    dosenJsonData.value = JSON.stringify(dosenList);
                    
                    // Reindex remaining file inputs
                    const fileInputs = formTambahDosen.querySelectorAll('input[data-dosen-index]');
                    fileInputs.forEach(input => {
                        const inputIndex = parseInt(input.getAttribute('data-dosen-index'));
                        if (inputIndex > idx) {
                            input.name = `profile_img_dosen_${inputIndex - 1}`;
                            input.setAttribute('data-dosen-index', inputIndex - 1);
                        }
                    });
                    
                    updateDosenTable();
                    
                    if (dosenList.length === 0) {
                        daftarDosen.innerHTML = `
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center">Belum ada dosen yang ditambahkan ke daftar</td>
                            </tr>
                        `;
                    }
                });
            }
            
            daftarDosen.appendChild(row);
        });
    }

    // Clear the form
    function clearForm() {
        if (namaDosen) namaDosen.value = '';
        if (nidnDosen) nidnDosen.value = '';
        if (statusDosen) statusDosen.value = 'Active';
        if (emailDosen) emailDosen.value = '';
        if (passwordDosen) passwordDosen.value = '';
        if (tanggalLahirDosen) tanggalLahirDosen.value = '';
        if (jenisKelaminDosen) jenisKelaminDosen.value = '';
        if (teleponDosen) teleponDosen.value = '';
        if (profileImgDosen) profileImgDosen.value = '';
        
        resetErrorMessages();
    }

    // Reset error messages
    function resetErrorMessages() {
        if (formError) {
            formError.classList.add('d-none');
            formError.textContent = '';
        }
        
        if (formTambahDosen) {
            const invalidInputs = formTambahDosen.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
        }
        
        // Clear individual error messages
        if (namaError) namaError.textContent = '';
        if (nidnError) nidnError.textContent = '';
        if (statusError) statusError.textContent = '';
        if (emailError) emailError.textContent = '';
        if (passwordError) passwordError.textContent = '';
        if (tanggalLahirError) tanggalLahirError.textContent = '';
        if (jenisKelaminError) jenisKelaminError.textContent = '';
        if (teleponError) teleponError.textContent = '';
        if (profileImgError) profileImgError.textContent = '';
    }

    // Reset modal on close
    function resetModal() {
        clearForm();
        resetErrorMessages();
        dosenList = [];
        if (dosenJsonData) dosenJsonData.value = '[]';
        if (isSingle) isSingle.value = '1';
        
        // Remove all hidden file inputs
        const fileInputs = formTambahDosen.querySelectorAll('input[data-dosen-index]');
        fileInputs.forEach(input => input.remove());
        
        if (daftarDosen) {
            daftarDosen.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="6" class="text-center">Belum ada dosen yang ditambahkan ke daftar</td>
                </tr>
            `;
        }
    }

    // Validate form for adding new dosen
    async function validateFormAsync() {
        let isValid = true;
        resetErrorMessages();
    
        // Validate Name
        if (namaDosen && !namaDosen.value.trim()) {
            namaDosen.classList.add('is-invalid');
            if (namaError) namaError.textContent = "Nama dosen wajib diisi";
            isValid = false;
        }
    
        // Validate NIDN
        if (nidnDosen && !nidnDosen.value.trim()) {
            nidnDosen.classList.add('is-invalid');
            if (nidnError) nidnError.textContent = "NIDN/NIP wajib diisi";
            isValid = false;
        } else if (nidnDosen) {
            const nidnValue = nidnDosen.value.trim();
    
            // Validate NIDN format
            if (!isValidNidn(nidnValue)) {
                nidnDosen.classList.add('is-invalid');
                if (nidnError) nidnError.textContent = "NIDN harus berupa angka dan tepat 10 digit";
                isValid = false;
            } else {
                // Check for duplicates in the list
                const nidnDuplicate = dosenList.some(dosen => dosen.nidn_dosen === nidnValue);
                if (nidnDuplicate) {
                    nidnDosen.classList.add('is-invalid');
                    if (nidnError) nidnError.textContent = "NIDN/NIP sudah ada di daftar yang akan ditambahkan";
                    isValid = false;
                } else {
                    // Check with database
                    nidnDosen.classList.add('is-loading');
                    try {
                        const nidnExists = await checkNidnExistsAsync(nidnValue);
                        nidnDosen.classList.remove('is-loading');
                        if (nidnExists) {
                            nidnDosen.classList.add('is-invalid');
                            if (nidnError) nidnError.textContent = "NIDN/NIP sudah terdaftar di database";
                            isValid = false;
                        }
                    } catch (error) {
                        console.error('Error checking NIDN:', error);
                        nidnDosen.classList.remove('is-loading');
                        if (formError) {
                            formError.textContent = "Terjadi kesalahan saat memeriksa NIDN. Silakan coba lagi.";
                            formError.classList.remove('d-none');
                        }
                        isValid = false;
                    }
                }
            }
        }
    
        // Validate Email
        if (emailDosen && !emailDosen.value.trim()) {
            emailDosen.classList.add('is-invalid');
            if (emailError) emailError.textContent = "Email wajib diisi";
            isValid = false;
        } else if (emailDosen && !isValidEmail(emailDosen.value.trim())) {
            emailDosen.classList.add('is-invalid');
            if (emailError) emailError.textContent = "Format email tidak valid";
            isValid = false;
        } else if (emailDosen) {
            const emailValue = emailDosen.value.trim();
            // Check for duplicates in the list
            const emailDuplicate = dosenList.some(dosen => dosen.email_dosen === emailValue);
            if (emailDuplicate) {
                emailDosen.classList.add('is-invalid');
                if (emailError) emailError.textContent = "Email sudah ada di daftar yang akan ditambahkan";
                isValid = false;
            } else {
                // Check with database
                emailDosen.classList.add('is-loading');
                try {
                    const emailExists = await checkEmailExistsAsync(emailValue);
                    emailDosen.classList.remove('is-loading');
                    if (emailExists) {
                        emailDosen.classList.add('is-invalid');
                        if (emailError) {
                            emailError.textContent = "Email sudah terdaftar di database";
                            emailError.style.display = 'block';
                        }
                        isValid = false;
                    }
                } catch (error) {
                    console.error('Error checking email:', error);
                    emailDosen.classList.remove('is-loading');
                    if (formError) {
                        formError.textContent = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
                        formError.classList.remove('d-none');
                    }
                    isValid = false;
                }
            }
        }
    
        // Validate Profile Image
        if (profileImgDosen && profileImgDosen.files.length > 0) {
            const file = profileImgDosen.files[0];
            const fileSize = file.size / 1024 / 1024;
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    
            if (!validExtensions.includes(file.type)) {
                profileImgDosen.classList.add('is-invalid');
                if (profileImgError) profileImgError.textContent = "Format file tidak valid. Gunakan jpeg, png, jpg, atau gif";
                isValid = false;
            } else if (fileSize > 2) {
                profileImgDosen.classList.add('is-invalid');
                if (profileImgError) profileImgError.textContent = "Ukuran file terlalu besar. Maksimal 2MB";
                isValid = false;
            }
        }
    
        return isValid;
    }

    // Handle "Add to List" button click
    async function handleAddToDaftarClick() {
        resetErrorMessages();
        
        btnTambahkanKeDaftarDosen.disabled = true;
        btnTambahkanKeDaftarDosen.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memeriksa...';
        
        try {
            const isValid = await validateFormAsync();
            
            btnTambahkanKeDaftarDosen.disabled = false;
            btnTambahkanKeDaftarDosen.innerHTML = 'Tambahkan ke Daftar';
            
            if (!isValid) {
                return;
            }
            
            addDosenToList();
            clearForm();
            if (isSingle) isSingle.value = "0";
        } catch (error) {
            console.error('Validation error:', error);
            btnTambahkanKeDaftarDosen.disabled = false;
            btnTambahkanKeDaftarDosen.innerHTML = 'Tambahkan ke Daftar';
            
            if (formError) {
                formError.textContent = "Terjadi kesalahan saat validasi. Silakan coba lagi.";
                formError.classList.remove('d-none');
            }
        }
    }

    // Handle form submission
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        // If there are dosen in the list, submit the form normally
        if (dosenList.length > 0 || isSingle.value === "1") {
            // Show loading state
            const submitBtn = formTambahDosen.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            }
            
            // Submit the form
            formTambahDosen.submit();
        } else {
            // Show error
            if (formError) {
                formError.textContent = "Belum ada dosen yang ditambahkan ke daftar.";
                formError.classList.remove('d-none');
            }
        }
    }

    // Fungsi untuk menangani error validasi dari server
    function handleValidationErrors(errors) {
        // Tampilkan error untuk setiap field
        for (const field in errors) {
            const input = document.getElementById(`${field}_${dosenId}`);
            if (input) {
                input.classList.add('is-invalid');
                
                // Tampilkan pesan error di bawah field
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                    errorElement.textContent = errors[field][0];
                } else {
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    errorDiv.textContent = errors[field][0];
                    input.parentNode.appendChild(errorDiv);
                }
            }
        }
        
        // Tampilkan pesan error umum
        if (formErrorContainer) {
            formErrorContainer.textContent = "Terdapat kesalahan pada form. Mohon periksa kembali data yang dimasukkan.";
            formErrorContainer.classList.remove('d-none');
        }
        
        // Scroll ke error pertama
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }
}

/**
 * Edit Dosen Functionality
 */
function editDosen() {
    // Get all edit forms on the page
    const editForms = document.querySelectorAll('form[id^="form_edit_"]');
    
    editForms.forEach(form => {
        const dosenId = form.id.replace('form_edit_', '');
        const formErrorContainer = document.getElementById(`edit_form_error_${dosenId}`);
        
        // Store original values for fields when modal opens
        const modalEl = document.getElementById(`modalDosen${dosenId}`);
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
        const namaInput = document.getElementById(`nama_dosen_${dosenId}`);
        const nidnInput = document.getElementById(`nidn_dosen_${dosenId}`);
        const emailInput = document.getElementById(`email_dosen_${dosenId}`);
        
        if (namaInput) {
            namaInput.addEventListener('blur', function() {
                validateNama(namaInput);
            });
        }
        
        if (nidnInput) {
            nidnInput.addEventListener('blur', function() {
                validateNidnFormat(nidnInput);
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
            
            // NIDN validation ONLY if changed
            if (nidnInput && nidnInput.value !== nidnInput.getAttribute('data-original-value')) {
                let nidnValid = validateNidnFormat(nidnInput);
                
                // Check database only if format is valid and value has changed
                if (nidnValid) {
                    nidnValid = await validateNidn(nidnInput, dosenId, nidnInput.getAttribute('data-original-value'));
                }
                
                isValid = isValid && nidnValid;
            }
            
            // Email validation ONLY if changed
            if (emailInput && emailInput.value !== emailInput.getAttribute('data-original-value')) {
                let emailValid = validateEmailFormat(emailInput);
                
                // Check database only if format is valid and value has changed
                if (emailValid) {
                    emailValid = await validateEmail(emailInput, dosenId, emailInput.getAttribute('data-original-value'));
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
                        handleValidationErrors(form, result.errors, dosenId);
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
function handleValidationErrors(form, errors, dosenId) {
    for (const field in errors) {
        const inputId = `${field}_${dosenId}`;
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
    const formErrorContainer = document.getElementById(`edit_form_error_${dosenId}`);
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
    const editModals = document.querySelectorAll('.modal[id^="modalDosen"]');
    
    editModals.forEach(modal => {
        const dosenId = modal.id.replace('modalDosen', '');
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

