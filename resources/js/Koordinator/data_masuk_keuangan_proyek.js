// data_masuk_keuangan_proyek_integrated.js
import swal from '../components';
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Initialize currency formatting for input fields
    initializeCurrencyInput();
    
    // Handle view project button clicks with debugging
    initializeViewProjectButtons();
    
    // Check if we're on the detail page and load transaction data
    if (document.getElementById('tableKeuangan')) {
        console.log('Detail page detected, loading transaction data');
        loadTransaksiData(1, {});
        initializeAddTransactionForm();
        initializeEditTransactionForm(); 
        updateFinancialSummary();
    }

    // Initialize form state
    initializeForm();
    initializeSubkategoriLogic();

    $("#edit_file_keuangan_tefa").change(function() {
        if (handleEditFileUpload()) {
            showEditFilePreview();
        }
    });
    
    if (document.getElementById('tableKeuangan')) {
        console.log('Detail page detected, initializing filters');
        initializePemasukanFilters();
    }
});



function initializeCurrencyInput() {
    const currencyInputs = document.querySelectorAll('.currency-input');
    console.log('Currency inputs found:', currencyInputs.length);
    
    currencyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value) {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            
            this.value = value;
        });
    });
}

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
            
            window.location.href = `/koordinator/data-masuk-keuangan-proyek/${proyekId}`;
        });
    });
    
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

function showLoadingIndicator() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('d-none');
        console.log('Loading indicator shown');
    } else {
        console.log('Loading indicator element not found');
    }
}

function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById('loadingIndicator');
    if (loadingIndicator) {
        loadingIndicator.classList.add('d-none');
    }
}

// Pagination 
function updatePaginationInfo(data) {
    const paginationInfo = document.getElementById('keuanganTefaPaginationInfo');
    if (paginationInfo) {
        paginationInfo.textContent = `Showing ${data.from} to ${data.to} of ${data.total} entries`;
    }
}


function initializeAddTransactionForm() {
    const form = document.getElementById('addTransactionForm');
    
    if (form) {
        const modal = document.getElementById('addTransactionModal');
        modal.addEventListener('shown.bs.modal', function() {
            const jenisTransaksiSelect = document.getElementById('jenis_transaksi_id');
            const jenisKeuanganSelect = document.getElementById('jenis_keuangan_tefa_id');
            
            document.getElementById('hidden_jenis_transaksi_id').value = jenisTransaksiSelect.value;
            document.getElementById('hidden_jenis_keuangan_tefa_id').value = jenisKeuanganSelect.value;
            
            const dateField = document.getElementById('tanggal_transaksi');
            if (!dateField.value) {
                const today = new Date().toISOString().split('T')[0];
                dateField.value = today;
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const jenisTransaksiSelect = document.getElementById('jenis_transaksi_id');
            const jenisKeuanganSelect = document.getElementById('jenis_keuangan_tefa_id');
            
            document.getElementById('hidden_jenis_transaksi_id').value = jenisTransaksiSelect.value;
            document.getElementById('hidden_jenis_keuangan_tefa_id').value = jenisKeuanganSelect.value;
            
            const isSingle = $("#isSinglePemasukan").val() === '1';
            
            if (!isSingle) {
                // Multiple mode - check if there are items in the list
                const itemList = JSON.parse($("#pemasukan_JsonData").val());
                if (itemList.length === 0) {
                    $("#form_pemasukan_error").removeClass('d-none')
                        .text('Tambahkan minimal satu item ke daftar sebelum menyimpan.');
                    return;
                }
                
                // If there are items in list, proceed to submit without form validation
                submitForm();
            } else {
                // Single mode - validate form first
                if (validateForm()) {
                    submitForm();
                }
            }
        });
    }
}

// Edit

function initializeEditButtons() {
    const editButtons = document.querySelectorAll('.btn-action-detail-keuangan');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const transaksiId = this.dataset.id;
            console.log('Edit button clicked for transaction ID:', transaksiId);
            
            if (transaksiId) {
                loadTransactionDetailForEdit(transaksiId);
            }
        });
    });
}

function loadTransactionDetailForEdit(transaksiId) {
    console.log('Loading transaction detail for edit:', transaksiId);
    
    // Show loading state
    $("#editLoadingState").show();
    $("#editFormContent").hide();
    $("#form_keuangan_tefa_edit_error").addClass('d-none').text('');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const proyekIdInput = document.querySelector('input[name="proyek_id"]');
    const proyekId = proyekIdInput?.value;
    
    if (!proyekId) {
        console.error('Could not find proyek_id');
        showEditError('Tidak dapat menemukan ID proyek');
        return;
    }
    
    fetch(`/koordinator/data-masuk-keuangan-proyek/${proyekId}/transaksi/${transaksiId}/detail`, {
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
        console.log('Transaction detail loaded:', data);
        
        if (data.success && data.data) {
            populateEditForm(data.data);
        } else {
            showEditError(data.message || 'Gagal memuat data transaksi');
        }
    })
    .catch(error => {
        console.error('Error loading transaction detail:', error);
        showEditError('Gagal memuat data transaksi. Silakan coba lagi.');
    })
    .finally(() => {
        $("#editLoadingState").hide();
        $("#editFormContent").show();
    });
}

function showEditFileInfo(fileName, fileUrl) {
    const previewContainer = $("#edit_file_preview_container");
    
    const extension = fileName.toLowerCase().split('.').pop();
    const fileIcon = getFileTypeIcon(extension);
    
    const fileInfo = `
        <div class="mt-2 p-2 border rounded">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="${fileIcon} fa-lg me-2"></i>
                    <div>
                        <small class="text-muted">File saat ini:</small><br>
                        <a href="${fileUrl}" target="_blank" >
                            ${fileName}</a>
                    </div>
                </div>

            </div>
            <small class="text-muted">Pilih file baru jika ingin mengganti</small>
        </div>
    `;
    
    previewContainer.html(fileInfo);
}

function handleEditFileUpload() {
    const fileInput = document.getElementById('edit_file_keuangan_tefa');
    
    $(fileInput).removeClass('is-invalid');
    $("#edit_file_keuangan_tefa_error").text('');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        console.log("No file selected for edit");
        return true;
    }
    
    const file = fileInput.files[0];
    console.log("Edit file details:", {
        name: file.name,
        size: file.size,
        type: file.type
    });
    
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        console.error("Edit file too large:", file.size);
        $(fileInput).addClass('is-invalid');
        $("#edit_file_keuangan_tefa_error").text('Ukuran file maksimal 10MB');
        return false;
    }
    
    const fileName = file.name.toLowerCase();
    const extension = fileName.split('.').pop();
    const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'];
    
    if (!allowedExtensions.includes(extension)) {
        $(fileInput).addClass('is-invalid');
        $("#edit_file_keuangan_tefa_error").text('Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG');
        return false;
    }
    
    return true;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showEditFilePreview() {
    const fileInput = document.getElementById('edit_file_keuangan_tefa');
    const previewContainer = $("#edit_file_preview_container");
    
    if (!fileInput.files || fileInput.files.length === 0) {
        return;
    }
    
    const file = fileInput.files[0];
    const extension = file.name.toLowerCase().split('.').pop();
    const fileSize = formatFileSize(file.size);
    
    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = `
                <div class="image-preview text-center">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px; max-width: 100%;" alt="Preview">
                </div>
            `;
            previewContainer.html(preview);
        }
        
        reader.readAsDataURL(file);
    } else {
        const fileTypeIcon = getFileTypeIcon(extension);
        const preview = `
        <div class="mt-2 p-2 border rounded">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="file-preview-card border rounded p-2 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="file-details flex-grow-1">
                                <p class="mb-1 fw-bold text-truncate" title="${file.name}">${file.name}</p>
                                <small class="text-muted">${fileSize} • ${extension.toUpperCase()}</small>
                            </div>
                        </div>
                        <div id="edit-file-preview-content" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
        `;
        previewContainer.html(preview);
    }
}

