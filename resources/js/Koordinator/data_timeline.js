document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 3000); 
    });
    
    initTimelineFunctionality();
    const viewEditButtons = document.querySelectorAll('.btn-action-detail');
    viewEditButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Prevent default action (important if the button is inside a form)
            e.preventDefault();
            
            // Get the timeline ID from the data-id attribute
            const timelineId = this.getAttribute('data-id');
            
            // Log for debugging
            console.log('Edit button clicked for timeline ID:', timelineId);
            
            // Fetch and display timeline data in the modal
            fetchTimelineData(timelineId);
        });
    });

    if (window.location.hash) {
        const element = document.querySelector(window.location.hash);
        
        if (element) {
            setTimeout(function() {
                element.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        }
    }
});

// Validate edit form - FIX: Use correct element IDs
function validateEditForm() {
    let isValid = true;
    
    // Validate name
    const editNama = document.getElementById('edit_nama_timeline');
    if (!editNama || !editNama.value.trim()) {
        showEditError('edit_nama_timeline_error', 'Nama timeline harus diisi');
        if (editNama) editNama.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate start date - FIX: Correct ID
    const editTanggalMulai = document.getElementById('edit_tanggal_mulai_timeline');
    if (!editTanggalMulai || !editTanggalMulai.value.trim()) {
        showEditError('edit_tanggal_mulai_timeline_error', 'Tanggal mulai harus diisi');
        if (editTanggalMulai) editTanggalMulai.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate end date - FIX: Correct ID
    const editTanggalSelesai = document.getElementById('edit_tanggal_selesai_timeline');
    if (!editTanggalSelesai || !editTanggalSelesai.value.trim()) {
        showEditError('edit_tanggal_selesai_timeline_error', 'Tanggal selesai harus diisi');
        if (editTanggalSelesai) editTanggalSelesai.classList.add('is-invalid');
        isValid = false;
    }
    
    // Validate start date < end date
    if (editTanggalMulai && editTanggalSelesai && 
        editTanggalMulai.value.trim() && editTanggalSelesai.value.trim()) {
        if (new Date(editTanggalMulai.value.trim()) > new Date(editTanggalSelesai.value.trim())) {
            showEditError('edit_tanggal_selesai_timeline_error', 'Tanggal selesai harus setelah tanggal mulai');
            editTanggalSelesai.classList.add('is-invalid');
            isValid = false;
        }
    }
    
    return isValid;
}

// Clear edit form errors - FIX: Check if elements exist before accessing them
function clearEditFormErrors() {
    // Function to safely clear error text
    function clearErrorText(id) {
        const element = document.getElementById(id);
        if (element) element.textContent = '';
    }
    
    // Function to safely remove invalid class
    function removeInvalidClass(id) {
        const element = document.getElementById(id);
        if (element) element.classList.remove('is-invalid');
    }
    
    // Clear error texts
    clearErrorText('edit_nama_timeline_error');
    clearErrorText('edit_tanggal_mulai_timeline_error');  // FIX: Correct ID
    clearErrorText('edit_tanggal_selesai_timeline_error'); // FIX: Correct ID
    clearErrorText('edit_deskripsi_timeline_error');
    
    // Clear and hide form error
    const formError = document.getElementById('edit_form_error');
    if (formError) {
        formError.textContent = '';
        formError.classList.add('d-none');
    }
    
    // Remove invalid class
    removeInvalidClass('edit_nama_timeline');
    removeInvalidClass('edit_tanggal_mulai_timeline'); // FIX: Correct ID
    removeInvalidClass('edit_tanggal_selesai_timeline'); // FIX: Correct ID
    removeInvalidClass('edit_deskripsi');
}

// Show edit form error - FIX: More robust error display
function showEditError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        
        // If this is a feedback element, also set the associated input as invalid
        if (elementId.endsWith('_error')) {
            const inputId = elementId.replace('_error', '');
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.classList.add('is-invalid');
            }
        }
    } else {
        // If we can't find the specific error element, show in the form error instead
        const formError = document.getElementById('edit_form_error');
        if (formError) {
            formError.textContent = message;
            formError.classList.remove('d-none');
        }
    }
}

