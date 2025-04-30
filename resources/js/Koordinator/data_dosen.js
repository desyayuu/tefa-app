document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000); 
    });
    
    tambahDosen();
    initEditDosenValidation();
});

function tambahDosen() {
    let dosenList = []; 

    // DOM Elements
    const btnTambahkanKeDaftar = document.getElementById('btnTambahkanKeDaftar'); 
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

    // Error message 
    const namaError = document.getElementById('nama_error');
    const nidnError = document.getElementById('nidn_error');
    const statusError = document.getElementById('status_akun_error');
    const emailError = document.getElementById('email_error');
    const passwordError = document.getElementById('password_error');
    const tanggalLahirError = document.getElementById('tanggal_lahir_error');
    const jenisKelaminError = document.getElementById('jenis_kelamin_error');
    const teleponError = document.getElementById('telepon_error');
    const profileImgError = document.getElementById('profile_img_error');

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }


    function addDosenToList() {
        // Create lecturer object
        const dosen = {
            nama_dosen: namaDosen.value.trim(),
            nidn_dosen: nidnDosen.value.trim(),
            status_akun_dosen: statusDosen.value,
            email_dosen: emailDosen.value.trim(),
            password_dosen: passwordDosen.value || 'password123', // Set default password if empty
            tanggal_lahir_dosen: tanggalLahirDosen.value,
            jenis_kelamin_dosen: jenisKelaminDosen.value || null,
            telepon_dosen: teleponDosen.value.trim()
        };
        
        // Add to list
        dosenList.push(dosen);
        
        // Update hidden input with JSON data
        dosenJsonData.value = JSON.stringify(dosenList);
        
        // Update the table display
        updateDosenTable();
    }

    function updateDosenTable() {
        // Remove empty row if exists
        if (emptyRow) {
            emptyRow.remove();
        }
        
        // Clear current table content and rebuild
        daftarDosen.innerHTML = '';
        
        dosenList.forEach((dosen, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${dosen.nama_dosen}</td>
                <td>${dosen.nidn_dosen}</td>
                <td>${dosen.email_dosen}</td>
                <td>${dosen.status_akun_dosen}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" data-index="${index}">Hapus</button>
                </td>
            `;
            
            // Add event listener to delete button
            const deleteBtn = row.querySelector('button[data-index]');
            deleteBtn.addEventListener('click', function() {
                const idx = this.getAttribute('data-index');
                dosenList.splice(idx, 1);
                dosenJsonData.value = JSON.stringify(dosenList);
                updateDosenTable();
                
                // Show empty row if no lecturers
                if (dosenList.length === 0) {
                    daftarDosen.innerHTML = `
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center">Belum ada dosen yang ditambahkan ke daftar</td>
                        </tr>
                    `;
                }
            });
            
            daftarDosen.appendChild(row);
        });
    }

    function clearForm() {
        namaDosen.value = '';
        nidnDosen.value = '';
        statusDosen.value = 'Active';
        emailDosen.value = '';
        passwordDosen.value = '';
        tanggalLahirDosen.value = '';
        jenisKelaminDosen.value = '';
        teleponDosen.value = '';
        profileImgDosen.value = '';
        
        resetErrorMessages();
    }

    function resetErrorMessages() {
        // Reset form error
        formError.classList.add('d-none');
        formError.textContent = '';
        
        // Reset field errors
        const invalidInputs = formTambahDosen.querySelectorAll('.is-invalid');
        invalidInputs.forEach(input => {
            input.classList.remove('is-invalid');
        });
        
        // Clear error messages
        namaError.textContent = '';
        nidnError.textContent = '';
        statusError.textContent = '';
        emailError.textContent = '';
        passwordError.textContent = '';
        tanggalLahirError.textContent = '';
        jenisKelaminError.textContent = '';
        teleponError.textContent = '';
        profileImgError.textContent = '';
    }

    if (modalTambahDosen) {
        modalTambahDosen.addEventListener('hidden.bs.modal', function() {
            clearForm();
            resetErrorMessages();
            dosenList = [];
            dosenJsonData.value = '[]';
            isSingle.value = '1';
            
            // Reset the table
            daftarDosen.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="5" class="text-center">Belum ada dosen yang ditambahkan ke daftar</td>
                </tr>
            `;
        });
    }

    async function validateFormAsync() {
        let isValid = true;
        resetErrorMessages();
        
        if (!namaDosen.value.trim()) {
            namaDosen.classList.add('is-invalid');
            namaError.textContent = "Nama dosen wajib diisi";
            isValid = false;
        }
        
        if (!nidnDosen.value.trim()) {
            nidnDosen.classList.add('is-invalid');
            nidnError.textContent = "NIDN/NIP wajib diisi";
            isValid = false;
        } else {
            const nidnDuplicate = dosenList.some(dosen => dosen.nidn_dosen === nidnDosen.value.trim());
            if (nidnDuplicate) {
                nidnDosen.classList.add('is-invalid');
                nidnError.textContent = "NIDN/NIP sudah ada di daftar yang akan ditambahkan";
                isValid = false;
            } else {
                const nidnValue = nidnDosen.value.trim();
                
                nidnDosen.classList.add('is-loading');
                
                try {
                    const nidnExists = await checkNidnExistsAsync(nidnValue);
                    nidnDosen.classList.remove('is-loading');
                    
                    if (nidnExists) {
                        nidnDosen.classList.add('is-invalid');
                        nidnError.textContent = "NIDN/NIP sudah terdaftar di database";
                        isValid = false;
                    }
                } catch (error) {
                    console.error('Error checking NIDN:', error);
                    nidnDosen.classList.remove('is-loading');
                    formError.textContent = "Terjadi kesalahan saat memeriksa NIDN. Silakan coba lagi.";
                    formError.classList.remove('d-none');
                    isValid = false;
                }
            }
        }
        
        if (!emailDosen.value.trim()) {
            emailDosen.classList.add('is-invalid');
            emailError.textContent = "Email wajib diisi";
            isValid = false;
        } else if (!isValidEmail(emailDosen.value.trim())) {
            emailDosen.classList.add('is-invalid');
            emailError.textContent = "Format email tidak valid";
            isValid = false;
        } else {
            const emailDuplicate = dosenList.some(dosen => dosen.email_dosen === emailDosen.value.trim());
            if (emailDuplicate) {
                emailDosen.classList.add('is-invalid');
                emailError.textContent = "Email sudah ada di daftar yang akan ditambahkan";
                isValid = false;
            } else {
                const emailValue = emailDosen.value.trim();
                
                emailDosen.classList.add('is-loading');
                
                try {
                    const emailExists = await checkEmailExistsAsync(emailValue);
                    emailDosen.classList.remove('is-loading');
                    
                    if (emailExists) {
                        emailDosen.classList.add('is-invalid');
                        emailError.textContent = "Email sudah terdaftar di database";
                        isValid = false;
                    }
                } catch (error) {
                    console.error('Error checking email:', error);
                    emailDosen.classList.remove('is-loading');
                    formError.textContent = "Terjadi kesalahan saat memeriksa email. Silakan coba lagi.";
                    formError.classList.remove('d-none');
                    isValid = false;
                }
            }
        }
        

        if (profileImgDosen.files.length > 0) {
            const file = profileImgDosen.files[0];
            const fileSize = file.size / 1024 / 1024;
            const validExtensions = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            
            if (!validExtensions.includes(file.type)) {
                profileImgDosen.classList.add('is-invalid');
                profileImgError.textContent = "Format file tidak valid. Gunakan jpeg, png, jpg, atau gif";
                isValid = false;
            } else if (fileSize > 2) {
                profileImgDosen.classList.add('is-invalid');
                profileImgError.textContent = "Ukuran file terlalu besar. Maksimal 2MB";
                isValid = false;
            }
        }
        return isValid;
    }

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

    if (btnTambahkanKeDaftar) {
        btnTambahkanKeDaftar.addEventListener('click', async function() {
            resetErrorMessages();
            
            btnTambahkanKeDaftar.disabled = true;
            btnTambahkanKeDaftar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memeriksa...';
            
            try {
                const isValid = await validateFormAsync();
                
                btnTambahkanKeDaftar.disabled = false;
                btnTambahkanKeDaftar.innerHTML = 'Tambahkan ke Daftar';
                
                if (!isValid) {
                    return;
                }
                
                addDosenToList();
                clearForm();
                isSingle.value = "0";
            } catch (error) {
                console.error('Validation error:', error);
                btnTambahkanKeDaftar.disabled = false;
                btnTambahkanKeDaftar.innerHTML = 'Tambahkan ke Daftar';
                
                formError.textContent = "Terjadi kesalahan saat validasi. Silakan coba lagi.";
                formError.classList.remove('d-none');
            }
        });
    }

    if (formTambahDosen) {
        formTambahDosen.addEventListener('submit', async function(e) {
            e.preventDefault(); 
            
            if (isSingle.value === "0" && dosenList.length === 0) {
                formError.textContent = "Silakan tambahkan minimal satu dosen ke daftar";
                formError.classList.remove('d-none');
                return;
            }
            
            if (isSingle.value === "1") {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                
                try {
                    const isValid = await validateFormAsync();
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = 'Simpan Data';
                    
                    if (isValid) {
                        formTambahDosen.submit();
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = 'Simpan Data';
                    
                    formError.textContent = "Terjadi kesalahan saat validasi. Silakan coba lagi.";
                    formError.classList.remove('d-none');
                }
            } else {
                formTambahDosen.submit();
            }
        });
    }
}