function validateTransactionName(name) {
    return name.trim().length > 0 && name.trim().length <= 255;
}

function handleFileUpload() {
    const fileInput = document.getElementById('bukti_transaksi');
    
    $(fileInput).removeClass('is-invalid');
    $("#bukti_transaksi_error").text('');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        console.log("No file selected");
        return true;
    }
    
    const file = fileInput.files[0];
    console.log("File details:", {
        name: file.name,
        size: file.size,
        type: file.type
    });
    
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        console.error("File too large:", file.size);
        $(fileInput).addClass('is-invalid');
        $("#bukti_transaksi_error").text('Ukuran file maksimal 10MB');
        return false;
    }
    
    const fileName = file.name.toLowerCase();
    const extension = fileName.split('.').pop();
    const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'];
    
    console.log("File validation:", {
        extension: extension,
        isAllowedExtension: allowedExtensions.includes(extension)
    });
    
    if (!allowedExtensions.includes(extension)) {
        $(fileInput).addClass('is-invalid');
        $("#bukti_transaksi_error").text('Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG');
        return false;
    }
    
    return true;
}

function showFilePreview() {
    const fileInput = document.getElementById('bukti_transaksi');
    const previewContainer = document.getElementById('file_preview_container') || 
        $('<div id="file_preview_container" class="mt-2"></div>').insertAfter(fileInput).get(0);
    
    $(previewContainer).empty();
    
    if (!fileInput.files || fileInput.files.length === 0) {
        console.log('No file selected for preview');
        return;
    }
    
    const file = fileInput.files[0];
    console.log('Preview file:', file.name, file.type, file.size);
    
    const extension = file.name.toLowerCase().split('.').pop();
    
    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail mt-2';
            img.style.maxHeight = '150px';
            previewContainer.appendChild(img);
        }
        
        reader.readAsDataURL(file);
    } else {
        const fileTypeIcon = getFileTypeIcon(extension);
        $(previewContainer).html(`
            <div class="d-flex align-items-center mt-2">
                <i class="${fileTypeIcon} fa-2x me-2"></i>
                <span>${file.name}</span>
            </div>
        `);
    }
}

function getFileTypeIcon(extension) {
    switch (extension) {
        case 'pdf':
            return 'bi bi-file-pdf';
        case 'doc':
        case 'docx':
            return 'bi bi-file-word';
        case 'xls':
        case 'xlsx':
            return 'bi bi-file-excel';
        case 'ppt':
        case 'pptx':
            return 'bi bi-file-ppt';
        case 'jpg':
        case 'jpeg':
        case 'png':
            return 'bi bi-file-image';
        default:
            return 'bi bi-file-earmark';
    }
}

function removeFromList(itemId) {
    console.log("Removing item with ID:", itemId);
    
    let currentList = JSON.parse($("#pemasukan_JsonData").val());
    console.log("Current list before removal:", currentList);
    
    const itemIdStr = String(itemId);
    
    currentList = currentList.filter(item => String(item.id) !== itemIdStr);
    
    console.log("List after removal:", currentList);
    
    $("#pemasukan_JsonData").val(JSON.stringify(currentList));
    
    if (window.pemasukanFiles && window.pemasukanFiles[itemIdStr]) {
        delete window.pemasukanFiles[itemIdStr];
        console.log(`File associated with item ${itemIdStr} has been removed from storage`);
    }
    
    $(`#item-${itemId}`).remove();
    
    if (currentList.length === 0) {
        $("#isSinglePemasukan").val('1');
        $("#daftarPemasukan").html(`
            <tr id="emptyRowPemasukan">
                <td colspan="5" class="text-center">Belum ada pemasukan yang ditambahkan ke daftar</td>
            </tr>
        `);
    }
    
    $("#form_pemasukan_error").removeClass('d-none alert-danger').addClass('alert-success')
        .text('Item berhasil dihapus dari daftar.');
    
    setTimeout(function() {
        $("#form_pemasukan_error").addClass('d-none').removeClass('alert-success');
    }, 3000);
}

function formatCurrency(amount) {
    let cleanAmount = String(amount).replace(/\D/g, '');
    
    if (!cleanAmount) {
        return '0';
    }
    
    return cleanAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}


function handleSubmitSuccess() {
    swal.successMessage('Dana pemasukan berhasi disimpan');
    
    $("#addTransactionForm")[0].reset();
    
    $("#btnSimpanPemasukan").prop('disabled', false).html('Simpan');
    
    $("#form_pemasukan_error").addClass('d-none').text('');
    $(".is-invalid").removeClass('is-invalid');
    
    $("#bukti_transaksi").val('');
    $("#file_preview_container").empty();
    
    if (window.pemasukanFiles) {
        window.pemasukanFiles = {};
    }
    $("#pemasukan_JsonData").val('[]');
    $("#daftarPemasukan").html('<tr id="emptyRowPemasukan"><td colspan="5" class="text-center">Belum ada pemasukan yang ditambahkan ke daftar</td></tr>');
    $("#isSinglePemasukan").val('1');
    
    $("#addTransactionModal").modal('hide');
    
    // Reload data (back to page 1 to show new entries)
    loadTransaksiData(1);
}

