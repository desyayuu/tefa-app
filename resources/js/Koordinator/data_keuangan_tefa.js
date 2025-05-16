$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadDataKeuanganTefa();
    initializeForm();
    loadJenisTransaksi();
    loadJenisKeuangan();
    
    // In your frontend JavaScript, update the nominal formatting:
    $("#nominal").on('input', function() {
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
        
        // Log the value for debugging
        console.log("Formatted value:", value);
        console.log("Raw value:", value.replace(/\D/g, ''));
    });

    // Make file upload optional
    $("#file_keuangan_tefa").prop('required', false);
    
    // Update file handling
    $("#file_keuangan_tefa").change(function() {
        handleFileUpload();
        showFilePreview();
    });

    // First submit handler
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
        
        // Check if we are in multiple mode with items
        if (!isSingle) {
            const itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
            if (itemList.length > 0) {
                // Skip other validations if we already have items in the list
                submitForm();
                return;
            }
        }
        
        // Only validate form for single entry or if no items in multiple mode
        if (validateForm()) {
            submitForm();
        }
    });
    
    $(document).on('change', '#jenis_transaksi_id', function() {
        const jenisTransaksiId = $(this).val();
        const jenisKeuanganId = $("#jenis_keuangan_tefa_id").val();
        
        toggleKategoriPengeluaranContainer();
        if (jenisTransaksiId && jenisKeuanganId) {
            loadSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId);
        } else {
            resetSubJenisTransaksiDropdown();
        }
    });
    
    $(document).on('change', '#jenis_keuangan_tefa_id', function() {
        const jenisKeuanganId = $(this).val();
        const jenisTransaksiId = $("#jenis_transaksi_id").val();
        
        toggleProyekContainer();
        if (jenisKeuanganId) {
            const isProyek = $(this).find('option:selected').text() === 'Proyek';
            if (isProyek) {
                loadProyek();
            }
        }
        
        if (jenisTransaksiId && jenisKeuanganId) {
            loadSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId);
        } else {
            resetSubJenisTransaksiDropdown();
        }
    });

    $("#btnTambahkanKeDaftarKeuanganTefa").click(function() {
        // Simpan teks tombol simpan sebelum diproses
        const originalBtnText = $("#btnSimpanKeuanganTefa").text();
        
        // Memastikan state tombol simpan tetap konsisten
        addToList();
        
        // Pastikan tombol simpan tetap memiliki text asli
        $("#btnSimpanKeuanganTefa").prop('disabled', false).text(originalBtnText);
        resetFormForNextEntry();
    });
    
    $(document).on('click', '.btn-remove-item', function() {
        const itemId = $(this).data('id');
        removeFromList(itemId);
    });
    
    $('#modalTambahKeuanganTefa').on('hidden.bs.modal', function() {
        resetForm();
    });
});

