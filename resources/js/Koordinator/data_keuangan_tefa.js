import swal from '../components';
// ===== VALIDATION FUNCTIONS =====

function validateForm() {
    let isValid = true;
    
    // Reset validation errors
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_error").addClass('d-none').text('');
    
    // Required fields validation
    const requiredFields = [
        { id: 'tanggal_transaksi', name: 'Tanggal Transaksi' },
        { id: 'jenis_transaksi_id', name: 'Jenis Transaksi' },
        { id: 'jenis_keuangan_tefa_id', name: 'Keperluan Transaksi' },
        { id: 'nama_transaksi', name: 'Nama Transaksi' },
        { id: 'nominal', name: 'Nominal' }
    ];
    
    $.each(requiredFields, function(i, field) {
        if (!$("#" + field.id).val()) {
            $("#" + field.id).addClass('is-invalid');
            $("#" + field.id + "_error").text(`${field.name} wajib diisi`);
            isValid = false;
        }
    });
    
    // Conditional validation for Proyek
    const jenisKeuangan = $("#jenis_keuangan_tefa_id option:selected").text();
    if (jenisKeuangan === 'Proyek' && !$("#proyek_id_selected").val()) {
        $("#proyek_id_selected").addClass('is-invalid');
        $("#proyek_id_selected_error").text('Pilih proyek terlebih dahulu');
        isValid = false;
    }
    
    // SIMPLE: Validasi kategori hanya jika container visible DAN field required
    if ($("#kategoriTransaksiContainer").is(':visible') && 
        $("#sub_jenis_transaksi_id").prop('required') && 
        !$("#sub_jenis_transaksi_id").val()) {
        
        $("#sub_jenis_transaksi_id").addClass('is-invalid');
        const jenisTransaksi = $("#jenis_transaksi_id option:selected").text();
        const kategoriLabel = jenisTransaksi === 'Pengeluaran' ? 'kategori pengeluaran' : 'kategori pemasukan';
        $("#sub_jenis_transaksi_id_error").text(`Pilih ${kategoriLabel}`);
        isValid = false;
    }
    
    // Rest of validation remains the same...
    const namaTransaksi = $("#nama_transaksi").val();
    if (namaTransaksi && !validateTransactionName(namaTransaksi)) {
        $("#nama_transaksi").addClass('is-invalid');
        $("#nama_transaksi_error").text('Nama transaksi harus berisi 1-255 karakter');
        isValid = false;
    }
    
    const nominal = $("#nominal").val();
    if (nominal) {
        const numValue = parseInt(nominal.replace(/\D/g, ''), 10);
        
        if (isNaN(numValue) || numValue <= 0 || numValue >= 10**13) {
            $("#nominal").addClass('is-invalid');
            $("#nominal_error").text('Nominal harus berupa angka positif dan tidak melebihi batas maksimum');
            isValid = false;
        }
    }
    
    if ($("#file_keuangan_tefa")[0].files && $("#file_keuangan_tefa")[0].files.length > 0) {
        if (!handleFileUpload()) {
            isValid = false;
        }
    }
    
    return isValid;
}

function validateEditForm() {
    let isValid = true;
    
    // Reset validation errors
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_edit_error").addClass('d-none').text('');
    
    // Required fields validation
    const requiredFields = [
        { id: 'edit_jenis_transaksi_id', name: 'Jenis Transaksi' },
        { id: 'edit_tanggal_transaksi', name: 'Tanggal Transaksi' },
        { id: 'edit_jenis_keuangan_tefa_id', name: 'Keperluan Transaksi' },
        { id: 'edit_nama_transaksi', name: 'Nama Transaksi' },
        { id: 'edit_nominal', name: 'Nominal' }
    ];
    
    $.each(requiredFields, function(i, field) {
        if (!$("#" + field.id).val()) {
            $("#" + field.id).addClass('is-invalid');
            $("#" + field.id + "_error").text(`${field.name} wajib diisi`);
            isValid = false;
        }
    });
    
    // Specific validation for transaction name
    const namaTransaksi = $("#edit_nama_transaksi").val();
    if (namaTransaksi && (namaTransaksi.trim().length === 0 || namaTransaksi.trim().length > 255)) {
        $("#edit_nama_transaksi").addClass('is-invalid');
        $("#edit_nama_transaksi_error").text('Nama transaksi harus berisi 1-255 karakter');
        isValid = false;
    }
    
    // Specific validation for nominal
    const nominal = $("#edit_nominal").val();
    if (nominal) {
        const numValue = parseInt(nominal.replace(/\D/g, ''), 10);
        
        if (isNaN(numValue) || numValue <= 0 || numValue >= 10**13) {
            $("#edit_nominal").addClass('is-invalid');
            $("#edit_nominal_error").text('Nominal harus berupa angka positif dan tidak melebihi batas maksimum');
            isValid = false;
        }
    }
    
    const jenisKeuangan = $("#edit_jenis_keuangan_tefa_id option:selected").text();
    if (jenisKeuangan === 'Proyek' && !$("#edit_proyek_id_selected").val()) {
        $("#edit_proyek_id_selected").addClass('is-invalid');
        $("#edit_proyek_id_selected_error").text('Pilih proyek terlebih dahulu');
        isValid = false;
    }
    
    // UPDATED: Validasi kategori dengan cek apakah field required
    const jenisTransaksi = $("#edit_jenis_transaksi_id option:selected").text();
    
    if ($("#edit_kategoriTransaksiContainer").is(':visible')) {
        // Cek apakah field edit_sub_jenis_transaksi_id required
        const isSubKategoriRequired = $("#edit_sub_jenis_transaksi_id").prop('required');
        
        if (isSubKategoriRequired && !$("#edit_sub_jenis_transaksi_id").val()) {
            $("#edit_sub_jenis_transaksi_id").addClass('is-invalid');
            const kategoriLabel = jenisTransaksi === 'Pengeluaran' ? 'kategori pengeluaran' : 'kategori pemasukan';
            $("#edit_sub_jenis_transaksi_id_error").text(`Pilih ${kategoriLabel}`);
            isValid = false;
        }
        // Jika tidak required, biarkan kosong (nullable)
    }
    
    if ($("#edit_file_keuangan_tefa")[0].files && $("#edit_file_keuangan_tefa")[0].files.length > 0) {
        if (!handleEditFileUpload()) {
            isValid = false;
        }
    }
    
    if (!isValid) {
        $("#form_keuangan_tefa_edit_error").removeClass('d-none').text('Silakan periksa kembali formulir Anda.');
    }
    
    return isValid;
}

function validateTransactionName(name) {
    // Minimal 1 karakter, maksimal 255 karakter
    return name.trim().length > 0 && name.trim().length <= 255;
}

// ===== FORM SUBMISSION FUNCTIONS =====

function submitForm() {
    // Get form status
    const isSingle = $("#isSingleKeuanganTefa").val() === '1';
    
    // Debug: Log form data
    console.log("Submit form with mode:", isSingle ? "Single" : "Multiple");
    
    // Get the current keuangan data list and log it for debugging
    const keuanganTefaData = $("#keuangan_tefa_JsonData").val();
    console.log("keuangan_tefa_JsonData before submission:", keuanganTefaData);
    
    // Debug: Check if file exists in form
    const fileInput = document.getElementById('file_keuangan_tefa');
    if (fileInput.files && fileInput.files.length > 0) {
        console.log("File attached in form:", fileInput.files[0].name);
    } else {
        console.log("No file attached in main form");
    }
    
    // For multiple mode, check stored files
    if (!isSingle && window.keuanganTefaFiles) {
        console.log("Stored files:", Object.keys(window.keuanganTefaFiles).length);
    }
    
    // Check if we have items in multiple mode
    if (!isSingle) {
        const itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
        console.log("itemList for multiple mode:", itemList);
        
        if (itemList.length === 0) {
            // Display error if no items in multiple mode
            $("#form_keuangan_tefa_error").removeClass('d-none').addClass('alert-danger')
                .text('Tambahkan minimal satu item ke daftar sebelum menyimpan.');
            return false;
        }
        
        // IMPORTANT: Use FormData for the main form and add the itemList as JSON
        const formData = new FormData($("#formTambahDataKeuanganTefa")[0]);
        
        // Prepare the full request dataset with files
        let hasFileErrors = false;
        
        // Add the JSON data
        formData.set('keuangan_tefa_data', JSON.stringify(itemList));
        formData.set('is_single', '0');
        
        // Add all file objects with their item IDs
        if (window.keuanganTefaFiles) {
            let fileCounter = 0;
            for (const [itemId, fileObj] of Object.entries(window.keuanganTefaFiles)) {
                if (fileObj instanceof File) {
                    formData.append(`file_keuangan_tefa_${itemId}`, fileObj);
                    fileCounter++;
                    console.log(`Added file for item ${itemId}: ${fileObj.name}`);
                }
            }
            console.log(`Total ${fileCounter} files added to request`);
        }
        
        if (hasFileErrors) {
            $("#form_keuangan_tefa_error").removeClass('d-none')
                .addClass('alert-danger')
                .text('Terdapat kesalahan pada file. Silakan periksa kembali.');
            return false;
        }
        
        // Show loading state
        $("#btnSimpanKeuanganTefa").prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        // Log the form data keys for debugging
        console.log("Form data keys:");
        for (let key of formData.keys()) {
            console.log(key);
        }
        
        // Submit the data
        $.ajax({
            url: '/koordinator/keuangan-tefa/store-with-files', // New endpoint that handles multiple files
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                handleSubmitSuccess(response);
            },
            error: function(xhr, status, error) {
                handleSubmitError(xhr, status, error, isSingle);
            }
        });
        
        updateFinancialSummary();
        return true;
    } else {
        // SINGLE MODE - Use the existing code path
        // Prepare form data
        const formData = new FormData($("#formTambahDataKeuanganTefa")[0]);
        
        // Show loading state
        $("#btnSimpanKeuanganTefa").prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        $.ajax({
            url: '/koordinator/keuangan-tefa/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                handleSubmitSuccess(response);
            },
            error: function(xhr, status, error) {
                handleSubmitError(xhr, status, error, isSingle);
            }
        });
        
        updateFinancialSummary();
        return true;
    }
}