function handleSubmitError(xhr, status, error, isSingle) {
    $("#btnSimpanPemasukan").prop('disabled', false).html('Simpan');
    
    console.error('Submit error:', {
        status: xhr.status,
        statusText: xhr.statusText,
        responseText: xhr.responseText,
        error: error
    });
    
    if (xhr.status === 419) {
        // CSRF token mismatch
        $("#form_pemasukan_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text('Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.');
        
        // Optionally refresh CSRF token
        setTimeout(function() {
            window.location.reload();
        }, 3000);
        
    } else if (xhr.status === 422) {
        // Validation errors
        const errors = xhr.responseJSON?.errors || {};
        
        $(".is-invalid").removeClass('is-invalid');
        
        let hasFormErrors = false;
        for (const field in errors) {
            const errorMsg = errors[field][0];
            const fieldElement = $(`#${field}`);
            const errorElement = $(`#${field}_error`);
            
            if (fieldElement.length && errorElement.length) {
                fieldElement.addClass('is-invalid');
                errorElement.text(errorMsg);
                hasFormErrors = true;
            }
        }
        
        if (hasFormErrors) {
            $("#form_pemasukan_error")
                .removeClass('d-none')
                .addClass('alert-danger')
                .text('Terdapat kesalahan pada form. Silakan periksa kembali.');
        } else {
            $("#form_pemasukan_error")
                .removeClass('d-none')
                .addClass('alert-danger')
                .text('Validasi gagal: ' + (xhr.responseJSON?.message || 'Silakan periksa data yang diinput.'));
        }
        
    } else if (xhr.status === 500) {
        // Server error
        const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan pada server.';
        $("#form_pemasukan_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text('Error Server: ' + errorMessage);
            
    } else if (xhr.status === 0) {
        // Network error
        $("#form_pemasukan_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
            
    } else {
        // Other errors
        const errorMessage = xhr.responseJSON?.message || 
                           xhr.statusText || 
                           'Terjadi kesalahan yang tidak diketahui.';
        $("#form_pemasukan_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text(`Error (${xhr.status}): ${errorMessage}`);
    }
}

// Delete Data

function initializeDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.btn-action-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const transaksiId = this.dataset.id;
            
            swal.confirmationDelete('Apakah Anda yakin ingin menghapus transaksi ini?', 'Data yang dihapus tidak dapat dikembalikan.')
            .then((result) => {
                if (result.isConfirmed) {
                    deleteTransaction(transaksiId);
                }
            });
        });
    });
}

