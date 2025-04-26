document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000); 
    });
    
    initTambahMitraFunctionality();
});

function initTambahMitraFunctionality() {
    // Multiple mitra data handling
    let mitraList = [];
    const btnTambahkanKeDaftar = document.getElementById('btnTambahkanKeDaftar');
    const btnSimpan = document.getElementById('btnSimpan');
    const daftarMitra = document.getElementById('daftarMitra');
    const mitraJsonData = document.getElementById('mitraJsonData');
    const isSingle = document.getElementById('isSingle');
    const emptyRow = document.getElementById('emptyRow');
    const form = document.getElementById('formTambahData');
    const modalTambahData = document.getElementById('modalTambahData');
    
    // Jika elemen tidak ditemukan, berarti kita tidak di halaman yang relevan
    if (!btnTambahkanKeDaftar || !daftarMitra) {
        return;
    }
    
    // Form inputs
    const namaMitra = document.getElementById('nama_mitra');
    const emailMitra = document.getElementById('email_mitra');
    const teleponMitra = document.getElementById('telepon_mitra');
    const alamatMitra = document.getElementById('alamat_mitra');
    
    // Error containers
    const namaError = document.getElementById('nama_mitra_error');
    const emailError = document.getElementById('email_mitra_error');
    const teleponError = document.getElementById('telepon_mitra_error');
    const alamatError = document.getElementById('alamat_mitra_error');
    const formError = document.getElementById('form_error');
    
    // Reset modal saat dibuka
    if (modalTambahData) {
        modalTambahData.addEventListener('show.bs.modal', function() {
            resetForm();
            clearValidationErrors();
            mitraList = [];
            updateMitraTable();
            updateJsonData();
            if (isSingle) isSingle.value = "1"; // Default sebagai single
        });
    }
    
    // Tambahkan mitra ke daftar
    btnTambahkanKeDaftar.addEventListener('click', async function() {
        // Clear previous errors
        clearValidationErrors();
        
        const nama = namaMitra.value.trim();
        const email = emailMitra.value.trim();
        const telepon = teleponMitra.value.trim();
        const alamat = alamatMitra.value.trim();
        
        // Validasi data
        let isValid = true;

        //Validari nama
        if (!nama) {
            showError(namaError, 'Nama mitra harus diisi');
            isValid = false;
        }
        
        // Validasi email
        if (!email) {
            showError(emailError, 'Email harus diisi');
            isValid = false;
        } else {
            // Validasi format email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError(emailError, 'Format email tidak valid');
                isValid = false;
            } else {
                // cek apakah email sudah ada di daftar data mitra yang akan ditambahkan
                const emailExistsInList = mitraList.some(mitra => mitra.email_mitra === email);
                if (emailExistsInList) {
                    showError(emailError, 'Email sudah ada di daftar mitra');
                    isValid = false;
                }else{
                    // Cek apakah email sudah ada di database
                    const emailExists = await checkEmailExistsAsync(email);
                    if (emailExists) {
                        showError(emailError, 'Email sudah terdaftar');
                        isValid = false;
                    }
                }
            }
        }
        
        
        if (telepon && !/^\d+$/.test(telepon)) {
            showError(teleponError, 'Telepon hanya boleh berisi angka');
            isValid = false;
        }
        
        if (alamat) {
            isValid = true;
        }
        
        
        if (!isValid) {
            return;
        }

        mitraList.push({
            nama_mitra: nama,
            email_mitra: email,
            telepon_mitra: telepon,
            alamat_mitra: alamat,
            id: Date.now() 
        });
        

        // Update table & json data
        updateMitraTable();
        updateJsonData();
        
        // Reset form untuk entri berikutnya
        resetForm();
        clearValidationErrors();
        
        // Focus ke input nama
        namaMitra.focus();
        
        // Tandai sebagai multiple
        isSingle.value = "0";
    });
    
    // Submit form handler
    if (form) {
        form.addEventListener('submit', function(e) {
            // Clear previous errors
            clearValidationErrors();
            
            // Jika mode single (tidak ada data di daftar) dan form terisi
            if (isSingle.value === "1") {
                const nama = namaMitra.value.trim();
                const email = emailMitra.value.trim();
                const telepon = teleponMitra.value.trim();
                const alamat = alamatMitra.value.trim();
                
                // Validasi data single
                let isValid = true;
                
                if (!nama) {
                    showError(namaError, 'Nama mitra harus diisi');
                    isValid = false;
                }
                
                if (!email) {
                    showError(emailError, 'Email harus diisi');
                    isValid = false;
                } else {
                    // Validasi email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        showError(emailError, 'Format email tidak valid');
                        isValid = false;
                    }
                }
                
                if (!telepon) {
                    showError(teleponError, 'Telepon harus diisi');
                    isValid = false;
                }
                
                if (!alamat) {
                    showError(alamatError, 'Alamat harus diisi');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
                
                // Konversi data form ke JSON untuk single insert
                const singleData = [{
                    nama_mitra: nama,
                    email_mitra: email,
                    telepon_mitra: telepon,
                    alamat_mitra: alamat
                }];
                
                mitraJsonData.value = JSON.stringify(singleData);
            } else {
                // Validasi untuk multiple (pastikan ada data)
                if (mitraList.length === 0) {
                    e.preventDefault();
                    showError(formError, 'Belum ada data mitra yang ditambahkan ke daftar');
                    return false;
                }
            }
            
            return true;
        });
    }
    
    // Fungsi update tabel mitra
    function updateMitraTable() {
        // Clear existing rows (except empty row)
        const existingRows = daftarMitra.querySelectorAll('tr:not(#emptyRow)');
        existingRows.forEach(row => row.remove());
        
        if (mitraList.length === 0) {
            // Pastikan baris kosong ditampilkan
            if (emptyRow) {
                emptyRow.style.display = 'table-row';
            }
        } else {
            // Sembunyikan baris kosong
            if (emptyRow) {
                emptyRow.style.display = 'none';
            }
            
            // Add new rows
            mitraList.forEach((mitra, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${mitra.nama_mitra}</td>
                    <td>${mitra.email_mitra}</td>
                    <td>${mitra.telepon_mitra}</td>
                    <td>${mitra.alamat_mitra}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-item" data-id="${mitra.id}">
                            Hapus
                        </button>
                    </td>
                `;
                daftarMitra.appendChild(row);
            });
            
            // Bind hapus buttons
            document.querySelectorAll('.btn-hapus-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    mitraList = mitraList.filter(item => item.id !== id);
                    updateMitraTable();
                    updateJsonData();
                    
                    // Jika sudah tidak ada data, set kembali ke single
                    if (mitraList.length === 0) {
                        isSingle.value = "1";
                    }
                });
            });
        }
    }
    
    // Update hidden JSON data
    function updateJsonData() {
        // Buat salinan data tanpa ID temporary
        const cleanData = mitraList.map(({ nama_mitra, email_mitra, telepon_mitra, alamat_mitra }) => ({
            nama_mitra, 
            email_mitra, 
            telepon_mitra, 
            alamat_mitra
        }));
        
        mitraJsonData.value = JSON.stringify(cleanData);
    }
    
    // Reset form inputs
    function resetForm() {
        namaMitra.value = '';
        emailMitra.value = '';
        teleponMitra.value = '';
        alamatMitra.value = '';
    }
    
    // Clear validation errors
    function clearValidationErrors() {
        if (namaError) namaError.textContent = '';
        if (emailError) emailError.textContent = '';
        if (teleponError) teleponError.textContent = '';
        if (alamatError) alamatError.textContent = '';
        
        // Hide form error
        if (formError) {
            formError.textContent = '';
            formError.classList.add('d-none');
        }
        
        // Remove invalid class from inputs
        namaMitra.classList.remove('is-invalid');
        emailMitra.classList.remove('is-invalid');
        teleponMitra.classList.remove('is-invalid');
        alamatMitra.classList.remove('is-invalid');
    }
    
    // Show error message
    function showError(element, message) {
        if (element) {
            element.textContent = message;
            
            // Add invalid class to the corresponding input
            if (element === namaError) namaMitra.classList.add('is-invalid');
            if (element === emailError) emailMitra.classList.add('is-invalid');
            if (element === teleponError) teleponMitra.classList.add('is-invalid');
            if (element === alamatError) alamatMitra.classList.add('is-invalid');
            
            // For form error, show the alert
            if (element === formError) {
                element.textContent = message;
                element.classList.remove('d-none');
            }
        }
    }

    function checkEmailExistsAsync(email) {
        return new Promise((resolve, reject) => {
            // Buat AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/koordinator/check-email-exists', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        resolve(response.exists);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        resolve(false);
                    }
                } else {
                    console.error('Error checking email:', this.status);
                    resolve(false);
                }
            };
            
            xhr.onerror = function() {
                console.error('Network error when checking email');
                resolve(false);
            };
            
            xhr.send(JSON.stringify({ email: email }));
        });
    }
}