function submitEditForm() {
    const formData = new FormData($("#formEditDataKeuanganTefa")[0]);
    const keuanganTefaId = $("#keuangan_tefa_id").val();
    $("#btnSimpanEditKeuanganTefa").prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
    
    $.ajax({
        url: `/koordinator/keuangan-tefa/update/${keuanganTefaId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                swal.successMessage(response.message)
                .then((result) => {
                    if (result.isConfirmed) {
                        $('#modalEditKeuanganTefa').modal('hide');
                        loadDataKeuanganTefa();
                    }
                });
            } else {
                swal.errorMessage(response.message);
            }
            $("#btnSimpanEditKeuanganTefa").prop('disabled', false).text('Simpan Data');
            updateFinancialSummary();
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', xhr.status, xhr.statusText);
            
            try {
                const jsonResponse = JSON.parse(xhr.responseText);
                if (xhr.status === 422 && jsonResponse.errors) {
                    let errorMessage = "Validation errors:<br>";
                    
                    Object.keys(jsonResponse.errors).forEach(key => {
                        const fieldName = key.replace('edit_', '');
                        errorMessage += `- ${fieldName}: ${jsonResponse.errors[key]}<br>`;
                    });
                    
                    $("#form_keuangan_tefa_edit_error").removeClass('d-none')
                        .html(errorMessage);
                } else {
                    swal.errorMessage('An error occurred while saving data.', jsonResponse.message || error);
                }
            } catch (e) {
                swal.errorMessage('An unexpected error occurred. Please try again later.', e);
            }
            $("#btnSimpanEditKeuanganTefa").prop('disabled', false).text('Simpan Data');
        }
    });
}

function handleSubmitSuccess(response) {
    console.log("=== SUBMIT SUCCESS ===");
    
    // Reset form dengan enhanced function (termasuk set tanggal hari ini)
    resetFormTambahDataAfterSave();
    
    // Refresh data table
    loadDataKeuanganTefa();
    
    // Update financial summary
    updateFinancialSummary();
    
    // Show success message dan tunggu user klik OK, baru tutup modal
    swal.successMessage('Data keuangan TEFA berhasil disimpan.')
        .then((result) => {
            if (result.isConfirmed) {
                console.log("User clicked OK - closing modal");
                // Tutup modal setelah user klik OK
                $("#modalTambahKeuanganTefa").modal('hide');
            }
        });
    
    console.log("Submit success completed - waiting for user to click OK");
}


function handleSubmitError(xhr, status, error, isSingle) {
    // Reset button state to normal
    $("#btnSimpanKeuanganTefa").prop('disabled', false)
        .html('Simpan Data');
    
    // Handle various error responses
    if (xhr.status === 422) {
        // Validation errors
        const errors = xhr.responseJSON.errors;
        
        // Clear previous error messages
        $(".is-invalid").removeClass('is-invalid');
        
        // Display errors on form fields
        for (const field in errors) {
            const errorMsg = errors[field][0];
            $(`#${field}`).addClass('is-invalid');
            $(`#${field}_error`).text(errorMsg);
        }
        
        // Show general error message
        $("#form_keuangan_tefa_error")
            .removeClass('d-none')
            .addClass('alert-danger')
            .text('Terdapat kesalahan pada form. Silakan periksa kembali.');
    } else {
        // Server error or other issues
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.',
            confirmButtonColor: '#dc3545'
        });
    }
}

// ===== UTILITY FUNCTIONS =====

function setupNominalFormatting(selector) {
    // Remove any existing handler first to prevent duplicates
    $(selector).off('input');
    
    // Add the input handler
    $(selector).on('input', function() {
        // Store cursor position before formatting
        const cursorPos = this.selectionStart;
        const originalLength = this.value.length;
        
        // Remove all non-digit characters
        let value = $(this).val().replace(/\D/g, '');
        
        if (value === '') {
            $(this).val('');
            return;
        }
        
        // Remove leading zeros
        value = value.replace(/^0+/, '') || '0';
        
        // Format with thousands separator (dot/period)
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        // Update the field value
        $(this).val(value);
        
        // Calculate new cursor position 
        // (adjust for added/removed thousand separator dots)
        const newLength = value.length;
        const newCursorPos = cursorPos + (newLength - originalLength);
        
        // Set cursor position - handle situation where position may be negative
        setTimeout(() => {
            const position = Math.max(0, Math.min(newCursorPos, newLength));
            this.setSelectionRange(position, position);
        }, 0);
        
        console.log("Formatted nominal:", value);
    });
    
    // Also handle paste events
    $(selector).on('paste', function(e) {
        // Get pasted data
        let pastedData = e.originalEvent.clipboardData.getData('text');
        
        // Only keep digits
        pastedData = pastedData.replace(/\D/g, '');
        
        // Cancel default paste
        e.preventDefault();
        
        // Insert the cleaned digits
        document.execCommand('insertText', false, pastedData);
    });
    
    // Also handle keydown to prevent unwanted characters
    $(selector).on('keydown', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right, up, down
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // Let it happen, don't do anything
            return;
        }
        
        // Ensure that it's a number and stop the keypress if not
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && 
            (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
}

function limitWordsWithTooltip(text, wordLimit = 3) {
    if (!text) return '';
    
    const words = text.trim().split(/\s+/);
    if (words.length <= wordLimit) {
        return text;
    }
    
    const limitedText = words.slice(0, wordLimit).join(' ') + '...';
    return `<span title="${text}">${limitedText}</span>`;
}

function formatCurrency(amount) {
    let cleanAmount = String(amount).replace(/\D/g, '');
    
    // Jika kosong, kembalikan 0
    if (!cleanAmount) {
        return '0';
    }
    
    // Format dengan separator ribuan tanpa desimal
    return cleanAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

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

// ===== FILE HANDLING FUNCTIONS =====

function handleFileUpload() {
    const fileInput = document.getElementById('file_keuangan_tefa');
    
    // Reset error state
    $(fileInput).removeClass('is-invalid');
    $("#file_keuangan_tefa_error").text('');
    
    // Check if file is selected
    if (!fileInput.files || fileInput.files.length === 0) {
        console.log("No file selected");
        return true; // No file is fine since it's optional
    }
    
    const file = fileInput.files[0];
    console.log("File details:", {
        name: file.name,
        size: file.size,
        type: file.type
    });
    
    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        console.error("File too large:", file.size);
        $(fileInput).addClass('is-invalid');
        $("#file_keuangan_tefa_error").text('Ukuran file maksimal 10MB');
        return false;
    }
    
    // Validate file extension - mendahulukan cek ekstensi seperti di DataDokumenPenunjangController
    const fileName = file.name.toLowerCase();
    const extension = fileName.split('.').pop();
    const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png'];
    
    console.log("File validation:", {
        extension: extension,
        isAllowedExtension: allowedExtensions.includes(extension)
    });
    
    if (!allowedExtensions.includes(extension)) {
        $(fileInput).addClass('is-invalid');
        $("#file_keuangan_tefa_error").text('Format file tidak didukung. Format yang diizinkan: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG');
        return false;
    }
    
    return true;
}

function handleEditFileUpload() {
    const fileInput = document.getElementById('edit_file_keuangan_tefa');
    
    // Reset error state
    $(fileInput).removeClass('is-invalid');
    $("#edit_file_keuangan_tefa_error").text('');
    
    // Check if file is selected
    if (!fileInput.files || fileInput.files.length === 0) {
        return true; // No file is fine since it's optional
    }
    
    const file = fileInput.files[0];
    
    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        $(fileInput).addClass('is-invalid');
        $("#edit_file_keuangan_tefa_error").text('Ukuran file maksimal 10MB');
        return false;
    }
    
    // Validate file extension
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

function showFilePreview() {
    const fileInput = document.getElementById('file_keuangan_tefa');
    const previewContainer = document.getElementById('file_preview_container') || 
        $('<div id="file_preview_container" class="mt-2"></div>').insertAfter(fileInput).get(0);
    
    // Clear previous preview
    $(previewContainer).empty();
    
    if (!fileInput.files || fileInput.files.length === 0) {
        console.log('No file selected for preview');
        return;
    }
    
    const file = fileInput.files[0];
    console.log('Preview file:', file.name, file.type, file.size);
    
    const extension = file.name.toLowerCase().split('.').pop();
    const fileSize = formatFileSize(file.size);
    
    // Create preview container with better styling
    const previewHtml = `
        <div class="file-preview-card border rounded p-3 mt-2 bg-light">
            <div class="d-flex align-items-center">
                <div class="file-icon me-3">
                    ${getFileIconHtml(extension)}
                </div>
                <div class="file-details flex-grow-1">
                    <h6 class="mb-1 text-truncate" title="${file.name}">${file.name}</h6>
                    <small class="text-muted">${fileSize} • ${extension.toUpperCase()}</small>
                </div>
                <div class="file-actions">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFilePreview()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
            <div id="file-preview-content" class="mt-3"></div>
        </div>
    `;
    
    $(previewContainer).html(previewHtml);
    
    // Show image preview for image files
    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imgPreview = `
                <div class="image-preview text-center">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px; max-width: 100%;" alt="Preview">
                </div>
            `;
            $('#file-preview-content').html(imgPreview);
        }
        
        reader.readAsDataURL(file);
    } else {
        // For non-image files, show file type info
        const fileInfo = `
            <div class="file-info-preview text-center text-muted">
                <i class="bi bi-file-earmark fs-1"></i>
                <p class="mb-0 small">Preview tidak tersedia untuk tipe file ini</p>
            </div>
        `;
        $('#file-preview-content').html(fileInfo);
    }
}