function deleteTransaction(transaksiId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Show loading
    Swal.fire({
        title: 'Sedang menghapus...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
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
            // Success
            Swal.fire({
                title: 'Berhasil!',
                text: 'Transaksi berhasil dihapus',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload data setelah user click OK
                loadTransaksiData();
            });
        } else {
            // Error dari server
            Swal.fire({
                title: 'Gagal!',
                text: data.message || 'Gagal menghapus transaksi',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error deleting transaction:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan. Silakan coba lagi.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function initializeSubkategoriLogic() {
    const subkategoriDataElement = document.getElementById('subkategoriData');
    const subkategoriContainer = document.getElementById('subkategoriContainer');
    
    if (subkategoriDataElement && subkategoriContainer) {
        try {
            const subkategoriData = JSON.parse(subkategoriDataElement.textContent);
            console.log('Subkategori data found:', subkategoriData.length, 'items');
            
            // Show/hide subkategori container based on data availability
            if (subkategoriData && subkategoriData.length > 0) {
                subkategoriContainer.style.display = 'block';
                
                // Update label untuk menunjukkan field ini required
                const label = subkategoriContainer.querySelector('label[for="subkategori_pemasukan_id"]');
                if (label && !label.querySelector('.text-danger')) {
                    // Pastikan ada asterisk merah untuk menunjukkan required
                    const asterisk = label.querySelector('.text-danger');
                    if (!asterisk) {
                        label.innerHTML = label.innerHTML.replace(
                            'Kategori Pemasukan', 
                            'Kategori Pemasukan <span class="text-danger">*</span>'
                        );
                    }
                }
                
                console.log('Subkategori dropdown shown - data available and required');
            } else {
                subkategoriContainer.style.display = 'none';
                console.log('Subkategori dropdown hidden - no data available');
            }
        } catch (error) {
            console.error('Error parsing subkategori data:', error);
            subkategoriContainer.style.display = 'none';
        }
    } else {
        console.log('Subkategori elements not found, hiding container');
        if (subkategoriContainer) {
            subkategoriContainer.style.display = 'none';
        }
    }
}

// Tambah Data 
function initializeForm() {
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
    
    $("#pemasukan_JsonData").val('[]');
    $("#isSinglePemasukan").val('1');

    // Handle file input change
    $("#bukti_transaksi").change(function() {
        handleFileUpload();
        showFilePreview();
    });

    // Handle add to list button
    $("#btnTambahkanKeDaftarPemasukan").off('click').on('click', function() {
        const originalBtnText = $(this).text();
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...');
        
        const success = addToList();
        
        $(this).prop('disabled', false).text(originalBtnText);
        
        if (!success) {
            setTimeout(function() {
                $('.is-invalid:first').focus();
            }, 100);
        }
    });

    // Handle remove from list
    $(document).on('click', '.btn-remove-item', function() {
        const itemId = $(this).data('id');
        removeFromList(itemId);
    });

    // Clear validation errors on input change - termasuk subkategori
    $("#addTransactionForm input, #addTransactionForm select, #addTransactionForm textarea").on('input change', function() {
        $(this).removeClass('is-invalid');
        const errorId = $(this).attr('id') + "_error";
        $("#" + errorId).text('');
        
        // Clear general form error juga
        $("#form_pemasukan_error").addClass('d-none').text('');
    });
}

function isSubkategoriRequired() { 
    const subkategoriContainer = document.getElementById('subkategoriContainer');
    const hasSubkategoriData = window.hasSubkategoriPemasukan;
    
    // Subkategori wajib jika container terlihat dan ada data subkategori
    return subkategoriContainer && 
           subkategoriContainer.style.display !== 'none' && 
           hasSubkategoriData;
}

function validateForm() {
    // Check if we're in multiple mode and have items in the list
    const isSingle = $("#isSinglePemasukan").val() === '1';
    const itemList = JSON.parse($("#pemasukan_JsonData").val());
    
    // Jika mode multiple dan sudah ada item di list, skip validasi form
    // karena kita submit data list, bukan data form
    if (!isSingle && itemList.length > 0) {
        console.log("Multiple mode with items in list - skipping form validation");
        return true;
    }
    
    // Validasi form normal (untuk single mode atau saat menambah ke list)
    let isValid = true;
    
    // Clear previous validation errors
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_pemasukan_error").addClass('d-none').text('');
    
    // Required fields dasar
    const requiredFields = [
        { id: 'tanggal_transaksi', name: 'Tanggal Transaksi' },
        { id: 'nama_transaksi', name: 'Nama Transaksi' },
        { id: 'nominal', name: 'Nominal' }
    ];
    
    // Tambahkan subkategori ke required fields jika diperlukan
    if (isSubkategoriRequired()) {
        requiredFields.push({ id: 'subkategori_pemasukan_id', name: 'Kategori Pemasukan' });
        console.log('Subkategori validation required');
    }
    
    // Validasi semua required fields
    $.each(requiredFields, function(i, field) {
        const fieldValue = $("#" + field.id).val();
        if (!fieldValue || fieldValue.trim() === '') {
            $("#" + field.id).addClass('is-invalid');
            $("#" + field.id + "_error").text(`${field.name} wajib diisi`);
            isValid = false;
            console.log(`Validation failed for field: ${field.id}`);
        }
    });
    
    // Validasi nama transaksi
    const namaTransaksi = $("#nama_transaksi").val();
    if (namaTransaksi && !validateTransactionName(namaTransaksi)) {
        $("#nama_transaksi").addClass('is-invalid');
        $("#nama_transaksi_error").text('Nama transaksi harus berisi 1-255 karakter');
        isValid = false;
    }
    
    // Validasi nominal
    const nominal = $("#nominal").val();
    if (nominal) {
        const numValue = parseInt(nominal.replace(/\D/g, ''), 10);
        
        if (isNaN(numValue) || numValue <= 0 || numValue >= 10**13) {
            $("#nominal").addClass('is-invalid');
            $("#nominal_error").text('Nominal harus berupa angka positif dan tidak melebihi batas maksimum');
            isValid = false;
        }
    }
    
    // Validasi file jika ada
    if ($("#bukti_transaksi")[0].files && $("#bukti_transaksi")[0].files.length > 0) {
        if (!handleFileUpload()) {
            isValid = false;
        }
    }
    
    return isValid;
}

function addToList() {
    if (!validateForm()) {
        $("#form_pemasukan_error").removeClass('d-none alert-success').addClass('alert-danger')
            .text('Harap periksa semua field yang wajib diisi.');
        return false;
    }
    
    const itemId = Date.now().toString();
    
    const namaTransaksi = $("#nama_transaksi").val().trim();
    const nominal = $("#nominal").val();
    const tanggal = $("#tanggal_transaksi").val();
    const deskripsi = $("#deskripsi_transaksi").val();
    
    // Ambil nilai subkategori jika ada
    const subkategoriId = $("#subkategori_pemasukan_id").val() || null;
    
    const displayNominal = formatCurrency(nominal);
    
    const fileInput = document.getElementById('bukti_transaksi');
    let fileObject = null;
    let fileName = '';
    
    if (fileInput.files && fileInput.files.length > 0) {
        fileObject = fileInput.files[0];
        fileName = fileInput.files[0].name;
        console.log(`File "${fileName}" associated with item ${itemId}`);
    }
    
    let currentList = JSON.parse($("#pemasukan_JsonData").val());
    
    const sequence = currentList.length;
    
    const itemData = {
        id: itemId,
        sequence: sequence,
        proyek_id: $('input[name="proyek_id"]').val(),
        jenis_transaksi_id: $("#hidden_jenis_transaksi_id").val(),
        jenis_keuangan_tefa_id: $("#hidden_jenis_keuangan_tefa_id").val(),
        subkategori_pemasukan_id: subkategoriId, // Tambahkan subkategori
        nama_transaksi: namaTransaksi,
        tanggal_transaksi: tanggal,
        nominal: nominal,
        deskripsi_transaksi: deskripsi,
        has_file: !!fileObject,
        file: fileObject,
        fileName: fileName
    };
    
    currentList.push(itemData);
    
    const serializedList = currentList.map(item => {
        const { file, ...rest } = item;
        return rest;
    });
    
    $("#pemasukan_JsonData").val(JSON.stringify(serializedList));
    
    if (!window.pemasukanFiles) {
        window.pemasukanFiles = {};
    }
    if (fileObject) {
        window.pemasukanFiles[itemId] = fileObject;
    }
    
    $("#isSinglePemasukan").val('0');
    $("#emptyRowPemasukan").remove();
    
    const displayDate = new Date(tanggal).toLocaleDateString('id-ID');
    
    // Create file display with icon
    let fileDisplay = '';
    if (fileName) {
        const extension = fileName.toLowerCase().split('.').pop();
        let fileIcon = '';
        let badgeColor = '';
        
        switch (extension) {
            case 'pdf':
                fileIcon = 'bi bi-file-earmark-pdf';
                badgeColor = 'bg-danger';
                break;
            case 'doc':
            case 'docx':
                fileIcon = 'bi bi-file-earmark-word';
                badgeColor = 'bg-primary';
                break;
            case 'xls':
            case 'xlsx':
                fileIcon = 'bi bi-file-earmark-excel';
                badgeColor = 'bg-success';
                break;
            case 'ppt':
            case 'pptx':
                fileIcon = 'bi bi-file-earmark-ppt';
                badgeColor = 'bg-warning';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
                fileIcon = 'bi bi-file-earmark-image';
                badgeColor = 'bg-info';
                break;
            default:
                fileIcon = 'bi bi-file-earmark';
                badgeColor = 'bg-secondary';
        }
        
        fileDisplay = `
            <div class="d-flex align-items-center">
                <span class="badge ${badgeColor} me-1" title="${fileName}">
                    <i class="${fileIcon}"></i>
                </span>
                <small class="text-truncate" style="max-width: 100px;" title="${fileName}">
                    ${fileName.length > 12 ? fileName.substring(0, 12) + '...' : fileName}
                </small>
            </div>
        `;
    } else {
        fileDisplay = `
            <span class="badge bg-light text-dark" title="Tidak ada file">
                <i class="bi bi-file-earmark-x"></i> Tidak Ada
            </span>
        `;
    } 
    
    const newRow = `
        <tr id="item-${itemId}">
            <td>${namaTransaksi}</td>
            <td>${displayDate}</td>
            <td class="text-end">Rp ${displayNominal}</td>
            <td>${fileDisplay}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remove-item" data-id="${itemId}">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </td>
        </tr>
    `;
    
    $("#daftarPemasukan").append(newRow);
    
    // Clear form
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    
    $("#nama_transaksi").val('');
    $("#nominal").val('');
    $("#deskripsi_transaksi").val('');
    $("#bukti_transaksi").val('');
    $("#subkategori_pemasukan_id").val(''); // Clear subkategori
    $("#file_preview_container").empty();
    
    $("#form_pemasukan_error").removeClass('d-none alert-danger').addClass('alert-success')
        .text('Item berhasil ditambahkan ke daftar.');
    
    setTimeout(function() {
        $("#form_pemasukan_error").addClass('d-none').removeClass('alert-success');
    }, 3000);
    
    return true;
}

function submitForm() {
    const isSingle = $("#isSinglePemasukan").val() === '1';
    const itemList = JSON.parse($("#pemasukan_JsonData").val());
    
    console.log("Submit form with mode:", isSingle ? "Single" : "Multiple");
    console.log("Items in list:", itemList.length);
    
    // Clear any previous error messages
    $("#form_pemasukan_error").addClass('d-none').text('');
    
    // Get CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                     $('input[name="_token"]').val() || 
                     document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found!');
        $("#form_pemasukan_error").removeClass('d-none').addClass('alert-danger')
            .text('CSRF token tidak ditemukan. Silakan refresh halaman.');
        return false;
    }
    
    console.log('Using CSRF token:', csrfToken.substring(0, 10) + '...');
    
    const formData = new FormData($("#addTransactionForm")[0]);
    formData.set('_token', csrfToken);
    
    const submitButton = $("#btnSimpanPemasukan");
    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
    
    if (!isSingle && itemList.length > 0) {
        // Multiple mode - ada item di list, submit data list
        console.log("Multiple mode - submitting list data, skip form validation");
        
        formData.set('pemasukan_data', JSON.stringify(itemList));
        formData.set('is_single', '0');
        
        // Add files for each item
        if (window.pemasukanFiles) {
            let fileCounter = 0;
            for (const [itemId, fileObj] of Object.entries(window.pemasukanFiles)) {
                if (fileObj instanceof File) {
                    formData.append(`bukti_transaksi_${itemId}`, fileObj);
                    fileCounter++;
                    console.log(`Added file for item ${itemId}: ${fileObj.name}`);
                }
            }
            console.log(`Total ${fileCounter} files added to request`);
        }
        
        // Submit dengan endpoint untuk multiple items
        $.ajax({
            url: '/koordinator/data-masuk-keuangan-proyek/store-with-files',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                handleSubmitSuccess(response);
            },
            error: function(xhr, status, error) {
                handleSubmitError(xhr, status, error, isSingle);
            }
        });
        
    } else if (isSingle) {
        // Single mode - validate form terlebih dahulu
        console.log("Single mode - validating form before submit");
        
        if (!validateForm()) {
            $("#form_pemasukan_error").removeClass('d-none').addClass('alert-danger')
                .text('Harap periksa semua field yang wajib diisi.');
            submitButton.prop('disabled', false).html('Simpan');
            
            // Focus on first invalid field
            setTimeout(function() {
                $('.is-invalid:first').focus();
            }, 100);
            return false;
        }
        
        formData.set('is_single', '1');
        
        // Submit dengan endpoint untuk single item
        $.ajax({
            url: '/koordinator/data-masuk-keuangan-proyek/tambah-transaksi',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                handleSubmitSuccess(response);
            },
            error: function(xhr, status, error) {
                handleSubmitError(xhr, status, error, isSingle);
            }
        });
        
    } else {
        // Multiple mode tapi tidak ada item di list
        console.log("Multiple mode but no items in list");
        $("#form_pemasukan_error").removeClass('d-none').addClass('alert-danger')
            .text('Tambahkan minimal satu item ke daftar sebelum menyimpan.');
        submitButton.prop('disabled', false).html('Simpan');
        return false;
    }
    
    return true;
}

// Edit Data 
function populateEditForm(data) {
    console.log('Populating edit form with data:', data);
    
    try {
        // Set basic fields
        $("#keuangan_tefa_id").val(data.keuangan_tefa_id);
        $("#edit_tanggal_transaksi").val(data.tanggal_transaksi);
        $("#edit_nama_transaksi").val(data.nama_transaksi);
        $("#edit_deskripsi_transaksi").val(data.deskripsi_transaksi || '');
        $("#edit_nominal").val(data.nominal_transaksi);
        
        // Set readonly fields
        $("#edit_jenis_transaksi_id").val(data.jenis_transaksi_id);
        $("#edit_jenis_keuangan_tefa_id").val(data.jenis_keuangan_tefa_id);
        $("#edit_nama_proyek").val(data.nama_proyek);
        
        // ✅ PERBAIKAN: Handle subkategori dropdown - conditional required
        const subkategoriContainer = $("#edit_kategoriTransaksiContainer");
        const subkategoriSelect = $("#edit_sub_jenis_transaksi_id");
        
        // Check if subkategori data is available in database
        const hasSubkategoriData = window.hasSubkategoriPemasukan || 
                                  (subkategoriSelect.find('option[value]:not([value=""])').length > 0);
        
        if (hasSubkategoriData) {
            // ✅ Ada data subkategori → dropdown muncul dan WAJIB diisi
            subkategoriContainer.show();
            
            // ✅ TAMPILKAN required indicator untuk form edit
            const editRequiredIndicator = subkategoriContainer.find('.text-danger');
            if (editRequiredIndicator.length === 0) {
                // Tambah required indicator jika belum ada
                subkategoriContainer.find('label').append(' <span class="text-danger">*</span>');
            }
            
            // ✅ Update placeholder dan tambah required
            const firstOption = subkategoriSelect.find('option[value=""]');
            if (firstOption.length) {
                firstOption.text('Pilih Kategori Pemasukan');
            }
            subkategoriSelect.attr('required', 'required');
            
            // Set the value if available
            if (data.sub_jenis_transaksi_id) {
                subkategoriSelect.val(data.sub_jenis_transaksi_id);
            } else {
                subkategoriSelect.val(''); // Set to first option
            }
            
            // ✅ Set flag for edit validation - subkategori REQUIRED jika ada data
            window.isEditSubkategoriRequired = true;
            
            console.log('Edit form: Subkategori dropdown shown and REQUIRED (data available)');
        } else {
            // ✅ Tidak ada data subkategori → sembunyikan dropdown
            subkategoriContainer.hide();
            window.isEditSubkategoriRequired = false;
            console.log('Edit form: Subkategori dropdown hidden (no data available)');
        }
        
        // Handle file preview
        $("#edit_file_preview_container").empty();
        if (data.has_file && data.file_url) {
            showEditFileInfo(data.file_name, data.file_url);
        }
        
        console.log('Edit form populated successfully', {
            'has_subkategori_data': hasSubkategoriData,
            'subkategori_value': data.sub_jenis_transaksi_id,
            'has_file': data.has_file,
            'subkategori_required': window.isEditSubkategoriRequired
        });
        
    } catch (error) {
        console.error('Error populating edit form:', error);
        showEditError('Gagal memuat data ke form. Silakan coba lagi.');
    }
}

function validateEditForm() {
    let isValid = true;
    
    // Clear previous validation errors
    clearEditValidationErrors();
    
    const requiredFields = [
        { id: 'edit_tanggal_transaksi', name: 'Tanggal Transaksi' },
        { id: 'edit_nama_transaksi', name: 'Nama Transaksi' },
        { id: 'edit_nominal', name: 'Nominal' }
    ];
    
    // ✅ PERBAIKAN: Tambah subkategori ke required fields HANYA jika tersedia di edit form
    if (window.isEditSubkategoriRequired) {
        requiredFields.push({ id: 'edit_sub_jenis_transaksi_id', name: 'Kategori Pemasukan' });
        console.log('Edit validation: Subkategori added to required fields');
    } else {
        console.log('Edit validation: Subkategori not required (no data available)');
    }
    
    $.each(requiredFields, function(i, field) {
        const fieldValue = $("#" + field.id).val();
        if (!fieldValue || fieldValue.trim() === '') {
            $("#" + field.id).addClass('is-invalid');
            $("#" + field.id + "_error").text(`${field.name} wajib diisi`);
            isValid = false;
            console.log(`Edit validation failed for field: ${field.id}`);
        }
    });
    
    // Validate transaction name length
    const namaTransaksi = $("#edit_nama_transaksi").val();
    if (namaTransaksi && !validateTransactionName(namaTransaksi)) {
        $("#edit_nama_transaksi").addClass('is-invalid');
        $("#edit_nama_transaksi_error").text('Nama transaksi harus berisi 1-255 karakter');
        isValid = false;
    }
    
    // Validate nominal
    const nominal = $("#edit_nominal").val();
    if (nominal) {
        const numValue = parseInt(nominal.replace(/\D/g, ''), 10);
        
        if (isNaN(numValue) || numValue <= 0 || numValue >= 10**13) {
            $("#edit_nominal").addClass('is-invalid');
            $("#edit_nominal_error").text('Nominal harus berupa angka positif dan tidak melebihi batas maksimum');
            isValid = false;
        }
    }
    
    // Validate file if uploaded
    if ($("#edit_file_keuangan_tefa")[0] && $("#edit_file_keuangan_tefa")[0].files.length > 0) {
        if (!handleEditFileUpload()) {
            isValid = false;
        }
    }
    
    console.log('Edit form validation result:', isValid);
    return isValid;
}

function submitEditForm() {
    console.log('Submitting edit form...');
    
    if (!validateEditForm()) {
        $("#form_keuangan_tefa_edit_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text('Harap periksa semua field yang wajib diisi.');
        
        // Focus on first invalid field
        setTimeout(function() {
            $('.is-invalid:first').focus();
        }, 100);
        return;
    }
    
    const transaksiId = $("#keuangan_tefa_id").val();
    if (!transaksiId) {
        showEditError('ID transaksi tidak ditemukan');
        return;
    }
    
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) {
        showEditError('CSRF token tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    
    // Prepare form data
    const formData = new FormData($("#formEditDataKeuanganProyek")[0]);
    formData.set('_token', csrfToken);
    formData.set('_method', 'PUT');
    
    // ✅ PERBAIKAN: Subkategori handling - conditional berdasarkan ketersediaan data
    const subkategoriValue = $("#edit_sub_jenis_transaksi_id").val();
    if (window.isEditSubkategoriRequired) {
        // Jika subkategori required, pastikan ada nilainya
        if (!subkategoriValue) {
            $("#edit_sub_jenis_transaksi_id").addClass('is-invalid');
            $("#edit_sub_jenis_transaksi_id_error").text('Kategori Pemasukan wajib dipilih');
            showEditError('Kategori Pemasukan wajib dipilih karena tersedia dalam sistem.');
            return;
        }
        formData.set('edit_sub_jenis_transaksi_id', subkategoriValue);
    } else {
        // Jika tidak required, set sebagai null
        formData.set('edit_sub_jenis_transaksi_id', '');
    }
    
    console.log('Edit form data being submitted:', {
        'transaksi_id': transaksiId,
        'has_subkategori': !!subkategoriValue,
        'subkategori_value': subkategoriValue,
        'subkategori_required': window.isEditSubkategoriRequired,
        'has_new_file': $("#edit_file_keuangan_tefa")[0].files.length > 0
    });
    
    // Disable submit button and show loading
    const submitButton = $("#btnSimpanEditKeuanganProyek");
    const originalText = submitButton.text();
    submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
    
    $.ajax({
        url: `/koordinator/data-masuk-keuangan-proyek/update-transaksi/${transaksiId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Edit response:', response);
            
            if (response.success) {
                swal.successMessage('Data transaksi berhasil diperbarui');
                $("#modalEditKeuanganProyek").modal('hide');
                loadTransaksiData();
            } else {
                showEditError(response.message || 'Gagal memperbarui data transaksi');
            }
        },
        error: function(xhr, status, error) {
            console.error('Edit error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            if (xhr.status === 422) {
                const errors = xhr.responseJSON?.errors || {};
                
                let hasFormErrors = false;
                for (const field in errors) {
                    const errorMsg = errors[field][0];
                    const fieldElement = $(`#${field}`);
                    const errorElement = $(`#${field}_error`);
                    
                    if (fieldElement.length && errorElement.length) {
                        fieldElement.addClass('is-invalid');
                        errorElement.text(errorMsg);
                        hasFormErrors = true;
                    }
                }
                
                if (!hasFormErrors) {
                    showEditError('Validasi gagal: ' + (xhr.responseJSON?.message || 'Silakan periksa data yang diinput.'));
                }
            } else if (xhr.status === 419) {
                showEditError('Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.');
            } else {
                const errorMessage = xhr.responseJSON?.message || xhr.statusText || 'Terjadi kesalahan yang tidak diketahui.';
                showEditError(`Error (${xhr.status}): ${errorMessage}`);
            }
        },
        complete: function() {
            submitButton.prop('disabled', false).text(originalText);
        }
    });
}

function initializeEditTransactionForm() {
    const editForm = document.getElementById('formEditDataKeuanganProyek');
    const editModal = document.getElementById('modalEditKeuanganProyek');
    
    if (editForm && editModal) {
        // Clear validation errors when modal is opened
        editModal.addEventListener('shown.bs.modal', function() {
            clearEditValidationErrors();
        });
        
        // Clear validation errors on input change
        $(editForm).find('input, select, textarea').on('input change', function() {
            $(this).removeClass('is-invalid');
            const errorId = $(this).attr('id') + "_error";
            $("#" + errorId).text('');
            $("#form_keuangan_tefa_edit_error").addClass('d-none').text('');
        });
        
        // Handle edit file input change
        $("#edit_file_keuangan_tefa").change(function() {
            handleEditFileUpload();
            showEditFilePreview();
        });
        
        // Handle form submission
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditForm();
        });
        
        console.log('Edit transaction form initialized with optional subkategori');
    }
}

