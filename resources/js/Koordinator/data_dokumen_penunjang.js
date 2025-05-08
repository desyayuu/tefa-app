$(document).ready(function() {
    if (typeof Swal === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
    }
    
    let documentsToSave = [];
    
    loadDokumenPenunjang();
    
    // Add document to preview list
    $("#btnTambahDokumen").on("click", function() {
        const namaDokumen = $("#nama_dokumen_penunjang").val();
        const jenisDokumenId = $("#jenis_dokumen_penunjang_id").val();
        const jenisDokumenText = $("#jenis_dokumen_penunjang_id option:selected").text();
        const fileInput = $("#file_dokumen_penunjang")[0];
        
        // Validate form
        if (!namaDokumen || !jenisDokumenId || fileInput.files.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Formulir Tidak Lengkap',
                    text: 'Silakan lengkapi semua field formulir'
                });
            } else {
                alert('Silakan lengkapi semua field formulir');
            }
            return;
        }
        
        // Create document object
        const newDocument = {
            id: Date.now(), // Temporary ID for client-side management
            nama_dokumen_penunjang: namaDokumen,
            jenis_dokumen_penunjang_id: jenisDokumenId,
            jenis_dokumen: jenisDokumenText,
            file: fileInput.files[0],
            fileName: fileInput.files[0].name
        };
        
        // Add to documents array
        documentsToSave.push(newDocument);
        
        // Update preview table
        updatePreviewTable();
        
        // Reset form inputs
        $("#nama_dokumen_penunjang").val('');
        $("#jenis_dokumen_penunjang_id").val('');
        $("#file_dokumen_penunjang").val('');
        
        // Scroll to preview section if first document
        if (documentsToSave.length === 1) {
            $("#previewDokumenSection").removeClass("d-none");
            $('html, body').animate({
                scrollTop: $("#previewDokumenSection").offset().top - 100
            }, 500);
        }
    });
    
    // Function to update preview table
    function updatePreviewTable() {
        const tableBody = $("#previewDokumenTable tbody");
        tableBody.empty();
        
        documentsToSave.forEach((doc, index) => {
            tableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${doc.nama_dokumen_penunjang}</td>
                    <td>${doc.jenis_dokumen}</td>
                    <td>
                        <span class="text-truncate-file">${doc.fileName}</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-preview-delete" data-id="${doc.id}">
                            <svg width="20" height="20" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.7379 3.12109C17.9743 3.12126 22.2193 7.314 22.2193 12.4854C22.2191 17.6565 17.9742 21.8485 12.7379 21.8486C7.50141 21.8486 3.25566 17.6566 3.25546 12.4854C3.25546 7.3139 7.50129 3.12109 12.7379 3.12109ZM9.15878 7.55273C8.76368 7.23427 8.1806 7.25655 7.8121 7.62012C7.44362 7.98402 7.42019 8.56086 7.74277 8.95117L7.8121 9.02539L11.3141 12.4844L7.8121 15.9443C7.41924 16.3324 7.41918 16.9616 7.8121 17.3496C8.20503 17.7374 8.84205 17.7375 9.23495 17.3496L12.7369 13.8896L16.2398 17.3496C16.6327 17.7376 17.2697 17.7373 17.6627 17.3496C18.0556 16.9615 18.0556 16.3324 17.6627 15.9443L14.1598 12.4844L17.6637 9.02539L17.732 8.95117C18.0546 8.56086 18.0321 7.98402 17.6637 7.62012C17.2952 7.25624 16.7112 7.23418 16.316 7.55273L16.2408 7.62012L12.7369 11.0791L9.23495 7.62012L9.15878 7.55273Z" fill="#E56F8C"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `);
        });
        
        // Attach delete events
        $(".btn-preview-delete").on("click", function() {
            const docId = $(this).data("id");
            removeDocumentFromPreview(docId);
        });
    }
    
    // Function to remove document from preview
    function removeDocumentFromPreview(id) {
        documentsToSave = documentsToSave.filter(doc => doc.id !== id);
        
        if (documentsToSave.length === 0) {
            $("#previewDokumenSection").addClass("d-none");
        } else {
            updatePreviewTable();
        }
    }
    
    // Cancel preview
    $("#btnBatalPreview").on("click", function() {
        documentsToSave = [];
        $("#previewDokumenSection").addClass("d-none");
    });
    
    // Save all documents
    $("#btnSimpanDokumen").on("click", function() {
        if (documentsToSave.length === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Ada Dokumen',
                    text: 'Tidak ada dokumen untuk disimpan'
                });
            } else {
                alert('Tidak ada dokumen untuk disimpan');
            }
            return;
        }
        
        let successCount = 0;
        let errorCount = 0;
        const totalDocuments = documentsToSave.length;
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan Dokumen...',
            html: `Menyimpan 0 dari ${totalDocuments}`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Save documents one by one
        const saveNextDocument = (index) => {
            if (index >= documentsToSave.length) {
                // All documents processed
                if (errorCount === 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `${successCount} dokumen berhasil disimpan`
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selesai Dengan Peringatan',
                        html: `${successCount} dokumen berhasil disimpan<br>${errorCount} dokumen gagal disimpan`
                    });
                }
                
                // Reset and reload
                documentsToSave = [];
                $("#previewDokumenSection").addClass("d-none");
                loadDokumenPenunjang();
                return;
            }
            
            const doc = documentsToSave[index];
            const formData = new FormData();
            formData.append('proyek_id', $('input[name="proyek_id"]').val());
            formData.append('nama_dokumen_penunjang', doc.nama_dokumen_penunjang);
            formData.append('jenis_dokumen_penunjang_id', doc.jenis_dokumen_penunjang_id);
            formData.append('file_dokumen_penunjang', doc.file);
            
            // Update loading message
            Swal.update({
                html: `Menyimpan ${index + 1} dari ${totalDocuments}`
            });
            
            $.ajax({
                url: '/koordinator/proyek/dokumen-penunjang/',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        successCount++;
                    } else {
                        errorCount++;
                        console.error('Error saving document:', response.message);
                    }
                    
                    // Process next document
                    saveNextDocument(index + 1);
                },
                error: function(xhr) {
                    errorCount++;
                    console.error('Error saving document:', xhr.responseText);
                    
                    // Process next document
                    saveNextDocument(index + 1);
                }
            });
        };
        
        // Start saving documents
        saveNextDocument(0);
    });
    
    // Get dokumen penunjang 
    function loadDokumenPenunjang() {
        const proyekId = $('input[name="proyek_id"]').val();
        
        $.ajax({
            url: `/koordinator/proyek/${proyekId}/dokumen-penunjang`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    renderDokumenTable(response.data);
                } else {
                    showEmptyMessage();
                }
            },
            error: function() {
                showEmptyMessage();
            }
        });
    }
    
    // Function to render dokumen table
    function renderDokumenTable(data) {
        const tableBody = $("#tableDokumenPenunjang tbody");
        tableBody.empty();
        
        if (data.length === 0) {
            showEmptyMessage();
            return;
        }
        
        // Hide empty message
        $("#emptyDokumenMessage").addClass("d-none");
        
        // Append rows to table
        data.forEach((dokumen, index) => {
            tableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${dokumen.nama_dokumen_penunjang}</td>
                    <td>${dokumen.jenis_dokumen}</td>
                    <td>${formatDate(dokumen.created_at)}</td>
                    <td>
                        <div class="d-flex">
                            <a href="/koordinator/proyek/dokumen-penunjang/download/${dokumen.dokumen_penunjang_proyek_id}" 
                            class="btn btn-action-download" 
                            title="Download">
                                <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="6.2425" cy="6.32" rx="6.2425" ry="6.32" transform="matrix(4.42541e-08 -1 -1 -4.31754e-08 18.96 20.8086)" fill="#E4F8EB"/>
                                    <path d="M10.0067 13.0054L12.64 15.6064M12.64 15.6064L15.2733 13.0054M12.64 15.6064L12.64 5.20228" stroke="#00BC39" stroke-linecap="round"/>
                                </svg>
                            </a>
                            <button type="button" class="btn btn-action-delete btn-delete-dokumen" title="Hapus" data-id="${dokumen.dokumen_penunjang_proyek_id}">
                                <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.5576 3.12109C17.7479 3.12109 21.9551 7.3139 21.9551 12.4854C21.9549 17.6566 17.7478 21.8486 12.5576 21.8486C7.36753 21.8486 3.16035 17.6566 3.16016 12.4854C3.16016 7.31393 7.36741 3.12115 12.5576 3.12109ZM9.01367 7.54883C8.62018 7.22898 8.03966 7.25268 7.67285 7.61816C7.30632 7.98355 7.28285 8.56113 7.60352 8.95312L7.67285 9.0293L11.1406 12.4844L7.67285 15.9404C7.28162 16.3302 7.28162 16.9627 7.67285 17.3525C8.06401 17.7421 8.69767 17.742 9.08887 17.3525L12.5576 13.8955L16.0264 17.3525C16.4176 17.7422 17.0522 17.7423 17.4434 17.3525C17.8344 16.9627 17.8345 16.3302 17.4434 15.9404L13.9736 12.4844L17.4424 9.0293L17.5117 8.95312C17.8325 8.56109 17.8091 7.98357 17.4424 7.61816C17.0756 7.25268 16.495 7.22898 16.1016 7.54883L16.0254 7.61816L12.5576 11.0723L9.08984 7.61816L9.01367 7.54883Z" fill="#E56F8C"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });
        
        // Attach delete events to buttons
        attachDeleteEvents();
    }
    
    // Function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }
    
    // Function to show empty message
    function showEmptyMessage() {
        $("#tableDokumenPenunjang tbody").empty();
        $("#emptyDokumenMessage").removeClass("d-none");
    }
    
    // Function to attach delete events
    function attachDeleteEvents() {
        $(".btn-delete-dokumen").on("click", function() {
            const dokumenId = $(this).data("id");
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus dokumen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDokumen(dokumenId);
                }
            });
        });
    }
    
    // Function to delete dokumen
    function deleteDokumen(id) {
        $.ajax({
            url: `/koordinator/proyek/dokumen-penunjang/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Dokumen penunjang berhasil dihapus'
                    });
                    
                    // Reload dokumen penunjang list
                    loadDokumenPenunjang();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal menghapus dokumen penunjang'
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan pada server';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMsg
                });
            }
        });
    }
});