function showEditFilePreview() {
    const fileInput = document.getElementById('edit_file_keuangan_tefa');
    const previewContainer = document.getElementById('edit_file_preview_container') || 
        $('<div id="edit_file_preview_container" class="mt-2"></div>').insertAfter(fileInput).get(0);
    
    // Only show new file preview, don't clear existing preview if no new file
    if (!fileInput.files || fileInput.files.length === 0) {
        return;
    }
    
    const file = fileInput.files[0];
    const extension = file.name.toLowerCase().split('.').pop();
    const fileSize = formatFileSize(file.size);
    
    // Clear existing preview and show "New file to upload:"
    const newFilePreview = `
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
    
    $(previewContainer).html(newFilePreview);
    
    // Show image preview for image files
    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imgPreview = `
                <div class="image-preview text-center">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px; max-width: 100%;" alt="Preview">
                </div>
            `;
            $('#edit-file-preview-content').html(imgPreview);
        }
        
        reader.readAsDataURL(file);
    }
}

function showExistingFilePreview(fileUrl, fileName) {
    const fileExtension = fileName.toLowerCase().split('.').pop();
    const isImage = ['jpg', 'jpeg', 'png'].includes(fileExtension);
    
    const existingFilePreview = `
        <div class="mt-2 p-2 border rounded">
            <div class="d-flex align-items-center">
                    <div>
                        <small class="text-muted">File saat ini:</small><br>
                        <a href="${fileUrl}" target="_blank">
                            ${fileName}</a>
                    </div>
            </div>
            <small class="text-muted">Pilih file baru jika ingin mengganti</small>
        </div>
    `;
    
    const previewContainer = $('<div id="edit_file_preview_container" class="mt-2"></div>').insertAfter('#edit_file_keuangan_tefa').get(0);
    
    $(previewContainer).html(existingFilePreview);
    
    // Show image preview for existing image files

}

function getFileIconHtml(extension) {
    const iconData = getFileIconData(extension);
    return `<i class="${iconData.icon} fs-1" style="color: ${iconData.color};"></i>`;
}

function getFileIconData(extension) {
    switch (extension) {
        case 'pdf':
            return { icon: 'bi bi-file-earmark-pdf', color: '#dc3545' };
        case 'doc':
        case 'docx':
            return { icon: 'bi bi-file-earmark-word', color: '#2b579a' };
        case 'xls':
        case 'xlsx':
            return { icon: 'bi bi-file-earmark-excel', color: '#217346' };
        case 'ppt':
        case 'pptx':
            return { icon: 'bi bi-file-earmark-ppt', color: '#d24726' };
        case 'jpg':
        case 'jpeg':
        case 'png':
            return { icon: 'bi bi-file-earmark-image', color: '#6f42c1' };
        default:
            return { icon: 'bi bi-file-earmark', color: '#6c757d' };
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showFilePreviewInTable(fileUrl, fileName) {
    if (!fileUrl || !fileName) {
        return '<span class="text-muted">-</span>';
    }
    
    const fileExtension = fileName.toLowerCase().split('.').pop();
    const iconData = getFileIconData(fileExtension);
    
    return `
        <div class="d-flex align-items-center">
            <i class="${iconData.icon} me-2" style="color: ${iconData.color};"></i>
            <div>
                <a href="${fileUrl}" target="_blank" class="text-decoration-none small">
                    ${fileName.length > 15 ? fileName.substring(0, 15) + '...' : fileName}
                </a>
                <br>
                <small class="text-muted">${fileExtension.toUpperCase()}</small>
            </div>
        </div>
    `;
}

function clearFilePreview() {
    document.getElementById('file_keuangan_tefa').value = '';
    $('#file_preview_container').empty();
    console.log('File preview cleared');
}

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadDataKeuanganTefa(1, {});
    initializeForm();
    loadJenisTransaksi();
    loadJenisKeuangan();
    updateFinancialSummary();

    $(document).on('click', '.keuangan-pagination-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadDataKeuanganTefa(page);
    });

    $('#modalTambahKeuanganTefa, #modalEditKeuanganTefa').on('shown.bs.modal', function() {
        initializeNominalFormatting();
    });

    $("#file_keuangan_tefa").prop('required', false);

    $("#file_keuangan_tefa").change(function() {
        if (handleFileUpload()) {
            showFilePreview();
        }
    });

    $("#edit_file_keuangan_tefa").change(function() {
        if (handleEditFileUpload()) {
            showEditFilePreview();
        }
    });

    $("#formTambahDataKeuanganTefa").submit(function(e) {
        e.preventDefault();
        const isSingle = $("#isSingleKeuanganTefa").val() === '1';
        
        // If single mode and has file, validate file
        if (isSingle && $("#file_keuangan_tefa")[0].files.length > 0) {
            const fileValid = handleFileUpload();
            if (!fileValid) {
                $("#form_keuangan_tefa_error").removeClass('d-none')
                    .text('Terdapat kesalahan pada file. Silakan periksa kembali.');
                return;
            }
        }
        
        if (!isSingle) {
            const itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
            if (itemList.length > 0) {
                submitForm();
                return;
            }
        }
        
        if (validateForm()) {
            submitForm();
        }
    });
    
    // ===== EVENT HANDLER UNTUK FORM TAMBAH DATA =====
    $(document).on('change', '#jenis_transaksi_id', function() {
        console.log("Form tambah - jenis_transaksi_id changed:", $(this).val());
        
        // Panggil kedua fungsi toggle untuk memastikan tampilan yang benar
        toggleKategoriTransaksiContainer();
        toggleProyekContainer();
    });
    
    $(document).on('change', '#jenis_keuangan_tefa_id', function() {
        console.log("Form tambah - jenis_keuangan_tefa_id changed:", $(this).val());
        
        // Panggil kedua fungsi toggle untuk memastikan tampilan yang benar  
        toggleKategoriTransaksiContainer();
        toggleProyekContainer();
        
        // Load proyek jika jenis keuangan adalah Proyek
        const jenisKeuanganText = $(this).find('option:selected').text();
        if (jenisKeuanganText === 'Proyek') {
            loadProyek();
        }
    });


    // ===== EVENT HANDLER UNTUK FORM EDIT =====
    $(document).on('change', '#edit_jenis_transaksi_id', function() {
        console.log("Form edit - edit_jenis_transaksi_id changed:", $(this).val());
        
        // Panggil kedua fungsi toggle untuk memastikan tampilan yang benar
        toggleEditKategoriTransaksiContainer();
        toggleEditProyekContainer();
    });
    
    $(document).on('change', '#edit_jenis_keuangan_tefa_id', function() {
        console.log("Form edit - edit_jenis_keuangan_tefa_id changed:", $(this).val());
        
        // Panggil kedua fungsi toggle untuk memastikan tampilan yang benar
        toggleEditKategoriTransaksiContainer();
        toggleEditProyekContainer();
        
        // Load proyek jika jenis keuangan adalah Proyek
        const jenisKeuanganText = $(this).find('option:selected').text();
        if (jenisKeuanganText === 'Proyek') {
            loadEditProyek();
        }
    });

    $("#btnTambahkanKeDaftarKeuanganTefa").off('click').on('click', function() {
        // Store original button text
        const originalBtnText = $(this).text();
        
        // Show loading state
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menambahkan...');
        
        // Add to list with validation and capture the return value
        const success = addToList();
        
        // Restore button state
        $(this).prop('disabled', false).text(originalBtnText);
        
        // If validation failed, focus on the first invalid field
        if (!success) {
            setTimeout(function() {
                $('.is-invalid:first').focus();
            }, 100);
        }
    });
    
    $(document).on('click', '.btn-remove-item', function() {
        const itemId = $(this).data('id');
        removeFromList(itemId);
    });

    $(document).on('click', '.btn-action-detail-keuangan', function() {
        const keuanganTefaId = $(this).data('id');
        loadKeuanganTefaForEdit(keuanganTefaId);
    });
  
    $("#formEditDataKeuanganTefa").submit(function(e) {
        e.preventDefault();
        
        if (validateEditForm()) {
            submitEditForm();
        }
    });

    $('#modalTambahKeuanganTefa').on('shown.bs.modal', function() {
        console.log("Modal tambah keuangan tefa opened");
        
        // Set tanggal ke hari ini jika belum ada nilai
        if (!$("#tanggal_transaksi").val()) {
            const today = new Date().toISOString().split('T')[0];
            $("#tanggal_transaksi").val(today);
            console.log("Set initial date to today:", today);
        }
        
        // Initialize formatting dan Select2
        initializeNominalFormatting();
        initializeSelect2();
    });

    
    $("#formTambahDataKeuanganTefa input, #formTambahDataKeuanganTefa select, #formTambahDataKeuanganTefa textarea").on('input change', function() {
        $(this).removeClass('is-invalid');
        const errorId = $(this).attr('id') + "_error";
        $("#" + errorId).text('');
    });
    
    $("#proyek_id_selected, #sub_jenis_transaksi_id").on('select2:select', function() {
        $(this).removeClass('is-invalid');
        const errorId = $(this).attr('id') + "_error";
        $("#" + errorId).text('');
    });

    $(document).off('click', '.btn-remove-item').on('click', '.btn-remove-item', function() {
        const itemId = $(this).data('id');
        console.log("Remove button clicked for item ID:", itemId);
        console.log("Item ID type:", typeof itemId);
        removeFromList(itemId);
    });
    
    // Format nominal in edit form
    $("#edit_nominal").on('input', function() {
        // Remove all non-digit characters
        let value = $(this).val().replace(/\D/g, '');
        
        if (value === '') {
            $(this).val('');
            return;
        }
        
        // Remove leading zeros
        value = value.replace(/^0+/, '') || '0';
        
        // Format with thousands separator
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        
        $(this).val(value);
    });

    initializeFilters();
    
    $("#btnToggleFilter").click(function() {
        $("#filterContainer").toggleClass("filter-collapsed");
        localStorage.setItem('keuanganTefaFilterVisible', !$("#filterContainer").hasClass("filter-collapsed"));
    });
    
    // Reset filter form
    $("#btnResetFilter").click(function() {
        resetFilters();
    });
    
    // Apply filter
    $("#formFilterKeuanganTefa").submit(function(e) {
        e.preventDefault();
        applyFilters();
    });
    
    // Format nominal inputs with thousand separator
    $("#filter_nominal_min, #filter_nominal_max").on('input', function() {
        formatNominalFilter($(this));
    });
    
    // Load initial state
    if (localStorage.getItem('keuanganTefaFilterVisible') === 'false') {
        $("#filterContainer").addClass("filter-collapsed");
    }
    
    // Try to load saved filters
    loadSavedFilters();

    $('#modalTambahKeuanganTefa').on('hidden.bs.modal', function() {
        console.log("Modal tambah keuangan tefa closed - resetting form");
        
        // Reset form dengan tanggal hari ini
        resetFormTambahDataAfterSave();
        
        // Destroy Select2 instances
        if ($.fn.select2) {
            $('#proyek_id_selected, #sub_jenis_transaksi_id').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });
        }
    });


    $('#modalEditKeuanganTefa').on('hidden.bs.modal', function() {
        if ($.fn.select2) {
            $('#edit_proyek_id_selected, #edit_sub_jenis_transaksi_id').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });
        }
    });

    $(document).on('click', '.keuangan-pagination-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const filters = $(this).data('filters') || {};
        loadDataKeuanganTefa(page, filters);
    });

    // ===== EVENT HANDLER UNTUK FILTER =====
    $(document).on('change', '#filter_jenis_keuangan', function() {
        console.log("Filter - jenis_keuangan changed to:", $(this).val());
        
        // Panggil kedua fungsi toggle untuk memastikan tampilan yang benar
        toggleFilterProyekContainer();
        toggleFilterKategoriTransaksiContainer();
    });

    $(document).on('change', '#filter_jenis_transaksi', function() {
        console.log("Filter - jenis_transaksi changed to:", $(this).val());
        
        // Panggil fungsi toggle untuk memastikan tampilan yang benar
        toggleFilterKategoriTransaksiContainer();
    });

    initializeFilterSelect2();
    
    // Tambahkan kode ini setelah event handler btnResetFilter
    $("#btnResetFilter").off('click').on('click', function() {
        resetFilters();
        // Re-initialize Select2 after reset
        setTimeout(initializeFilterSelect2, 100);
    });

    $(document).on('click', '.btn-action-delete', function() {
        const id = $(this).data('id');
        deleteKeuanganTefa(id);
    });

    // ===== DEBUGGING =====
    console.log("Container kategoriTransaksiContainer exists:", $("#kategoriTransaksiContainer").length);
    console.log("Container edit_kategoriTransaksiContainer exists:", $("#edit_kategoriTransaksiContainer").length);
    console.log("Container filterKategoriTransaksiContainer exists:", $("#filterKategoriTransaksiContainer").length);
    
    // TEST MANUAL UNTUK FILTER
    window.testFilterKategori = function() {
        console.log("=== TESTING FILTER KATEGORI ===");
        console.log("Container exists:", $("#filterKategoriTransaksiContainer").length);
        console.log("Current visibility:", $("#filterKategoriTransaksiContainer").is(':visible'));
        
        // Manual show
        $("#filterKategoriTransaksiContainer").show();
        $("label[for='filter_sub_jenis_transaksi']").text('Test Kategori Filter');
        
        console.log("After manual show:", $("#filterKategoriTransaksiContainer").is(':visible'));
        console.log("=== END TEST ===");
    };
    
    console.log("Run 'testFilterKategori()' in console to test filter");
});

// ===== UTILITY FUNCTIONS =====

function updateSelect2Placeholder(selector, placeholder, parent) {
    if ($.fn.select2 && $(selector).hasClass('select2-hidden-accessible')) {
        $(selector).select2('destroy');
        
        const select2Options = {
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: true
        };
        
        if (parent) {
            select2Options.dropdownParent = $(parent);
        }
        
        $(selector).select2(select2Options);
    }
}

function updateFilterLabels() {
    const jenisTransaksi = $("#filter_jenis_transaksi").val();
    
    if (jenisTransaksi === 'Pengeluaran') {
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Pengeluaran');
    } else if (jenisTransaksi === 'Pemasukan') {
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Pemasukan');
    } else {
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
    }
}

function applyFilters() {
    // Validate date range
    const startDate = $("#filter_tanggal_mulai").val();
    const endDate = $("#filter_tanggal_akhir").val();
    
    // Only validate if both dates are provided
    if (startDate && endDate) {
        const startDateObj = new Date(startDate);
        const endDateObj = new Date(endDate);
        
        // Compare dates
        if (endDateObj < startDateObj) {
            // Show SweetAlert notification
            swal.errorMessage('Tanggal Akhir tidak boleh lebih awal dari Tanggal Mulai');
            return; // Stop filter application
        }
    }
    
    // Collect filter values
    const filters = {
        tanggal_mulai: startDate,
        tanggal_akhir: endDate,
        jenis_transaksi: $("#filter_jenis_transaksi").val(),
        jenis_keuangan: $("#filter_jenis_keuangan").val(),
        nama_transaksi: $("#filter_nama_transaksi").val(),
        proyek_id: $("#filter_proyek").val(),
        sub_jenis_transaksi_id: $("#filter_sub_jenis_transaksi").val()
    };
    
    // Log the filters for debugging
    console.log("Applied filters:", filters);
    
    // Save filters for future use
    saveFiltersToLocalStorage();
    
    // Load data with filters
    loadDataKeuanganTefa(1, filters);
}

function saveFiltersToLocalStorage() {
    // Save all filter values to localStorage
    $('#formFilterKeuanganTefa').find('input, select').each(function() {
        if ($(this).attr('id')) {
            localStorage.setItem($(this).attr('id'), $(this).val());
        }
    });
    
    // Save filter application timestamp
    localStorage.setItem('keuanganTefaFiltersApplied', new Date().getTime());
}

function resetFilters() {
    console.log("Starting comprehensive filter reset...");
    
    // 1. Reset semua form fields
    $("#formFilterKeuanganTefa")[0].reset();
    
    // 2. Reset dan sembunyikan container proyek
    console.log("Resetting proyek container...");
    $("#filterProyekContainer").hide();
    resetFilterProyekDropdown();
    
    // 3. Reset dan sembunyikan container kategori transaksi
    console.log("Resetting kategori container...");
    $("#filterKategoriTransaksiContainer").hide();
    resetFilterSubJenisTransaksiDropdown();
    
    // 4. Clear saved filters dari localStorage
    clearSavedFilters();
    
    // 5. Reload data tanpa filter
    loadDataKeuanganTefa(1, {});
    
    console.log("Filter reset completed successfully");
}

function setTanggalHariIni() {
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
    console.log("Tanggal set to today:", today);
}

// Fungsi untuk format tanggal Indonesia (untuk display)
function formatTanggalIndonesia(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long', 
        day: 'numeric'
    });
}

// ===== TOGGLE FUNCTIONS - FORM TAMBAH DATA =====

function toggleKategoriTransaksiContainer() {
    const jenisTransaksi = $("#jenis_transaksi_id option:selected").text();
    const jenisKeuangan = $("#jenis_keuangan_tefa_id option:selected").text();
    
    console.log("toggleKategoriTransaksiContainer called, jenisTransaksi:", jenisTransaksi, "jenisKeuangan:", jenisKeuangan);
    
    // Hanya coba load jika KEDUA field sudah dipilih dan jenis transaksi valid
    if ((jenisTransaksi === 'Pengeluaran' || jenisTransaksi === 'Pemasukan') && 
        (jenisKeuangan === 'Proyek' || jenisKeuangan === 'Non Proyek')) {
        
        console.log("Attempting to load sub kategori for", jenisTransaksi);
        
        // PENTING: Load sub jenis transaksi dan biarkan fungsi loadSubJenisTransaksi yang menentukan show/hide
        const jenisTransaksiId = $("#jenis_transaksi_id").val();
        const jenisKeuanganId = $("#jenis_keuangan_tefa_id").val();
        
        if (jenisTransaksiId && jenisKeuanganId) {
            loadSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId);
        }
        
    } else {
        console.log("Hiding kategori - not both fields selected");
        $("#kategoriTransaksiContainer").hide();
        $("#sub_jenis_transaksi_id").prop('required', false);
        resetSubJenisTransaksiDropdown();
    }
}


// ===== TOGGLE FUNCTIONS - FORM EDIT =====

function toggleEditKategoriTransaksiContainer() {
    const jenisTransaksi = $("#edit_jenis_transaksi_id option:selected").text();
    const jenisKeuangan = $("#edit_jenis_keuangan_tefa_id option:selected").text();
    
    console.log("toggleEditKategoriTransaksiContainer called, jenisTransaksi:", jenisTransaksi, "jenisKeuangan:", jenisKeuangan);

    // Hanya coba load jika KEDUA field sudah dipilih dan jenis transaksi valid
    if ((jenisTransaksi === 'Pengeluaran' || jenisTransaksi === 'Pemasukan') && 
        (jenisKeuangan === 'Proyek' || jenisKeuangan === 'Non Proyek')) {
        
        console.log("Attempting to load edit sub kategori for", jenisTransaksi);
        
        // PENTING: Load sub jenis transaksi dan biarkan fungsi loadEditSubJenisTransaksi yang menentukan show/hide
        const jenisTransaksiId = $("#edit_jenis_transaksi_id").val();
        const jenisKeuanganId = $("#edit_jenis_keuangan_tefa_id").val();
        
        if (jenisTransaksiId && jenisKeuanganId) {
            loadEditSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId);
        }
        
    } else {
        console.log("Hiding edit kategori - not both fields selected");
        $("#edit_kategoriTransaksiContainer").hide();
        $("#edit_sub_jenis_transaksi_id").prop('required', false);
        resetEditSubJenisTransaksiDropdown();
    }
}


// ===== TOGGLE FUNCTIONS - FILTER =====

function toggleFilterKategoriTransaksiContainer() {
    const jenisTransaksi = $("#filter_jenis_transaksi").val();
    const jenisKeuangan = $("#filter_jenis_keuangan").val();
    
    console.log("Toggle filter kategori, jenisTransaksi:", jenisTransaksi, "jenisKeuangan:", jenisKeuangan);
    
    // Hanya coba load jika KEDUA field sudah dipilih
    if (jenisTransaksi && jenisKeuangan && 
        (jenisTransaksi === 'Pengeluaran' || jenisTransaksi === 'Pemasukan')) {
        
        console.log("Attempting to load filter sub kategori");
        
        // PENTING: Load sub jenis transaksi dan biarkan fungsi loadSubJenisTransaksiForFilter yang menentukan show/hide
        loadSubJenisTransaksiForFilter();
        
    } else {
        console.log("Hiding filter kategori container - not both fields selected");
        $("#filterKategoriTransaksiContainer").hide();
        
        // Reset value
        $("#filter_sub_jenis_transaksi").val('');
        
        // Reset label ke default
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
    }
}


function toggleProyekContainer() {
    const jenisKeuangan = $("#jenis_keuangan_tefa_id option:selected").text();
    
    if (jenisKeuangan === 'Proyek') {
        $("#proyekContainer").show();
        $("#proyek_id_selected").prop('required', true);
    } else {
        $("#proyekContainer").hide();
        $("#proyek_id_selected").prop('required', false);
    }
}

function toggleEditProyekContainer() {
    const jenisKeuangan = $("#edit_jenis_keuangan_tefa_id option:selected").text();
    
    if (jenisKeuangan === 'Proyek') {
        $("#edit_proyekContainer").show();
        $("#edit_proyek_id_selected").prop('required', true);
    } else {
        $("#edit_proyekContainer").hide();
        $("#edit_proyek_id_selected").prop('required', false);
    }
}

function toggleFilterProyekContainer() {
    const jenisKeuangan = $("#filter_jenis_keuangan").val();
    
    console.log("Toggle filter proyek container, jenisKeuangan:", jenisKeuangan);
    
    if (jenisKeuangan === 'Proyek') {
        console.log("Showing filter proyek container");
        $("#filterProyekContainer").show();
        
        // Load proyek options jika belum ada
        if ($("#filter_proyek option").length <= 1) {
            loadProyekForFilter();
        }
    } else {
        console.log("Hiding filter proyek container");
        $("#filterProyekContainer").hide();
        
        // Reset proyek selection saat disembunyikan
        $("#filter_proyek").val('');
        
        // Jika Select2 active, clear selection
        if ($.fn.select2 && $('#filter_proyek').hasClass('select2-hidden-accessible')) {
            $('#filter_proyek').val(null).trigger('change');
        }
    }
}

// ===== INITIALIZATION FUNCTIONS =====

function initializeSelect2() {
    // Initialize for add form
    if ($.fn.select2) {
        try {
            // First destroy any existing instances to prevent duplicates
            $('#proyek_id_selected, #sub_jenis_transaksi_id').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            
            $('#edit_proyek_id_selected, #edit_sub_jenis_transaksi_id').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
            
            // For regular form
            $('#proyek_id_selected').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Proyek',
                allowClear: true,
                dropdownParent: $('#modalTambahKeuanganTefa')
            });
            
            $('#sub_jenis_transaksi_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true,
                dropdownParent: $('#modalTambahKeuanganTefa')
            });
            
            // For edit form
            $('#edit_proyek_id_selected').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Proyek',
                allowClear: true,
                dropdownParent: $('#modalEditKeuanganTefa')
            });
            
            $('#edit_sub_jenis_transaksi_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true,
                dropdownParent: $('#modalEditKeuanganTefa')
            });
        } catch (e) {
            console.warn('Error initializing Select2:', e);
        }
    } else {
        console.warn('Select2 library not loaded. Searchable dropdowns will not be available.');
    }
}

function initializeNominalFormatting() {
    setupNominalFormatting("#nominal");
    setupNominalFormatting("#edit_nominal");
}

function initializeFilterSelect2() {
    // Initialize Select2 for the project filter dropdown
    if ($.fn.select2) {
        try {
            // First destroy any existing instances to prevent duplicates
            if ($('#filter_proyek').hasClass('select2-hidden-accessible')) {
                $('#filter_proyek').select2('destroy');
            }
            
            if ($('#filter_sub_jenis_transaksi').hasClass('select2-hidden-accessible')) {
                $('#filter_sub_jenis_transaksi').select2('destroy');
            }
            
            // Initialize Select2 for project filter
            $('#filter_proyek').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Proyek',
                allowClear: true
            });
            
            // Initialize Select2 for sub jenis transaksi filter
            $('#filter_sub_jenis_transaksi').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true
            });
        } catch (e) {
            console.warn('Error initializing Select2 for filters:', e);
        }
    } else {
        console.warn('Select2 library not loaded. Searchable dropdowns will not be available.');
    }
}

function initializeFilters() {
    // Initialize date filters with default values (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    // Format dates as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    // Set default date range (can be commented out if not desired)
    $("#filter_tanggal_mulai").val(formatDate(firstDay));
    $("#filter_tanggal_akhir").val(formatDate(lastDay));
    
    // Load project data for the filter dropdown
    loadProyekForFilter();
    toggleFilterProyekContainer();
    toggleFilterKategoriTransaksiContainer();
}

function initializeForm() {
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
    
    $("#keuangan_tefa_JsonData").val('[]');
    $("#isSingleKeuanganTefa").val('1');
}

// ===== AJAX FUNCTIONS =====

function loadJenisKeuangan() {
    $.ajax({
        url: '/koordinator/keuangan-tefa/jenis-keuangan-tefa',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#jenis_keuangan_tefa_id').html('<option value="" disabled selected>Loading...</option>');
        },
        success: function(response) {
            console.log('Response from jenis-keuangan-tefa:', response);
            
            if (response.success && response.results) {
                let options = '<option value="" disabled selected>Pilih Keperluan Transaksi</option>';
                $.each(response.results, function(key, item) {
                    options += `<option value="${item.jenis_keuangan_tefa_id}">${item.nama_jenis_keuangan_tefa}</option>`;
                });
                $('#jenis_keuangan_tefa_id').html(options);
            } else {
                console.error('Error loading jenis keuangan:', response.message || 'Unknown error');
                $('#jenis_keuangan_tefa_id').html('<option value="" disabled selected>Error loading data</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error details:', {
                url: '/koordinator/keuangan-tefa/jenis-keuangan-tefa',
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#jenis_keuangan_tefa_id').html('<option value="" disabled selected>Error loading data</option>');
        }
    });
}

function loadJenisTransaksi() {
    $.ajax({
        url: '/koordinator/keuangan-tefa/jenis-transaksi',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#jenis_transaksi_id').html('<option value="" disabled selected>Loading...</option>');
        },
        success: function(response) {
            console.log('Response from jenis-transaksi API:', response);
            
            if (response.success && response.data) {
                let options = '<option value="" disabled selected>Pilih Jenis Transaksi</option>';
                $.each(response.data, function(key, item) {
                    options += `<option value="${item.jenis_transaksi_id}">${item.nama_jenis_transaksi}</option>`;
                });
                $('#jenis_transaksi_id').html(options);
            } else {
                console.error('Error loading jenis transaksi:', response.message || 'Unknown error');
                $('#jenis_transaksi_id').html('<option value="" disabled selected>Error loading data</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#jenis_transaksi_id').html('<option value="" disabled selected>Error loading data</option>');
        }
    });
}

function loadProyek() {
    $.ajax({
        url: '/koordinator/keuangan-tefa/data-proyek',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#proyek_id_selected').html('<option value="" disabled selected>Loading...</option>');
            
            // Destroy previous Select2 instance if exists
            try {
                if ($.fn.select2 && $('#proyek_id_selected').hasClass('select2-hidden-accessible')) {
                    $('#proyek_id_selected').select2('destroy');
                }
            } catch(e) {
                console.warn('Error destroying Select2:', e);
            }
        },
        success: function(response) {
            console.log('Response from proyek API:', response);
            
            if (response.success && response.data) {
                let options = '<option value="" disabled selected>Pilih Proyek</option>';
                $.each(response.data, function(key, item) {
                    options += `<option value="${item.proyek_id}">${item.nama_proyek}</option>`;
                });
                $('#proyek_id_selected').html(options);
                
                // Initialize Select2 after populating options
                try {
                    if ($.fn.select2) {
                        $('#proyek_id_selected').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: 'Pilih Proyek',
                            allowClear: true,
                            dropdownParent: $('#modalTambahKeuanganTefa')
                        });
                    }
                } catch(e) {
                    console.warn('Error initializing Select2 for proyek:', e);
                }
            } else {
                console.error('Error loading proyek:', response.message || 'Unknown error');
                $('#proyek_id_selected').html('<option value="" disabled selected>Error loading data</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#proyek_id_selected').html('<option value="" disabled selected>Error loading data</option>');
        }
    });
}

function loadSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId) {
    $.ajax({
        url: '/koordinator/keuangan-tefa/get-sub-jenis-transaksi',
        type: 'GET',
        data: {
            jenis_transaksi_id: jenisTransaksiId,
            jenis_keuangan_tefa_id: jenisKeuanganId
        },
        dataType: 'json',
        beforeSend: function() {
            $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Loading...</option>');
            
            try {
                if ($.fn.select2 && $('#sub_jenis_transaksi_id').hasClass('select2-hidden-accessible')) {
                    $('#sub_jenis_transaksi_id').select2('destroy');
                }
            } catch(e) {
                console.warn('Error destroying Select2:', e);
            }
        },
        success: function(response) {
            console.log('Response from get-sub-jenis-transaksi:', response);
            
            if (response.success && response.results && response.results.length > 0) {
                // Ada data - tampilkan container dan populate dropdown
                console.log('Found sub categories:', response.results.length);
                
                $("#kategoriTransaksiContainer").show();
                
                // Determine placeholder based on current jenis transaksi
                const jenisTransaksi = $("#jenis_transaksi_id option:selected").text();
                const placeholder = jenisTransaksi === 'Pengeluaran' ? 'Pilih Kategori Pengeluaran' : 'Pilih Kategori Pemasukan';
                
                let options = `<option value="" disabled selected>${placeholder}</option>`;
                $.each(response.results, function(key, item) {
                    options += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#sub_jenis_transaksi_id').html(options);
                
                // Set sebagai required karena ada data
                $('#sub_jenis_transaksi_id').prop('required', true);
                
                // Update label normal
                const kategoriLabel = jenisTransaksi === 'Pengeluaran' ? 'Kategori Pengeluaran' : 'Kategori Pemasukan';
                $("label[for='sub_jenis_transaksi_id']").html(`${kategoriLabel} <span class="text-danger">*</span>`);
                
                // Initialize Select2
                try {
                    if ($.fn.select2) {
                        $('#sub_jenis_transaksi_id').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: placeholder,
                            allowClear: true,
                            dropdownParent: $('#modalTambahKeuanganTefa')
                        });
                    }
                } catch(e) {
                    console.warn('Error initializing Select2 for sub jenis transaksi:', e);
                }
                
            } else {
                // Tidak ada data - sembunyikan container
                console.log('No sub categories found - hiding container');
                $("#kategoriTransaksiContainer").hide();
                
                // Reset field to not required
                $('#sub_jenis_transaksi_id').prop('required', false);
                $('#sub_jenis_transaksi_id').val('');
                
                // Reset label
                $("label[for='sub_jenis_transaksi_id']").text('Kategori Transaksi');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            // Error - sembunyikan container untuk safety
            $("#kategoriTransaksiContainer").hide();
            $('#sub_jenis_transaksi_id').prop('required', false);
            $('#sub_jenis_transaksi_id').val('');
        }
    });
}

function loadProyekForFilter() {
    $.ajax({
        url: '/koordinator/keuangan-tefa/data-proyek',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            // Destroy previous Select2 instance if exists
            try {
                if ($.fn.select2 && $('#filter_proyek').hasClass('select2-hidden-accessible')) {
                    $('#filter_proyek').select2('destroy');
                }
            } catch(e) {
                console.warn('Error destroying Select2:', e);
            }
            
            $('#filter_proyek').html('<option value="">Loading...</option>');
        },
        success: function(response) {
            if (response.success && response.data) {
                let options = '<option value="">Semua Proyek</option>';
                $.each(response.data, function(key, item) {
                    options += `<option value="${item.proyek_id}">${item.nama_proyek}</option>`;
                });
                $('#filter_proyek').html(options);
                
                // Restore selected value if exists
                const savedValue = localStorage.getItem('filter_proyek');
                if (savedValue) {
                    $('#filter_proyek').val(savedValue);
                }
                
                // Initialize Select2 after populating options
                if ($.fn.select2) {
                    $('#filter_proyek').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Pilih Proyek',
                        allowClear: true
                    });
                }
            } else {
                $('#filter_proyek').html('<option value="">Error loading data</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading proyek data for filter:', error);
            $('#filter_proyek').html('<option value="">Error loading data</option>');
        }
    });
}

function loadSubJenisTransaksiForFilter() {
    const jenisTransaksiValue = $("#filter_jenis_transaksi").val();
    const jenisKeuanganValue = $("#filter_jenis_keuangan").val();
    
    console.log("Loading sub jenis for filter:", jenisTransaksiValue, jenisKeuanganValue);
    
    if (jenisTransaksiValue && jenisKeuanganValue) {
        let jenisTransaksiId;
        let jenisKeuanganId;
        
        // Get the actual DB IDs from the AJAX endpoints
        $.ajax({
            url: '/koordinator/keuangan-tefa/jenis-transaksi',
            type: 'GET',
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.success && response.data) {
                    const found = response.data.find(item => item.nama_jenis_transaksi === jenisTransaksiValue);
                    if (found) {
                        jenisTransaksiId = found.jenis_transaksi_id;
                    }
                }
            }
        });
        
        $.ajax({
            url: '/koordinator/keuangan-tefa/jenis-keuangan-tefa',
            type: 'GET',
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.success && response.results) {
                    const found = response.results.find(item => item.nama_jenis_keuangan_tefa === jenisKeuanganValue);
                    if (found) {
                        jenisKeuanganId = found.jenis_keuangan_tefa_id;
                    }
                }
            }
        });
        
        if (jenisTransaksiId && jenisKeuanganId) {
            $.ajax({
                url: '/koordinator/keuangan-tefa/get-sub-jenis-transaksi',
                type: 'GET',
                data: {
                    jenis_transaksi_id: jenisTransaksiId,
                    jenis_keuangan_tefa_id: jenisKeuanganId
                },
                dataType: 'json',
                beforeSend: function() {
                    try {
                        if ($.fn.select2 && $('#filter_sub_jenis_transaksi').hasClass('select2-hidden-accessible')) {
                            $('#filter_sub_jenis_transaksi').select2('destroy');
                        }
                    } catch(e) {
                        console.warn('Error destroying Select2:', e);
                    }
                    
                    $('#filter_sub_jenis_transaksi').html('<option value="">Loading...</option>');
                },
                success: function(response) {
                    console.log("Filter - Sub jenis transaksi response:", response);
                    
                    if (response.success && response.results && response.results.length > 0) {
                        // Ada data - tampilkan container
                        console.log('Filter - Found sub categories:', response.results.length);
                        
                        $("#filterKategoriTransaksiContainer").show();
                        
                        let options = '<option value="">Semua</option>';
                        $.each(response.results, function(key, item) {
                            options += `<option value="${item.id}">${item.text}</option>`;
                        });
                        $('#filter_sub_jenis_transaksi').html(options);
                        
                        // Restore selected value if exists
                        const savedValue = localStorage.getItem('filter_sub_jenis_transaksi');
                        if (savedValue) {
                            $('#filter_sub_jenis_transaksi').val(savedValue);
                        }
                        
                        // Initialize Select2
                        if ($.fn.select2) {
                            const placeholder = jenisTransaksiValue === 'Pengeluaran' 
                                ? 'Pilih Kategori Pengeluaran' 
                                : 'Pilih Kategori Pemasukan';
                                
                            $('#filter_sub_jenis_transaksi').select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: placeholder,
                                allowClear: true
                            });
                        }
                        
                        // Update labels
                        updateFilterLabels();
                        
                    } else {
                        // Tidak ada data - sembunyikan container
                        console.log('Filter - No sub categories found - hiding container');
                        $("#filterKategoriTransaksiContainer").hide();
                        
                        // Reset value
                        $("#filter_sub_jenis_transaksi").val('');
                        
                        // Reset label
                        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Filter - Error loading sub jenis transaksi:', error);
                    
                    // Error - sembunyikan container untuk safety
                    $("#filterKategoriTransaksiContainer").hide();
                    $("#filter_sub_jenis_transaksi").val('');
                }
            });
        }
    } else {
        // Reset - sembunyikan container
        $("#filterKategoriTransaksiContainer").hide();
        $('#filter_sub_jenis_transaksi').html('<option value="">Semua</option>');
        $("#filter_sub_jenis_transaksi").val('');
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
    }
}


function loadDataKeuanganTefa(page = 1, filters = {}) {
    $.ajax({
        url: '/koordinator/data-keuangan-tefa',
        type: 'GET',
        data: {
            page: page,
            ...filters 
        },
        dataType: 'json',
        beforeSend: function() {
            $("#tableDataKeuanganTefa tbody")
            .html(`<tr>
                    <td colspan="8" class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>`);
        },
        success: function(response) {
            if (response.success) {
                const keuanganTefa = response.keuanganTefa;
                
                // Clear existing table data
                $("#tableDataKeuanganTefa tbody").empty();
                
                // Check if data exists
                if (keuanganTefa.data.length > 0) {
                    // Hide empty message if shown
                    $("#emptyDataKeuanganTefaMessage").addClass('d-none');
                    
                    // Populate table with data
                    $.each(keuanganTefa.data, function(index, item) {
                        // Format date (assuming tanggal_transaksi is in YYYY-MM-DD format)
                        const date = new Date(item.tanggal_transaksi);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                        
                        // Format nominal with thousand separator
                        const formattedNominal = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(item.nominal_transaksi);
                        
                        // Format saldo with thousand separator
                        const formattedSaldo = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(item.saldo);
                        
                        // Determine badge style for transaction type
                        let badgeClass = '';
                        if (item.nama_jenis_transaksi === 'Pemasukan') {
                            badgeClass = 'badge bg-success';
                        } else if (item.nama_jenis_transaksi === 'Pengeluaran') {
                            badgeClass = 'badge bg-danger';
                        }
                        
                        // Create badge for jenis keuangan
                        let jenisKeuanganBadge = '';
                        if (item.nama_jenis_keuangan_tefa === 'Proyek') {
                            jenisKeuanganBadge = '<span>Proyek</span>';
                        } else if (item.nama_jenis_keuangan_tefa === 'Non Proyek') {
                            jenisKeuanganBadge = '<span>Non Proyek</span>';
                        } else {
                            jenisKeuanganBadge = `<span>${item.nama_jenis_keuangan_tefa}</span>`;
                        }

                        const limitedJenisTransaksi = limitWordsWithTooltip(item.nama_jenis_transaksi, 3);
                        const limitedNamaProyek = limitWordsWithTooltip(item.nama_proyek, 3);
                        const limitedNamaTransaksi = limitWordsWithTooltip(item.nama_transaksi, 3);
                        
                        // Create table row
                        let row = `
                            <tr>
                                <td>${formattedDate}</td>
                                <td><span class="${badgeClass}">${limitedJenisTransaksi}</span></td>
                                <td>${jenisKeuanganBadge}</td>
                                <td>${item.nama_jenis_keuangan_tefa === 'Proyek' ? (limitedNamaProyek || '-') : '-'}</td>
                                <td>${limitedNamaTransaksi}</td>
                                <td>${formattedNominal}</td>
                                <td>${formattedSaldo}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-action-detail-keuangan" data-id="${item.keuangan_tefa_id}" data-bs-toggle="modal" data-bs-target="#modalEditKeuanganTefa">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                                <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="btn btn-action-delete" 
                                                data-id="${item.keuangan_tefa_id}">
                                            <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        
                        $("#tableDataKeuanganTefa tbody").append(row);
                    });
                    
                    // Update pagination with filters included
                    updatePaginationWithFilters(keuanganTefa, filters);
                } else {
                    // Show empty message with filter indication
                    const filterApplied = Object.keys(filters).some(key => filters[key] !== '' && filters[key] !== undefined);
                    const emptyMessage = filterApplied 
                        ? '<p class="mb-0">Tidak ada data keuangan TEFA yang sesuai dengan filter yang dipilih.</p>' 
                        : '<p class="mb-0">Tidak ada data keuangan TEFA yang tersedia.</p>';
                        
                    $("#emptyDataKeuanganTefaMessage").removeClass('d-none')
                        .find('div').html(emptyMessage);
                    
                    // Update pagination info for 0 entries
                    $("#keuanganTefaPaginationInfo").text("Showing 0 to 0 of 0 entries");
                    $("#keuanganTefaPagination").empty();
                }
                
                // Also update financial summary
                updateFinancialSummary();
            } else {
                // Handle error in response
                $("#tableDataKeuanganTefa tbody").html('<tr><td colspan="8" class="text-center text-danger">Error loading data.</td></tr>');
                console.error("Error in response:", response.message);
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX error
            $("#tableDataKeuanganTefa tbody").html('<tr><td colspan="8" class="text-center text-danger">Failed to load data. Please try again.</td></tr>');
            console.error('AJAX Error:', error);
        }
    });
}