function clearEditValidationErrors() {
    $("#formEditDataKeuanganProyek .is-invalid").removeClass("is-invalid");
    $("#formEditDataKeuanganProyek .invalid-feedback").text('');
    $("#form_keuangan_tefa_edit_error").addClass('d-none').text('');
    console.log('Edit form validation errors cleared');
}

function showEditError(message) {
    $("#form_keuangan_tefa_edit_error")
        .removeClass('d-none')
        .addClass('alert-danger')
        .text(message);
    $("#editLoadingState").hide();
    $("#editFormContent").show();
}

// Filter Data 

function initializePemasukanFilterHandlers() {
    // Format nominal inputs with thousand separator
    $("#filter_pemasukan_nominal_min, #filter_pemasukan_nominal_max").on('input', function() {
        formatNominalFilter($(this));
    });
    
    // Handle filter form submission
    $("#formFilterKeuanganTefa").submit(function(e) {
        e.preventDefault();
        applyPemasukanFilters();
    });
    
    // Handle reset filter button
    $("#btnResetFilter").click(function() {
        resetPemasukanFilters();
    });
    
    // Real-time validation for date range
    $("#filter_tanggal_mulai, #filter_tanggal_akhir").change(function() {
        validatePemasukanDateRange();
    });
}

// Validate Date Range
function validatePemasukanDateRange() {
    const startDate = $("#filter_tanggal_mulai").val();
    const endDate = $("#filter_tanggal_akhir").val();
    
    if (startDate && endDate) {
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        
        if (endDateObj < startDateObj) {
            $("#filter_tanggal_akhir").addClass('is-invalid');
            return false;
        } else {
            $("#filter_tanggal_akhir").removeClass('is-invalid');
            return true;
        }
    }
    
    $("#filter_tanggal_akhir").removeClass('is-invalid');
    return true;
}