function initEditDosenValidation() {
    // Find all edit modals
    const editModals = document.querySelectorAll('[id^="modalDosen"]');
    
    editModals.forEach(modal => {
        const modalId = modal.id;
        const dosenId = modalId.replace('modalDosen', '');
        
        const form = modal.querySelector('form');
        const nidnInput = document.getElementById(`nidn_dosen_${dosenId}`);
        const emailInput = document.getElementById(`email_dosen_${dosenId}`);
        
        // Simpan nilai original sebagai atribut data- pada elemen
        if (nidnInput) {
            const originalNidn = nidnInput.value;
            nidnInput.setAttribute('data-original-value', originalNidn);
        }
        
        if (emailInput) {
            const originalEmail = emailInput.getAttribute('data-original') || emailInput.value;
            emailInput.setAttribute('data-original-value', originalEmail);
        }
        
        // Reset saat modal dibuka
        modal.addEventListener('show.bs.modal', function() {
            if (nidnInput) {
                const originalNidn = nidnInput.getAttribute('data-original-value');
                nidnInput.value = originalNidn;
                nidnInput.classList.remove('is-invalid');
                const nidnErrorElement = nidnInput.nextElementSibling;
                if (nidnErrorElement && nidnErrorElement.classList.contains('invalid-feedback')) {
                    nidnErrorElement.textContent = '';
                }
            }
            
            if (emailInput) {
                const originalEmail = emailInput.getAttribute('data-original-value');
                emailInput.value = originalEmail;
                emailInput.classList.remove('is-invalid');
                const emailErrorElement = emailInput.nextElementSibling;
                if (emailErrorElement && emailErrorElement.classList.contains('invalid-feedback')) {
                    emailErrorElement.textContent = '';
                }
            }
        });
        
        // Validation for NIDN change
        if (nidnInput) {
            nidnInput.addEventListener('change', function() {
                const originalNidn = nidnInput.getAttribute('data-original-value');
                validateNidn(nidnInput, dosenId, originalNidn);
            });
        }
        
        // Validation for Email change
        if (emailInput) {
            emailInput.addEventListener('change', function() {
                const originalEmail = emailInput.getAttribute('data-original-value');
                validateEmail(emailInput, dosenId, originalEmail);
            });
        }
        
        // Form submission handling
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
                
                // Validate NIDN and Email if they've changed
                let isValid = true;
                
                if (nidnInput) {
                    const originalNidn = nidnInput.getAttribute('data-original-value');
                    if (nidnInput.value !== originalNidn) {
                        const nidnValid = await validateNidn(nidnInput, dosenId, originalNidn);
                        isValid = isValid && nidnValid;
                    }
                }
                
                if (emailInput) {
                    const originalEmail = emailInput.getAttribute('data-original-value');
                    if (emailInput.value !== originalEmail) {
                        const emailValid = await validateEmail(emailInput, dosenId, originalEmail);
                        isValid = isValid && emailValid;
                    }
                }
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan Perubahan';
                
                if (isValid) {
                    form.submit();
                }
            });
        }
    });
}