function updatePaginationWithFilters(data, filters) {
    // Update showing entries text
    const from = data.from || 0;
    const to = data.to || 0;
    const total = data.total || 0;
    
    $("#keuanganTefaPaginationInfo").text(`Showing ${from} to ${to} of ${total} entries`);
    
    // Clear existing pagination
    $("#keuanganTefaPagination").empty();
    
    // Only generate pagination if we have data
    if (data.total > 0) {
        let pagination = '<ul class="pagination">';
        
        // Previous button - use data-page instead of onclick and include filters
        pagination += `<li class="page-item ${data.current_page <= 1 ? 'disabled' : ''}">
            <a class="page-link keuangan-pagination-link" href="javascript:void(0)" data-page="${data.current_page - 1}" data-filters='${JSON.stringify(filters)}' aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>`;
        
        // Page numbers - use data-page instead of onclick and include filters
        for (let i = 1; i <= data.last_page; i++) {
            pagination += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
                <a class="page-link keuangan-pagination-link" href="javascript:void(0)" data-page="${i}" data-filters='${JSON.stringify(filters)}'>${i}</a>
            </li>`;
        }
        
        // Next button - use data-page instead of onclick and include filters
        pagination += `<li class="page-item ${data.current_page >= data.last_page ? 'disabled' : ''}">
            <a class="page-link keuangan-pagination-link" href="javascript:void(0)" data-page="${data.current_page + 1}" data-filters='${JSON.stringify(filters)}' aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>`;
        
        pagination += '</ul>';
        
        $("#keuanganTefaPagination").html(pagination);
    }
}

function updateFinancialSummary() {

    $("#totalPemasukan").text('Loading...');    
    $("#totalPengeluaran").text('Loading...');
    $("#saldoAkhir").text('Loading...');

    console.log("Updating financial summary...");
    $.ajax({
        url: `/koordinator/keuangan-tefa/get-summary`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Financial summary response:', response);
            if (response.success) {
                const formatCurrency = (amount) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(amount);
                };
                
                $('#totalPemasukan').text(formatCurrency(response.total_pemasukan || 0));
                $('#totalPengeluaran').text(formatCurrency(response.total_pengeluaran || 0));
                $('#saldoAkhir').text(formatCurrency(response.saldo || 0));

                if ((response.saldo || 0) < 0) {
                    $('#saldoAkhir').removeClass('text-primary').addClass('text-danger');
                } else {
                    $('#saldoAkhir').removeClass('text-danger').addClass('text-primary');
                }
            }
        },

    });
}

function loadSavedFilters() {
    // Check if we have recently saved filters (within last hour)
    const lastApplied = localStorage.getItem('keuanganTefaFiltersApplied');
    if (!lastApplied) return;
    
    const oneHourAgo = new Date().getTime() - (60 * 60 * 1000);
    if (parseInt(lastApplied) < oneHourAgo) {
        // Filters are older than 1 hour, clear them
        clearSavedFilters();
        return;
    }
    
    // Restore saved filter values
    $('#formFilterKeuanganTefa').find('input, select').each(function() {
        const id = $(this).attr('id');
        if (id) {
            const savedValue = localStorage.getItem(id);
            if (savedValue !== null) {
                $(this).val(savedValue);
            }
        }
    });

    toggleFilterProyekContainer();
    toggleFilterKategoriTransaksiContainer();

    // Load dependent options if needed
    if ($("#filter_jenis_keuangan").val() === 'Proyek') {
        loadProyekForFilter();
    }
    
    if ($("#filter_jenis_transaksi").val() && $("#filter_jenis_keuangan").val()) {
        loadSubJenisTransaksiForFilter();
    }
    
    // Apply filters automatically if any were saved
    if ($("#filter_tanggal_mulai").val() || 
        $("#filter_tanggal_akhir").val() || 
        $("#filter_jenis_transaksi").val() || 
        $("#filter_jenis_keuangan").val() ||
        $("#filter_nama_transaksi").val() ||
        $("#filter_proyek").val() ||
        $("#filter_nominal_min").val() ||
        $("#filter_nominal_max").val()) {
        
        // Apply with slight delay to ensure all components are loaded
        setTimeout(function() {
            applyFilters();
        }, 500);
    }
}

function clearSavedFilters() {
    // Clear all keuanganTefa filter-related items in localStorage
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('filter_')) {
            localStorage.removeItem(key);
        }
    }
    localStorage.removeItem('keuanganTefaFiltersApplied');
}

function addToList() {
    // First validate the form
    if (!validateForm()) {
        $("#form_keuangan_tefa_error").removeClass('d-none alert-success').addClass('alert-danger')
            .text('Harap periksa semua field yang wajib diisi.');
        return false; // Return false to indicate validation failed
    }
    
    // Generate unique ID for this item
    const itemId = Date.now().toString();
    
    // Get form values
    const jenisTransaksiText = $("#jenis_transaksi_id option:selected").text();
    const jenisKeuanganText = $("#jenis_keuangan_tefa_id option:selected").text();
    const namaTransaksi = $("#nama_transaksi").val().trim();
    const nominal = $("#nominal").val();
    const tanggal = $("#tanggal_transaksi").val();
    
    // Format nominal untuk tampilan: hanya pemisahan ribuan, tanpa desimal
    const displayNominal = formatCurrency(nominal);
    
    // IMPROVED FILE HANDLING: Store file with the item if present
    const fileInput = document.getElementById('file_keuangan_tefa');
    let fileObject = null;
    let fileName = '';
    
    if (fileInput.files && fileInput.files.length > 0) {
        // Store a copy of the file to associate with this specific item
        fileObject = fileInput.files[0];
        fileName = fileInput.files[0].name;
        console.log(`File "${fileName}" associated with item ${itemId}`);
    }
    
    // Get current list
    let currentList = JSON.parse($("#keuangan_tefa_JsonData").val());
    
    // Add sequence number to track entry order - this is key for maintaining order
    const sequence = currentList.length;
    
    // Create item data object
    const itemData = {
        id: itemId,
        sequence: sequence, // Store the sequence number
        jenis_transaksi_id: $("#jenis_transaksi_id").val(),
        jenis_transaksi_text: jenisTransaksiText,
        jenis_keuangan_tefa_id: $("#jenis_keuangan_tefa_id").val(),
        jenis_keuangan_text: jenisKeuanganText,
        proyek_id: $("#proyek_id_selected").val() || null,
        proyek_text: $("#proyek_id_selected option:selected").text() !== 'Pilih Proyek' ? $("#proyek_id_selected option:selected").text() : '',
        sub_jenis_transaksi_id: $("#sub_jenis_transaksi_id").val() || null,
        sub_jenis_transaksi_text: $("#sub_jenis_transaksi_id option:selected").text() !== 'Pilih Kategori Pengeluaran' ? $("#sub_jenis_transaksi_id option:selected").text() : '',
        nama_transaksi: namaTransaksi,
        tanggal_transaksi: tanggal,
        nominal: nominal,
        deskripsi_transaksi: $("#deskripsi_transaksi").val(),
        has_file: !!fileObject,
        file: fileObject, // Store the actual file object (will be null if no file)
        fileName: fileName // Store the filename for display
    };
    
    // Add to list (maintaining chronological order based on when items were added)
    currentList.push(itemData);
    
    // Update hidden input - remove file objects before JSON stringification
    const serializedList = currentList.map(item => {
        // Create a copy without the file property
        const { file, ...rest } = item;
        return rest;
    });
    
    $("#keuangan_tefa_JsonData").val(JSON.stringify(serializedList));
    
    // Store file objects separately in a global array
    if (!window.keuanganTefaFiles) {
        window.keuanganTefaFiles = {};
    }
    if (fileObject) {
        window.keuanganTefaFiles[itemId] = fileObject;
    }
    
    // Update UI - Change mode to multiple
    $("#isSingleKeuanganTefa").val('0');
    $("#emptyRowKeuanganTefa").remove();
    
    // Format date for display
    const displayDate = new Date(tanggal).toLocaleDateString('id-ID');
    
    // Add row to table with formatted nominal (NO decimal places)
    const newRow = `
        <tr id="item-${itemId}">
            <td>${namaTransaksi}</td>
            <td>${displayDate}</td>
            <td>${jenisTransaksiText}</td>
            <td>${jenisKeuanganText}</td>
            <td class="text-end">${displayNominal}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remove-item" data-id="${itemId}">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </td>
        </tr>
    `;
    
    // Add to the table - append to maintain original order of entry
    $("#daftarKeuanganTefa").append(newRow);
    
    // IMPORTANT: Clear all validation errors FIRST
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    
    // THEN reset form fields
    $("#nama_transaksi").val('');
    $("#nominal").val('');
    $("#deskripsi_transaksi").val('');
    $("#file_keuangan_tefa").val('');
    $("#file_preview_container").empty();
    
    // Reset Select2 styling if it exists
    if ($.fn.select2) {
        try {
            $('#proyek_id_selected, #sub_jenis_transaksi_id').each(function() {
                const $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    const selectInstance = $this.data('select2');
                    if (selectInstance) {
                        selectInstance.$container.removeClass('is-invalid');
                        selectInstance.$selection.removeClass('is-invalid');
                    }
                }
            });
        } catch (e) {
            console.warn('Error updating Select2 instances:', e);
        }
    }
    
    // Show success message
    $("#form_keuangan_tefa_error").removeClass('d-none alert-danger').addClass('alert-success')
        .text('Item berhasil ditambahkan ke daftar.');
    
    // Hide success message after 3 seconds
    setTimeout(function() {
        $("#form_keuangan_tefa_error").addClass('d-none').removeClass('alert-success');
    }, 3000);
    
    return true; // Return true to indicate successful addition
}

// ===== EDIT FUNCTIONS =====

function loadEditProyek(selectedId) {
    $.ajax({
        url: '/koordinator/keuangan-tefa/data-proyek',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#edit_proyek_id_selected').html('<option value="" disabled selected>Loading...</option>');
            
            // Destroy previous Select2 instance if exists
            if ($.fn.select2 && $('#edit_proyek_id_selected').data('select2')) {
                $('#edit_proyek_id_selected').select2('destroy');
            }
        },
        success: function(response) {
            if (response.success && response.data) {
                let options = '<option value="" disabled>Pilih Proyek</option>';
                $.each(response.data, function(key, item) {
                    const selected = item.proyek_id == selectedId ? 'selected' : '';
                    options += `<option value="${item.proyek_id}" ${selected}>${item.nama_proyek}</option>`;
                });
                $('#edit_proyek_id_selected').html(options);
                
                // Initialize Select2 after populating options
                if ($.fn.select2) {
                    $('#edit_proyek_id_selected').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Pilih Proyek',
                        allowClear: true,
                        dropdownParent: $('#modalEditKeuanganTefa')
                    });
                }
            } else {
                console.error('Error loading proyek:', response.message || 'Unknown error');
                $('#edit_proyek_id_selected').html('<option value="" disabled selected>Error loading data</option>');
            }
        },
        error: function(error) {
            console.error('AJAX error details:', error);
            $('#edit_proyek_id_selected').html('<option value="" disabled selected>Error loading data</option>');
        }
    });
}

function loadEditSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId, selectedId) {
    $.ajax({
        url: '/koordinator/keuangan-tefa/get-sub-jenis-transaksi',
        type: 'GET',
        data: {
            jenis_transaksi_id: jenisTransaksiId,
            jenis_keuangan_tefa_id: jenisKeuanganId
        },
        dataType: 'json',
        beforeSend: function() {
            $('#edit_sub_jenis_transaksi_id').html('<option value="" disabled selected>Loading...</option>');
            
            if ($.fn.select2 && $('#edit_sub_jenis_transaksi_id').data('select2')) {
                $('#edit_sub_jenis_transaksi_id').select2('destroy');
            }
        },
        success: function(response) {
            console.log('Edit form - Response from get-sub-jenis-transaksi:', response);
            
            if (response.success && response.results && response.results.length > 0) {
                // Ada data - tampilkan container dan populate dropdown
                console.log('Edit form - Found sub categories:', response.results.length);
                
                $("#edit_kategoriTransaksiContainer").show();
                
                // Determine placeholder based on current jenis transaksi
                const jenisTransaksi = $("#edit_jenis_transaksi_id option:selected").text();
                const placeholder = jenisTransaksi === 'Pengeluaran' ? 'Pilih Kategori Pengeluaran' : 'Pilih Kategori Pemasukan';
                
                let options = `<option value="" disabled>${placeholder}</option>`;
                $.each(response.results, function(key, item) {
                    const selected = item.id == selectedId ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.text}</option>`;
                });
                $('#edit_sub_jenis_transaksi_id').html(options);
                
                // Set sebagai required karena ada data
                $('#edit_sub_jenis_transaksi_id').prop('required', true);
                
                // Update label
                const kategoriLabel = jenisTransaksi === 'Pengeluaran' ? 'Kategori Pengeluaran' : 'Kategori Pemasukan';
                $("label[for='edit_sub_jenis_transaksi_id']").html(`${kategoriLabel} <span class="text-danger">*</span>`);
                
                // Initialize Select2
                if ($.fn.select2) {
                    $('#edit_sub_jenis_transaksi_id').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: placeholder,
                        allowClear: true,
                        dropdownParent: $('#modalEditKeuanganTefa')
                    });
                }
                
            } else {
                // Tidak ada data - sembunyikan container
                console.log('Edit form - No sub categories found - hiding container');
                $("#edit_kategoriTransaksiContainer").hide();
                
                // Reset field to not required
                $('#edit_sub_jenis_transaksi_id').prop('required', false);
                $('#edit_sub_jenis_transaksi_id').val('');
                
                // Reset label
                $("label[for='edit_sub_jenis_transaksi_id']").text('Kategori Transaksi');
            }
        },
        error: function(xhr, status, error) {
            console.error('Edit AJAX error details:', error);
            
            // Error - sembunyikan container untuk safety
            $("#edit_kategoriTransaksiContainer").hide();
            $('#edit_sub_jenis_transaksi_id').prop('required', false);
            $('#edit_sub_jenis_transaksi_id').val('');
        }
    });
}