// Apply Filters
function applyPemasukanFilters() {
    console.log("Applying pemasukan filters...");
    
    // Validate date range first
    if (!validatePemasukanDateRange()) {
        swal.errorMessage('Tanggal Akhir tidak boleh lebih awal dari Tanggal Mulai');
        return;
    }
    
    // Collect filter values
    const filters = {
        tanggal_mulai: $("#filter_tanggal_mulai").val(),
        tanggal_akhir: $("#filter_tanggal_akhir").val(),
        nama_transaksi: $("#filter_nama_transaksi").val(),
        kategori_pemasukan: $("#filter_jenis_keuangan").val(),
        status_bukti: $("#filter_sub_jenis_transaksi").val()
    };
    
    // Log filters for debugging
    console.log("Applied filters:", filters);
    
    // Save filters for future use
    savePemasukanFiltersToLocalStorage();
    
    // Load data with filters
    loadTransaksiData(1, filters);
}

// Clear Saved Filters from LocalStorage
function clearSavedPemasukanFilters() {
    // Clear all pemasukan filter-related items in localStorage
    for (let i = localStorage.length - 1; i >= 0; i--) {
        const key = localStorage.key(i);
        if (key && (key.startsWith('filter_') || key === 'pemasukanFiltersApplied')) {
            localStorage.removeItem(key);
        }
    }
}