function loadDataKeuanganTefa(page = 1) {
    $.ajax({
        url: '/koordinator/data-keuangan-tefa',
        type: 'GET',
        data: {
            page: page
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
                        
                        // Create table row
                        let row = `
                            <tr>
                                <td>${formattedDate}</td>
                                <td><span class="${badgeClass}">${item.nama_jenis_transaksi}</span></td>
                                <td>${jenisKeuanganBadge}</td>
                                <td>${item.nama_jenis_keuangan_tefa === 'Proyek' ? item.nama_proyek || '-' : '-'}</td>
                                <td>${item.nama_transaksi}</td>
                                <td>${formattedNominal}</td>
                                <td>${formattedSaldo}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-action-detail-progres btn-view" 
                                                data-id="${item.keuangan_tefa_id}" 
                                                onclick="viewKeuanganTefa('${item.keuangan_tefa_id}')">
                                                <svg width="15" height="15" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                                    <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                                </svg>
                                        </button>
                                        <button type="button" class="btn btn-action-delete" 
                                                data-id="${item.keuangan_tefa_id}" 
                                                onclick="deleteKeuanganTefa('${item.keuangan_tefa_id}')">
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
                    
                    // Update pagination
                    updatePagination(keuanganTefa);
                } else {
                    // Show empty message
                    $("#emptyDataKeuanganTefaMessage").removeClass('d-none')
                        .find('div').html('<p class="mb-0">Tidak ada data keuangan TEFA yang tersedia.</p>');
                    
                    // Update pagination info for 0 entries
                    $("#keuanganTefaPaginationInfo").text("Showing 0 to 0 of 0 entries");
                    $("#keuanganTefaPagination").empty();
                }
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

// Function to update pagination
function updatePagination(data) {
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
        
        // Previous button
        pagination += `<li class="page-item ${data.current_page <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadDataKeuanganTefa(${data.current_page - 1})" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>`;
        
        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            pagination += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadDataKeuanganTefa(${i})">${i}</a>
            </li>`;
        }
        
        // Next button
        pagination += `<li class="page-item ${data.current_page >= data.last_page ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadDataKeuanganTefa(${data.current_page + 1})" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>`;
        
        pagination += '</ul>';
        
        $("#keuanganTefaPagination").html(pagination);
    }
}

function initializeForm() {
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
    
    $("#keuangan_tefa_JsonData").val('[]');
    $("#isSingleKeuanganTefa").val('1');
}


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

// Load Jenis Transaksi
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

// Load Proyek - Updated to match controller response
function loadProyek() {
    $.ajax({
        url: '/koordinator/keuangan-tefa/data-proyek',
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#proyek_id_selected').html('<option value="" disabled selected>Loading...</option>');
        },
        success: function(response) {
            console.log('Response from proyek API:', response);
            
            if (response.success && response.data) {
                let options = '<option value="" disabled selected>Pilih Proyek</option>';
                $.each(response.data, function(key, item) {
                    options += `<option value="${item.proyek_id}">${item.nama_proyek}</option>`;
                });
                $('#proyek_id_selected').html(options);
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

// Load Sub Jenis Transaksi berdasarkan Jenis Transaksi dan Jenis Keuangan
function loadSubJenisTransaksi(jenisTransaksiId, jenisKeuanganId) {
    $.ajax({
        url: '/koordinator/get-sub-jenis-transaksi',
        type: 'GET',
        data: {
            jenis_transaksi_id: jenisTransaksiId,
            jenis_keuangan_tefa_id: jenisKeuanganId
        },
        dataType: 'json',
        beforeSend: function() {
            $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Loading...</option>');
        },
        success: function(response) {
            console.log('Response from get-sub-jenis-transaksi:', response);
            
            if (response.success && response.results && response.results.length > 0) {
                let options = '<option value="" disabled selected>Pilih Kategori Pengeluaran</option>';
                $.each(response.results, function(key, item) {
                    options += `<option value="${item.id}">${item.text}</option>`;
                });
                $('#sub_jenis_transaksi_id').html(options);
            } else {
                $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Tidak ada kategori tersedia</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Error loading data</option>');
        }
    });
}

// Toggle Kategori Pengeluaran Container
function toggleKategoriPengeluaranContainer() {
    const jenisTransaksi = $("#jenis_transaksi_id option:selected").text();
    
    if (jenisTransaksi === 'Pengeluaran') {
        $("#kategoriPengeluaranContainer").show();
        $("#sub_jenis_transaksi_id").prop('required', true);
    } else {
        $("#kategoriPengeluaranContainer").hide();
        $("#sub_jenis_transaksi_id").prop('required', false);
    }
}

// Toggle Proyek Container
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

// Reset Sub Jenis Transaksi Dropdown
function resetSubJenisTransaksiDropdown() {
    $('#sub_jenis_transaksi_id').html('<option value="" disabled selected>Pilih Jenis Transaksi dan Keperluan terlebih dahulu</option>');
}

// Remove Item from List
function removeFromList(itemId) {
    // Get current list
    let currentList = JSON.parse($("#keuangan_tefa_JsonData").val());
    
    // Filter out the item to remove
    currentList = currentList.filter(item => item.id !== itemId);
    
    // Update hidden input
    $("#keuangan_tefa_JsonData").val(JSON.stringify(currentList));
    
    // Remove row from table
    $(`#item-${itemId}`).remove();
    
    // If no items left, show empty message and switch back to single mode
    if (currentList.length === 0) {
        $("#isSingleKeuanganTefa").val('1');
        $("#daftarKeuanganTefa").html(`
            <tr id="emptyRowKeuanganTefa">
                <td colspan="6" class="text-center">Belum ada keuangan tefa yang ditambahkan ke daftar</td>
            </tr>
        `);
    }
}

function validateForm() {
    let isValid = true;
    
    // Reset validation errors
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_error").addClass('d-none').text('');
    
    // Check if we're in single or multiple mode
    const isSingle = $("#isSingleKeuanganTefa").val() === '1';
    
    // Get the item list for multiple entries
    let itemList = [];
    if (!isSingle) {
        itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
        
        // If in multiple mode and have items, skip validation
        if (itemList.length > 0) {
            return true;
        }
    }
    
    // Required fields validation for single entry or if no items added yet
    const requiredFields = [
        { id: 'jenis_transaksi_id', name: 'Jenis Transaksi' },
        { id: 'tanggal_transaksi', name: 'Tanggal Transaksi' },
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
    
    // Validasi khusus untuk nama transaksi
    const namaTransaksi = $("#nama_transaksi").val();
    if (namaTransaksi && !validateTransactionName(namaTransaksi)) {
        $("#nama_transaksi").addClass('is-invalid');
        $("#nama_transaksi_error").text('Nama transaksi harus berisi 1-255 karakter');
        isValid = false;
    }
    
    // Validasi khusus untuk nominal
    if ($("#nominal").val() && !validateNominal($("#nominal").val())) {
        $("#nominal").addClass('is-invalid');
        $("#nominal_error").text('Nominal harus berupa angka positif dan tidak melebihi batas maksimum');
        isValid = false;
    }
    
    // Conditional validation for Proyek
    const jenisKeuangan = $("#jenis_keuangan_tefa_id option:selected").text();
    if (jenisKeuangan === 'Proyek' && !$("#proyek_id_selected").val()) {
        $("#proyek_id_selected").addClass('is-invalid');
        $("#proyek_id_selected_error").text('Pilih proyek terlebih dahulu');
        isValid = false;
    }
    
    // Conditional validation for kategori pengeluaran
    const jenisTransaksi = $("#jenis_transaksi_id option:selected").text();
    if (jenisTransaksi === 'Pengeluaran' && !$("#sub_jenis_transaksi_id").val()) {
        $("#sub_jenis_transaksi_id").addClass('is-invalid');
        $("#sub_jenis_transaksi_id_error").text('Pilih kategori pengeluaran');
        isValid = false;
    }
    
    // File validation only if a file is selected (optional)
    if ($("#file_keuangan_tefa")[0].files && $("#file_keuangan_tefa")[0].files.length > 0) {
        if (!handleFileUpload()) {
            isValid = false;
        }
    }
    
    // Show general error message if validation fails
    if (!isValid) {
        $("#form_keuangan_tefa_error").removeClass('d-none').text('Silakan periksa kembali formulir Anda.');
    }
    
    return isValid;
}

// Perbaikan submit form dengan file handling yang benar
function submitForm() {
    // Get form status
    const isSingle = $("#isSingleKeuanganTefa").val() === '1';
    
    // Debug: Log form data
    console.log("Submit form with mode:", isSingle ? "Single" : "Multiple");
    
    // Debug: Check if file exists in form
    const fileInput = document.getElementById('file_keuangan_tefa');
    if (fileInput.files && fileInput.files.length > 0) {
        console.log("File attached in form:", fileInput.files[0].name);
    } else if (window.savedKeuanganTefaFile) {
        console.log("File saved from earlier:", window.savedKeuanganTefaFile.name);
    } else {
        console.log("No file attached");
    }
    
    // Check if we have items in multiple mode
    if (!isSingle) {
        const itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
        if (itemList.length === 0) {
            // Display error if no items in multiple mode
            $("#form_keuangan_tefa_error").removeClass('d-none').text('Tambahkan minimal satu item ke daftar sebelum menyimpan.');
            return false;
        }
        
        // PENTING: Jika dalam mode multiple dengan items, kita perlu mengisi field yang required
        // dengan nilai sementara agar bisa melewati validasi server
        if (itemList.length > 0) {
            // Set dummy values untuk field required
            if (!$("#nama_transaksi").val()) {
                $("#nama_transaksi").val("temp_value");
            }
            if (!$("#nominal").val()) {
                $("#nominal").val("1");
            }
            if (!$("#jenis_transaksi_id").val()) {
                // Gunakan value dari item pertama
                $("#jenis_transaksi_id").val(itemList[0].jenis_transaksi_id);
            }
            if (!$("#jenis_keuangan_tefa_id").val()) {
                // Gunakan value dari item pertama
                $("#jenis_keuangan_tefa_id").val(itemList[0].jenis_keuangan_tefa_id);
            }
            if (!$("#tanggal_transaksi").val()) {
                // Gunakan value dari item pertama atau hari ini
                $("#tanggal_transaksi").val(itemList[0].tanggal_transaksi || new Date().toISOString().split('T')[0]);
            }
        }
    }
    
    // Prepare form data
    const formData = new FormData($("#formTambahDataKeuanganTefa")[0]);
    
    // PERBAIKAN: Jika ada file yang tersimpan dari sebelumnya, tambahkan ke formData
    if (!isSingle && window.savedKeuanganTefaFile && (!fileInput.files || fileInput.files.length === 0)) {
        formData.set('file_keuangan_tefa', window.savedKeuanganTefaFile);
        console.log("File dari penyimpanan ditambahkan ke form:", window.savedKeuanganTefaFile.name);
    }
    
    // Debug: log all form data
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
    }
    
    // Show loading state
    $("#btnSimpanKeuanganTefa").prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
    
    $.ajax({
        url: '/koordinator/keuangan-tefa/store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Success response:', response);
            if (response.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset form and close modal
                        resetForm();
                        $('#modalTambahKeuanganTefa').modal('hide');
                        // Reload data table to show new entries
                        if (typeof loadDataKeuanganTefa === 'function') {
                            loadDataKeuanganTefa();
                        }
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', xhr.status, xhr.statusText);
            console.error('Response text:', xhr.responseText);
            
            try {
                const jsonResponse = JSON.parse(xhr.responseText);
                console.log('Parsed error response:', jsonResponse);
                
                // Display detailed validation errors
                if (xhr.status === 422 && jsonResponse.errors) {
                    let errorMessage = "Validation errors:<br>";
                    
                    // Handle nested errors in keuangan_tefa_data
                    Object.keys(jsonResponse.errors).forEach(key => {
                        if (key.startsWith('keuangan_tefa_data.')) {
                            errorMessage += `- Item data: ${jsonResponse.errors[key]}<br>`;
                        } else {
                            errorMessage += `- ${key}: ${jsonResponse.errors[key]}<br>`;
                        }
                    });
                    
                    $("#form_keuangan_tefa_error").removeClass('d-none')
                        .html(errorMessage);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: `Server error: ${jsonResponse.message || error}`,
                        confirmButtonText: 'OK'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: `Server error: ${error}`,
                    confirmButtonText: 'OK'
                });
            }
            
            // Reset button state
            $("#btnSimpanKeuanganTefa").prop('disabled', false).text('Simpan Data');
            
            // Kembalikan field form ke keadaan sebelumnya jika gagal
            if (!isSingle) {
                const itemList = JSON.parse($("#keuangan_tefa_JsonData").val());
                if (itemList.length > 0) {
                    // Reset temporary values
                    if ($("#nama_transaksi").val() === "temp_value") {
                        $("#nama_transaksi").val("");
                    }
                    if ($("#nominal").val() === "1") {
                        $("#nominal").val("");
                    }
                }
            }
        }
    });
}

// Reset semua referensi yang tersimpan saat reset form
function resetForm() {
    // Reset complete form
    $("#formTambahDataKeuanganTefa")[0].reset();
    
    // Reset validation
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_error").addClass('d-none').text('');
    
    // Reset file input
    $("#file_keuangan_tefa").val('');
    $("#file_preview_container").empty();
    
    // PERBAIKAN: Reset file yang tersimpan
    window.savedKeuanganTefaFile = null;
    
    // Reset data list
    $("#keuangan_tefa_JsonData").val('[]');
    $("#isSingleKeuanganTefa").val('1');
    
    // Reset table
    $("#daftarKeuanganTefa").html(`
        <tr id="emptyRowKeuanganTefa">
            <td colspan="6" class="text-center">Belum ada keuangan tefa yang ditambahkan ke daftar</td>
        </tr>
    `);
    
    // Hide conditional sections
    $("#kategoriPengeluaranContainer").hide();
    $("#proyekContainer").hide();
    
    // Set default date
    const today = new Date().toISOString().split('T')[0];
    $("#tanggal_transaksi").val(today);
}


// Reset Form for Next Entry
function resetFormForNextEntry() {
    // Reset only specific fields, keep dropdowns
    $("#nama_transaksi").val('');
    $("#nominal").val('');
    $("#deskripsi_transaksi").val('');
    $("#file_keuangan_tefa").val('');
    $("#file_preview_container").empty();
    
    // Reset validation
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text('');
    $("#form_keuangan_tefa_error").addClass('d-none').text('');
}

function validateTransactionName(name) {
    // Minimal 1 karakter, maksimal 255 karakter
    return name.trim().length > 0 && name.trim().length <= 255;
}

function formatCurrency(amount) {
    // Pastikan amount adalah string dan hapus semua karakter non-digit
    let cleanAmount = String(amount).replace(/\D/g, '');
    
    // Jika kosong, kembalikan 0
    if (!cleanAmount) {
        return '0';
    }
    
    // Format dengan separator ribuan tanpa desimal
    return cleanAmount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Ambil nilai numerik dari string nominal yang terformat
function parseNominal(nominalStr) {
    // Hapus semua karakter non-digit (termasuk titik pemisah ribuan)
    return parseInt(nominalStr.replace(/\D/g, ''), 10);
}

// Validasi nominal sebelum submit
function validateNominal(nominalStr) {
    // Bersihkan dari format dan konversi ke angka
    const numValue = parseNominal(nominalStr);
    
    // Cek validitas
    if (isNaN(numValue) || numValue <= 0) {
        return false;
    }
    
    // Cek batas maksimum (decimal(15,2))
    if (numValue >= 10**13) {
        return false;
    }
    
    return true;
}

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

// Updated addToList function to include file handling
// Perbaikan untuk mengingat file saat add to list
function addToList() {
    // Validate form before adding to list
    if (!validateForm()) {
        return;
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
    
    // PERBAIKAN: Simpan file untuk digunakan nanti
    const fileInput = document.getElementById('file_keuangan_tefa');
    if (fileInput.files && fileInput.files.length > 0) {
        // Simpan file untuk submit form nanti
        window.savedKeuanganTefaFile = fileInput.files[0];
        console.log("File disimpan untuk submit nanti:", window.savedKeuanganTefaFile.name);
    }
    
    // Create item data object
    const itemData = {
        id: itemId,
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
        has_file: fileInput.files && fileInput.files.length > 0
    };
    
    // Get current list
    let currentList = JSON.parse($("#keuangan_tefa_JsonData").val());
    
    // Add to list
    currentList.push(itemData);
    
    // Update hidden input
    $("#keuangan_tefa_JsonData").val(JSON.stringify(currentList));
    
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
            <td class="text-end">Rp ${displayNominal}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btn-remove-item" data-id="${itemId}">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </td>
        </tr>
    `;
    
    $("#daftarKeuanganTefa").append(newRow);
    
    // Reset form for next entry but keep the dropdowns
    resetFormForNextEntry();
    
    // Show success message
    $("#form_keuangan_tefa_error").removeClass('d-none alert-danger').addClass('alert-success')
        .text('Item berhasil ditambahkan ke daftar.');
    
    // Hide success message after 3 seconds
    setTimeout(function() {
        $("#form_keuangan_tefa_error").addClass('d-none').removeClass('alert-success');
    }, 3000);
}


// Function untuk menampilkan preview file yang diunggah
// Function untuk file preview yang menyimpan file
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
    
    // Check if it's an image
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
        // For non-image files, show file type icon and name
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