function loadKeuanganTefaForEdit(id) {
    console.log("Loading keuangan tefa data for ID:", id);
    
    // Reset form and validation errors before fetching new data
    $("#formEditDataKeuanganTefa")[0].reset();
    $("#form_keuangan_tefa_edit_error").addClass('d-none').text('');
    $(".is-invalid").removeClass("is-invalid");
    $("#edit_file_preview_container").remove(); // Remove any existing file preview
    
    // Show loading state in modal
    const loadingHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading data...</p></div>';
    const originalContent = $("#formEditDataKeuanganTefa .modal-body").html();
    
    // Save original content to restore later if needed
    if (!window.originalEditModalContent) {
        window.originalEditModalContent = originalContent;
    }
    
    // Show loading spinner only inside the form's row area
    $("#formEditDataKeuanganTefa .modal-body .row").html(loadingHTML);
    
    $.ajax({
        url: `/koordinator/keuangan-tefa/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                console.log('Loaded data:', data);
                
                // Restore original content structure if we replaced it with loading spinner
                if ($("#formEditDataKeuanganTefa .modal-body .row").find(".spinner-border").length > 0) {
                    // Only restore the row content, not the entire modal body
                    if (window.originalEditModalContent) {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = window.originalEditModalContent;
                        const rowHTML = $(tempDiv).find('.row').html();
                        $("#formEditDataKeuanganTefa .modal-body .row").html(rowHTML);
                    }
                }
                
                // Now populate the form with data
                populateEditForm(data);
                
            } else {
                // Show error message
                $("#formEditDataKeuanganTefa .modal-body .row").html(window.originalEditModalContent || originalContent);
                $("#form_keuangan_tefa_edit_error").removeClass('d-none').text(response.message || 'Failed to load data.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            
            // Restore original content
            $("#formEditDataKeuanganTefa .modal-body .row").html(window.originalEditModalContent || originalContent);
            
            // Show error message
            $("#form_keuangan_tefa_edit_error").removeClass('d-none')
                .text('Error loading data. Please try again. ' + error);
        }
    });
}

function populateEditForm(data) {
    console.log("Populating edit form with data:", data);
    
    // Set hidden ID field
    $("#keuangan_tefa_id").val(data.keuangan_tefa_id);
    
    // Format date for input
    const rawDate = data.tanggal_transaksi;
    let formattedDate;

    if (rawDate.includes('T')) {
        formattedDate = rawDate.split('T')[0];
    } else if (rawDate.includes(' ')) {
        formattedDate = rawDate.split(' ')[0];
    } else {
        formattedDate = rawDate;
    }

    // Fill basic form fields
    $("#edit_tanggal_transaksi").val(formattedDate);
    $("#edit_nama_transaksi").val(data.nama_transaksi);
    $("#edit_deskripsi_transaksi").val(data.deskripsi_transaksi);
    
    // Format nominal with thousand separator
    const formattedNominal = new Intl.NumberFormat('id-ID', {
        useGrouping: true,
        maximumFractionDigits: 0
    }).format(data.nominal_transaksi).replace(/,/g, '.');
    
    $("#edit_nominal").val(formattedNominal);
    
    // Load dropdowns synchronously instead of asynchronously
    // First, load all options then set selected values
    Promise.all([
        // Load jenis transaksi
        new Promise((resolve) => {
            $.ajax({
                url: '/koordinator/keuangan-tefa/jenis-transaksi',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        let options = '<option value="" disabled>Pilih Jenis Transaksi</option>';
                        $.each(response.data, function(key, item) {
                            const selected = item.jenis_transaksi_id == data.jenis_transaksi_id ? 'selected' : '';
                            options += `<option value="${item.jenis_transaksi_id}" ${selected}>${item.nama_jenis_transaksi}</option>`;
                        });
                        $('#edit_jenis_transaksi_id').html(options);
                        resolve();
                    } else {
                        $('#edit_jenis_transaksi_id').html('<option value="" disabled selected>Error loading data</option>');
                        resolve();
                    }
                },
                error: function() {
                    $('#edit_jenis_transaksi_id').html('<option value="" disabled selected>Error loading data</option>');
                    resolve();
                }
            });
        }),
        
        // Load jenis keuangan
        new Promise((resolve) => {
            $.ajax({
                url: '/koordinator/keuangan-tefa/jenis-keuangan-tefa',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.results) {
                        let options = '<option value="" disabled>Pilih Keperluan Transaksi</option>';
                        $.each(response.results, function(key, item) {
                            const selected = item.jenis_keuangan_tefa_id == data.jenis_keuangan_tefa_id ? 'selected' : '';
                            options += `<option value="${item.jenis_keuangan_tefa_id}" ${selected}>${item.nama_jenis_keuangan_tefa}</option>`;
                        });
                        $('#edit_jenis_keuangan_tefa_id').html(options);
                        resolve();
                    } else {
                        $('#edit_jenis_keuangan_tefa_id').html('<option value="" disabled selected>Error loading data</option>');
                        resolve();
                    }
                },
                error: function() {
                    $('#edit_jenis_keuangan_tefa_id').html('<option value="" disabled selected>Error loading data</option>');
                    resolve();
                }
            });
        })
    ]).then(() => {
        if (data.jenis_keuangan_tefa_id && data.nama_jenis_keuangan_tefa === 'Proyek') {
            $("#edit_proyekContainer").show();
            loadEditProyek(data.proyek_id);
        } else {
            $("#edit_proyekContainer").hide();
        }
        
        // Handle kategori transaksi dropdown - show for both Pengeluaran and Pemasukan
        if (data.jenis_transaksi_id && (data.nama_jenis_transaksi === 'Pengeluaran' || data.nama_jenis_transaksi === 'Pemasukan')) {
            $("#edit_kategoriTransaksiContainer").show();
            if (data.jenis_transaksi_id && data.jenis_keuangan_tefa_id) {
                loadEditSubJenisTransaksi(data.jenis_transaksi_id, data.jenis_keuangan_tefa_id, data.sub_jenis_transaksi_id);
            }
        } else {
            $("#edit_kategoriTransaksiContainer").hide();
        }
    });
    
    // ENHANCED: Show existing file preview if exists
    if (data.bukti_transaksi) {
        const fileUrl = '/' + data.bukti_transaksi;
        const fileName = data.bukti_transaksi.split('/').pop();
        showExistingFilePreview(fileUrl, fileName);
    }
    
    // Set up the edit file input handler with enhanced preview
    $("#edit_file_keuangan_tefa").change(function() {
        if (handleEditFileUpload()) {
            showEditFilePreview();
        }
    });
}

// ===== DELETE FUNCTIONS =====
function deleteKeuanganTefa(id) {
    swal.confirmationDelete('Apakah yakin ini mengahapus data ini? ').then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/koordinator/keuangan-tefa/delete/${id}`,
                type: 'DELETE',
                dataType: 'json',
                beforeSend: function() {
                    $(`button[data-id="${id}"]`).prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        swal.successMessage(response.message || 'Data keuangan TEFA berhasil dihapus.');
                        loadDataKeuanganTefa();
                        updateFinancialSummary();
                    } else {
                        swal.errorMessage(response.message || 'Gagal menghapus data.');
                        $(`button[data-id="${id}"]`).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting data:', error);
                    const errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.';
                    swal.errorMessage('Error', errorMsg);
                    $(`button[data-id="${id}"]`).prop('disabled', false);
                }
            });
        }
    });
}

