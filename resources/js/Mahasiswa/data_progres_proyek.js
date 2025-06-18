import swal from '../components';

document.addEventListener('DOMContentLoaded', function () {
    const proyekId = document.getElementById('proyek_id').value;
    
    // Initialize variables untuk both sections
    let progressList = [];
    let myProgressList = [];
    let proyekData = null;
    
    // Data Progres Proyek variables
    let currentPageProgresProyek = 1;
    let perPageProgresProyek = 3;
    
    // My Progres variables
    let currentPageMyProgres = 1;
    let perPageMyProgres = 3;
    
    // Initialize pada load
    // initializeSelect2();
    loadDataProgresProyek(1);
    loadMyProgresProyek(1);
    
    // Load project data pada awal
    loadProyekData();

    // Handle hash navigation
    if (window.location.hash === '#data-progres-proyek-section') {
        setTimeout(function() {
            scrollToDataProgresProyekSection();
        }, 300); 
    } else if (window.location.hash === '#my-progres-proyek-section') {
        setTimeout(function() {
            scrollToMyProgresSection();
        }, 300);
    }

    function scrollToDataProgresProyekSection() {
        const dataProgresProyekSection = $('#data-progres-proyek-section');
        if (dataProgresProyekSection.length) {
            $('html, body').animate({
                scrollTop: dataProgresProyekSection.offset().top - 80 
            }, 500);
        }
    }

    function scrollToMyProgresSection() {
        const myProgresSection = $('#my-progres-proyek-section');
        if (myProgresSection.length) {
            $('html, body').animate({
                scrollTop: myProgresSection.offset().top - 80 
            }, 500);
        }
    }

    // Function untuk load project data
    function loadProyekData() {
        const proyekId = $('input[name="proyek_id"]').val() || $('#my_proyek_id').val();
        
        if (!proyekId) {
            console.error('Proyek ID tidak ditemukan');
            return;
        }
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${proyekId}/get`,
            type: 'GET',
            data: {
                page: 1,
                per_page_progres_proyek: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.proyek) {
                    proyekData = response.proyek;
                    console.log('✅ ProyekData loaded successfully:', proyekData);
                } else {
                    console.error('Failed to load proyekData');
                }
            },
            error: function(xhr) {
                console.error("Error loading proyekData:", xhr.responseText);
            }
        });
    }

    // ================================
    // DATA PROGRES PROYEK SECTION
    // ================================

    // Pagination untuk Data Progres Proyek
    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        currentPageProgresProyek = $(this).data('page');
        loadDataProgresProyek(currentPageProgresProyek);
        
        $('html, body').animate({
            scrollTop: $('#tableDataProgresProyek').offset().top - 100
        }, 500);
    });

    // Search untuk Data Progres Proyek
    $('#searchProgresForm').on('submit', function(e) {
        e.preventDefault();
        const searchValue = $('#searchProgres').val();
        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('search', searchValue);
        currentUrl.hash = 'data-progres-proyek-section'; 
        
        window.history.pushState({}, '', currentUrl.toString());
        
        currentPageProgresProyek = 1;
        loadDataProgresProyek(currentPageProgresProyek);
        scrollToDataProgresProyekSection();
    });

    // Status change handlers
    $('#status_progres').on('change', function() {
        const selectedStatus = $(this).val();
        handleStatusChange(selectedStatus, '');
        
        // Auto set persentase berdasarkan status
        if (selectedStatus === 'Done') {
            $('#persentase_progres').val('100');
        } else if(selectedStatus === 'To Do'){
            $('#persentase_progres').val('0');
        }
    });

    $('#edit_status_progres').on('change', function() {
        const selectedStatus = $(this).val();
        handleStatusChange(selectedStatus, 'edit_');
        
        // Auto set persentase berdasarkan status
        if (selectedStatus === 'Done') {
            $('#edit_persentase_progres').val('100');
        } else if(selectedStatus === 'To Do'){
            $('#edit_persentase_progres').val('0');
        }
    });

    // Event handler untuk My Progres status change
    $(document).on('change', '#my_status_progres', function() {
        const selectedStatus = $(this).val();
        console.log('✅ My Progres status changed to:', selectedStatus);
        
        handleStatusChangeMyProgres(selectedStatus);
        
        // Auto set persentase berdasarkan status
        if (selectedStatus === 'Done') {
            $('#my_persentase_progres').val('100');
        } else if(selectedStatus === 'To Do'){
            $('#my_persentase_progres').val('0');
        }
    });

    function loadDataProgresProyek(page = 1) {
        const proyekId = $('input[name="proyek_id"]').val();
        const searchParam = $("#searchProgres").val() || '';
        
        $("#tableDataProgresProyek tbody").empty();
        
        $("#tableDataProgresProyek tbody").html(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2 text-muted small">Memuat data...</p>
                </td>
            </tr>
        `);
        
        $("#emptyDataProgresProyekMessage").addClass("d-none");
        $("#progresProyekPagination").html('');
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${proyekId}/get`,
            type: 'GET',
            data: {
                search: searchParam,
                page: page, 
                per_page_progres_proyek: perPageProgresProyek
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("#tableDataProgresProyek tbody").empty();
                
                if (typeof response === 'string' && response.indexOf('<!DOCTYPE html>') >= 0) {
                    console.error("Received HTML response instead of JSON");
                    showEmptyMessageProgresProyek(searchParam);
                    return;
                }
                
                if (response.success && response.data) {
                    if (response.proyek) {
                        proyekData = response.proyek;
                    }
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        if (response.pagination) {
                            updatePaginationProgresProyekInfo(
                                response.pagination.current_page,
                                response.pagination.per_page_progres_proyek,
                                response.pagination.total
                            );
                            
                            if (response.pagination.html) {
                                $("#progresProyekPagination").html(response.pagination.html);
                            }
                        }
                        renderProgresProyekTable(response.data, response);
                    } else {
                        showEmptyMessageProgresProyek(searchParam);
                        updatePaginationProgresProyekInfo(1, perPageProgresProyek, 0);
                    }
                } else {
                    showEmptyMessageProgresProyek(searchParam);
                    updatePaginationProgresProyekInfo(1, perPageProgresProyek, 0);
                }
            },
            error: function(xhr) {
                console.error("Error loading progresProyek:", xhr.responseText);
                $("#tableDataProgresProyek tbody").html(`
                    <tr>
                        <td colspan="8" class="text-center text-danger py-4">
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
                $("#progresProyekPagination").html('');
                updatePaginationProgresProyekInfo(1, perPageProgresProyek, 0);
            }
        });
    }

    // ================================
    // MY PROGRES SECTION
    // ================================

    // Pagination untuk My Progres
    $(document).on('click', '.my-progres-pagination-link', function(e) {
        e.preventDefault();
        currentPageMyProgres = $(this).data('page');
        loadMyProgresProyek(currentPageMyProgres);
        
        $('html, body').animate({
            scrollTop: $('#tableMyProgresProyek').offset().top - 100
        }, 500);
    });

    // Search untuk My Progres
    $('#searchMyProgresForm').on('submit', function(e) {
        e.preventDefault();
        const searchValue = $('#searchMyProgres').val();
        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('search_my_progres', searchValue);
        currentUrl.hash = 'my-progres-proyek-section'; 
        
        window.history.pushState({}, '', currentUrl.toString());
        
        currentPageMyProgres = 1;
        loadMyProgresProyek(currentPageMyProgres);
        scrollToMyProgresSection();
    });

    function loadMyProgresProyek(page = 1) {
        const proyekId = $('input[name="proyek_id"]').val();
        const searchParam = $("#searchMyProgres").val() || '';
        
        $("#tableMyProgresProyek tbody").empty();
        
        $("#tableMyProgresProyek tbody").html(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <p class="mt-2 text-muted small">Memuat my progres...</p>
                </td>
            </tr>
        `);
        
        $("#emptyMyProgresMessage").addClass("d-none");
        $("#myProgresPagination").html('');
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${proyekId}/my-progres/get`,
            type: 'GET',
            data: {
                search_my_progres_proyek: searchParam,
                page: page, 
                per_page_my_progres: perPageMyProgres
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("#tableMyProgresProyek tbody").empty();
                
                if (typeof response === 'string' && response.indexOf('<!DOCTYPE html>') >= 0) {
                    console.error("Received HTML response instead of JSON");
                    showEmptyMessageMyProgres(searchParam);
                    return;
                }
                
                if (response.success && response.data) {
                    // Update nama mahasiswa di modal jika ada
                    if (response.mahasiswaInfo && response.mahasiswaInfo.nama_mahasiswa) {
                        $('#my_assignment_display').val(response.mahasiswaInfo.nama_mahasiswa);
                    }
                    
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        if (response.pagination) {
                            updatePaginationMyProgresInfo(
                                response.pagination.current_page,
                                response.pagination.per_page_my_progres,
                                response.pagination.total
                            );
                            
                            if (response.pagination.html) {
                                $("#myProgresPagination").html(response.pagination.html.replace(/pagination-link/g, 'my-progres-pagination-link'));
                            }
                        }
                        renderMyProgresTable(response.data, response);
                    } else {
                        showEmptyMessageMyProgres(searchParam);
                        updatePaginationMyProgresInfo(1, perPageMyProgres, 0);
                    }
                } else {
                    showEmptyMessageMyProgres(searchParam);
                    updatePaginationMyProgresInfo(1, perPageMyProgres, 0);
                }
            },
            error: function(xhr) {
                console.error("Error loading my progres:", xhr.responseText);
                $("#tableMyProgresProyek tbody").html(`
                    <tr>
                        <td colspan="8" class="text-center text-danger py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 9L9 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 9L15 15" stroke="#FF5757" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p>Terjadi kesalahan saat memuat my progres</p>
                            <p class="small text-muted">Silakan coba lagi nanti</p>
                        </td>
                    </tr>
                `);
                $("#myProgresPagination").html('');
                updatePaginationMyProgresInfo(1, perPageMyProgres, 0);
            }
        });
    }

    // ================================
    // RENDER FUNCTIONS
    // ================================

    function renderProgresProyekTable(data, responseData) {
        const tableBody = $("#tableDataProgresProyek tbody");
        tableBody.empty();
        
        if (!data || !Array.isArray(data) || data.length === 0) {
            const searchParam = $("#searchProgres").val() || '';
            showEmptyMessageProgresProyek(searchParam);
            return;
        }
        
        $("#emptyDataProgresProyekMessage").addClass("d-none");
        
        // Mahasiswa selalu member, bukan leader
        const isLeader = false;
        let tableHtml = '';
        
        data.forEach((progresProyek, index) => {
            const progresProyekId = progresProyek.progres_proyek_id;
            
            if (!progresProyekId) {
                console.warn("Missing progresProyek ID for item at index", index, progresProyek);
                return;
            }
            
            const namaProgresProyek = progresProyek.nama_progres || 'Unnamed';
            const statusProgresProyek = progresProyek.status_progres || 'Unknown';
            const assignedTo = progresProyek.assigned_name || 'Not Assigned';

            // Format dates
            const tanggalMulai = progresProyek.tanggal_mulai_progres ? formatDate(progresProyek.tanggal_mulai_progres) : '-';
            const tanggalSelesai = progresProyek.tanggal_selesai_progres ? formatDate(progresProyek.tanggal_selesai_progres) : '-';
            const updatedAt = progresProyek.updated_at ? formatDate(progresProyek.updated_at) : formatDate(progresProyek.created_at);
            
            let badgeClass = '';
            let statusHtml = '';
            if (statusProgresProyek === 'Done') {
                badgeClass = 'badge bg-success';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
            } else if (statusProgresProyek === 'In Progress') {
                badgeClass = 'badge bg-primary';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
                
                // Check if overdue
                if (progresProyek.is_overdue) {
                    statusHtml += ` <span class="badge bg-danger ms-1">Overdue</span>`;
                }
            } else if (statusProgresProyek === 'To Do') {
                badgeClass = 'badge bg-secondary';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
            }
            
            let actionButtons = '';
            const canEdit = progresProyek.can_edit || false;
            const canDelete = progresProyek.can_delete || false;
            
            // EDIT BUTTON - View button for read-only, Edit for editable
            if (canEdit) {
                actionButtons += `
                    <button type="button" class="btn btn-action-detail-progres" data-id="${progresProyekId}" data-bs-toggle="modal" data-bs-target="#modalEditProgres" title="Edit progres">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                            <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                        </svg>
                    </button>`;
            } else {
                actionButtons += `
                    <button type="button" class="btn btn-action-detail-progres" data-id="${progresProyekId}" data-bs-toggle="modal" data-bs-target="#modalEditProgres" title="Lihat detail (read-only)">
                        <svg width="15" height="15" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.0571 10.9056C21.4729 11.3872 21.6808 11.628 21.6808 12C21.6808 12.372 21.4729 12.6128 21.0571 13.0944C19.5628 14.8252 16.307 18 12.5313 18C8.7555 18 5.49977 14.8252 4.00541 13.0944C3.58961 12.6128 3.38171 12.372 3.38171 12C3.38171 11.628 3.58961 11.3872 4.00541 10.9056C5.49977 9.17485 8.7555 6 12.5313 6C16.307 6 19.5628 9.17485 21.0571 10.9056Z" fill="#3C21F7"/>
                            <path d="M15.6641 12C15.6641 13.6569 14.2615 15 12.5313 15C10.801 15 9.39844 13.6569 9.39844 12C9.39844 10.3431 10.801 9 12.5313 9C14.2615 9 15.6641 10.3431 15.6641 12Z" fill="white"/>
                        </svg>
                    </button>`;
            }
            
            // DELETE BUTTON - hanya untuk progres yang bisa dihapus
            if (canDelete) {
                actionButtons += `
                    <button type="button" class="btn btn-action-delete btn-delete-progres-proyek" data-id="${progresProyekId}" title="Hapus progres yang Anda buat">
                        <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                        </svg>
                    </button>`;
            }
            
            let nameDisplay = namaProgresProyek;
            if (canEdit && !canDelete) {
                nameDisplay = `${namaProgresProyek} <span class="badge bg-warning ms-1">assigned to you</span>`;
            }
            
            tableHtml += `
                <tr data-id="${progresProyekId}">
                    <td>${nameDisplay}</td>
                    <td>${statusHtml}</td>
                    <td>${progresProyek.persentase_progres}%</td>
                    <td>${assignedTo}</td>
                    <td>${tanggalMulai}</td>
                    <td>${tanggalSelesai}</td>
                    <td>${updatedAt}</td>
                    <td>
                        <div class="d-flex gap-2">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tableBody.html(tableHtml);
        attachEventHandlers();
    }

    function renderMyProgresTable(data, responseData) {
        const tableBody = $("#tableMyProgresProyek tbody");
        tableBody.empty();
        
        if (!data || !Array.isArray(data) || data.length === 0) {
            const searchParam = $("#searchMyProgres").val() || '';
            showEmptyMessageMyProgres(searchParam);
            return;
        }
        
        $("#emptyMyProgresMessage").addClass("d-none");
        
        // Mahasiswa selalu member, bukan leader
        const isLeader = false;
        let tableHtml = '';
        
        data.forEach((progresProyek, index) => {
            const progresProyekId = progresProyek.progres_proyek_id;
            
            if (!progresProyekId) {
                console.warn("Missing my progres ID for item at index", index, progresProyek);
                return;
            }
            
            const namaProgresProyek = progresProyek.nama_progres || 'Unnamed';
            const statusProgresProyek = progresProyek.status_progres || 'Unknown';
            const assignedTo = progresProyek.assigned_name || 'Not Assigned';
            
            // Format dates
            const tanggalMulai = progresProyek.tanggal_mulai_progres ? formatDate(progresProyek.tanggal_mulai_progres) : '-';
            const tanggalSelesai = progresProyek.tanggal_selesai_progres ? formatDate(progresProyek.tanggal_selesai_progres) : '-';
            const updatedAt = progresProyek.updated_at ? formatDate(progresProyek.updated_at) : formatDate(progresProyek.created_at);
            
            let badgeClass = '';
            let statusHtml = '';
            if (statusProgresProyek === 'Done') {
                badgeClass = 'badge bg-success';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
            } else if (statusProgresProyek === 'In Progress') {
                badgeClass = 'badge bg-primary';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
                
                // Check if overdue
                if (progresProyek.is_overdue) {
                    statusHtml += ` <span class="badge bg-danger ms-1">Overdue</span>`;
                }
            } else if (statusProgresProyek === 'To Do') {
                badgeClass = 'badge bg-secondary';
                statusHtml = `<span class="${badgeClass}">${statusProgresProyek}</span>`;
            }
            
            // Progress type badge dan creation status
            let typeBadge = '';
            let nameDisplay = namaProgresProyek;
            
            if (progresProyek.progress_type === 'assigned') {
                typeBadge = '<span class="badge bg-warning ms-1">assigned to you</span>';
            } else if (progresProyek.progress_type === 'created') {
                typeBadge = '<span class="badge bg-primary ms-1">created by you</span>';
            } else if (progresProyek.progress_type === 'created_and_assigned') {
                typeBadge = '<span class="badge bg-success ms-1">your task</span>';
            }

            nameDisplay = `${namaProgresProyek} ${typeBadge}`;
            
            // Action buttons berdasarkan can_edit dan can_delete dari backend
            let actionButtons = '';
            const canEdit = progresProyek.can_edit || false;
            const canDelete = progresProyek.can_delete || false;
            
            // EDIT BUTTON
            if (canEdit) {
                actionButtons += `
                    <button type="button" class="btn btn-action-detail-progres" data-id="${progresProyekId}" data-bs-toggle="modal" data-bs-target="#modalEditProgres" title="Edit my progres">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                            <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                        </svg>
                    </button>`;
            }
            
            // DELETE BUTTON
            if (canDelete) {
                actionButtons += `
                    <button type="button" class="btn btn-action-delete btn-delete-progres-proyek" data-id="${progresProyekId}" title="Hapus progres yang Anda buat">
                        <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                        </svg>
                    </button>`;
            }
            
            if (!actionButtons.trim()) {
                actionButtons = '<span class="text-muted small">No actions available</span>';
            }
            
            tableHtml += `
                <tr data-id="${progresProyekId}">
                    <td>${nameDisplay}</td>
                    <td>${statusHtml}</td>
                    <td>${progresProyek.persentase_progres}%</td>
                    <td>${assignedTo}</td>
                    <td>${tanggalMulai}</td>
                    <td>${tanggalSelesai}</td>
                    <td>${updatedAt}</td>
                    <td>
                        <div class="d-flex gap-2">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tableBody.html(tableHtml);
        attachEventHandlers();
    }

    // ================================
    // COMMON FUNCTIONS
    // ================================

    function attachEventHandlers() {
        $('.btn-delete-progres-proyek').off('click').on('click', function() {
            const id = $(this).data('id');
            confirmDeleteProgresProyek(id);
        });

        $('.btn-action-detail-progres').off('click').on('click', function() {
            const id = $(this).data('id');
            loadProgresProyekDetail(id);
        });
    }

    function loadProgresProyekDetail(id) {
        $('#edit_form_error').addClass('d-none').text('');
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        resetAllModalRestrictions();
        
        $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', true);
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${id}/detail`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', false);
                
                if (response.success) {
                    const progres = response.data;
                    const isLeader = false; // Mahasiswa tidak pernah leader
                    const canEdit = response.canEdit || false;
                    const editableFields = response.editableFields || [];
                    const isCreatedByCurrentUser = response.isCreatedByCurrentUser || false;
                    
                    // Set ALL field values
                    $('#edit_progres_id').val(progres.progres_proyek_id);
                    $('#edit_nama_progres').val(progres.nama_progres);
                    $('#edit_status_progres').val(progres.status_progres);
                    $('#edit_persentase_progres').val(progres.persentase_progres);
                    $('#edit_deskripsi_progres').val(progres.deskripsi_progres || '');
                    
                    // Set tanggal values
                    $('#edit_tanggal_mulai_progres').val(progres.tanggal_mulai_progres || '');
                    $('#edit_tanggal_selesai_progres').val(progres.tanggal_selesai_progres || '');
                    
                    // Trigger status change
                    handleStatusChange(progres.status_progres, 'edit_');
                    
                    // For mahasiswa, no assignment change allowed - just display info
                    $('#edit_assigned_to').val(progres.assigned_to || '');
                    $('#edit_assigned_type_hidden').val(progres.assigned_type || '');
                    $('#edit_assigned_type').val(progres.assigned_type || '').trigger('change');
                    
                    // Apply field restrictions after loading data
                    setTimeout(function() {
                        applyFieldRestrictions(isLeader, canEdit, editableFields, isCreatedByCurrentUser);
                    }, 150);
                } else {
                    $('#edit_form_error').removeClass('d-none').text(response.message || 'Gagal memuat data progres');
                }
            },
            error: function(xhr) {
                $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', false);
                $('#edit_form_error').removeClass('d-none').text('Terjadi kesalahan saat memuat data');
                console.error('Error loading progress detail:', xhr.responseText);
            }
        });
    }

    function applyFieldRestrictions(isLeader, canEdit, editableFields, isCreatedByCurrentUser = false) {
        const allFields = {
            'nama_progres': '#edit_nama_progres',
            'status_progres': '#edit_status_progres', 
            'persentase_progres': '#edit_persentase_progres',
            'deskripsi_progres': '#edit_deskripsi_progres',
            'assigned_type': '#edit_assigned_type',
            'assigned_to': '#edit_assigned_to'
        };
        
        // Reset semua field ke state normal terlebih dahulu
        Object.values(allFields).forEach(selector => {
            const $field = $(selector);
            $field.prop('disabled', false);
            $field.prop('readonly', false);
            $field.removeClass('text-muted');
            $field.removeAttr('title');
        });
        
        // Remove existing notices
        $('#assignment-restriction-notice').remove();
        $('#member-edit-notice').remove();
        $('#readonly-notice').remove();
        $('#created-edit-notice').remove();
        $('#assigned-edit-notice').remove();
        
        if (!canEdit) {
            // READ-ONLY MODE untuk mahasiswa
            Object.values(allFields).forEach(fieldSelector => {
                $(fieldSelector).prop('disabled', true).addClass('text-muted');
                if ($(fieldSelector).is('input[type="text"], input[type="number"], textarea')) {
                    $(fieldSelector).attr('readonly', true);
                }
                $(fieldSelector).attr('title', 'Anda tidak dapat mengedit progres yang tidak ditugaskan kepada Anda');
            });
            
            $('#edit_deskripsi_progres').prop('disabled', true).addClass('text-muted').attr('readonly', true);
            
            // Disable date fields
            $('#edit_tanggal_mulai_progres').prop('disabled', true).addClass('text-muted');
            $('#edit_tanggal_selesai_progres').prop('disabled', true).addClass('text-muted');
            
            $('#btnUpdateProgres').prop('disabled', true);
            $('#btnUpdateProgres').html('<i class="bi bi-eye me-2"></i>Lihat Saja (Read Only)');
            
            $('#edit_nama_progres').closest('.mb-3').before(`
                <div id="readonly-notice" class="alert alert-info alert-sm mb-3">
                    <small>Sebagai mahasiswa, Anda sedang melihat progres yang tidak ditugaskan kepada Anda. Semua field hanya dapat dibaca saja.</small>
                </div>
            `);
            
        } else {
            // EDIT MODE untuk mahasiswa
            Object.keys(allFields).forEach(fieldName => {
                const fieldSelector = allFields[fieldName];
                const isEditable = editableFields.includes(fieldName);
                
                $(fieldSelector).prop('disabled', !isEditable);
                
                if (!isEditable) {
                    $(fieldSelector).addClass('text-muted');
                    if (fieldName === 'nama_progres' && !isCreatedByCurrentUser) {
                        $(fieldSelector).attr('title', 'Nama progres hanya dapat diedit oleh yang membuatnya');
                    } else if (fieldName === 'assigned_type' || fieldName === 'assigned_to') {
                        $(fieldSelector).attr('title', 'Mahasiswa tidak dapat mengubah assignment progres');
                    } else {
                        $(fieldSelector).attr('title', 'Field ini tidak dapat diedit oleh mahasiswa');
                    }
                } else {
                    $(fieldSelector).removeClass('text-muted');
                    $(fieldSelector).removeAttr('title');
                }
            });
            
            // Deskripsi field handling
            const canEditDescription = editableFields.includes('deskripsi_progres');
            $('#edit_deskripsi_progres').prop('disabled', !canEditDescription);
            
            if (!canEditDescription) {
                $('#edit_deskripsi_progres').addClass('text-muted');
                $('#edit_deskripsi_progres').attr('title', 'Field ini tidak dapat diedit oleh mahasiswa');
            } else {
                $('#edit_deskripsi_progres').removeClass('text-muted');
                $('#edit_deskripsi_progres').removeAttr('title');
            }
            
            // Assignment fields always disabled for mahasiswa
            $('#edit_assigned_type').prop('disabled', true).addClass('text-muted');
            $('#edit_assigned_type').attr('title', 'Mahasiswa tidak dapat mengubah assignment progres');
            
            $('#btnUpdateProgres').prop('disabled', false);
            $('#btnUpdateProgres').html('<i class="bi bi-save me-2"></i>Update Progress');
            
            // Show appropriate notice
            if (isCreatedByCurrentUser) {
                $('#edit_nama_progres').closest('.mb-3').before(`
                    <div id="created-edit-notice" class="alert alert-success alert-sm mb-3">
                        <small><strong>Progres yang Anda buat:</strong> Anda dapat mengedit nama, status, persentase, dan deskripsi progres.</small>
                    </div>
                `);
            } else {
                $('#edit_nama_progres').closest('.mb-3').before(`
                    <div id="assigned-edit-notice" class="alert alert-warning alert-sm mb-3">
                        <small><strong>Progres yang ditugaskan:</strong> Anda dapat mengedit status, persentase, dan deskripsi progres (nama tidak dapat diubah)</small>
                    </div>
                `);
            }
        }
    }

    function confirmDeleteProgresProyek(progresProyekId) {
        const confirmMessage = 'Apakah Anda yakin ingin menghapus progres ini?';
        
        swal.confirmationDelete(confirmMessage).then((result) => {
            if (result.isConfirmed) {
                deleteProgresProyek(progresProyekId);
            }
        });
    }

    function deleteProgresProyek(progresProyekId) {
        $.ajax({
            url: `/mahasiswa/progres-proyek/${progresProyekId}/delete`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    swal.successMessage(response.message);
                    loadDataProgresProyek(currentPageProgresProyek);
                    loadMyProgresProyek(currentPageMyProgres);
                } else {
                    swal.errorMessage(response.message || 'Gagal menghapus progres proyek');
                }
            },
            error: function(xhr) {
                console.error("Error deleting progres proyek:", xhr.responseText);
                
                const response = xhr.responseJSON;
                let errorMessage = 'Terjadi kesalahan saat menghapus progres proyek';
                
                if (response && response.message) {
                    errorMessage = response.message;
                }
                
                swal.errorMessage(errorMessage);
            }
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID');
    }

    function handleStatusChange(status, prefix) {
        const tanggalMulaiSection = $(`#${prefix}tanggal_mulai_progres_section`);
        const tanggalSelesaiSection = $(`#${prefix}tanggal_selesai_progres_section`);
        const tanggalMulaiInput = $(`#${prefix}tanggal_mulai_progres`);
        const tanggalSelesaiInput = $(`#${prefix}tanggal_selesai_progres`);
        
        if (status === 'In Progress') {
            tanggalMulaiSection.removeClass('d-none');
            tanggalSelesaiSection.removeClass('d-none');
            tanggalMulaiInput.prop('required', true);
            tanggalSelesaiInput.prop('required', true);
            
            if (proyekData) {
                const proyekMulaiField = proyekData.tanggal_mulai_progres || proyekData.tanggal_mulai;
                const proyekSelesaiField = proyekData.tanggal_selesai_progres || proyekData.tanggal_selesai;
                
                if (proyekMulaiField && proyekSelesaiField) {
                    $(`#${prefix}tanggal_mulai_progres_hint`).text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    $(`#${prefix}tanggal_selesai_progres_hint`).text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    
                    tanggalMulaiInput.attr('min', proyekMulaiField);
                    tanggalMulaiInput.attr('max', proyekSelesaiField);
                    tanggalSelesaiInput.attr('min', proyekMulaiField);
                    tanggalSelesaiInput.attr('max', proyekSelesaiField);
                }
            }
        } else {
            tanggalMulaiSection.addClass('d-none');
            tanggalSelesaiSection.addClass('d-none');
            tanggalMulaiInput.prop('required', false);
            tanggalSelesaiInput.prop('required', false);
            
            if (prefix !== 'edit_') {
                tanggalMulaiInput.val('');
                tanggalSelesaiInput.val('');
            }
            
            $(`#${prefix}tanggal_mulai_progres_error`).text('');
            $(`#${prefix}tanggal_selesai_progres_error`).text('');
            $(`#${prefix}tanggal_mulai_progres`).removeClass('is-invalid');
            $(`#${prefix}tanggal_selesai_progres`).removeClass('is-invalid');
        }
    }

    // Function untuk handle status change My Progres
    function handleStatusChangeMyProgres(status) {
        const tanggalMulaiSection = $('#my_tanggal_mulai_progres_section');
        const tanggalSelesaiSection = $('#my_tanggal_selesai_progres_section');
        const tanggalMulaiInput = $('#my_tanggal_mulai_progres');
        const tanggalSelesaiInput = $('#my_tanggal_selesai_progres');
        
        if (status === 'In Progress') {
            tanggalMulaiSection.removeClass('d-none');
            tanggalSelesaiSection.removeClass('d-none');
            tanggalMulaiInput.prop('required', true);
            tanggalSelesaiInput.prop('required', true);
            
            if (proyekData) {
                const proyekMulaiField = proyekData.tanggal_mulai_progres || proyekData.tanggal_mulai;
                const proyekSelesaiField = proyekData.tanggal_selesai_progres || proyekData.tanggal_selesai;
                
                if (proyekMulaiField && proyekSelesaiField) {
                    $('#my_tanggal_mulai_progres_hint').text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    $('#my_tanggal_selesai_progres_hint').text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    
                    tanggalMulaiInput.attr('min', proyekMulaiField);
                    tanggalMulaiInput.attr('max', proyekSelesaiField);
                    tanggalSelesaiInput.attr('min', proyekMulaiField);
                    tanggalSelesaiInput.attr('max', proyekSelesaiField);
                }
            }
        } else {
            tanggalMulaiSection.addClass('d-none');
            tanggalSelesaiSection.addClass('d-none');
            tanggalMulaiInput.prop('required', false);
            tanggalSelesaiInput.prop('required', false);
            tanggalMulaiInput.val('');
            tanggalSelesaiInput.val('');
            
            $('#my_tanggal_mulai_progres_error').text('');
            $('#my_tanggal_selesai_progres_error').text('');
            $('#my_tanggal_mulai_progres').removeClass('is-invalid');
            $('#my_tanggal_selesai_progres').removeClass('is-invalid');
        }
    }

    function validateDates(tanggalMulai, tanggalSelesai, prefix = '') {
        const errors = {};
        
        if (!proyekData) {
            if (tanggalMulai) {
                errors[`${prefix}tanggal_mulai_progres`] = 'Data proyek belum dimuat. Silakan tutup dan buka kembali modal ini.';
            }
            if (tanggalSelesai) {
                errors[`${prefix}tanggal_selesai_progres`] = 'Data proyek belum dimuat. Silakan tutup dan buka kembali modal ini.';
            }
            return errors;
        }
        
        const proyekMulaiField = proyekData.tanggal_mulai_progres || proyekData.tanggal_mulai;
        const proyekSelesaiField = proyekData.tanggal_selesai_progres || proyekData.tanggal_selesai;
        
        if (!proyekMulaiField || !proyekSelesaiField) {
            if (tanggalMulai) {
                errors[`${prefix}tanggal_mulai_progres`] = 'Data tanggal proyek tidak lengkap.';
            }
            if (tanggalSelesai) {
                errors[`${prefix}tanggal_selesai_progres`] = 'Data tanggal proyek tidak lengkap.';
            }
            return errors;
        }
        
        const proyekMulai = new Date(proyekMulaiField);
        const proyekSelesai = new Date(proyekSelesaiField);
        
        if (tanggalMulai) {
            const mulai = new Date(tanggalMulai);
            
            if (mulai < proyekMulai) {
                errors[`${prefix}tanggal_mulai_progres`] = `Tanggal mulai tidak boleh sebelum ${formatDate(proyekMulaiField)}`;
            }
            if (mulai > proyekSelesai) {
                errors[`${prefix}tanggal_mulai_progres`] = `Tanggal mulai tidak boleh setelah ${formatDate(proyekSelesaiField)}`;
            }
        }
        
        if (tanggalSelesai) {
            const selesai = new Date(tanggalSelesai);
            
            if (selesai < proyekMulai) {
                errors[`${prefix}tanggal_selesai_progres`] = `Tanggal selesai tidak boleh sebelum ${formatDate(proyekMulaiField)}`;
            }
            if (selesai > proyekSelesai) {
                errors[`${prefix}tanggal_selesai_progres`] = `Tanggal selesai tidak boleh setelah ${formatDate(proyekSelesaiField)}`;
            }
        }
        
        if (tanggalMulai && tanggalSelesai) {
            const mulai = new Date(tanggalMulai);
            const selesai = new Date(tanggalSelesai);
            
            if (mulai > selesai) {
                errors[`${prefix}tanggal_selesai_progres`] = 'Tanggal selesai tidak boleh sebelum tanggal mulai';
            }
        }
        
        return errors;
    }

    // ================================
    // MY PROGRES MODAL FUNCTIONS
    // ================================

    $('#modalTambahProgresFromMahasiswaSelf').on('hidden.bs.modal', function () {
        $('#formTambahMyProgres')[0].reset();
        $('.invalid-feedback').text('');
        $('#my_form_progres_error').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        // Hide date sections
        $('#my_tanggal_mulai_progres_section').addClass('d-none');
        $('#my_tanggal_selesai_progres_section').addClass('d-none');
        
        // Clear date values
        $('#my_tanggal_mulai_progres').val('').prop('required', false);
        $('#my_tanggal_selesai_progres').val('').prop('required', false);
        
        // Clear date hints
        $('#my_tanggal_mulai_progres_hint').text('');
        $('#my_tanggal_selesai_progres_hint').text('');
        
        myProgressList = [];
        $('#myProgresJsonData').val('[]');
        $('#myIsSingleProgres').val('1');
        updateMyProgressTable();
    });

    // My Progres add to list dengan validasi tanggal yang benar
    $('#btnTambahkanKeDaftarMyProgres').on('click', function() {
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('#my_form_progres_error').addClass('d-none').text('');
        
        const namaProgres = $('#my_nama_progres').val();
        const statusProgres = $('#my_status_progres').val();
        let persentaseProgres = $('#my_persentase_progres').val();
        const deskripsiProgres = $('#my_deskripsi_progres').val();
        const tanggalMulai = $('#my_tanggal_mulai_progres').val();
        const tanggalSelesai = $('#my_tanggal_selesai_progres').val();
        
        let isValid = true;
        
        if (!namaProgres) {
            $('#my_nama_progres').addClass('is-invalid');
            $('#my_nama_progres_error').text('Nama progres harus diisi');
            isValid = false;
        }
        
        if (!statusProgres) {
            $('#my_status_progres').addClass('is-invalid');
            $('#my_status_progres_error').text('Status progres harus dipilih');
            isValid = false;
        }
        
        if (persentaseProgres !== '') {
            if (isNaN(persentaseProgres)) {
                $('#my_persentase_progres').addClass('is-invalid');
                $('#my_persentase_progres_error').text('Persentase harus berupa angka');
                isValid = false;
            } else if (persentaseProgres < 0 || persentaseProgres > 100) {
                $('#my_persentase_progres').addClass('is-invalid');
                $('#my_persentase_progres_error').text('Persentase harus antara 0-100');
                isValid = false;
            }
        } else {
            persentaseProgres = '0';
            $('#my_persentase_progres').val('0');
        }
        
        // Validasi tanggal untuk My Progres
        if (statusProgres === 'In Progress') {
            $('#my_tanggal_mulai_progres_error').text('');
            $('#my_tanggal_selesai_progres_error').text('');
            $('#my_tanggal_mulai_progres').removeClass('is-invalid');
            $('#my_tanggal_selesai_progres').removeClass('is-invalid');
            
            if (!tanggalMulai) {
                $('#my_tanggal_mulai_progres').addClass('is-invalid');
                $('#my_tanggal_mulai_progres_error').text('Tanggal mulai harus diisi untuk status In Progress');
                isValid = false;
            }
            
            if (!tanggalSelesai) {
                $('#my_tanggal_selesai_progres').addClass('is-invalid');
                $('#my_tanggal_selesai_progres_error').text('Tanggal selesai harus diisi untuk status In Progress');
                isValid = false;
            }
            
            if (!proyekData) {
                $('#my_form_progres_error').removeClass('d-none').text('Data proyek belum dimuat. Tutup dan buka kembali modal ini.');
                isValid = false;
            } else {
                if (tanggalMulai || tanggalSelesai) {
                    const dateErrors = validateDates(tanggalMulai, tanggalSelesai, 'my_');
                    
                    if (Object.keys(dateErrors).length > 0) {
                        for (const field in dateErrors) {
                            $(`#${field}_error`).text(dateErrors[field]);
                            $(`#${field}`).addClass('is-invalid');
                        }
                        isValid = false;
                    }
                }
            }
        } else {
            $('#my_tanggal_mulai_progres_error').text('');
            $('#my_tanggal_selesai_progres_error').text('');
            $('#my_tanggal_mulai_progres').removeClass('is-invalid');
            $('#my_tanggal_selesai_progres').removeClass('is-invalid');
        }
        
        if (!isValid) {
            return;
        }
        
        const progressItem = {
            nama_progres: namaProgres,
            status_progres: statusProgres,
            persentase_progres: persentaseProgres,
            deskripsi_progres: deskripsiProgres,
            tanggal_mulai_progres: tanggalMulai,
            tanggal_selesai_progres: tanggalSelesai,
            assigned_name: 'Saya (Auto)'
        };
        
        myProgressList.push(progressItem);
        $('#myProgresJsonData').val(JSON.stringify(myProgressList));
        $('#myIsSingleProgres').val('0');
        
        updateMyProgressTable();
        
        // Reset form fields
        $('#my_nama_progres').val('');
        $('#my_status_progres').val('').trigger('change');
        $('#my_persentase_progres').val('0');
        $('#my_deskripsi_progres').val('');
        $('#my_tanggal_mulai_progres').val('');
        $('#my_tanggal_selesai_progres').val('');
        $('#my_form_progres_error').addClass('d-none');
    });

    function updateMyProgressTable() {
        const tableBody = $('#daftarMyProgres');
        
        tableBody.empty();
        
        if (myProgressList.length === 0) {
            tableBody.append(`
                <tr id="emptyRowMyProgres">
                    <td colspan="7" class="text-center">Belum ada progres yang ditambahkan ke daftar</td>
                </tr>
            `);
        } else {
            myProgressList.forEach((item, index) => {
                const tanggalMulai = item.tanggal_mulai_progres ? formatDate(item.tanggal_mulai_progres) : '-';
                const tanggalSelesai = item.tanggal_selesai_progres ? formatDate(item.tanggal_selesai_progres) : '-';
                
                tableBody.append(`
                    <tr>
                        <td>${item.nama_progres}</td>
                        <td>${item.status_progres}</td>
                        <td>${item.persentase_progres}%</td>
                        <td>${tanggalMulai}</td>
                        <td>${tanggalSelesai}</td>
                        <td>${item.assigned_name}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-my-progres" data-index="${index}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
        
        $('.remove-my-progres').on('click', function() {
            const index = $(this).data('index');
            myProgressList.splice(index, 1);
            $('#myProgresJsonData').val(JSON.stringify(myProgressList));
            
            if (myProgressList.length === 0) {
                $('#myIsSingleProgres').val('1');
            }
            
            updateMyProgressTable();
        });
    }

    // ================================
    // EDIT MODAL FUNCTIONS
    // ================================

    $('#formEditProgres').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if ($(form).data('submitting')) {
            return;
        }
        
        $(form).data('submitting', true);
        
        $('.invalid-feedback').text('');
        $('#edit_form_error').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        const progresId = $('#edit_progres_id').val();
        
        if (!progresId) {
            $('#edit_form_error').removeClass('d-none').text('ID progres tidak valid');
            $(form).data('submitting', false);
            return;
        }
        
        // Validasi tanggal untuk status In Progress di modal edit
        const statusProgres = $('#edit_status_progres').val();
        const tanggalMulai = $('#edit_tanggal_mulai_progres').val();
        const tanggalSelesai = $('#edit_tanggal_selesai_progres').val();
        
        let isValid = true;
        
        if (statusProgres === 'In Progress') {
            $('#edit_tanggal_mulai_progres_error').text('');
            $('#edit_tanggal_selesai_progres_error').text('');
            $('#edit_tanggal_mulai_progres').removeClass('is-invalid');
            $('#edit_tanggal_selesai_progres').removeClass('is-invalid');
            
            if (!tanggalMulai) {
                $('#edit_tanggal_mulai_progres').addClass('is-invalid');
                $('#edit_tanggal_mulai_progres_error').text('Tanggal mulai harus diisi untuk status In Progress');
                isValid = false;
            }
            
            if (!tanggalSelesai) {
                $('#edit_tanggal_selesai_progres').addClass('is-invalid');
                $('#edit_tanggal_selesai_progres_error').text('Tanggal selesai harus diisi untuk status In Progress');
                isValid = false;
            }
            
            if (!proyekData) {
                $('#edit_form_error').removeClass('d-none').text('Data proyek belum dimuat. Tutup dan buka kembali modal ini.');
                isValid = false;
            } else {
                if (tanggalMulai || tanggalSelesai) {
                    const dateErrors = validateDates(tanggalMulai, tanggalSelesai, 'edit_');
                    
                    if (Object.keys(dateErrors).length > 0) {
                        for (const field in dateErrors) {
                            $(`#${field}_error`).text(dateErrors[field]);
                            $(`#${field}`).addClass('is-invalid');
                        }
                        isValid = false;
                    }
                }
            }
        } else {
            $('#edit_tanggal_mulai_progres_error').text('');
            $('#edit_tanggal_selesai_progres_error').text('');
            $('#edit_tanggal_mulai_progres').removeClass('is-invalid');
            $('#edit_tanggal_selesai_progres').removeClass('is-invalid');
        }
        
        if (!isValid) {
            $(form).data('submitting', false);
            return;
        }
        
        const formData = new FormData(form);
        
        const submitBtn = $('#btnUpdateProgres');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${progresId}/update`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                if (response.success) {
                    $('#modalEditProgres').modal('hide');
                    
                    swal.successMessage('Data progres proyek berhasil diperbarui')
                    .then(() => {
                        loadDataProgresProyek(currentPageProgresProyek);
                        loadMyProgresProyek(currentPageMyProgres);
                    });
                    
                    $(form).data('submitting', false);
                } else {
                    $('#edit_form_error').removeClass('d-none').text(response.message || 'Gagal memperbarui data');
                    $(form).data('submitting', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating progress:', error);
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                const response = xhr.responseJSON;
                
                if (response && response.errors) {
                    for (const field in response.errors) {
                        const errorField = `#edit_${field}_error`;
                        $(errorField).text(response.errors[field][0]);
                        $(`#edit_${field}`).addClass('is-invalid');
                    }
                    
                    $('#edit_form_error').removeClass('d-none').text('Mohon periksa kembali data yang dimasukkan.');
                } else {
                    $('#edit_form_error').removeClass('d-none').text(response.message || 'Terjadi kesalahan saat memperbarui data.');
                }
                
                $(form).data('submitting', false);
            }
        });
    });

    $('#modalEditProgres').on('hidden.bs.modal', function() {
        $('#formEditProgres')[0].reset();
        
        $('.invalid-feedback').text('');
        $('#edit_form_error').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        resetAllModalRestrictions();
        
        $('#edit_tanggal_mulai_progres_section').addClass('d-none');
        $('#edit_tanggal_selesai_progres_section').addClass('d-none');
        
        $('#edit_tanggal_mulai_progres').val('').prop('required', false);
        $('#edit_tanggal_selesai_progres').val('').prop('required', false);
        
        $('#edit_tanggal_mulai_progres_hint').text('');
        $('#edit_tanggal_selesai_progres_hint').text('');
    });

    function resetAllModalRestrictions() {
        // Remove ALL restriction notices
        $('#assignment-restriction-notice').remove();
        $('#member-edit-notice').remove();
        $('#readonly-notice').remove();
        $('#created-edit-notice').remove();
        $('#assigned-edit-notice').remove();
        
        const allEditFields = [
            '#edit_nama_progres',
            '#edit_status_progres', 
            '#edit_persentase_progres',
            '#edit_deskripsi_progres',
            '#edit_assigned_type',
            '#edit_assigned_to',
            '#edit_assigned_type_hidden',
            '#edit_tanggal_mulai_progres',
            '#edit_tanggal_selesai_progres'
        ];
        
        // Reset semua input fields
        allEditFields.forEach(selector => {
            const $field = $(selector);
            $field.prop('disabled', false);
            $field.prop('readonly', false);
            $field.removeClass('text-muted');
            $field.removeAttr('title');
            $field.css('opacity', '');
            $field.css('cursor', '');
        });
        
        $('#modalEditProgres input, #modalEditProgres select, #modalEditProgres textarea').each(function() {
            const $this = $(this);
            $this.prop('disabled', false);
            $this.prop('readonly', false);
            $this.removeClass('text-muted');
            $this.removeAttr('title');
            $this.css('opacity', '');
            $this.css('cursor', '');
        });
        
        $('#btnUpdateProgres').prop('disabled', false);
        $('#btnUpdateProgres').html('<i class="bi bi-save me-2"></i>Simpan Perubahan');
    }

    // Load project data for My Progres modal  
    $('#modalTambahProgresFromMahasiswaSelf').on('shown.bs.modal', function () {
        if (!proyekData) {
            loadProyekData();
        }
        
        // Load current mahasiswa name
        loadCurrentMahasiswaName();
    });

    function loadCurrentMahasiswaName() {
        const proyekId = $('input[name="proyek_id"]').val() || $('#my_proyek_id').val();
        
        $.ajax({
            url: `/mahasiswa/progres-proyek/${proyekId}/my-progres/get`,
            type: 'GET',
            data: {
                page: 1,
                per_page_my_progres: 1
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.mahasiswaInfo && response.mahasiswaInfo.nama_mahasiswa) {
                    $('#my_assignment_display').val(response.mahasiswaInfo.nama_mahasiswa);
                }
            },
            error: function(xhr) {
                console.error("Error loading mahasiswa name:", xhr.responseText);
                $('#my_assignment_display').val('Error loading name');
            }
        });
    }

    // Combined form submission handler untuk My Progres modal
    $('#formTambahMyProgres').on('submit', function(e) {
        e.preventDefault();
        
        const self = this;
        
        if ($(self).data('submitting')) {
            return;
        }
        
        $(self).data('submitting', true);
        
        $('.invalid-feedback').text('');
        $('#my_form_progres_error').addClass('d-none').text('');
        
        const formData = new FormData(this);
        
        // Check for list validation
        if ($('#myIsSingleProgres').val() === '0' && myProgressList.length === 0) {
            $('#my_form_progres_error').removeClass('d-none').text('Belum ada progres yang ditambahkan ke daftar.');
            $(self).data('submitting', false);
            return;
        }
        
        const submitBtn = $('#btnSimpanMyProgres');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '/mahasiswa/progres-proyek/my-progres/store',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);

                if (response.success) {
                    $(self).closest('.modal').modal('hide');
                        
                    swal.successMessage(response.message || 'My Progres berhasil disimpan').then(() => {
                        myProgressList = [];
                        $('#myProgresJsonData').val('[]');
                        $('#myIsSingleProgres').val('1');
                        
                        setTimeout(function() {
                            loadDataProgresProyek(currentPageProgresProyek);
                            loadMyProgresProyek(currentPageMyProgres);
                        }, 300);
                    });
                    
                    $(self).data('submitting', false);
    
                } else {
                    $('#my_form_progres_error').removeClass('d-none').text(response.message || 'Terjadi kesalahan');
                    $(self).data('submitting', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error in form submission:', error);
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                const response = xhr.responseJSON;
                
                if (response && response.errors) {
                    for (const field in response.errors) {
                        const errorField = `#my_${field}_error`;
                        $(errorField).text(response.errors[field][0]);
                        const fieldSelector = `#my_${field}`;
                        $(fieldSelector).addClass('is-invalid');
                    }
                    
                    swal.errorMessage('Mohon periksa kembali data yang dimasukkan.');
                } else {
                    swal.errorMessage('Terjadi kesalahan saat menyimpan data.');
                }
                
                $(self).data('submitting', false);
            }
        });
    });

    // ================================
    // HELPER FUNCTIONS
    // ================================

    function updatePaginationProgresProyekInfo(currentPage, perPage, total) {
        const from = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
        const to = Math.min(currentPage * perPage, total);
        
        $("#progresProyekPaginationInfo").html(`Showing ${from} to ${to} of ${total} entries`);
    }

    function updatePaginationMyProgresInfo(currentPage, perPage, total) {
        const from = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
        const to = Math.min(currentPage * perPage, total);
        
        $("#myProgresProyekPaginationInfo").html(`Showing ${from} to ${to} of ${total} entries`);
    }

    function showEmptyMessageProgresProyek(searchParam) {
        $("#tableDataProgresProyek tbody").empty();
        $("#emptyDataProgresProyekMessage").removeClass("d-none");
        
        if (searchParam) {
            $("#emptyDataProgresProyekMessage div").html(`
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <circle cx="11" cy="11" r="8" stroke="#8E8E8E" stroke-width="1.5"/>
                    <path d="M16.5 16.5L21 21" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 7V11" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 15H11.01" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-muted mb-1">Tidak ada data progres proyek ditemukan dengan kata kunci: <strong>"${searchParam}"</strong></p>
            `);
        } else {
            $("#emptyDataProgresProyekMessage div").html(`
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
                <p class="text-muted">Belum ada data progres proyek</p>
            `);
        }
    }

    function showEmptyMessageMyProgres(searchParam) {
        $("#tableMyProgresProyek tbody").empty();
        $("#emptyMyProgresMessage").removeClass("d-none");
        
        if (searchParam) {
            $("#emptyMyProgresMessage div").html(`
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                    <circle cx="11" cy="11" r="8" stroke="#8E8E8E" stroke-width="1.5"/>
                    <path d="M16.5 16.5L21 21" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 7V11" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11 15H11.01" stroke="#8E8E8E" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <p class="text-muted mb-1">Tidak ada my progres ditemukan dengan kata kunci: <strong>"${searchParam}"</strong></p>
            `);
        } else {
            $("#emptyMyProgresMessage div").html(`
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
                <p class="text-muted">Belum ada my progres</p>
            `);
        }
    }
});