// Format Nominal Filter Input
function formatNominalFilter(input) {
    // Remove non-digits
    let value = input.val().replace(/\D/g, '');
    
    if (value === '') {
        input.val('');
        return;
    }
    
    // Remove leading zeros
    value = value.replace(/^0+/, '') || '0';
    
    // Format with thousands separator (dots for Indonesian format)
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    input.val(value);
}

// ===== UPDATE EXISTING loadTransaksiData FUNCTION =====
function loadTransaksiData(page = 1, filters = {}) {
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
    console.log('Loading transaction data for proyek ID:', proyekId, 'Page:', page, 'Filters:', filters);
    
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
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found');
    }
    
    const params = new URLSearchParams({
        page: page,
        per_page: 5,
        ...filters 
    });
    
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
            let html = '';
            data.data.forEach(item => {
                html += `
                <tr>
                    <td>${item.tanggal}</td>
                    <td>${item.keterangan}</td>
                    <td>Rp ${item.nominal}</td>
                    <td>${item.bukti}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-action-detail-keuangan" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#modalEditKeuanganProyek">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                    <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-action-delete" data-id="${item.id}" title="Hapus">
                                <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            });
            tableBody.innerHTML = html;
            
            // Initialize delete buttons for newly loaded data
            initializeDeleteButtons();
            // Initialize edit buttons for newly loaded data
            initializeEditButtons();
            
            // Update pagination info and controls
            updatePaginationInfo(data);
            updatePaginationControls(data.pagination, filters); // ✅ Pass filters to pagination
            
        } else {
            // Show no data message
            const filterApplied = Object.keys(filters).some(key => filters[key] !== '' && filters[key] !== undefined);
            const emptyMessage = filterApplied 
                ? 'Tidak ada data transaksi yang sesuai dengan filter yang dipilih.' 
                : 'Belum ada data transaksi pemasukan';
                
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
                            <p class="text-muted">${emptyMessage}</p>
                        </div>
                    </td>
                </tr>
            `;
            updatePaginationInfo({ from: 0, to: 0, total: 0 });
            updatePaginationControls(null);
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


function updatePaginationControls(pagination, filters = {}) {
    const paginationContainer = document.getElementById('keuanganTefaPagination');
    if (!paginationContainer) return;
    
    if (!pagination || pagination.total === 0) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHtml = '<nav aria-label="Table pagination"><ul class="pagination pagination-sm mb-0">';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <button class="page-link pagination-btn" data-page="${pagination.current_page - 1}" data-filters='${JSON.stringify(filters)}' aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </button>
            </li>
        `;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </span>
            </li>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    if (startPage > 1) {
        paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="1" data-filters='${JSON.stringify(filters)}'>1</button></li>`;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        if (i === pagination.current_page) {
            paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="${i}" data-filters='${JSON.stringify(filters)}'>${i}</button></li>`;
        }
    }
    
    if (endPage < pagination.last_page) {
        if (endPage < pagination.last_page - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `<li class="page-item"><button class="page-link pagination-btn" data-page="${pagination.last_page}" data-filters='${JSON.stringify(filters)}'>${pagination.last_page}</button></li>`;
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHtml += `
            <li class="page-item">
                <button class="page-link pagination-btn" data-page="${pagination.current_page + 1}" data-filters='${JSON.stringify(filters)}' aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </li>
        `;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </span>
            </li>
        `;
    }
    
    paginationHtml += '</ul></nav>';
    paginationContainer.innerHTML = paginationHtml;
    
    // Initialize event listeners untuk pagination buttons
    initializePaginationEventListeners();
}


function initializePaginationEventListeners() {
    const paginationButtons = document.querySelectorAll('.pagination-btn');
    
    paginationButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.dataset.page);
            const filters = JSON.parse(this.dataset.filters || '{}');
            
            if (page && !isNaN(page)) {
                console.log('Loading page with filters:', page, filters);
                loadTransaksiData(page, filters);
            }
        });
    });
}