// ===== RESET FUNCTIONS =====

function resetSubJenisTransaksiDropdown() {
    try {
        console.log("Resetting sub jenis transaksi dropdown...");
        
        // Destroy Select2 if exists
        if ($.fn.select2 && $('#sub_jenis_transaksi_id').hasClass('select2-hidden-accessible')) {
            $('#sub_jenis_transaksi_id').select2('destroy');
        }
        
        // Reset HTML content dengan pesan default
        $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Pilih Jenis Transaksi dan Keperluan terlebih dahulu</option>');
        $('#sub_jenis_transaksi_id').val('');
        
        // Reset label ke default
        $("label[for='sub_jenis_transaksi_id']").text('Kategori Transaksi');
        
        // Reinitialize Select2 if needed
        if ($.fn.select2) {
            $('#sub_jenis_transaksi_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true,
                dropdownParent: $('#modalTambahKeuanganTefa')
            });
        }
        
        console.log("Sub jenis transaksi dropdown reset completed");
    } catch(e) {
        console.warn('Error resetting sub jenis transaksi dropdown:', e);
        // Fallback
        $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Pilih Jenis Transaksi dan Keperluan terlebih dahulu</option>').val('');
        $("label[for='sub_jenis_transaksi_id']").text('Kategori Transaksi');
    }
}

