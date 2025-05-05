document.addEventListener('DOMContentLoaded', function() {
    autoCloseAlerts();
    tambahMahasiswa();
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

// Validate NIM format (10 digits)
function isValidNim(nim) {
    return /^\d{10}$/.test(nim);
}

// Check if email exists in database
function checkEmailExistsAsync(email) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('email_mahasiswa', email);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/koordinator/check-email-nim-exists', {
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

// Check if NIM exists in database
function checkNimExistsAsync(nim) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('nim_mahasiswa', nim);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/koordinator/check-email-nim-exists', {
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
            resolve(data.nimExists);
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
            errorElement.textContent = 'Nama mahasiswa wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Nama mahasiswa wajib diisi';
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

// Validate NIM format
function validateNimFormat(nimInput) {
    const nimValue = nimInput.value.trim();
    
    // Validasi NIM tidak boleh kosong
    if (!nimValue) {
        nimInput.classList.add('is-invalid');
        const errorElement = nimInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'NIM wajib diisi';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'NIM wajib diisi';
            nimInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    // Validasi NIM harus berupa angka dan tepat 10 digit
    if (!/^\d{10}$/.test(nimValue)) {
        nimInput.classList.add('is-invalid');
        const errorElement = nimInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'NIM harus berupa angka dan tepat 10 digit';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'NIM harus berupa angka dan tepat 10 digit';
            nimInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
    
    nimInput.classList.remove('is-invalid');
    const errorElement = nimInput.nextElementSibling;
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

// Validate NIM with database check
async function validateNim(nimInput, mahasiswaId, originalNim) {
    const nimValue = nimInput.value.trim();
    
    // Skip validation if the value is unchanged
    if (nimValue === originalNim) {
        nimInput.classList.remove('is-invalid');
        const errorElement = nimInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
    
    // Check NIM format first
    if (!validateNimFormat(nimInput)) {
        return false;
    }
    
    try {
        const formData = new FormData();
        formData.append('nim_mahasiswa', nimValue);
        formData.append('mahasiswa_id', mahasiswaId); // Tambahkan mahasiswa_id ke request
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add loading indicator
        nimInput.classList.add('is-loading');
        
        const response = await fetch('/koordinator/check-email-nim-exists', {
            method: 'POST',
            body: formData
        });
        
        // Remove loading indicator
        nimInput.classList.remove('is-loading');
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.nimExists) {
            nimInput.classList.add('is-invalid');
            const errorElement = nimInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = 'NIM sudah terdaftar dalam sistem.';
            } else {
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('invalid-feedback');
                errorDiv.textContent = 'NIM sudah terdaftar dalam sistem.';
                nimInput.parentNode.appendChild(errorDiv);
            }
            return false;
        } else {
            nimInput.classList.remove('is-invalid');
            const errorElement = nimInput.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = '';
            }
            return true;
        }
    } catch (error) {
        console.error('Error validating NIDN:', error);
        
        // Remove loading indicator if there was an error
        nimInput.classList.remove('is-loading');
        
        // Show error message
        nimInput.classList.add('is-invalid');
        const errorElement = nimInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = 'Gagal memeriksa NIDN. Silakan coba lagi.';
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = 'Gagal memeriksa NIDN. Silakan coba lagi.';
            nimInput.parentNode.appendChild(errorDiv);
        }
        return false;
    }
}

// Validate email with database check
async function validateEmail(emailInput, mahasiswaId, originalEmail) {
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
        formData.append('email_mahasiswa', emailValue);
        formData.append('mahasiswa_id', mahasiswaId); // Tambahkan mahasiswa_id ke request
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Add loading indicator
        emailInput.classList.add('is-loading');
        
        const response = await fetch('/koordinator/check-email-nim-exists', {
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

function tambahMahasiswa() {
    let mahasiswaList = [];

    // DOM Elements
    const btnTambahkanKeDaftarMahasiswa= document.getElementById('btnTambahkanKeDaftarMahasiswa'); 
    const daftarMahasiswa = document.getElementById('daftarMahasiswa');
    const mahasiswaJsonData = document.getElementById('mahasiswaJsonData');
    const isSingle = document.getElementById('isSingle');
    const emptyRow = document.getElementById('emptyRow');
    const formTambahMahasiswa = document.getElementById('formTambahMahasiswa');
    const modalTambahMahasiswa = document.getElementById('modalTambahMahasiswa');
    const btnSimpan = document.getElementById('btnSimpan');
    const formError = document.getElementById('form_error');

    // Input fields
    const namaMahasiswa = document.getElementById('nama_mahasiswa');
    const nimMahasiswa = document.getElementById('nim_mahasiswa');
    const statusMahasiswa = document.getElementById('status_akun_mahasiswa');
    const emailMahasiswa = document.getElementById('email_mahasiswa');
    const passwordMahasiswa = document.getElementById('password_mahasiswa');
    const tanggalLahirMahasiswa = document.getElementById('tanggal_lahir_mahasiswa');
    const jenisKelaminMahasiswa = document.getElementById('jenis_kelamin_mahasiswa');
    const teleponMahasiswa = document.getElementById('telepon_mahasiswa');
    const profileImgMahasiswa = document.getElementById('profile_img_mahasiswa');

    // Error message elements 
    const namaError = document.getElementById('nama_error');
    const nimError = document.getElementById('nim_error');
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
    if (modalTambahMahasiswa) {
        modalTambahMahasiswa.addEventListener('hidden.bs.modal', resetModal);
    }

    // Button event listeners
    if (btnTambahkanKeDaftarMahasiswa) {
        btnTambahkanKeDaftarMahasiswa.addEventListener('click', handleAddToDaftarClick);
    }

    // Form submission
    if (formTambahMahasiswa) {
        formTambahMahasiswa.addEventListener('submit', handleFormSubmit);
    }

    // Password toggle setup
    function setupPasswordToggle() {
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword && passwordMahasiswa) {
            togglePassword.addEventListener('click', function() {
                const type = passwordMahasiswa.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordMahasiswa.setAttribute('type', type);
                
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
        if (namaMahasiswa) {
            namaMahasiswa.addEventListener('blur', function() {
                validateNama(namaMahasiswa);
            });
        }

        if (nimMahasiswa) {
            nimMahasiswa.addEventListener('blur', function() {
                validateNimFormat(nimMahasiswa);
            });
        }
        
        if (emailMahasiswa) {
            emailMahasiswa.addEventListener('blur', function() {
                validateEmailFormat(emailMahasiswa);
            });
        }
    }

    // Add mahasiswa to the list
    function addMahasiswaToList() {
        // Generate a unique index for this mahasiswa
        const index = mahasiswaList.length;
        
        // Create the mahasiswa object without the image first
        const mahasiswa = {
            nama_mahasiswa: namaMahasiswa.value.trim(),
            nim_mahasiswa: nimMahasiswa.value.trim(),
            status_akun_mahasiswa: statusMahasiswa.value,
            email_mahasiswa: emailMahasiswa.value.trim(),
            password_mahasiswa: passwordMahasiswa.value || nimMahasiswa.value.trim(), // Use NIM as default password
            tanggal_lahir_mahasiswa: tanggalLahirMahasiswa.value,
            jenis_kelamin_mahasiswa: jenisKelaminMahasiswa.value || null,
            telepon_mahasiswa: teleponMahasiswa.value.trim(),
            has_profile_img: profileImgMahasiswa.files.length > 0,
            profile_img_name: profileImgMahasiswa.files.length > 0 ? profileImgMahasiswa.files[0].name : ''
        };
        
        // Add to the list
        mahasiswaList.push(mahasiswa);
        mahasiswaJsonData.value = JSON.stringify(mahasiswaList);
        
        // Handle the image file if exists
        if (profileImgMahasiswa.files.length > 0) {
            // Create a clone of the file input with a unique name for this mahasiswa
            const fileClone = document.createElement('input');
            fileClone.type = 'file';
            fileClone.name = `profile_img_mahasiswa_${index}`;
            fileClone.classList.add('d-none'); // Hide it
            fileClone.setAttribute('data-mahasiswa-index', index);
            
            // Create a DataTransfer object to set the files
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(profileImgMahasiswa.files[0]);
            fileClone.files = dataTransfer.files;
            
            // Add to the form
            formTambahMahasiswa.appendChild(fileClone);
        }
        
        // Update the table
        updateMahasiswaTable();
    }

    // Update the mahasiswa table display
    function updateMahasiswaTable() {
        if (emptyRow) {
            emptyRow.remove();
        }
        
        daftarMahasiswa.innerHTML = '';
        
        mahasiswaList.forEach((mahasiswa, index) => {
            const row = document.createElement('tr');
            
            // Display the photo info if available
            const photoInfo = mahasiswa.has_profile_img 
                ? `<i class="bi bi-image text-success"></i> ${mahasiswa.profile_img_name}`
                : 'No image';
                
            row.innerHTML = `
                <td>${mahasiswa.nama_mahasiswa}</td>
                <td>${mahasiswa.nim_mahasiswa}</td>
                <td>${mahasiswa.email_mahasiswa}</td>
                <td>${mahasiswa.status_akun_mahasiswa}</td>
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
                    const fileInput = formTambahMahasiswa.querySelector(`input[name="profile_img_mahasiswa_${idx}"]`);
                    if (fileInput) {
                        fileInput.remove();
                    }
                    
                    // Remove from the list
                    mahasiswaList.splice(idx, 1);
                    mahasiswaJsonData.value = JSON.stringify(mahasiswaList);
                    
                    // Reindex remaining file inputs
                    const fileInputs = formTambahMahasiswa.querySelectorAll('input[data-mahasiswa-index]');
                    fileInputs.forEach(input => {
                        const inputIndex = parseInt(input.getAttribute('data-mahasiswa-index'));
                        if (inputIndex > idx) {
                            input.name = `profile_img_mahasiswa_${inputIndex - 1}`;
                            input.setAttribute('data-mahasiswa-index', inputIndex - 1);
                        }
                    });
                    
                    updateMahasiswaTable();
                    
                    if (mahasiswaList.length === 0) {
                        daftarMahasiswa.innerHTML = `
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center">Belum ada mahasiswa yang ditambahkan ke daftar</td>
                            </tr>
                        `;
                    }
                });
            }
            
            daftarMahasiswa.appendChild(row);
        });
    }

    // Clear the form
    function clearForm() {
        if (namaMahasiswa) namaMahasiswa.value = '';
        if (nimMahasiswa) nimMahasiswa.value = '';
        if (statusMahasiswa) statusMahasiswa.value = 'Active';
        if (emailMahasiswa) emailMahasiswa.value = '';
        if (passwordMahasiswa) passwordMahasiswa.value = '';
        if (tanggalLahirMahasiswa) tanggalLahirMahasiswa.value = '';
        if (jenisKelaminMahasiswa) jenisKelaminMahasiswa.value = '';
        if (teleponMahasiswa) teleponMahasiswa.value = '';
        if (profileImgMahasiswa) profileImgMahasiswa.value = '';
        
        resetErrorMessages();
    }

    // Reset error messages
    function resetErrorMessages() {
        if (formError) {
            formError.classList.add('d-none');
            formError.textContent = '';
        }
        
        if (formTambahMahasiswa) {
            const invalidInputs = formTambahMahasiswa.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
        }
        
        // Clear individual error messages
        if (namaError) namaError.textContent = '';
        if (nimError) nimError.textContent = '';
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
        mahasiswaList = [];
        if (mahasiswaJsonData) mahasiswaJsonData.value = '[]';
        if (isSingle) isSingle.value = '1';
        
        // Remove all hidden file inputs
        const fileInputs = formTambahMahasiswa.querySelectorAll('input[data-mahasiswa-index]');
        fileInputs.forEach(input => input.remove());
        
        if (daftarMahasiswa) {
            daftarMahasiswa.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="6" class="text-center">Belum ada mahasiswa yang ditambahkan ke daftar</td>
                </tr>
            `;
        }
    }

    // Validate form for adding new mahasiswa
    async function validateFormAsync() {
        let isValid = true;
        resetErrorMessages();
    
        // Validate Name
        if (namaMahasiswa && !namaMahasiswa.value.trim()) {
            namaMahasiswa.classList.add('is-invalid');
            if (namaError) namaError.textContent = "Nama mahasiswa wajib diisi";
            isValid = false;
        }
    
        // Validate NIDN
        if (nimMahasiswa && !nimMahasiswa.value.trim()) {
            nimMahasiswa.classList.add('is-invalid');
            if (nimError) nimError.textContent = "NIM wajib diisi";
            isValid = false;
        } else if (nimMahasiswa) {
            const nimValue = nimMahasiswa.value.trim();
    
            // Validate NIM format
            if (!isValidNim(nimValue)) {
                nimMahasiswa.classList.add('is-invalid');
                if (nimError) nimError.textContent = "NIM harus berupa angka dan tepat 10 digit";
                isValid = false;
            } else {
                // Check for duplicates in the list
                const nimDuplicate = mahasiswaList.some(mahasiswa => mahasiswa.nim_mahasiswa === nimValue);
                if (nimDuplicate) {
                    nimMahasiswa.classList.add('is-invalid');
                    if (nimError) nimError.textContent = "NIM sudah ada di daftar yang akan ditambahkan";
                    isValid = false;
                } else {
                    // Check with database
                    nimMahasiswa.classList.add('is-loading');
                    try {
                        const nimExists = await checkNimExistsAsync(nimValue);
                        nimMahasiswa.classList.remove('is-loading');
                        if (nimExists) {
                            nimMahasiswa.classList.add('is-invalid');
                            if (nimError) nimError.textContent = "NIM sudah terdaftar di database";
                            isValid = false;
                        }
                    } catch (error) {
                        console.error('Error checking NIDN:', error);
                        nimMahasiswa.classList.remove('is-loading');
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
        if (emailMahasiswa && !emailMahasiswa.value.trim()) {
            emailMahasiswa.classList.add('is-invalid');
            if (emailError) emailError.textContent = "Email wajib diisi";
            isValid = false;
        } else if (emailMahasiswa && !isValidEmail(emailMahasiswa.value.trim())) {
            emailMahasiswa.classList.add('is-invalid');
            if (emailError) emailError.textContent = "Format email tidak valid";
            isValid = false;
        } else if (emailMahasiswa) {
            const emailValue = emailMahasiswa.value.trim();
            // Check for duplicates in the list
            const emailDuplicate = mahasiswaList.some(mahasiswa => mahasiswa.email_mahasiswa === emailValue);
            if (emailDuplicate) {
                emailMahasiswa.classList.add('is-invalid');
                if (emailError) emailError.textContent = "Email sudah ada di daftar yang akan ditambahkan";
                isValid = false;
            } else {
                // Check with database
                emailMahasiswa.classList.add('is-loading');
                try {
                    const emailExists = await checkEmailExistsAsync(emailValue);
                    emailMahasiswa.classList.remove('is-loading');
                    if (emailExists) {
                        emailMahasiswa.classList.add('is-invalid');
                        if (emailError) {
                            emailError.textContent = "Email sudah terdaftar di database";
                            emailError.style.display = 'block';
                        }
                        isValid = false;
                    }
                } catch (error) {
                    console.error('Error checking email:', error);
                    emailMahasiswa.classList.remove('is-loading');
                    if (formError) {
                        formError.textContent = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
                        formError.classList.remove('d-none');
                    }
                    isValid = false;
                }
            }
        }
    
        // Validate Profile Image
        if (profileImgMahasiswa && profileImgMahasiswa.files.length > 0) {
            const file = profileImgMahasiswa.files[0];
            const fileSize = file.size / 1024 / 1024;
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    
            if (!validExtensions.includes(file.type)) {
                profileImgMahasiswa.classList.add('is-invalid');
                if (profileImgError) profileImgError.textContent = "Format file tidak valid. Gunakan jpeg, png, jpg, atau gif";
                isValid = false;
            } else if (fileSize > 2) {
                profileImgMahasiswa.classList.add('is-invalid');
                if (profileImgError) profileImgError.textContent = "Ukuran file terlalu besar. Maksimal 2MB";
                isValid = false;
            }
        }
    
        return isValid;
    }

    // Handle "Add to List" button click
    async function handleAddToDaftarClick() {
        resetErrorMessages();
        
        btnTambahkanKeDaftarMahasiswa.disabled = true;
        btnTambahkanKeDaftarMahasiswa.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memeriksa...';
        
        try {
            const isValid = await validateFormAsync();
            
            btnTambahkanKeDaftarMahasiswa.disabled = false;
            btnTambahkanKeDaftarMahasiswa.innerHTML = 'Tambahkan ke Daftar';
            
            if (!isValid) {
                return;
            }
            
            addMahasiswaToList();
            clearForm();
            if (isSingle) isSingle.value = "0";
        } catch (error) {
            console.error('Validation error:', error);
            btnTambahkanKeDaftarMahasiswa.disabled = false;
            btnTambahkanKeDaftarMahasiswa.innerHTML = 'Tambahkan ke Daftar';
            
            if (formError) {
                formError.textContent = "Terjadi kesalahan saat validasi. Silakan coba lagi.";
                formError.classList.remove('d-none');
            }
        }
    }

    // Handle form submission
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        // If there are mahasiswa in the list, submit the form normally
        if (mahasiswaList.length > 0 || isSingle.value === "1") {
            // Show loading state
            const submitBtn = formTambahMahasiswa.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            }
            
            // Submit the form
            formTambahMahasiswa.submit();
        } else {
            // Show error
            if (formError) {
                formError.textContent = "Belum ada mahasiswa yang ditambahkan ke daftar.";
                formError.classList.remove('d-none');
            }
        }
    }

    // Fungsi untuk menangani error validasi dari server
    function handleValidationErrors(errors) {
        // Tampilkan error untuk setiap field
        for (const field in errors) {
            const input = document.getElementById(`${field}_${mahasiswaId}`);
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