document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const formTambahDosen = document.getElementById('formTambahDosen');
    const btnTambahkanKeDaftar = document.getElementById('btnTambahkanKeDaftar');
    const daftarDosen = document.getElementById('daftarDosen');
    const dosenJsonData = document.getElementById('dosenJsonData');
    const isSingle = document.getElementById('isSingle');
    const btnSimpan = document.getElementById('btnSimpan');
    const emptyRow = document.getElementById('emptyRow');
    
    // Form fields
    const fields = [
        'nama_dosen', 'nidn_dosen', 'email_dosen', 'password', 
        'status', 'tanggal_lahir_dosen', 'jenis_kelamin_dosen', 
        'telepon_dosen', 'profile_img_dosen'
    ];
    
    // Store dosen data
    let dosenList = [];
    let dosenFiles = []; // Store files separately
    
    // Add dosen to list
    if (btnTambahkanKeDaftar) {
        btnTambahkanKeDaftar.addEventListener('click', function() {
            // Reset validation
            resetValidation();
            
            // Get input values
            const dosen = {
                nama_dosen: document.getElementById('nama_dosen').value.trim(),
                nidn_dosen: document.getElementById('nidn_dosen').value.trim(),
                email_dosen: document.getElementById('email_dosen').value.trim(),
                password: document.getElementById('password').value,
                status: document.getElementById('status').value,
                tanggal_lahir_dosen: document.getElementById('tanggal_lahir_dosen').value,
                jenis_kelamin_dosen: document.getElementById('jenis_kelamin_dosen').value,
                telepon_dosen: document.getElementById('telepon_dosen').value.trim(),
                has_profile_img: false // Initialize to false
            };
            
            // Basic validation
            let isValid = true;
            
            if (!dosen.nama_dosen) {
                showError('nama_dosen', 'Nama dosen harus diisi');
                isValid = false;
            }
            
            if (!dosen.nidn_dosen) {
                showError('nidn_dosen', 'NIDN/NIP harus diisi');
                isValid = false;
            }
            
            if (!dosen.email_dosen) {
                showError('email_dosen', 'Email harus diisi');
                isValid = false;
            } else if (!isValidEmail(dosen.email_dosen)) {
                showError('email_dosen', 'Format email tidak valid');
                isValid = false;
            }
            
            if (!isValid) return;
            
            // Check if NIDN or email already exists in the list
            const nidnExists = dosenList.some(item => item.nidn_dosen === dosen.nidn_dosen);
            const emailExists = dosenList.some(item => item.email_dosen === dosen.email_dosen);
            
            if (nidnExists) {
                showError('nidn_dosen', 'NIDN/NIP sudah ada dalam daftar yang akan ditambahkan');
                return;
            }
            
            if (emailExists) {
                showError('email_dosen', 'Email sudah ada dalam daftar yang akan ditambahkan');
                return;
            }
            
            // Important: Check if NIDN or email exists in database
            checkExistingDataPromise(dosen.nidn_dosen, dosen.email_dosen)
                .then(function(exists) {
                    if (exists.nidnExists) {
                        showError('nidn_dosen', 'NIDN/NIP sudah terdaftar dalam sistem');
                        return;
                    }
                    
                    if (exists.emailExists) {
                        showError('email_dosen', 'Email sudah terdaftar dalam sistem');
                        return;
                    }
                    
                    // Handle file separately - store the actual file in a separate array
                    // Update the file capturing code in the btnTambahkanKeDaftar click handler
                    const fileInput = document.getElementById('profile_img_dosen');
                    if (fileInput && fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        
                        // Validate file if needed
                        
                        // Store a clone of the file to make sure it doesn't get cleared
                        dosenFiles.push(file);
                        dosen.has_profile_img = true;
                        
                        console.log('File captured for list:', file.name, file.size, file.type);
                    } else {
                        dosenFiles.push(null);
                        dosen.has_profile_img = false;
                        console.log('No file captured for this entry');
                    }
                    
                    // Add to dosen list
                    dosenList.push(dosen);
                    
                    // Update JSON data
                    dosenJsonData.value = JSON.stringify(dosenList);
                    
                    // Update table
                    updateDosenTable();
                    
                    // Clear form
                    clearForm();
                    
                    // Set to multiple mode
                    isSingle.value = '0';
                })
                .catch(function(error) {
                    console.error('Error checking data:', error);
                    // Display error to user
                    document.getElementById('form_error').innerText = 'Terjadi kesalahan saat memeriksa data: ' + error.message;
                    document.getElementById('form_error').classList.remove('d-none');
                });
        });
    }
    
    // Convert checkExistingData to Promise-based for better handling
    function checkExistingDataPromise(nidn, email) {
        return new Promise(function(resolve, reject) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/koordinator/check-email-nidn-exists', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            console.log('Sending data:', { nidn_dosen: nidn, email_dosen: email });
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Response from server:', response);
                        resolve(response);
                    } catch (e) {
                        reject(new Error('Invalid response format'));
                    }
                } else {
                    console.error('Request failed. Status:', xhr.status, 'Response:', xhr.responseText);
                    reject(new Error('Server error: ' + xhr.status));
                }
            };
            
            xhr.onerror = function() {
                console.error('Request failed with network error');
                reject(new Error('Network error'));
            };
            
            xhr.send(JSON.stringify({ nidn_dosen: nidn, email_dosen: email }));
        });
    }
    
    // Submit form
    if (formTambahDosen) {
        formTambahDosen.addEventListener('submit', function(e) {
            // Reset validation errors first
            resetValidation();
            
            // If in multiple mode and table is empty, show error
            if (isSingle.value === '0' && dosenList.length === 0) {
                e.preventDefault();
                document.getElementById('form_error').innerText = 'Belum ada data dosen yang ditambahkan ke daftar';
                document.getElementById('form_error').classList.remove('d-none');
                return;
            }
            
            // If in multiple mode with files, we need to handle it specially
            if (isSingle.value === '0' && dosenFiles.some(file => file !== null)) {
                e.preventDefault();
                
                console.log('Submitting in multiple mode with files');
                console.log('Files in dosenFiles:', dosenFiles.length);
                dosenFiles.forEach((file, index) => {
                    if (file) {
                        console.log(`File ${index}: ${file.name}, ${file.size} bytes`);
                    } else {
                        console.log(`File ${index}: none`);
                    }
                });
                
                submitFormWithFiles();
                return;
            }
            
            // If in single mode, validate form
            if (isSingle.value === '1') {
                let isValid = true;
                
                if (!document.getElementById('nama_dosen').value.trim()) {
                    showError('nama_dosen', 'Nama dosen harus diisi');
                    isValid = false;
                }
                
                if (!document.getElementById('nidn_dosen').value.trim()) {
                    showError('nidn_dosen', 'NIDN/NIP harus diisi');
                    isValid = false;
                }
                
                if (!document.getElementById('email_dosen').value.trim()) {
                    showError('email_dosen', 'Email harus diisi');
                    isValid = false;
                } else if (!isValidEmail(document.getElementById('email_dosen').value.trim())) {
                    showError('email_dosen', 'Format email tidak valid');
                    isValid = false;
                }
                
                // Validate profile image if one was selected
                const fileInput = document.getElementById('profile_img_dosen');
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                    
                    if (!allowedTypes.includes(file.type)) {
                        showError('profile_img_dosen', 'File harus berupa gambar (jpeg, png, jpg, gif).');
                        isValid = false;
                    }
                    
                    // Check file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        showError('profile_img_dosen', 'Ukuran file terlalu besar. Maksimal 2MB.');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return;
                }
                
                // Prevent form submission and handle AJAX check first
                e.preventDefault();
                
                // Check if NIDN or email exists in database
                checkExistingDataPromise(
                    document.getElementById('nidn_dosen').value.trim(),
                    document.getElementById('email_dosen').value.trim()
                ).then(function(exists) {
                    if (exists.nidnExists) {
                        showError('nidn_dosen', 'NIDN/NIP sudah terdaftar di database');
                    } else if (exists.emailExists) {
                        showError('email_dosen', 'Email sudah terdaftar di database');
                    } else {
                        // Form is valid and data doesn't exist, submit the form
                        console.log("Form is valid, submitting...");
                        formTambahDosen.submit();
                    }
                }).catch(function(error) {
                    console.error('Error checking data:', error);
                    // Display error to user
                    document.getElementById('form_error').innerText = 'Terjadi kesalahan saat memeriksa data: ' + error.message;
                    document.getElementById('form_error').classList.remove('d-none');
                });
            }
        });
    }
    
    // Function to submit form with files
    function submitFormWithFiles() {
        // Create FormData for the actual submission
        const formData = new FormData();
        
        // Add all original form fields
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', csrfToken);
        formData.append('is_single', isSingle.value);
        formData.append('dosen_data', dosenJsonData.value);
        
        // Add files directly to the FormData with indexed names
        for (let i = 0; i < dosenFiles.length; i++) {
            if (dosenFiles[i]) {
                console.log('Attaching file for index', i, dosenFiles[i].name, dosenFiles[i].size);
                formData.append(`profile_img_dosen_${i}`, dosenFiles[i]);
            }
        }
        
        // Log the FormData contents for debugging
        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? 
                pair[1].name + ' (' + pair[1].size + ' bytes)' : pair[1]));
        }
        
        // Submit using fetch API
        fetch(formTambahDosen.action, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (response.ok) {
                window.location.href = '/koordinator/data-dosen';
            } else {
                return response.text().then(text => {
                    throw new Error('Server error: ' + text);
                });
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            document.getElementById('form_error').innerText = 'Error: ' + error.message;
            document.getElementById('form_error').classList.remove('d-none');
        });
    }
    
    // Update dosen table
    function updateDosenTable() {
        // Clear table
        daftarDosen.innerHTML = '';
        
        if (dosenList.length === 0) {
            daftarDosen.appendChild(emptyRow.cloneNode(true));
            return;
        }
        
        // Add rows
        dosenList.forEach(function(dosen, index) {
            const row = document.createElement('tr');
            
            // Create cells
            const nameCell = document.createElement('td');
            nameCell.textContent = dosen.nama_dosen;
            
            const nidnCell = document.createElement('td');
            nidnCell.textContent = dosen.nidn_dosen;
            
            const emailCell = document.createElement('td');
            emailCell.textContent = dosen.email_dosen;
            
            const statusCell = document.createElement('td');
            statusCell.textContent = dosen.status;
            
            const actionCell = document.createElement('td');
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-sm btn-danger';
            deleteBtn.innerHTML = '<i class="fa fa-trash"></i> Hapus';
            deleteBtn.dataset.index = index;
            deleteBtn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                dosenList.splice(idx, 1);
                dosenFiles.splice(idx, 1);
                dosenJsonData.value = JSON.stringify(dosenList);
                updateDosenTable();
            });
            
            actionCell.appendChild(deleteBtn);
            
            // Add cells to row
            row.appendChild(nameCell);
            row.appendChild(nidnCell);
            row.appendChild(emailCell);
            row.appendChild(statusCell);
            row.appendChild(actionCell);
            
            // Add row to table
            daftarDosen.appendChild(row);
        });
    }
    
    // Legacy checkExistingData for backward compatibility 
    function checkExistingData(nidn, email, callback, isSync = false) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/koordinator/check-email-nidn-exists', !isSync);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        console.log('Sending data:', { nidn_dosen: nidn, email_dosen: email });
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                console.log('Response from server:', response);
                callback(response);
            } else {
                console.error('Request failed. Status:', xhr.status, 'Response:', xhr.responseText);
                callback({ nidnExists: false, emailExists: false });
            }
        };
        
        xhr.onerror = function() {
            console.error('Request failed with network error');
            callback({ nidnExists: false, emailExists: false });
        };
        
        xhr.send(JSON.stringify({ nidn_dosen: nidn, email_dosen: email }));
        
        if (isSync) {
            return JSON.parse(xhr.responseText);
        }
    }
    
    // Clear form
    function clearForm() {
        fields.forEach(function(field) {
            const element = document.getElementById(field);
            if (element) {
                if (element.tagName === 'SELECT') {
                    if (field === 'status') {
                        element.value = 'Active'; // Default value for status
                    } else {
                        element.selectedIndex = 0;
                    }
                } else {
                    element.value = '';
                }
            }
        });
        
        // Clear file input
        const fileInput = document.getElementById('profile_img_dosen');
        if (fileInput) {
            fileInput.value = '';
        }
    }
    
    // Show validation error - Fix for different error element IDs
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        // Check both possible error ID formats (with _error and without)
        let errorDiv = document.getElementById(fieldId + '_error');
        
        // Handle special case for nidn_dosen field
        if (fieldId === 'nidn_dosen' && !errorDiv) {
            errorDiv = document.getElementById('nidn_error');
        }
        
        // Handle special case for profile_img_dosen field
        if (fieldId === 'profile_img_dosen' && !errorDiv) {
            errorDiv = document.getElementById('profile_img_dosen_error');
        }
        
        if (field) {
            field.classList.add('is-invalid');
        }
        
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        } else {
            console.error('Error div not found for field:', fieldId);
        }
    }
    
    // Reset validation
    function resetValidation() {
        // Reset all fields
        fields.forEach(function(field) {
            const element = document.getElementById(field);
            if (element) {
                element.classList.remove('is-invalid');
            }
            
            // Try both error ID formats
            const errorDiv = document.getElementById(field + '_error') || 
                             document.getElementById(field.replace('_dosen', '_error'));
            
            if (errorDiv) {
                errorDiv.textContent = '';
            }
        });
        
        // Special case for nidn field
        const nidnError = document.getElementById('nidn_error');
        if (nidnError) {
            nidnError.textContent = '';
        }
        
        // Special case for profile image field
        const profileImgError = document.getElementById('profile_img_dosen_error');
        if (profileImgError) {
            profileImgError.textContent = '';
        }
        
        document.getElementById('form_error').classList.add('d-none');
    }
    
    // Validate email format
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Add event listener for file input to validate on change
    const fileInput = document.getElementById('profile_img_dosen');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            resetValidation();
            
            if (this.files.length > 0) {
                const file = this.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                
                console.log('File selected:', file.name, 'Type:', file.type, 'Size:', file.size);
                
                if (!allowedTypes.includes(file.type)) {
                    showError('profile_img_dosen', 'File harus berupa gambar (jpeg, png, jpg, gif)');
                    this.value = ''; // Clear the file input
                }
                
                // Check file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showError('profile_img_dosen', 'Ukuran file terlalu besar. Maksimal 2MB');
                    this.value = ''; // Clear the file input
                }
            }
        });
    }
});