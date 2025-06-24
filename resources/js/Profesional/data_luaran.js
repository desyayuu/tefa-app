$(document).ready(function() {
    const existingFiles = $("#dokumentasi")[0].files;
    if (existingFiles && existingFiles.length > 0) {
        accumualtedFiles = Array.from(existingFiles);
    }
    // Make sure dokumentasiPreviewItems container exists
    if ($("#dokumentasiPreviewContainer").length && !$("#dokumentasiPreviewItems").length) {
        $("#dokumentasiPreviewContainer").append('<div id="dokumentasiPreviewItems"></div>');
    }

    $(document).on('click', '.poster-item, .poster-item img', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Find the image source
        let src;
        if ($(this).hasClass('poster-item')) {
            src = $(this).find('img').attr('src');
        } else {
            src = $(this).attr('src');
        }
        
        $("#posterPreviewImage").attr('src', src);
        $("#posterPreviewModal").modal('show');
        
        return false;
    });
    
    // ===== DATA FETCHING FUNCTIONS =====
    
    // Function untuk mendapatkan data luaran dan dokumentasi proyek
    function getDataLuaranDokumentasi(proyekId) {
        $("#luaran-section").append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        $.ajax({
            url: `/profesional/proyek/${proyekId}/luaran`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                if (response.success) {
                    updateFormWithData(response.data);
                } else {
                    console.error("Error fetching data:", response.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: response.message || 'Terjadi kesalahan saat memuat data.',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                console.error("AJAX error when fetching data:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data.',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                $(".loading-overlay").remove();
            }
        });
    }
    
    function updateFormWithData(data) {
        const luaran = data.luaran;
        const dokumentasi = data.dokumentasi;

        $(".dokumentasi-gallery").empty();
        
        $(".poster-item").remove();
        const posterFile = $("#poster_proyek")[0].files;
        const dokumentasiFiles = $("#dokumentasi")[0].files;
        
        $("#formLuaranProyek")[0].reset();
        $("#posterPreview").empty();

        if (luaran) {
            $('input[name="luaran_proyek_id"]').val(luaran.luaran_proyek_id);
            
            // Set form fields
            $("#link_proyek").val(luaran.link_proyek || '');
            $("#deskripsi_luaran").val(luaran.deskripsi_luaran || '');
            
            // Show poster if exists
            if (luaran.poster_proyek) {
                const extension = luaran.poster_proyek.split('.').pop().toLowerCase();
                const posterPath = ensureAbsolutePath(luaran.poster_proyek);
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    // Image preview
                    const posterHtml = `
                        <div class="poster-item">
                            <img src="${posterPath}?t=${new Date().getTime()}" class="img-fluid" alt="Poster Proyek">
                        </div>
                    `;
                    $(".poster-item").remove(); 
                    $("#poster_proyek").before(posterHtml);
                }
            }
        } else {
            console.log("No luaran data found");
        }
        
        // Add dokumentasi if exists
        if (dokumentasi && dokumentasi.length > 0) {
            
            dokumentasi.forEach(function(dok) {
                const dokPath = ensureAbsolutePath(dok.path_file);
                
                const dokItem = `
                    <div class="dokumentasi-item position-relative">
                        <img src="${dokPath}?t=${new Date().getTime()}" 
                            alt="${dok.nama_file}"
                            class="img-fluid rounded">
                        <button type="button" 
                                class="btn btn-hapus-detail btn-delete-dokumentasi" 
                                data-id="${dok.dokumentasi_proyek_id}">
                            <svg width="16" height="16" viewBox="0 0 19 19" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7851 9.48484C18.7851 14.6563 14.6051 18.8486 9.44866 18.8486C4.29227 18.8486 0.112183 14.6563 0.112183 9.48484C0.112183 4.31339 4.29227 0.121094 9.44866 0.121094C14.6051 0.121094 18.7851 4.31339 18.7851 9.48484ZM12.8922 4.61499L12.968 4.54584C13.3602 4.22524 13.9388 4.24842 14.3043 4.61499C14.6698 4.98156 14.6929 5.56186 14.3733 5.9552L14.3043 6.03127L10.8608 9.48484L14.3043 12.9384C14.6942 13.3295 14.6942 13.9636 14.3043 14.3546C13.9143 14.7457 13.2821 14.7457 12.8921 14.3546L9.44866 10.9011L6.00519 14.3546C5.61524 14.7457 4.98299 14.7457 4.59304 14.3546C4.20309 13.9636 4.20309 13.3295 4.59304 12.9384L8.03651 9.48484L4.59299 6.03127L4.52404 5.9552C4.20437 5.56186 4.22749 4.98157 4.59299 4.61499C4.9585 4.24842 5.5371 4.22524 5.9293 4.54584L6.00514 4.61499L9.44866 8.06857L12.8922 4.61499Z"/>
                            </svg>
                        </button>
                    </div>`;
                $(".dokumentasi-gallery").append(dokItem);
            });
        } else {
            $(".dokumentasi-gallery").append('<div id="dokumentasiGalleryContainer" class="d-flex flex-wrap gap-3"></div>');
        }
        
        // Add cache-busting timestamp to prevent browser caching old images
        $(".dokumentasi-gallery img, .poster-item img").each(function() {
            const currentSrc = $(this).attr('src');
            if (currentSrc && !currentSrc.includes('data:image') && !currentSrc.includes('?t=')) {
                $(this).attr('src', currentSrc + '?t=' + new Date().getTime());
            }
        });
    }

    function ensureAbsolutePath(path) {
        if (!path) return '';

        if (path.startsWith('http://') || path.startsWith('https://')) {
            return path;
        }
        
        if (path.startsWith('/')) {
            return path;
        }
        
        return '/' + path;
    }
    
    const proyekId = $('input[name="proyek_id"]').val();
    if (proyekId) {
        getDataLuaranDokumentasi(proyekId);
    } else {
        console.warn("No proyek_id found on page load");
    }
    
    $(document).on('click', '#btnRefreshData', function() {
        const proyekId = $('input[name="proyek_id"]').val();
        if (proyekId) {
            getDataLuaranDokumentasi(proyekId);
        } else {
            console.warn("Cannot refresh - No proyek_id found");
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'ID Proyek tidak ditemukan.',
                confirmButtonText: 'OK'
            });
        }
    });
    
    // ===== DOKUMENTASI PROYEK FUNCTIONS =====
    $("#btnUploadDokumentasi").off('click').on('click', function() {
        $("#dokumentasi").click();
    });
    
    $(document).on('click', '.btn-delete-dokumentasi', function() {
        const dokumentasiId = $(this).data('id');
        const item = $(this).closest('.dokumentasi-item');
        
        
        Swal.fire({
            title: 'Hapus Dokumentasi',
            text: 'Apakah Anda yakin ingin menghapus dokumentasi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                item.css('opacity', '0.5');
                
                // Send delete request
                $.ajax({
                    url: `/profesional/proyek/dokumentasi/${dokumentasiId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            item.fadeOut(300, function() {
                                $(this).remove();
                            });
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Dokumentasi berhasil dihapus.',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            item.css('opacity', '1');
                            console.error("Delete error:", response.message);
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat menghapus dokumentasi.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Reset opacity and show error
                        item.css('opacity', '1');
                        console.error("Delete AJAX error:", xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghapus dokumentasi.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            } else {
                console.log("Delete confirmation: No");
            }
        });
    });
    
    $(document).on('click', '.dokumentasi-gallery .dokumentasi-item img', function(e) {
        const src = $(this).attr('src');
        $("#dokumentasiPreviewImage").attr('src', src);
        
        // Show the modal
        $("#dokumentasiPreviewModal").modal('show');
    });
    
    // Fix poster modal event binding - make it more inclusive
    $(document).on('click', '#posterPreview img', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const src = $(this).attr('src');
        $("#posterPreviewImage").attr('src', src);
        $("#posterPreviewModal").modal('show');
        
        return false;
    });
    
    // ===== POSTER PROYEK FUNCTIONS =====
    $("#poster_proyek").on('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            const fileType = file.type;
            
            if (fileType.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#posterPreviewContainer").show();
                    
                    // Update preview with delete button
                    $("#posterPreview").html(`
                        <div class="poster-item position-relative">
                            <img src="${e.target.result}" class="img-fluid">
                            <button type="button" class="btn btn-hapus-detail btn-remove-poster">
                                <svg width="16" height="16" viewBox="0 0 19 19" fill="white" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7851 9.48484C18.7851 14.6563 14.6051 18.8486 9.44866 18.8486C4.29227 18.8486 0.112183 14.6563 0.112183 9.48484C0.112183 4.31339 4.29227 0.121094 9.44866 0.121094C14.6051 0.121094 18.7851 4.31339 18.7851 9.48484ZM12.8922 4.61499L12.968 4.54584C13.3602 4.22524 13.9388 4.24842 14.3043 4.61499C14.6698 4.98156 14.6929 5.56186 14.3733 5.9552L14.3043 6.03127L10.8608 9.48484L14.3043 12.9384C14.6942 13.3295 14.6942 13.9636 14.3043 14.3546C13.9143 14.7457 13.2821 14.7457 12.8921 14.3546L9.44866 10.9011L6.00519 14.3546C5.61524 14.7457 4.98299 14.7457 4.59304 14.3546C4.20309 13.9636 4.20309 13.3295 4.59304 12.9384L8.03651 9.48484L4.59299 6.03127L4.52404 5.9552C4.20437 5.56186 4.22749 4.98157 4.59299 4.61499C4.9585 4.24842 5.5371 4.22524 5.9293 4.54584L6.00514 4.61499L9.44866 8.06857L12.8922 4.61499Z"/>
                                </svg>
                            </button>
                        </div>
                    `);
                }
                reader.readAsDataURL(file);
            } else {
                $("#posterPreview").empty();
                if ($("#posterPreview").children().length === 0) {
                    $("#posterPreviewContainer").hide();
                }
            }
        } else {
            $("#posterPreview").empty();
            $("#posterPreviewContainer").hide();
        }
    });

    $(document).on('click', '.btn-remove-poster', function(e) {
        // Penting: Hentikan event propagation agar tidak memicu event click pada parent
        e.stopPropagation();
        
        // Hapus preview
        $("#posterPreview").empty();
        
        // Reset input file
        $("#poster_proyek").val('');
        
        // Sembunyikan container
        $("#posterPreviewContainer").hide();
    });

    $("#dokumentasi").off('change').on('change', function() {
        const files = this.files;
        
        // Add new files to preview
        if (files.length > 0) {
            // Ensure the preview container exists and is shown
            if (!$("#dokumentasiPreviewItems").length) {
                if (!$("#dokumentasiPreviewContainer").length) {
                    $(".dokumentasi-gallery").after('<div id="dokumentasiPreviewContainer" class="dokumentasi-preview-container mt-3"><p class="dokumentasi-section-title">Preview Dokumentasi Baru</p><div id="dokumentasiPreviewItems"></div></div>');
                } else {
                    $("#dokumentasiPreviewContainer").append('<div id="dokumentasiPreviewItems"></div>');
                }
            }
            
            // Show the container
            $("#dokumentasiPreviewContainer").show();
            
            // Generate preview for each file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.match('image.*')) {
                    console.warn("Skipping non-image file:", file.name);
                    continue; // Skip non-image files
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = `
                        <div class="dokumentasi-item position-relative">
                            <img src="${e.target.result}" 
                                class="img-fluid rounded bg-light">
                            
                            <button type="button" class="btn btn-hapus-detail btn-remove-preview" data-filename="${file.name}">
                                <svg width="16" height="16" viewBox="0 0 19 19" fill="white" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7851 9.48484C18.7851 14.6563 14.6051 18.8486 9.44866 18.8486C4.29227 18.8486 0.112183 14.6563 0.112183 9.48484C0.112183 4.31339 4.29227 0.121094 9.44866 0.121094C14.6051 0.121094 18.7851 4.31339 18.7851 9.48484ZM12.8922 4.61499L12.968 4.54584C13.3602 4.22524 13.9388 4.24842 14.3043 4.61499C14.6698 4.98156 14.6929 5.56186 14.3733 5.9552L14.3043 6.03127L10.8608 9.48484L14.3043 12.9384C14.6942 13.3295 14.6942 13.9636 14.3043 14.3546C13.9143 14.7457 13.2821 14.7457 12.8921 14.3546L9.44866 10.9011L6.00519 14.3546C5.61524 14.7457 4.98299 14.7457 4.59304 14.3546C4.20309 13.9636 4.20309 13.3295 4.59304 12.9384L8.03651 9.48484L4.59299 6.03127L4.52404 5.9552C4.20437 5.56186 4.22749 4.98157 4.59299 4.61499C4.9585 4.24842 5.5371 4.22524 5.9293 4.54584L6.00514 4.61499L9.44866 8.06857L12.8922 4.61499Z"/>
                                </svg>
                            </button>
                        </div>`;
                    $("#dokumentasiPreviewItems").append(preview);
                };
                
                reader.onerror = function() {
                    console.error("Error reading file:", file.name);
                };
                
                reader.readAsDataURL(file);
            }
        } else {
            if ($("#dokumentasiPreviewItems").children().length === 0) {
                $("#dokumentasiPreviewContainer").hide();
            }
        }
    });
    
    // ===== SAVE DATA FUNCTIONS =====
    $("#btnSaveChanges").off('click').on('click', function() {

        // Prepare form data
        const formData = new FormData($("#formLuaranProyek")[0]);
        
        // Reset validation errors
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").text('');
        
        // Show loading state
        const button = $(this);
        const originalText = button.html();
        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        button.prop('disabled', true);

        const updateLuaranUrl = $("#formLuaranProyek").data('luaranUrl');
        const addDokumentasiUrl = $("#formLuaranProyek").data('dokumentasiUrl');
        
        
        if (!updateLuaranUrl) {
            console.error("Missing luaran URL data attribute");
            finishSaving(false, 'URL untuk menyimpan data luaran tidak ditemukan.', button, originalText);
            return;
        }
        
        // Save luaran proyek data
        $.ajax({
            url: updateLuaranUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {     
                if (response.success) {
                    // Update hidden input with new luaran_proyek_id if it was just created
                    if (response.data && response.data.id && !$('input[name="luaran_proyek_id"]').val()) {
                        $('input[name="luaran_proyek_id"]').val(response.data.id);
                    }
                    
                    // Check if we have dokumentasi files to upload
                    if ($("#dokumentasi")[0].files.length > 0) {
                        uploadDokumentasi(response.data.id, button, originalText);
                    } else {
                        $("#dokumentasi").val('');
                        $("#poster_proyek").val('');
                        finishSaving(true, 'Data luaran proyek berhasil disimpan.', button, originalText);
                    }
                } else {
                    console.error("Save luaran error:", response.message);
                    finishSaving(false, response.message || 'Terjadi kesalahan saat menyimpan data.', button, originalText);
                }
            },
            error: function(xhr) {
                console.error("Save luaran AJAX error:", xhr.responseText);
                
                if (xhr.status === 422) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            $.each(response.errors, function(field, messages) {
                                const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}_error`).text(errorMessage);
                            });
                            
                            finishSaving(false, 'Mohon periksa kembali data yang diinput.', button, originalText);
                        } else {
                            finishSaving(false, 'Terjadi kesalahan validasi data.', button, originalText);
                        }
                    } catch (e) {
                        console.error("Error parsing validation response:", e);
                        finishSaving(false, 'Terjadi kesalahan saat validasi data.', button, originalText);
                    }
                } else {
                    finishSaving(false, 'Terjadi kesalahan saat menyimpan data.', button, originalText);
                }
            }
        });
    });
    
    // Function to upload dokumentasi
    function uploadDokumentasi(luaranId, button, originalText) {
        // Create a new FormData for dokumentasi
        const dokFormData = new FormData();
        dokFormData.append('luaran_proyek_id', luaranId);
        
        // Add all files
        const files = $("#dokumentasi")[0].files;
        for (let i = 0; i < files.length; i++) {
            dokFormData.append('dokumentasi[]', files[i]);
        }


        const addDokumentasiUrl = $("#formLuaranProyek").data('dokumentasiUrl');
        if (!addDokumentasiUrl) {
            console.error("Missing dokumentasi URL data attribute");
            finishSaving(false, 'URL untuk mengunggah dokumentasi tidak ditemukan.', button, originalText);
            return;
        }
        
        // Upload dokumentasi
        $.ajax({
            url: addDokumentasiUrl,
            type: 'POST',
            data: dokFormData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(dokResponse) {
                if (dokResponse.success) {
                    // Reset the file input
                    $("#dokumentasi").val('');
                    $("#poster_proyek").val('');
                    finishSaving(true, 'Data luaran dan dokumentasi proyek berhasil disimpan.', button, originalText);
                } else {
                    console.error("Upload dokumentasi error:", dokResponse.message);
                    finishSaving(false, 'Data luaran berhasil disimpan, namun terjadi kesalahan saat mengunggah dokumentasi.', button, originalText);
                }
            },
            error: function(xhr) {
                console.error("Upload dokumentasi AJAX error:", xhr.responseText);
                finishSaving(false, 'Data luaran berhasil disimpan, namun terjadi kesalahan saat mengunggah dokumentasi.', button, originalText);
            }
        });
    }
    
    function finishSaving(success, message, button, originalText) {
        button.html(originalText);
        button.prop('disabled', false);

        if (success) {
            $("#posterPreview").empty();
            $("#posterPreviewContainer").hide();
            $("#dokumentasiPreviewItems").empty();
            $("#dokumentasiPreviewContainer").hide();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                confirmButtonText: 'OK'
            }).then(() => {
                const proyekId = $('input[name="proyek_id"]').val();
                if (proyekId) {
                    getDataLuaranDokumentasi(proyekId);
                } else {
                    window.location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    }

    let accumualtedFiles = [];

    $("#btnUploadDokumentasi").off('click').on('click', function() {
        
        // Create a temporary input element
        const tempFileInput = document.createElement('input');
        tempFileInput.type = 'file';
        tempFileInput.multiple = true;
        tempFileInput.accept = ".jpg,.jpeg,.png";
        
        // Add change event listener
        tempFileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                // Add newly selected files to our accumualtedFiles array
                const newFiles = Array.from(this.files);
                
                // Add these files to our accumulation
                addFilesToAccumulation(newFiles);
                
                // Generate previews for the new files
                generatePreviews(newFiles);
                
                // Update the hidden file input
                updateHiddenInput();
            }
        });
        
        // Trigger file selection
        tempFileInput.click();

    function addFilesToAccumulation(newFiles) {
        newFiles.forEach(file => {
            const duplicateIndex = accumualtedFiles.findIndex(f => f.name === file.name);
            if (duplicateIndex === -1) {
                accumualtedFiles.push(file);
            } else {
                accumualtedFiles[duplicateIndex] = file;
                $(`.btn-remove-preview[data-filename="${file.name}"]`).closest('.dokumentasi-item').remove();
            }
        });
    }

    // Function to generate previews for files
    function generatePreviews(files) {
        // Ensure the preview container exists and is shown
        if (!$("#dokumentasiPreviewItems").length) {
            if (!$("#dokumentasiPreviewContainer").length) {
                $(".dokumentasi-gallery").after('<div id="dokumentasiPreviewContainer" class="dokumentasi-preview-container mt-3"><p class="dokumentasi-section-title">Preview Dokumentasi Baru</p><div id="dokumentasiPreviewItems"></div></div>');
            } else {
                $("#dokumentasiPreviewContainer").append('<div id="dokumentasiPreviewItems"></div>');
            }
        }
        
        // Show the container
        $("#dokumentasiPreviewContainer").show();
        
        // Generate preview for each file
        files.forEach(file => {
            if (!file.type.match('image.*')) {
                console.warn("Skipping non-image file:", file.name);
                return; // Skip non-image files
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = `
                    <div class="dokumentasi-item position-relative">
                        <img src="${e.target.result}" 
                            class="img-fluid rounded bg-light">
                        
                        <button type="button" class="btn btn-hapus-detail btn-remove-preview" data-filename="${file.name}">
                            <svg width="16" height="16" viewBox="0 0 19 19" fill="white" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7851 9.48484C18.7851 14.6563 14.6051 18.8486 9.44866 18.8486C4.29227 18.8486 0.112183 14.6563 0.112183 9.48484C0.112183 4.31339 4.29227 0.121094 9.44866 0.121094C14.6051 0.121094 18.7851 4.31339 18.7851 9.48484ZM12.8922 4.61499L12.968 4.54584C13.3602 4.22524 13.9388 4.24842 14.3043 4.61499C14.6698 4.98156 14.6929 5.56186 14.3733 5.9552L14.3043 6.03127L10.8608 9.48484L14.3043 12.9384C14.6942 13.3295 14.6942 13.9636 14.3043 14.3546C13.9143 14.7457 13.2821 14.7457 12.8921 14.3546L9.44866 10.9011L6.00519 14.3546C5.61524 14.7457 4.98299 14.7457 4.59304 14.3546C4.20309 13.9636 4.20309 13.3295 4.59304 12.9384L8.03651 9.48484L4.59299 6.03127L4.52404 5.9552C4.20437 5.56186 4.22749 4.98157 4.59299 4.61499C4.9585 4.24842 5.5371 4.22524 5.9293 4.54584L6.00514 4.61499L9.44866 8.06857L12.8922 4.61499Z"/>
                            </svg>
                        </button>
                    </div>`;
                $("#dokumentasiPreviewItems").append(preview);
            };
            
            reader.onerror = function() {
                console.error("Error reading file:", file.name);
            };
            
            reader.readAsDataURL(file);
        });
    }

    });

    $(document).on('click', '.btn-remove-preview', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const filename = $(this).data('filename');
        
        // Hapus file dari array accumualtedFiles
        const fileIndex = accumualtedFiles.findIndex(file => file.name === filename);
        if (fileIndex !== -1) {
            accumualtedFiles.splice(fileIndex, 1);
            updateHiddenInput();
        }
        
        $(this).closest('.dokumentasi-item').fadeOut(300, function() {
            $(this).remove();
            
            if ($("#dokumentasiPreviewItems").children().length === 0) {
                $("#dokumentasiPreviewContainer").hide();
            }
        });
    });

    function updateHiddenInput() {
        // Create a DataTransfer object
        const dataTransfer = new DataTransfer();
        
        // Add all files to it
        accumualtedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        // Set the files to the input element
        $("#dokumentasi")[0].files = dataTransfer.files;
    }
});