// Enhanced logging for debugging
function debugEditForm() {
    console.log("=== Edit Form Debug ===");
    
    // Check if modal exists
    const modal = document.getElementById('modalEditTimeline');
    console.log("Modal exists:", !!modal);
    
    // Check form and fields
    const form = document.getElementById('formEditTimeline');
    console.log("Form exists:", !!form);
    
    // Check each input field
    const fields = [
        'edit_timeline_id', 
        'edit_nama_timeline', 
        'edit_tanggal_mulai_timeline', 
        'edit_tanggal_selesai_timeline',
        'edit_deskripsi'
    ];
    
    fields.forEach(id => {
        const element = document.getElementById(id);
        console.log(`Field ${id} exists:`, !!element);
        if (element) {
            console.log(`Field ${id} value:`, element.value);
        }
    });
}

// Modified function to fetch timeline data - More robust with better error handling and debugging
function fetchTimelineData(timelineId) {
    console.log("Fetching timeline data for ID:", timelineId);
    
    // Show loading
    const modal = new bootstrap.Modal(document.getElementById('modalEditTimeline'));
    modal.show();
    
    const updateBtn = document.getElementById('btnUpdateTimeline');
    if (updateBtn) {
        updateBtn.disabled = true;
        updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
    }
    
    // Clear previous errors
    clearEditFormErrors();
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Fetch timeline data
    fetch(`/koordinator/proyek/timeline/${timelineId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Timeline data received:", data);
        
        if (data.success && data.data) {
            const timeline = data.data;
            
            // Set form action
            const editForm = document.getElementById('formEditTimeline');
            if (editForm) {
                editForm.action = `/koordinator/proyek/timeline/${timelineId}`;
            }
            
            // Set values in form fields - With robust error handling
            function setFieldValue(id, value) {
                const field = document.getElementById(id);
                if (field) {
                    field.value = value || '';
                } else {
                    console.error(`Field ${id} not found!`);
                }
            }
            
            // Set timeline ID
            setFieldValue('edit_timeline_id', timeline.timeline_proyek_id);
            
            // Set name
            setFieldValue('edit_nama_timeline', timeline.nama_timeline_proyek);
            
            // Set start date
            if (timeline.tanggal_mulai_timeline) {
                let startDate = timeline.tanggal_mulai_timeline;
                if (startDate.includes(' ')) {
                    startDate = startDate.split(' ')[0];
                }
                setFieldValue('edit_tanggal_mulai_timeline', startDate);
            }
            
            // Set end date
            if (timeline.tanggal_selesai_timeline) {
                let endDate = timeline.tanggal_selesai_timeline;
                if (endDate.includes(' ')) {
                    endDate = endDate.split(' ')[0];
                }
                setFieldValue('edit_tanggal_selesai_timeline', endDate);
            }
            
            // Set description
            setFieldValue('edit_deskripsi', timeline.deskripsi_timeline);
            
            // Debug to check values
            debugEditForm();
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to load timeline data'
            });
            
            setTimeout(() => {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalEditTimeline'));
                if (modalInstance) modalInstance.hide();
            }, 1000);
        }
    })
    .catch(error => {
        console.error("Error fetching timeline data:", error);
        
        // Show error message
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while fetching timeline data'
        });
        
        setTimeout(() => {
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalEditTimeline'));
            if (modalInstance) modalInstance.hide();
        }, 1000);
    })
    .finally(() => {
        // Reset button state
        if (updateBtn) {
            updateBtn.disabled = false;
            updateBtn.textContent = 'Simpan Perubahan';
        }
    });
}

// Document ready function to set up event listeners for the view/edit butto
function initTimelineFunctionality() {
    let timelineList = [];
    const btnTambahkanKeDaftarTimeline = document.getElementById('btnTambahkanKeDaftarTimeline');
    const btnSimpanTimeline = document.getElementById('btnSimpanTimeline');
    const daftarTimeline = document.getElementById('daftarTimeline');
    const timelineJsonData = document.getElementById('timelineJsonData');
    const isSingleTimeline = document.getElementById('isSingleTimeline');
    const emptyRowTimeline = document.getElementById('emptyRowTimeline');
    const formTimeline = document.getElementById('formTambahDataTimeline');
    const modalTambahTimeline = document.getElementById('modalTambahTimeline');
    
    // Jika elemen tidak ditemukan, berarti kita tidak di halaman yang relevan
    if (!btnTambahkanKeDaftarTimeline || !daftarTimeline) {
        return;
    }
    
    // Form inputs
    const namaTimeline = document.getElementById('nama_timeline');
    const tanggalMulai = document.getElementById('tanggal_mulai_timeline');
    const tanggalSelesai = document.getElementById('tanggal_selesai_timeline');
    const deskripsiTimeline = document.getElementById('deskripsi');
    
    // Error containers
    const namaTimelineError = document.getElementById('nama_timeline_error');
    const tanggalMulaiTimelineError = document.getElementById('tanggal_mulai_timeline_error');
    const tanggalSelesaiTimelineError = document.getElementById('tanggal_selesai_timeline_error');
    const deskripsiTimelineError = document.getElementById('deskripsi_timeline_error');
    const formTimelineError = document.getElementById('form_timeline_error');
    
    // Reset modal saat dibuka
    if (modalTambahTimeline) {
        modalTambahTimeline.addEventListener('show.bs.modal', function() {
            resetForm();
            clearValidationErrors();
            timelineList = [];
            updateTimelineTable();
            updateJsonData();
            if (isSingleTimeline) isSingleTimeline.value = "1"; // Default sebagai single
        });
    }
    
    // Tambahkan timeline ke daftar
    btnTambahkanKeDaftarTimeline.addEventListener('click', function() {
        // Clear previous errors
        clearValidationErrors();
        
        const nama = namaTimeline.value.trim();
        const tglMulai = tanggalMulai.value.trim();
        const tglSelesai = tanggalSelesai.value.trim();
        const deskripsi = deskripsiTimeline.value.trim();
        
        // Validasi data
        let isValid = true;
        
        // Validasi nama
        if (!nama) {
            showError(namaTimelineError, 'Nama timeline harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal mulai
        if (!tglMulai) {
            showError(tanggalMulaiTimelineError, 'Tanggal mulai harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal selesai
        if (!tglSelesai) {
            showError(tanggalSelesaiTimelineError, 'Tanggal selesai harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal mulai < tanggal selesai
        if (tglMulai && tglSelesai) {
            if (new Date(tglMulai) > new Date(tglSelesai)) {
                showError(tanggalSelesaiTimelineError, 'Tanggal selesai harus setelah tanggal mulai');
                isValid = false;
            }
        }
        
        if (!isValid) {
            return;
        }
        
        // Format tanggal untuk tampilan
        const formattedStartDate = formatDateForDisplay(tglMulai);
        const formattedEndDate = formatDateForDisplay(tglSelesai);
        
        // Tambahkan ke array
        timelineList.push({
            nama_timeline: nama,
            tanggal_mulai_timeline: tglMulai,
            tanggal_selesai_timeline: tglSelesai,
            deskripsi_timeline: deskripsi || null,
            id: Date.now(),
            // Tambahkan format tanggal untuk tampilan
            formattedStartDate: formattedStartDate,
            formattedEndDate: formattedEndDate
        });
        
        // Update table & json data
        updateTimelineTable();
        updateJsonData();
        
        // Reset form untuk entri berikutnya
        namaTimeline.value = '';
        tanggalMulai.value = '';
        tanggalSelesai.value = '';
        deskripsiTimeline.value = '';
        // Tanggal tidak direset agar user bisa menambahkan timeline berurutan
        
        // Tandai sebagai multiple
        isSingleTimeline.value = "0";
    });
    
    // Submit form handler
    if (formTimeline) {
        formTimeline.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            clearValidationErrors();
            formTimelineError.textContent = '';
            formTimelineError.classList.add('d-none');
            
            // Cek mode (single atau multiple)
            if (isSingleTimeline.value === "1") {
                // Validasi form single
                if (!validateSingleForm()) {
                    return;
                }
                
                // Konversi data form ke JSON untuk single insert
                const singleData = [{
                    nama_timeline: namaTimeline.value.trim(),
                    tanggal_mulai_timeline: tanggalMulai.value.trim(),
                    tanggal_selesai_timeline: tanggalSelesai.value.trim(),
                    deskripsi_timeline: deskripsiTimeline.value.trim() || null
                }];
                
                timelineJsonData.value = JSON.stringify(singleData);
            } else {
                // Validasi untuk multiple (pastikan ada data)
                if (timelineList.length === 0) {
                    showError(formTimelineError, 'Belum ada data timeline yang ditambahkan ke daftar');
                    return;
                }
            }
            
            // Siapkan data form
            const formData = new FormData(formTimeline);
            
            // Kirim request AJAX
            submitTimelineData(formData);
        });
    }
    
    // Fungsi untuk validasi form single
    function validateSingleForm() {
        let isValid = true;
        
        // Validasi nama
        if (!namaTimeline.value.trim()) {
            showError(namaTimelineError, 'Nama timeline harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal mulai
        if (!tanggalMulai.value.trim()) {
            showError(tanggalMulaiTimelineError, 'Tanggal mulai harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal selesai
        if (!tanggalSelesai.value.trim()) {
            showError(tanggalSelesaiTimelineError, 'Tanggal selesai harus diisi');
            isValid = false;
        }
        
        // Validasi tanggal mulai < tanggal selesai
        if (tanggalMulai.value.trim() && tanggalSelesai.value.trim()) {
            if (new Date(tanggalMulai.value.trim()) > new Date(tanggalSelesai.value.trim())) {
                showError(tanggalSelesaiTimelineError, 'Tanggal selesai harus setelah tanggal mulai');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Fungsi untuk submit data timeline
    function submitTimelineData(formData) {
        // Show loading state
        btnSimpanTimeline.disabled = true;
        btnSimpanTimeline.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
        
        // Ambil CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Kirim request AJAX
        fetch('/koordinator/proyek/timeline', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message
                }).then(() => {
                    // Reset form and close modal
                    resetForm();
                    const modal = bootstrap.Modal.getInstance(modalTambahTimeline);
                    modal.hide();
                    
                    // Reload page to show new data
                    window.location.reload();
                });
            } else {
                // Error handling
                let errorMessage = data.message;
                
                if (data.errors) {
                    errorMessage = '';
                    for (const key in data.errors) {
                        errorMessage += data.errors[key][0] + '<br>';
                    }
                }
                
                showError(formTimelineError, errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(formTimelineError, 'Terjadi kesalahan saat memproses permintaan');
        })
        .finally(() => {
            // Reset button state
            btnSimpanTimeline.disabled = false;
            btnSimpanTimeline.textContent = 'Simpan Data';
        });
    }
    
    // Fungsi update tabel timeline
    function updateTimelineTable() {
        // Clear existing rows (except empty row)
        const existingRows = daftarTimeline.querySelectorAll('tr:not(#emptyRowTimeline)');
        existingRows.forEach(row => row.remove());
        
        if (timelineList.length === 0) {
            // Pastikan baris kosong ditampilkan
            if (emptyRowTimeline) {
                emptyRowTimeline.style.display = 'table-row';
            }
        } else {
            // Sembunyikan baris kosong
            if (emptyRowTimeline) {
                emptyRowTimeline.style.display = 'none';
            }
            
            // Add new rows
            timelineList.forEach((timeline, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${timeline.nama_timeline}</td>
                    <td>${timeline.formattedStartDate || timeline.tanggal_mulai_timeline}</td>
                    <td>${timeline.formattedEndDate || timeline.tanggal_selesai_timeline}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-item" data-id="${timeline.id}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7627 13.2734C21.7627 18.677 17.6296 23.0575 12.5312 23.0575C7.43286 23.0575 3.2998 18.677 3.2998 13.2734C3.2998 7.86976 7.43286 3.48926 12.5312 3.48926C17.6296 3.48926 21.7627 7.86976 21.7627 13.2734ZM15.9478 8.19757L16.0215 8.12654C16.4027 7.79716 16.965 7.82103 17.3203 8.19757C17.6756 8.57412 17.6981 9.17013 17.3873 9.57414L17.3203 9.65228L13.9038 13.2734L17.3204 16.8945C17.6994 17.2963 17.6994 17.9475 17.3204 18.3492C16.9413 18.751 16.3268 18.751 15.9478 18.3492L12.5312 14.7281L9.11464 18.3492C8.73563 18.751 8.12112 18.751 7.74211 18.3492C7.3631 17.9475 7.3631 17.2963 7.74211 16.8945L11.1587 13.2734L7.74214 9.65227L7.67513 9.57414C7.36436 9.17012 7.38687 8.57411 7.74214 8.19757C8.09742 7.82102 8.65976 7.79716 9.04095 8.12654L9.11467 8.19757L12.5312 11.8187L15.9478 8.19757Z" fill="#E56F8C"/>
                            </svg>
                            Hapus
                        </button>
                    </td>
                `;
                daftarTimeline.appendChild(row);
            });
            
            // Bind hapus buttons
            document.querySelectorAll('.btn-hapus-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = parseInt(this.getAttribute('data-id'));
                    timelineList = timelineList.filter(item => item.id !== id);
                    updateTimelineTable();
                    updateJsonData();
                    
                    // Jika sudah tidak ada data, set kembali ke single
                    if (timelineList.length === 0) {
                        isSingleTimeline.value = "1";
                    }
                });
            });
        }
    }
    
    // Update hidden JSON data
    function updateJsonData() {
        // Buat salinan data tanpa ID temporary dan format date
        const cleanData = timelineList.map(({ nama_timeline, tanggal_mulai_timeline, tanggal_selesai_timeline, deskripsi_timeline }) => ({
            nama_timeline, 
            tanggal_mulai_timeline, 
            tanggal_selesai_timeline, 
            deskripsi_timeline
        }));
        
        timelineJsonData.value = JSON.stringify(cleanData);
    }
    
    // Reset form inputs
    function resetForm() {
        if (namaTimeline) namaTimeline.value = '';
        if (tanggalMulai) tanggalMulai.value = '';
        if (tanggalSelesai) tanggalSelesai.value = '';
        if (deskripsiTimeline) deskripsiTimeline.value = '';
        
        // Reset timeline list
        timelineList = [];
    }
    
    // Clear validation errors
    function clearValidationErrors() {
        if (namaTimelineError) namaTimelineError.textContent = '';
        if (tanggalMulaiTimelineError) tanggalMulaiTimelineError.textContent = '';
        if (tanggalSelesaiTimelineError) tanggalSelesaiTimelineError.textContent = '';
        if (deskripsiTimelineError) deskripsiTimelineError.textContent = '';
        
        // Hide form error
        if (formTimelineError) {
            formTimelineError.textContent = '';
            formTimelineError.classList.add('d-none');
        }
        
        // Remove invalid class from inputs
        if (namaTimeline) namaTimeline.classList.remove('is-invalid');
        if (tanggalMulai) tanggalMulai.classList.remove('is-invalid');
        if (tanggalSelesai) tanggalSelesai.classList.remove('is-invalid');
        if (deskripsiTimeline) deskripsiTimeline.classList.remove('is-invalid');
    }
    
    // Show error message
    function showError(element, message) {
        if (!element) return;
        
        if (element === formError) {
            element.innerHTML = message;
            element.classList.remove('d-none');
        } else {
            element.textContent = message;
            
            // Add invalid class to the corresponding input
            if (element === namaTimelineError && namaTimeline) namaTimeline.classList.add('is-invalid');
            if (element === tanggalMulaiTimelineError && tanggalMulai) tanggalMulai.classList.add('is-invalid');
            if (element === tanggalSelesaiTimelineError && tanggalSelesai) tanggalSelesai.classList.add('is-invalid');
            if (element === deskripsiTimelineError && deskripsiTimeline) deskripsiTimeline.classList.add('is-invalid');
        }
    }
    
    // Format date for display (YYYY-MM-DD to DD Month YYYY)
    // Format date for display (YYYY-MM-DD to DD Month YYYY)
    function formatDateForDisplay(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        
        try {
            return date.toLocaleDateString('id-ID', options);
        } catch(e) {
            console.error('Error formatting date:', e);
            return dateString;
        }
    }
    
    // Set up action handlers for the timeline list table
    setupTimelineActionHandlers();
}