function resetEditSubJenisTransaksiDropdown() {
    try {
        // First check if Select2 is initialized
        if ($.fn.select2 && $('#edit_sub_jenis_transaksi_id').hasClass('select2-hidden-accessible')) {
            $('#edit_sub_jenis_transaksi_id').select2('destroy');
        }
        
        // Then update the HTML
        $('#edit_sub_jenis_transaksi_id').html('<option value="" disabled selected>Pilih Jenis Transaksi dan Keperluan terlebih dahulu</option>');
        
        // Reinitialize Select2 if needed
        if ($.fn.select2) {
            $('#edit_sub_jenis_transaksi_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true,
                dropdownParent: $('#modalEditKeuanganTefa')
            });
        }
    } catch(e) {
        console.warn('Error resetting edit sub jenis transaksi dropdown:', e);
        // Fallback to simple HTML update
        $('#edit_sub_jenis_transaksi_id').html('<option value="" disabled selected>Pilih Jenis Transaksi dan Keperluan terlebih dahulu</option>');
    }
}

function resetFilterProyekDropdown() {
    try {
        console.log("Resetting filter proyek dropdown...");
        
        // Destroy Select2 instance jika ada
        if ($.fn.select2 && $('#filter_proyek').hasClass('select2-hidden-accessible')) {
            $('#filter_proyek').select2('destroy');
        }
        
        // Reset HTML content ke default
        $('#filter_proyek').html('<option value="">Semua Proyek</option>');
        $('#filter_proyek').val('');
        
        // Reinitialize Select2
        if ($.fn.select2) {
            $('#filter_proyek').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Proyek',
                allowClear: true
            });
        }
        
        console.log("Filter proyek dropdown reset completed");
    } catch(e) {
        console.warn('Error resetting filter proyek dropdown:', e);
        // Fallback
        $('#filter_proyek').html('<option value="">Semua Proyek</option>').val('');
    }
}

