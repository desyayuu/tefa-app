$(document).ready(function() {
    let currentPageTimeline = 1; 
    let perPageTimeline = 3; 
    loadDataTimeline(1);

    if (window.location.hash === '#data-timeline-section') {
        setTimeout(function() {
            scrollToDataTimelineSection();
        }, 300); 
    }

    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        currentPageTimeline = $(this).data('page');
        loadDataTimeline(currentPageTimeline);
        
        $('html, body').animate({
            scrollTop: $('#tableDataTimeline').offset().top - 100
        }, 500);
    });

    // Button 
    $('#searchTimelineForm').on('submit', function(e) {
        e.preventDefault();
        const searchValue = $('#searchTimeline').val();
        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('search', searchValue);
        currentUrl.hash = 'data-timeline-section'; 
        
        window.history.pushState({}, '', currentUrl.toString());
        
        currentPageTimeline =1;
        loadDataTimeline();
        scrollToDataTimelineSection();
    });

    $('#btnClearSearch').on('click', function() {
        $('#searchTimeline').val('');
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.delete('search');
        window.history.pushState({}, '', currentUrl.toString());
        
        loadDataTimeline();
        $('#btnClearSearch').addClass('d-none');
    });

    $("#formEditTimeline").on('submit', function(e) {
        e.preventDefault();
        
        // Reset error messages
        $("#edit_form_error").addClass('d-none').text('');
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").text('');
        
        const timelineId = $("#edit_timeline_id").val();
        const formData = new FormData(this);
        
        // Validasi sederhana
        const namaTimeline = $("#edit_nama_timeline").val();
        const tanggalMulai = $("#edit_tanggal_mulai_timeline").val();
        const tanggalSelesai = $("#edit_tanggal_selesai_timeline").val();
        
        let hasErrors = false;
        
        if (!namaTimeline) {
            $("#edit_nama_timeline_error").text("Nama timeline harus diisi");
            $("#edit_nama_timeline").addClass("is-invalid");
            hasErrors = true;
        }
        
        if (!tanggalMulai) {
            $("#edit_tanggal_mulai_timeline_error").text("Tanggal mulai harus diisi");
            $("#edit_tanggal_mulai_timeline").addClass("is-invalid");
            hasErrors = true;
        }
        
        if (!tanggalSelesai) {
            $("#edit_tanggal_selesai_timeline_error").text("Tanggal selesai harus diisi");
            $("#edit_tanggal_selesai_timeline").addClass("is-invalid");
            hasErrors = true;
        }
        
        if (tanggalMulai && tanggalSelesai && new Date(tanggalMulai) > new Date(tanggalSelesai)) {
            $("#edit_tanggal_selesai_timeline_error").text("Tanggal selesai harus setelah tanggal mulai");
            $("#edit_tanggal_selesai_timeline").addClass("is-invalid");
            hasErrors = true;
        }
        
        if (hasErrors) {
            return false;
        }
        
        // Tampilkan loading state
        const submitButton = $("#btnUpdateTimeline");
        const originalText = submitButton.text();
        submitButton.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Menyimpan...</span>
        `);
        
        // Kirim data ke server
        $.ajax({
            url: `/koordinator/proyek/timeline/${timelineId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Refresh data
                        loadDataTimeline();
                        // Tutup modal
                        $('#modalEditTimeline').modal('hide');
                    });
                } else {
                    // Tampilkan pesan error
                    $("#edit_form_error").removeClass('d-none').text(response.message);
                }
            },
            error: function(xhr) {
                console.error("Error updating timeline:", xhr.responseText);
                
                if (xhr.status === 422) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            // Tampilkan error untuk setiap field
                            $.each(response.errors, function(field, messages) {
                                const errorField = field === 'nama_timeline' ? 'edit_nama_timeline' : 
                                                  field === 'tanggal_mulai_timeline' ? 'edit_tanggal_mulai_timeline' : 
                                                  field === 'tanggal_selesai_timeline' ? 'edit_tanggal_selesai_timeline' : 
                                                  field === 'deskripsi_timeline' ? 'edit_deskripsi' : field;
                                
                                const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                                $(`#${errorField}_error`).text(errorMessage);
                                $(`#${errorField}`).addClass('is-invalid');
                            });
                        } else {
                            $("#edit_form_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    } catch (e) {
                        $("#edit_form_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                    }
                } else {
                    $("#edit_form_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                }
            },
            complete: function() {
                // Reset loading state
                submitButton.prop('disabled', false).html(originalText);
            }
        });
    });

    function updatePaginationTimelineInfo(currentPageTimeline, perPageTimeline, total) {
        const from = total > 0 ? (currentPageTimeline - 1) * perPageTimeline + 1 : 0;
        const to = Math.min(currentPageTimeline * perPageTimeline, total);
        
        $("#timelinePaginationInfo").html(`Showing ${from} to ${to} of ${total} entries`);
    }

    function scrollToDataTimelineSection() {
        const dataTimelineSection = $('#data-timeline-section');
        if (dataTimelineSection.length) {
            $('html, body').animate({
                scrollTop: dataTimelineSection.offset().top - 80 
            }, 500);
        }
    }

    // Load data timeline
    function loadDataTimeline(page=1) {
        const proyekId = $('input[name="proyek_id"]').val();
        const searchParam = $("#searchTimeline").val() || '';
        
        console.log("Loading timeline data for project ID:", proyekId);
        console.log("Search parameter:", searchParam);
        console.log("Current page:", page);
        
        // Clear any existing data first to prevent duplication
        $("#tableDataTimeline tbody").empty();
        
        // Show loading state
        $("#tableDataTimeline tbody").html(`
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2 text-muted small">Memuat data...</p>
                </td>
            </tr>
        `);
        
        // Hide empty message while loading
        $("#emptyDataTimelineMessage").addClass("d-none");
        $("#timelinePagination").html('');
        
        $.ajax({
            url: `/koordinator/proyek/${proyekId}/timeline`,
            type: 'GET',
            data: {
                search: searchParam,
                page: page, 
                per_page_timeline: perPageTimeline
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // First clear existing data again to be absolutely sure we don't get duplicates
                $("#tableDataTimeline tbody").empty();
                
                console.log("API Response:", response);
                
                // Check if we got HTML instead of JSON
                if (typeof response === 'string' && response.indexOf('<!DOCTYPE html>') >= 0) {
                    console.error("Received HTML response instead of JSON");
                    showEmptyMessageTimeline(searchParam);
                    return;
                }
                
                // Handle various response formats but focus on the main expected structure
                if (response.success && response.data) {
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        console.log(`Rendering ${response.data.length} timeline items`);
                        
                        // Update pagination info if we have pagination data
                        if (response.pagination) {
                            updatePaginationTimelineInfo(
                                response.pagination.current_page,
                                response.pagination.per_page_timeline,
                                response.pagination.total
                            );
                            
                            // Update pagination links if available
                            if (response.pagination.html) {
                                $("#timelinePagination").html(response.pagination.html);
                            }
                        }
                        
                        // Render the timeline data
                        renderTimelineTable(response.data);
                    } else {
                        console.log("No timeline data found in response");
                        // No data found
                        showEmptyMessageTimeline(searchParam);
                        updatePaginationTimelineInfo(1, perPageTimeline, 0);
                    }
                } else {
                    console.log("Invalid or empty response structure");
                    // Fallback for any other response format or empty data
                    showEmptyMessageTimeline(searchParam);
                    updatePaginationTimelineInfo(1, perPageTimeline, 0);
                }
            },
            error: function(xhr) {
                console.error("Error loading timeline:", xhr.responseText);
                
                $("#tableDataTimeline tbody").html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 9L9 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 9L15 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Terjadi kesalahan saat memuat data</p>
                            <p class="small text-muted">Silakan coba lagi nanti</p>
                        </td>
                    </tr>
                `);
                $("#timelinePagination").html('');
                updatePaginationTimelineInfo(1, perPageTimeline, 0);
            }
        });
    }

    function loadTimelineDetail(timelineId) {
        // Reset form
        $("#formEditTimeline")[0].reset();
        $("#edit_form_error").addClass('d-none').text('');
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").text('');
        
        // Set timeline ID
        $("#edit_timeline_id").val(timelineId);
        
        // Tampilkan loading state
        const modalBody = $("#modalEditTimeline .modal-body");
        const originalContent = modalBody.html();
        
        modalBody.html(`
            <div class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="ms-3 mb-0">Memuat data...</p>
            </div>
        `);
        
        // Ambil data dari server
        $.ajax({
            url: `/koordinator/proyek/timeline/${timelineId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.data) {
                    // Pulihkan konten modal
                    modalBody.html(originalContent);
                    
                    // Isi form dengan data
                    const data = response.data;
                    $("#edit_nama_timeline").val(data.nama_timeline_proyek);
                    $("#edit_tanggal_mulai_timeline").val(formatDateForInput(data.tanggal_mulai_timeline));
                    $("#edit_tanggal_selesai_timeline").val(formatDateForInput(data.tanggal_selesai_timeline));
                    $("#edit_deskripsi").val(data.deskripsi_timeline);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memuat data timeline',
                        confirmButtonText: 'OK'
                    });
                    
                    // Tutup modal
                    $('#modalEditTimeline').modal('hide');
                }
            },
            error: function(xhr) {
                console.error("Error loading timeline detail:", xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memuat data timeline',
                    confirmButtonText: 'OK'
                });
                
                // Tutup modal
                $('#modalEditTimeline').modal('hide');
            }
        });
    }

    function renderTimelineTable(data) {
        console.log("Rendering timeline table with data:", data);
        
        // Get the table body and ensure it's empty
        const tableBody = $("#tableDataTimeline tbody");
        tableBody.empty();
        
        // Check if we have valid data
        if (!data || !Array.isArray(data) || data.length === 0) {
            const searchParam = $("#searchTimeline").val() || '';
            console.log("No timeline data to render, showing empty message");
            showEmptyMessageTimeline(searchParam);
            return;
        }
        
        // Hide empty message when we have data
        $("#emptyDataTimelineMessage").addClass("d-none");
        
        // Create a string to hold all rows to avoid multiple DOM manipulations
        let tableHtml = '';
        
        // Process each timeline item
        data.forEach((timeline, index) => {
            // Ensure we have valid timeline ID and other required fields
            const timelineId = timeline.timeline_proyek_id;
            
            if (!timelineId) {
                console.warn("Missing timeline ID for item at index", index, timeline);
                return; // Skip this item
            }
            
            const namaTimeline = timeline.nama_timeline_proyek || 'Unnamed';
            const tanggalMulai = formatDate(timeline.tanggal_mulai_timeline);
            const tanggalSelesai = formatDate(timeline.tanggal_selesai_timeline);
            
            // Add row HTML
            tableHtml += `
                <tr data-id="${timelineId}">
                    <td>${namaTimeline}</td>
                    <td>${tanggalMulai}</td>
                    <td>${tanggalSelesai}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-action-detail" data-id="${timelineId}" data-bs-toggle="modal" data-bs-target="#modalEditTimeline">
                                <svg width="15" height="15" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                                    <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-action-delete btn-delete-timeline" data-id="${timelineId}">
                                <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        // Set the HTML content all at once (more efficient)
        tableBody.html(tableHtml);
        
        // Attach event handlers after adding to DOM
        attachEventHandlers();
        
        console.log("Timeline table rendered successfully with", data.length, "items");
    }
    
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        return `${year}-${month}-${day}`;
    }

    function confirmDeleteTimeline(timelineId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus timeline ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteTimeline(timelineId);
            }
        });
    }
    
    function deleteTimeline(timelineId) {
        $.ajax({
            url: `/koordinator/proyek/timeline/${timelineId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                    
                    // Refresh data
                    loadDataTimeline(currentPageTimeline);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Gagal menghapus timeline',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                console.error("Error deleting timeline:", xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menghapus timeline',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
    
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
    }
    
    function showEmptyMessageTimeline(searchParam) {
        $("#tableDataTimeline tbody").empty();
        $("#emptyDataTimelineMessage").removeClass("d-none");
        
        if (searchParam) {
            $("#emptyDataTimelineMessage div").html(`
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <circle cx="11" cy="11" r="8" stroke="#8E8E8E" stroke-width="1.5"/>
                    <path d="M16.5 16.5L21 21" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 7V11" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 15H11.01" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-muted mb-1">Tidak ada timeline ditemukan dengan kata kunci: <strong>"${searchParam}"</strong></p>
            `);
        } else {
            $("#emptyDataTimelineMessage div").html(`
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
                <p class="text-muted">Belum ada timeline</p>
            `);
        }
    }
    
    let timelineCollection = [];
    
    // Tombol tambahkan ke daftar
    $("#btnTambahkanKeDaftarTimeline").on('click', function() {
        // Reset error messages
        resetFormErrors();
        
        // Ambil nilai form
        const namaTimeline = $("#nama_timeline").val();
        const tanggalMulai = $("#tanggal_mulai_timeline").val();
        const tanggalSelesai = $("#tanggal_selesai_timeline").val();
        const deskripsi = $("#deskripsi_timeline").val() || '';
        
        // Validasi sederhana
        let errors = {};
        let hasErrors = false;
        
        if (!namaTimeline) {
            $("#nama_timeline").addClass("is-invalid");
            $("#nama_timeline_error").text("Nama timeline harus diisi");
            hasErrors = true;
        }
        
        if (!tanggalMulai) {
            $("#tanggal_mulai_timeline").addClass("is-invalid");
            $("#tanggal_mulai_timeline_error").text("Tanggal mulai harus diisi");
            hasErrors = true;
        }
        
        if (!tanggalSelesai) {
            $("#tanggal_selesai_timeline").addClass("is-invalid");
            $("#tanggal_selesai_timeline_error").text("Tanggal selesai harus diisi");
            hasErrors = true;
        }
        
        if (tanggalMulai && tanggalSelesai && new Date(tanggalMulai) > new Date(tanggalSelesai)) {
            $("#tanggal_selesai_timeline").addClass("is-invalid");
            $("#tanggal_selesai_timeline_error").text("Tanggal selesai harus setelah tanggal mulai");
            hasErrors = true;
        }
        
        if (hasErrors) {
            return;
        }
        
        // Tambahkan ke collection
        const timelineItem = {
            id: Date.now(), // ID unik sementara
            nama_timeline: namaTimeline,
            tanggal_mulai_timeline: tanggalMulai,
            tanggal_selesai_timeline: tanggalSelesai,
            deskripsi_timeline: deskripsi
        };
        
        timelineCollection.push(timelineItem);
        
        // Update hidden input
        $("#timelineJsonData").val(JSON.stringify(timelineCollection));
        
        // Set is_single ke 0 (multiple)
        $("#isSingleTimeline").val("0");
        
        // Render collection to table
        renderTimelineCollection();
        
        // Reset form
        resetTimelineForm();
    });
    
    // Reset form timeline
    function resetTimelineForm() {
        $("#nama_timeline").val('');
        $("#tanggal_mulai_timeline").val('');
        $("#tanggal_selesai_timeline").val('');
        $("#deskripsi").val('');
        resetFormErrors();
    }
    
    // Reset error messages
    function resetFormErrors() {
        // Reset validation classes and messages
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").text('');
        $("#form_timeline_error").addClass('d-none').text('');
    }
    
    // Render collection ke tabel
    function renderTimelineCollection() {
        const tbody = $("#daftarTimeline");
        
        if (timelineCollection.length === 0) {
            tbody.html(`
                <tr id="emptyRowTimeline">
                    <td colspan="4" class="text-center">Belum ada timeline yang ditambahkan ke daftar</td>
                </tr>
            `);
            return;
        }
        
        // Hapus row kosong jika ada
        $("#emptyRowTimeline").remove();
        
        // Clear tbody dan re-render semua item
        tbody.empty();
        
        timelineCollection.forEach((item, index) => {
            const formattedMulai = formatDate(item.tanggal_mulai_timeline);
            const formattedSelesai = formatDate(item.tanggal_selesai_timeline);
            
            tbody.append(`
                <tr>
                    <td>${item.nama_timeline}</td>
                    <td>${formattedMulai}</td>
                    <td>${formattedSelesai}</td>
                    <td>
                        <button type="button" class="btn btn-action-delete btn-remove-timeline" data-id="${item.id}">
                            <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `);
        });
        
        // Attach click event for remove button
        $(".btn-remove-timeline").off('click').on('click', function() {
            const id = $(this).data('id');
            removeTimelineItem(id);
        });
    }
    
    // Hapus item dari collection
    function removeTimelineItem(id) {
        timelineCollection = timelineCollection.filter(item => item.id !== id);
        $("#timelineJsonData").val(JSON.stringify(timelineCollection));
        
        // Set is_single ke 1 jika tidak ada item
        if (timelineCollection.length === 0) {
            $("#isSingleTimeline").val("1");
        }
        
        renderTimelineCollection();
    }
    
    // Reset Form saat modal ditutup
    $('#modalTambahTimeline').on('hidden.bs.modal', function () {
        resetTimelineForm();
        timelineCollection = [];
        $("#timelineJsonData").val('[]');
        $("#isSingleTimeline").val("1");
        renderTimelineCollection();
    });
    
    // Submit form untuk menambahkan timeline
    $("#formTambahDataTimeline").on('submit', function(e) {
        e.preventDefault();
        
        // Prevent multiple submissions
        if ($(this).data('submitting')) {
            console.log('Form is already being submitted - preventing duplicate submission');
            return false;
        }
        
        $(this).data('submitting', true);
        
        // Reset any previous error messages
        resetFormErrors();
        
        const isSingle = $("#isSingleTimeline").val() === "1";
        const formData = new FormData(this);
        
        // Debug what's being sent
        console.log("Form submission - Is single timeline:", isSingle);
        console.log("Proyek ID:", formData.get('proyek_id'));
        
        // Validate based on mode (single or multiple)
        if (isSingle) {
            // Validate input for single timeline
            const namaTimeline = $("#nama_timeline").val();
            const tanggalMulai = $("#tanggal_mulai_timeline").val();
            const tanggalSelesai = $("#tanggal_selesai_timeline").val();
            
            let hasErrors = false;
            
            if (!namaTimeline) {
                $("#nama_timeline_error").text("Nama timeline harus diisi");
                $("#nama_timeline").addClass("is-invalid");
                hasErrors = true;
            }
            
            if (!tanggalMulai) {
                $("#tanggal_mulai_timeline_error").text("Tanggal mulai harus diisi");
                $("#tanggal_mulai_timeline").addClass("is-invalid");
                hasErrors = true;
            }
            
            if (!tanggalSelesai) {
                $("#tanggal_selesai_timeline_error").text("Tanggal selesai harus diisi");
                $("#tanggal_selesai_timeline").addClass("is-invalid");
                hasErrors = true;
            }
            
            if (tanggalMulai && tanggalSelesai && new Date(tanggalMulai) > new Date(tanggalSelesai)) {
                $("#tanggal_selesai_timeline_error").text("Tanggal selesai harus setelah tanggal mulai");
                $("#tanggal_selesai_timeline").addClass("is-invalid");
                hasErrors = true;
            }
            
            if (hasErrors) {
                $(this).data('submitting', false);
                return false;
            }
        } else {
            // Validate for multiple timeline
            if (timelineCollection.length === 0) {
                $("#form_timeline_error").removeClass('d-none').text('Anda belum menambahkan timeline ke daftar');
                $(this).data('submitting', false);
                return false;
            }
        }
        
        // Show loading state
        const submitButton = $("#btnSimpanTimeline");
        const originalText = submitButton.text();
        submitButton.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span>Menyimpan...</span>
        `);
        
        const self = this;
        
        // Send data to server
        $.ajax({
            url: `/koordinator/proyek/timeline/`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log("Add timeline success:", response);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset form and collection
                        resetTimelineForm();
                        timelineCollection = [];
                        $("#timelineJsonData").val('[]');
                        $("#isSingleTimeline").val("1");
                        
                        // Close modal first to prevent any race conditions
                        $('#modalTambahTimeline').modal('hide');
                        
                        // Reset the form state before reloading data
                        $(self).data('submitting', false);
                        
                        // IMPORTANT: Wait a moment before reloading to avoid race conditions
                        setTimeout(function() {
                            // Reload data to first page to show the newly added item
                            currentPageTimeline = 1;
                            loadDataTimeline(currentPageTimeline);
                        }, 300);
                    });
                } else {
                    $("#form_timeline_error").removeClass('d-none').text(response.message || 'Terjadi kesalahan');
                    $(self).data('submitting', false);
                }
            },
            error: function(xhr) {
                console.error("Error adding timeline:", xhr.responseText);
                
                // Parse validation errors
                if (xhr.status === 422) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors) {
                            $.each(response.errors, function(field, messages) {
                                const errorMessage = Array.isArray(messages) ? messages[0] : messages;
                                $(`#${field}_error`).text(errorMessage);
                                $(`#${field}`).addClass('is-invalid');
                            });
                        } else {
                            $("#form_timeline_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                        }
                    } catch (e) {
                        $("#form_timeline_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                    }
                } else {
                    $("#form_timeline_error").removeClass('d-none').text('Terjadi kesalahan. Silakan coba lagi.');
                }
                
                $(self).data('submitting', false);
            },
            complete: function() {
                // Reset loading state
                submitButton.prop('disabled', false).html(originalText);
            }
        });
    });
    
    function attachEventHandlers() {
        // Event handler untuk btn-action-detail (tombol edit)
        $('.btn-action-detail').off('click').on('click', function() {
            const timelineId = $(this).data('id');
            loadTimelineDetail(timelineId);
        });
        
        // Event handler untuk btn-delete-timeline
        $('.btn-delete-timeline').off('click').on('click', function() {
            const timelineId = $(this).data('id');
            confirmDeleteTimeline(timelineId);
        });
    }
    
    function resetTimelineForm() {
        $("#nama_timeline").val('');
        $("#tanggal_mulai_timeline").val('');
        $("#tanggal_selesai_timeline").val('');
        $("#deskripsi_timeline").val('');
        resetFormErrors();
    }

    function resetFormErrors() {
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").empty(); 
        $("#form_timeline_error").addClass('d-none').empty(); 
    }

    function renderTimelineCollection() {
        const tbody = $("#daftarTimeline");
        
        if (timelineCollection.length === 0) {
            tbody.html(`
                <tr id="emptyRowTimeline">
                    <td colspan="4" class="text-center">Belum ada timeline yang ditambahkan ke daftar</td>
                </tr>
            `);
            return;
        }
        
        // Hapus row kosong jika ada
        $("#emptyRowTimeline").remove();
        
        // Clear tbody dan re-render semua item
        tbody.empty();
        
        timelineCollection.forEach((item, index) => {
            const formattedMulai = formatDate(item.tanggal_mulai_timeline);
            const formattedSelesai = formatDate(item.tanggal_selesai_timeline);
            
            tbody.append(`
                <tr>
                    <td>${item.nama_timeline}</td>
                    <td>${formattedMulai}</td>
                    <td>${formattedSelesai}</td>
                    <td>
                        <button type="button" class="btn btn-action-delete btn-remove-timeline" data-id="${item.id}">
                            <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `);
        });
        
        // Attach click event for remove button
        $(".btn-remove-timeline").off('click').on('click', function() {
            const id = $(this).data('id');
            removeTimelineItem(id);
        }); 
    }

    function removeTimelineItem(id) {
        timelineCollection = timelineCollection.filter(item => item.id !== id);
        $("#timelineJsonData").val(JSON.stringify(timelineCollection));
        
        // Set is_single ke 1 jika tidak ada item
        if (timelineCollection.length === 0) {
            $("#isSingleTimeline").val("1");
        }
        
        renderTimelineCollection();
    }

    $('#modalTambahTimeline').on('hidden.bs.modal', function () {
        resetTimelineForm();
        timelineCollection = [];
        $("#timelineJsonData").val('[]');
        $("#isSingleTimeline").val("1");
        renderTimelineCollection();
    });
}); 