// Setup event handlers for edit and delete buttons in the timeline table
function setupTimelineActionHandlers() {
    // Hapus timeline handler
    document.querySelectorAll('.btn-delete-timeline').forEach(btn => {
        btn.addEventListener('click', function() {
            const timelineId = this.getAttribute('data-id');
            confirmDeleteTimeline(timelineId);
        });
    });
    
    // Edit timeline handler
    document.querySelectorAll('.btn-edit-timeline').forEach(btn => {
        btn.addEventListener('click', function() {
            const timelineId = this.getAttribute('data-id');
            openEditTimelineModal(timelineId);
        });
    });
    
    // Edit form submit handler
    const editForm = document.getElementById('formEditTimeline');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditTimelineForm(this);
        });
    }
}

// Open edit modal and load timeline data
function openEditTimelineModal(timelineId) {
    const editForm = document.getElementById('formEditTimeline');
    if (!editForm) return;
    
    // Set loading state
    clearEditFormErrors();
    document.getElementById('btnUpdateTimeline').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Fetch timeline data
    fetch(`/koordinator/proyek/timeline/${timelineId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const timeline = data.data;
            
            // Populate form fields
            document.getElementById('edit_timeline_id').value = timeline.timeline_proyek_id;
            document.getElementById('edit_nama_timeline').value = timeline.nama_timeline_proyek;
            
            // Format date fields (YYYY-MM-DD HH:MM:SS to YYYY-MM-DD)
            if (timeline.tanggal_mulai_timeline) {
                document.getElementById('edit_tanggal_mulai_timeline').value = timeline.tanggal_mulai_timeline.split(' ')[0];
            }
            
            if (timeline.tanggal_selesai_timeline) {
                document.getElementById('edit_tanggal_selesai_timeline').value = timeline.tanggal_selesai_timeline.split(' ')[0];
            }
            
            document.getElementById('edit_deskripsi').value = timeline.deskripsi_timeline || '';
            
            // Show modal
            const editModal = new bootstrap.Modal(document.getElementById('modalEditTimeline'));
            editModal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Gagal memuat data timeline'
            });
        }
    })
    .catch(error => {
        console.error('Error fetching timeline data:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat memuat data timeline'
        });
    })
    .finally(() => {
        // Reset button
        document.getElementById('btnUpdateTimeline').textContent = 'Perbarui Data';
    });
}

// Submit edit timeline form
function submitEditTimelineForm(form) {
    // Clear previous errors
    clearEditFormErrors();
    
    // Validate form
    if (!validateEditForm()) {
        return;
    }
    
    // Set loading state
    const updateBtn = document.getElementById('btnUpdateTimeline');
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
    
    // Get timeline ID
    const timelineId = document.getElementById('edit_timeline_id').value;
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Send AJAX request
    fetch(`/koordinator/proyek/timeline/${timelineId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success notification
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Timeline berhasil diperbarui'
            }).then(() => {
                // Close modal and reload page
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditTimeline'));
                modal.hide();
                window.location.reload();
            });
        } else {
            // Error handling
            let errorMessage = data.message || 'Gagal memperbarui timeline';
            
            if (data.errors) {
                // Display validation errors
                const errors = data.errors;
                for (const key in errors) {
                    if (key === 'nama_timeline') {
                        showEditError('edit_nama_timeline_error', errors[key][0]);
                    } else if (key === 'tanggal_mulai_timeline') {
                        showEditError('edit_tanggal_mulai_timeline_error', errors[key][0]);
                    } else if (key === 'tanggal_selesai_timeline') {
                        showEditError('edit_tanggal_selesai_timeline_error', errors[key][0]);
                    } else if (key === 'deskripsi_timeline') {
                        showEditError('edit_deskripsi_timeline_error', errors[key][0]);
                    }
                }
            } else {
                // Show general error
                document.getElementById('edit_form_error').textContent = errorMessage;
                document.getElementById('edit_form_error').classList.remove('d-none');
            }
        }
    })
    .catch(error => {
        console.error('Error updating timeline:', error);
        document.getElementById('edit_form_error').textContent = 'Terjadi kesalahan saat memperbarui timeline';
        document.getElementById('edit_form_error').classList.remove('d-none');
    })
    .finally(() => {
        // Reset button state
        updateBtn.disabled = false;
        updateBtn.textContent = 'Perbarui Data';
    });
}


// Confirm and delete timeline
function confirmDeleteTimeline(timelineId) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus timeline ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteTimeline(timelineId);
        }
    });
}

// Delete timeline
function deleteTimeline(timelineId) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Send delete request
    fetch(`/koordinator/proyek/timeline/${timelineId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Timeline berhasil dihapus'
            }).then(() => {
                // Reload page
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Gagal menghapus timeline'
            });
        }
    })
    .catch(error => {
        console.error('Error deleting timeline:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat menghapus timeline'
        });
    });
}