// Reset Sub Jenis Transaksi Dropdown di Filter
function resetFilterSubJenisTransaksiDropdown() {
    try {
        console.log("Resetting filter sub jenis transaksi dropdown...");
        
        // Destroy Select2 instance jika ada
        if ($.fn.select2 && $('#filter_sub_jenis_transaksi').hasClass('select2-hidden-accessible')) {
            $('#filter_sub_jenis_transaksi').select2('destroy');
        }
        
        // Reset HTML content ke default
        $('#filter_sub_jenis_transaksi').html('<option value="">Semua</option>');
        $('#filter_sub_jenis_transaksi').val('');
        
        // Reset label ke default
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
        
        // Reinitialize Select2
        if ($.fn.select2) {
            $('#filter_sub_jenis_transaksi').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Kategori Transaksi',
                allowClear: true
            });
        }
        
        console.log("Filter sub jenis transaksi dropdown reset completed");
    } catch(e) {
        console.warn('Error resetting filter sub jenis dropdown:', e);
        // Fallback
        $('#filter_sub_jenis_transaksi').html('<option value="">Semua</option>').val('');
        $("label[for='filter_sub_jenis_transaksi']").text('Kategori Transaksi');
    }
}

function resetFormTambahDataAfterSave() {
    console.log("Resetting form tambah data after successful save...");
    
    // 1. Reset semua form fields KECUALI yang akan diset manual
    $("#formTambahDataKeuanganTefa")[0].reset();
    
    // 2. Set tanggal transaksi ke hari ini
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
    console.log("Tanggal transaksi set to today:", today);
    
    // 3. Clear validation errors
    $(".is-invalid").removeClass('is-invalid');
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_error").addClass('d-none').text('');
    
    // 4. Reset dan sembunyikan container proyek
    $("#proyekContainer").hide();
    $("#proyek_id_selected").prop('required', false);
    resetProyekDropdown();
    
    // 5. Reset dan sembunyikan container kategori transaksi
    $("#kategoriTransaksiContainer").hide();
    $("#sub_jenis_transaksi_id").prop('required', false);
    resetSubJenisTransaksiDropdown();
    
    // 6. Clear file input dan preview
    $("#file_keuangan_tefa").val('');
    $("#file_preview_container").empty();
    
    // 7. Reset multiple item mode ke single
    if (window.keuanganTefaFiles) {
        window.keuanganTefaFiles = {};
    }
    $("#keuangan_tefa_JsonData").val('[]');
    $("#daftarKeuanganTefa").html('<tr id="emptyRowKeuanganTefa"><td colspan="6" class="text-center">Belum ada keuangan tefa yang ditambahkan ke daftar</td></tr>');
    $("#isSingleKeuanganTefa").val('1');
    
    // 8. Reset button state
    $("#btnSimpanKeuanganTefa").prop('disabled', false).html('Simpan Data');
    
    console.log("Form reset completed - ready for new entry");
}

function resetProyekDropdown() {
    try {
        console.log("Resetting proyek dropdown...");
        
        // Destroy Select2 if exists
        if ($.fn.select2 && $('#proyek_id_selected').hasClass('select2-hidden-accessible')) {
            $('#proyek_id_selected').select2('destroy');
        }
        
        // Reset HTML content
        $('#proyek_id_selected').html('<option value="" disabled selected>Pilih Proyek</option>');
        $('#proyek_id_selected').val('');
        
        // Reinitialize Select2 if needed
        if ($.fn.select2) {
            $('#proyek_id_selected').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Proyek',
                allowClear: true,
                dropdownParent: $('#modalTambahKeuanganTefa')
            });
        }
        
        console.log("Proyek dropdown reset completed");
    } catch(e) {
        console.warn('Error resetting proyek dropdown:', e);
        // Fallback
        $('#proyek_id_selected').html('<option value="" disabled selected>Pilih Proyek</option>').val('');
    }
}