function initializeSelect2Filters() {
    console.log("Initializing Select2 for filters...");
    
    // Check if Select2 library is available
    if (!$.fn.select2) {
        console.warn('Select2 library not loaded. Searchable dropdowns will not be available.');
        return;
    }
    
    try {
        // Initialize Select2 for kategori pemasukan filter
        if ($("#filter_jenis_keuangan").length) {
            // First destroy any existing instances to prevent duplicates
            if ($('#filter_jenis_keuangan').hasClass('select2-hidden-accessible')) {
                $('#filter_jenis_keuangan').select2('destroy');
            }
            
            $('#filter_jenis_keuangan').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Pemasukan',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Tidak ada kategori yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
        
        // Initialize Select2 for status bukti filter if exists
        if ($("#filter_sub_jenis_transaksi").length) {
            // First destroy any existing instances to prevent duplicates
            if ($('#filter_sub_jenis_transaksi').hasClass('select2-hidden-accessible')) {
                $('#filter_sub_jenis_transaksi').select2('destroy');
            }
            
            $('#filter_sub_jenis_transaksi').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Status Bukti',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Tidak ada status yang ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        }
        
        console.log("Select2 filters initialized successfully");
        
    } catch (e) {
        console.warn('Error initializing Select2 for filters:', e);
    }
}

function loadKategoriPemasukanForFilter() {
    const proyekIdInput = document.querySelector('input[name="proyek_id"]');
    if (!proyekIdInput) {
        console.error('Proyek ID input not found');
        return;
    }
    
    const proyekId = proyekIdInput.value;
    const requestUrl = '/koordinator/data-masuk-keuangan-proyek/get-kategori-pemasukan';
    console.log('Calling URL:', requestUrl, 'with proyek_id:', proyekId);
    
    $.ajax({
        url: requestUrl,
        type: 'GET',
        data: { proyek_id: proyekId },
        dataType: 'json',
        beforeSend: function() {
            // Destroy existing Select2 before updating
            if ($("#filter_jenis_keuangan").hasClass('select2-hidden-accessible')) {
                $("#filter_jenis_keuangan").select2('destroy');
            }
            $('#filter_jenis_keuangan').html('<option value="">Loading...</option>');
        },
        success: function(response) {
            if (response.success && response.results && response.results.length > 0) {
                let options = '<option value="">Semua Kategori</option>';
                $.each(response.results, function(key, item) {
                    options += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#filter_jenis_keuangan').html(options);
                
                // Show kategori container
                $("#filterKategoriTransaksiContainer").show();
                
                // Initialize Select2 after loading data with Bootstrap 5 theme
                try {
                    $("#filter_jenis_keuangan").select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Pilih Kategori Pemasukan',
                        allowClear: true,
                        language: {
                            noResults: function() {
                                return "Tidak ada kategori yang ditemukan";
                            },
                            searching: function() {
                                return "Mencari...";
                            }
                        }
                    });
                } catch (e) {
                    console.warn('Error initializing Select2 for kategori filter:', e);
                }
                
                // Restore selected value if exists
                const savedValue = localStorage.getItem('filter_jenis_keuangan');
                if (savedValue) {
                    $('#filter_jenis_keuangan').val(savedValue).trigger('change');
                }
                
                console.log('Kategori loaded and Select2 initialized with Bootstrap 5 theme');
            } else {
                $("#filterKategoriTransaksiContainer").hide();
                $('#filter_jenis_keuangan').html('<option value="">Semua Kategori</option>');
                console.log('No kategori data available');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load kategori pemasukan:', {
                status: xhr.status, 
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            // Hide container dan show error state
            $("#filterKategoriTransaksiContainer").hide();
            $('#filter_jenis_keuangan').html('<option value="">Error loading data</option>');
        }
    });
}

function resetPemasukanFilters() {
    console.log("Resetting pemasukan filters...");
    
    // Destroy Select2 instances before reset using proper check
    $("#formFilterKeuanganTefa select").each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
    
    // Reset form
    $("#formFilterKeuanganTefa")[0].reset();
    
    // Clear validation errors
    $("#formFilterKeuanganTefa .is-invalid").removeClass('is-invalid');
    
    // Clear saved filters from localStorage
    clearSavedPemasukanFilters();
    
    // Reload kategori pemasukan and reinitialize Select2
    const proyekIdInput = document.querySelector('input[name="proyek_id"]');
    if (proyekIdInput && proyekIdInput.value) {
        console.log("Reloading kategori pemasukan after reset...");
        loadKategoriPemasukanForFilter();
    } else {
        console.warn("No proyek_id found, hiding kategori container");
        $("#filterKategoriTransaksiContainer").hide();
        $("#filter_jenis_keuangan").html('<option value="">Semua Kategori</option>');
    }
    
    // Reinitialize other Select2 dropdowns that don't depend on AJAX
    setTimeout(function() {
        initializeSelect2Filters();
    }, 100);
    
    // Reload data without filters
    loadTransaksiData(1, {});
    
    console.log("Pemasukan filters reset completed");
}


function initializePemasukanFilters() {
    console.log("Initializing pemasukan filters...");
    
    // Load kategori pemasukan for filter dropdown
    loadKategoriPemasukanForFilter();
    
    // Initialize Select2 for static dropdowns
    initializeSelect2Filters();
    
    // Set up filter form handlers
    initializePemasukanFilterHandlers();
    
    // Load saved filters if any
    loadSavedPemasukanFilters();
    
    console.log("Pemasukan filters initialized successfully");
}

function savePemasukanFiltersToLocalStorage() {
    $('#formFilterKeuanganTefa').find('input, select').each(function() {
        if ($(this).attr('id')) {
            // For Select2 elements, get the actual selected value
            let value = $(this).val();
            if ($(this).data('select2')) {
                value = $(this).select2('val');
            }
            localStorage.setItem($(this).attr('id'), value || '');
        }
    });
    
    // Save filter application timestamp
    localStorage.setItem('pemasukanFiltersApplied', new Date().getTime());
}

function loadSavedPemasukanFilters() {
    // Check if we have recently saved filters (within last hour)
    const lastApplied = localStorage.getItem('pemasukanFiltersApplied');
    if (!lastApplied) return;
    
    const oneHourAgo = new Date().getTime() - (60 * 60 * 1000);
    if (parseInt(lastApplied) < oneHourAgo) {
        // Filters are older than 1 hour, clear them
        clearSavedPemasukanFilters();
        return;
    }
    
    // Restore saved filter values
    $('#formFilterKeuanganTefa').find('input, select').each(function() {
        const id = $(this).attr('id');
        if (id) {
            const savedValue = localStorage.getItem(id);
            if (savedValue !== null && savedValue !== '') {
                $(this).val(savedValue);
                
                // Trigger change event for Select2 elements
                if ($(this).data('select2')) {
                    $(this).trigger('change');
                }
            }
        }
    });
    
    // Load dependent data if needed
    loadKategoriPemasukanForFilter();
    
    // Apply filters automatically if any were saved
    if ($("#filter_tanggal_mulai").val() || 
        $("#filter_tanggal_akhir").val() || 
        $("#filter_nama_transaksi").val() ||
        $("#filter_jenis_keuangan").val() ||
        $("#filter_sub_jenis_transaksi").val()) {
        
        // Apply with slight delay to ensure all components are loaded
        setTimeout(function() {
            applyPemasukanFilters();
        }, 500);
    }
}

function updateFinancialSummary() {
    // Show loading state
    $("#totalPemasukan").text('Loading...');

    console.log("Updating financial summary...");
    
    // Get proyek ID from input field
    const proyekIdInput = document.querySelector('input[name="proyek_id"]');
    if (!proyekIdInput) {
        console.error('Proyek ID input not found');
        $("#totalPemasukan").text('Error');
        return;
    }
    
    const proyekId = proyekIdInput.value;
    console.log('Getting summary for proyek ID:', proyekId);
    
    $.ajax({
        url: `/koordinator/data-masuk-keuangan-proyek/${proyekId}/summary`,
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log('Financial summary response:', response);
            
            if (response.success && response.data) {
                const data = response.data;
                
                const formatCurrency = (amount) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount || 0);
                };
                
                // Update summary display
                $('#totalPemasukan').text(formatCurrency(data.total_pemasukan));

                // Optional: Update additional summary info if elements exist
                if ($("#jumlahTransaksiPemasukan").length) {
                    $("#jumlahTransaksiPemasukan").text(data.transaksi_count || 0);
                }
                
                // Optional: Update kategori breakdown if container exists
                if ($("#kategoriBreakdown").length && data.pemasukan_by_kategori) {
                    updateKategoriBreakdown(data.pemasukan_by_kategori);
                }
                
                console.log('Financial summary updated successfully');
                
            } else {
                throw new Error(response.message || 'Invalid response format');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error getting financial summary:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            // Show error state
            $('#totalPemasukan').text('Error');
            
            // Optional: Show error message to user
            if ($("#summaryError").length) {
                $("#summaryError").removeClass('d-none').text('Gagal memuat ringkasan keuangan');
            }
        }
    });
}