async function validateNidn(nidnInput, dosenId, originalNidn) {
    // Skip validation if the value is unchanged
    if (nidnInput.value.trim() === originalNidn) {
        nidnInput.classList.remove('is-invalid');
        const errorElement = nidnInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
    
    try {
        const formData = new FormData();
        formData.append('nidn_dosen', nidnInput.value.trim());
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        const response = await fetch('/koordinator/check-email-nidn-exists', {
            method: 'POST',
            body: formData
        });
        
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
            // Reset nilai input ke nilai aslinya
            nidnInput.value = originalNidn;
            
            // Simpan nilai asli ke data-original-value untuk referensi
            nidnInput.setAttribute('data-original-value', originalNidn);
            
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
        // Reset nilai input ke nilai aslinya jika terjadi error
        nidnInput.value = originalNidn;
        return false;
    }
}

async function validateEmail(emailInput, dosenId, originalEmail) {
    if (emailInput.value.trim() === originalEmail) {
        emailInput.classList.remove('is-invalid');
        const errorElement = emailInput.nextElementSibling;
        if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            errorElement.textContent = '';
        }
        return true;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value.trim())) {
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
        // Reset nilai input ke nilai aslinya
        emailInput.value = originalEmail;
        
        // Simpan nilai asli ke data-original-value untuk referensi
        emailInput.setAttribute('data-original-value', originalEmail);
        
        return false;
    }
    
    try {
        const formData = new FormData();
        formData.append('email_dosen', emailInput.value.trim());
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        const response = await fetch('/koordinator/check-email-nidn-exists', {
            method: 'POST',
            body: formData
        });
        
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
            emailInput.value = originalEmail;
            emailInput.setAttribute('data-original-value', originalEmail);
            
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
        // Reset nilai input ke nilai aslinya jika terjadi error
        emailInput.value = originalEmail;
        return false;
    }
}