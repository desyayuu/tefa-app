import swal from '../components';

document.addEventListener('DOMContentLoaded', function () {
    const proyekId = document.getElementById('proyek_id').value;
    initializeSelect2();
    
    let progressList = [];
    let currentPageProgresProyek = 1;
    let perPageProgresProyek = 3;
    let proyekData = null; 
    
    loadDataProgresProyek(1);

    if (window.location.hash === '#data-progres-proyek-section') {
        setTimeout(function() {
            scrollToDataProgresProyekSection();
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

    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        currentPageProgresProyek = $(this).data('page');
        loadDataProgresProyek(currentPageProgresProyek);
        
        $('html, body').animate({
            scrollTop: $('#tableDataProgresProyek').offset().top - 100
        }, 500);
    });

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

    // Handle status change to show/hide date fields
    $('#status_progres').on('change', function() {
        const selectedStatus = $(this).val();
        
        // Handle show/hide date fields
        handleStatusChange(selectedStatus, '');
        
        // Auto set persentase 100% untuk status Done
        if (selectedStatus === 'Done') {
            $('#persentase_progres').val('100');
            console.log('Status Done selected - Persentase set to 100%');
        }else if(selectedStatus === 'To Do'){
            $('#persentase_progres').val('0');
            console.log('Status To Do selected - Persentase set to 0%');
        }
    });

    $('#edit_status_progres').on('change', function() {
        const selectedStatus = $(this).val();
        
        // Handle show/hide date fields
        handleStatusChange(selectedStatus, 'edit_');
        
        // Auto set persentase 100% untuk status Done
        if (selectedStatus === 'Done') {
            $('#edit_persentase_progres').val('100');
        }else if(selectedStatus === 'To Do'){
            $('#edit_persentase_progres').val('0');
        }
    });

    // Perbaikan fungsi handleStatusChange
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
            
            // Set date hints if project data is available
            if (proyekData) {
                console.log('Setting date hints with proyekData:', proyekData);
                
                // PERBAIKAN: Gunakan field yang benar dari proyekData
                const proyekMulaiField = proyekData.tanggal_mulai || proyekData.tanggal_mulai_progres;
                const proyekSelesaiField = proyekData.tanggal_selesai || proyekData.tanggal_selesai_progres;
                
                if (proyekMulaiField && proyekSelesaiField) {
                    $(`#${prefix}tanggal_mulai_progres_hint`).text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    $(`#${prefix}tanggal_selesai_progres_hint`).text(`Rentang: ${formatDate(proyekMulaiField)} - ${formatDate(proyekSelesaiField)}`);
                    
                    // Set min and max attributes untuk browser validation
                    tanggalMulaiInput.attr('min', proyekMulaiField);
                    tanggalMulaiInput.attr('max', proyekSelesaiField);
                    tanggalSelesaiInput.attr('min', proyekMulaiField);
                    tanggalSelesaiInput.attr('max', proyekSelesaiField);
                } else {
                    console.warn('Project date fields not found:', proyekData);
                }
            } else {
                console.warn('proyekData not available yet');
            }
        } else {
            tanggalMulaiSection.addClass('d-none');
            tanggalSelesaiSection.addClass('d-none');
            tanggalMulaiInput.prop('required', false);
            tanggalSelesaiInput.prop('required', false);
            tanggalMulaiInput.val('');
            tanggalSelesaiInput.val('');
            
            // Clear error messages when hiding fields
            $(`#${prefix}tanggal_mulai_progres_error`).text('');
            $(`#${prefix}tanggal_selesai_progres_error`).text('');
            $(`#${prefix}tanggal_mulai_progres`).removeClass('is-invalid');
            $(`#${prefix}tanggal_selesai_progres`).removeClass('is-invalid');
        }
    }

    // Perbaikan fungsi validateDates
    function validateDates(tanggalMulai, tanggalSelesai, prefix = '') {
        const errors = {};
        
        console.log('validateDates called with:', { tanggalMulai, tanggalSelesai, prefix, proyekData });
        
        // Jika proyekData belum ada, berikan pesan error yang jelas
        if (!proyekData) {
            if (tanggalMulai) {
                errors[`${prefix}tanggal_mulai_progres`] = 'Data proyek belum dimuat. Silakan tutup dan buka kembali modal ini.';
            }
            if (tanggalSelesai) {
                errors[`${prefix}tanggal_selesai_progres`] = 'Data proyek belum dimuat. Silakan tutup dan buka kembali modal ini.';
            }
            return errors;
        }
        
        // PERBAIKAN: Gunakan field yang benar dari proyekData
        // Cek dulu field mana yang ada di proyekData
        const proyekMulaiField = proyekData.tanggal_mulai || proyekData.tanggal_mulai_progres;
        const proyekSelesaiField = proyekData.tanggal_selesai || proyekData.tanggal_selesai_progres;
        
        console.log('Project date fields:', { 
            tanggal_mulai: proyekData.tanggal_mulai, 
            tanggal_selesai: proyekData.tanggal_selesai,
            tanggal_mulai_progres: proyekData.tanggal_mulai_progres, 
            tanggal_selesai_progres: proyekData.tanggal_selesai_progres 
        });
        
        if (!proyekMulaiField || !proyekSelesaiField) {
            console.error('Project date fields not found in proyekData');
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
        
        console.log('Project date range:', { proyekMulai, proyekSelesai });
        
        if (tanggalMulai) {
            const mulai = new Date(tanggalMulai);
            console.log('Validating tanggal mulai:', mulai);
            
            if (mulai < proyekMulai) {
                errors[`${prefix}tanggal_mulai_progres`] = `Tanggal mulai tidak boleh sebelum ${formatDate(proyekMulaiField)}`;
            }
            if (mulai > proyekSelesai) {
                errors[`${prefix}tanggal_mulai_progres`] = `Tanggal mulai tidak boleh setelah ${formatDate(proyekSelesaiField)}`;
            }
        }
        
        if (tanggalSelesai) {
            const selesai = new Date(tanggalSelesai);
            console.log('Validating tanggal selesai:', selesai);
            
            if (selesai < proyekMulai) {
                errors[`${prefix}tanggal_selesai_progres`] = `Tanggal selesai tidak boleh sebelum ${formatDate(proyekMulaiField)}`;
            }
            if (selesai > proyekSelesai) {
                errors[`${prefix}tanggal_selesai_progres`] = `Tanggal selesai tidak boleh setelah ${formatDate(proyekSelesaiField)}`;
            }
        }
        
        // Validasi tanggal mulai vs tanggal selesai
        if (tanggalMulai && tanggalSelesai) {
            const mulai = new Date(tanggalMulai);
            const selesai = new Date(tanggalSelesai);
            console.log('Comparing dates:', { mulai, selesai });
            
            if (mulai > selesai) {
                errors[`${prefix}tanggal_selesai_progres`] = 'Tanggal selesai tidak boleh sebelum tanggal mulai';
            }
        }
        
        console.log('Validation errors:', errors);
        return errors;
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID');
    }

    function loadDataProgresProyek(page = 1) {
        const proyekId = $('input[name="proyek_id"]').val();
        const searchParam = $("#searchProgres").val() || '';
        
        $("#tableDataProgresProyek tbody").empty();
        
        $("#tableDataProgresProyek tbody").html(`
            <tr>
                <td colspan="6" class="text-center py-4">
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
        $("#emptyDataProgresProyekMessage").addClass("d-none");
        $("#progresProyekPagination").html('');
        
        $.ajax({
            url: `/koordinator/proyek/${proyekId}/progres-proyek`,
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
                    // Store project data for date validation
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
                        renderProgresProyekTable(response.data);
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
                        <td colspan="6" class="text-center text-danger py-4">
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

    // Function to load progress detail when edit button is clicked
    function loadProgresProyekDetail(id) {
        // Show loading state in the modal
        $('#edit_form_error').addClass('d-none').text('');
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        // Disable form fields while loading
        $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', true);
        
        // Fetch progress detail from the server
        $.ajax({
            url: `/koordinator/proyek/progres-proyek/${id}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Remove loading indicator
                $('#edit-loading-indicator').remove();
                
                // Enable form fields
                $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', false);
                
                if (response.success) {
                    const progres = response.data;
                    

                    // Set form values
                    $('#edit_progres_id').val(progres.progres_proyek_id);
                    $('#edit_nama_progres').val(progres.nama_progres);
                    $('#edit_status_progres').val(progres.status_progres);
                    $('#edit_persentase_progres').val(progres.persentase_progres);
                    $('#edit_deskripsi_progres').val(progres.deskripsi_progres);

                    $('#edit_tanggal_mulai_progres').val(progres.tanggal_mulai_progres);
                    $('#edit_tanggal_selesai_progres').val(progres.tanggal_selesai_progres);
                    
                    // Set hidden fields for assignee
                    $('#edit_assigned_to').val(progres.assigned_to || '');
                    $('#edit_assigned_type_hidden').val(progres.assigned_type || '');
                    
                    // Set the selected type in dropdown and trigger status change
                    $('#edit_assigned_type').val(progres.assigned_type || '').trigger('change');
                    $('#edit_status_progres').trigger('change'); // This will show/hide date fields
                    
                    // Load team members for edit form
                    loadTeamMembersForEdit(function() {
                        // After team members are loaded, set the selected assignee
                        if (progres.assigned_type && progres.assigned_to) {
                            switch(progres.assigned_type) {
                                case 'leader':
                                    $('#edit_leader_assign_id').val(progres.assigned_to).trigger('change');
                                    break;
                                case 'dosen':
                                    $('#edit_dosen_assign_id').val(progres.assigned_to).trigger('change');
                                    break;
                                case 'profesional':
                                    $('#edit_profesional_assign_id').val(progres.assigned_to).trigger('change');
                                    break;
                                case 'mahasiswa':
                                    $('#edit_mahasiswa_assign_id').val(progres.assigned_to).trigger('change');
                                    break;
                            }
                        }
                    });
                } else {
                    $('#edit_form_error').removeClass('d-none').text(response.message || 'Gagal memuat data progres');
                    console.error('Failed to load progress detail:', response.message);
                }
            },
            error: function(xhr) {
                // Remove loading indicator
                $('#edit-loading-indicator').remove();
                
                // Enable form fields
                $('#formEditProgres input, #formEditProgres select, #formEditProgres textarea').prop('disabled', false);
                
                // Show error message
                $('#edit_form_error').removeClass('d-none').text('Terjadi kesalahan saat memuat data');
                console.error('Error loading progress detail:', xhr.responseText);
            }
        });
    }

    // Function to load team members for the edit form
    function loadTeamMembersForEdit(callback) {
        const proyekId = $('#proyek_id').val();
        
        if (!proyekId) {
            console.error('Project ID not found');
            return;
        }
        
        // Show loading indicator
        $('#edit_leader_assign_id, #edit_dosen_assign_id, #edit_profesional_assign_id, #edit_mahasiswa_assign_id').html('<option value="">Loading...</option>');
        
        $.ajax({
            url: `/koordinator/proyek/${proyekId}/team-members`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Store project data if not already available
                    if (!proyekData && response.proyek) {
                        proyekData = response.proyek;
                    }
                    
                    // Reset all selects with default option
                    $('#edit_leader_assign_id').empty().append('<option value="">Pilih Project Leader</option>');
                    $('#edit_dosen_assign_id').empty().append('<option value="">Pilih Dosen</option>');
                    $('#edit_profesional_assign_id').empty().append('<option value="">Pilih Profesional</option>');
                    $('#edit_mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                    
                    // Add leader if exists
                    if (data.leader) {
                        $('#edit_leader_assign_id').append(`<option value="${data.leader.project_leader_id}">${data.leader.nama}</option>`);
                    }
                    
                    // Add dosen members
                    if (data.dosen && data.dosen.length > 0) {
                        data.dosen.forEach(dosen => {
                            $('#edit_dosen_assign_id').append(`<option value="${dosen.project_member_dosen_id}">${dosen.nama_dosen}</option>`);
                        });
                    } else {
                        $('#edit_dosen_assign_id').append('<option value="" disabled>Tidak ada dosen</option>');
                    }
                    
                    // Add profesional members
                    if (data.profesional && data.profesional.length > 0) {
                        data.profesional.forEach(profesional => {
                            $('#edit_profesional_assign_id').append(`<option value="${profesional.project_member_profesional_id}">${profesional.nama_profesional}</option>`);
                        });
                    } else {
                        $('#edit_profesional_assign_id').append('<option value="" disabled>Tidak ada profesional</option>');
                    }
                    
                    // Add mahasiswa members
                    if (data.mahasiswa && data.mahasiswa.length > 0) {
                        $('#edit_mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                        
                        data.mahasiswa.forEach(mahasiswa => {
                            $('#edit_mahasiswa_assign_id').append(`<option value="${mahasiswa.project_member_mahasiswa_id}">${mahasiswa.nama_mahasiswa}</option>`);
                        });
                    } else {
                        $('#edit_mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                        $('#edit_mahasiswa_assign_id').append('<option value="" disabled>Tidak ada mahasiswa</option>');
                    }
                    
                    // Initialize Select2 for edit form
                    initializeEditSelect2();
                    
                    // Execute callback after everything is loaded
                    if (typeof callback === 'function') {
                        callback();
                    }
                } else {
                    console.error('Failed to get team data for edit form:', response.message);
                    $('#edit_leader_assign_id, #edit_dosen_assign_id, #edit_profesional_assign_id, #edit_mahasiswa_assign_id').html('<option value="">Error loading data</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching team members for edit form:', error);
                $('#edit_leader_assign_id, #edit_dosen_assign_id, #edit_profesional_assign_id, #edit_mahasiswa_assign_id').html('<option value="">Error loading data</option>');
            }
        });
    }

    // Initialize Select2 for edit form
    function initializeEditSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            try {
                $('.select2-edit-leader').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalEditProgres'),
                    placeholder: 'Pilih Project Leader',
                    width: '100%'
                });
                
                $('.select2-edit-dosen').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalEditProgres'),
                    placeholder: 'Pilih Dosen',
                    width: '100%'
                });
                
                $('.select2-edit-profesional').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalEditProgres'),
                    placeholder: 'Pilih Profesional',
                    width: '100%'
                });
                
                $('.select2-edit-mahasiswa').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalEditProgres'),
                    placeholder: 'Pilih Mahasiswa',
                    width: '100%'
                });
            } catch (e) {
                console.error('Error initializing Edit form Select2:', e);
            }
        } else {
            console.warn('Select2 library not found for edit form');
        }
    }

    $('#edit_assigned_type').on('change', function() {
        $('#edit_leader_section, #edit_dosen_section, #edit_profesional_section, #edit_mahasiswa_section').addClass('d-none');
        
        const selectedType = $(this).val();
        
        if (selectedType === 'leader') {
            $('#edit_leader_section').removeClass('d-none');
        } else if (selectedType === 'dosen') {
            $('#edit_dosen_section').removeClass('d-none');
        } else if (selectedType === 'profesional') {
            $('#edit_profesional_section').removeClass('d-none');
        } else if (selectedType === 'mahasiswa') {
            $('#edit_mahasiswa_section').removeClass('d-none');
        }
        
        // Clear hidden inputs
        $('#edit_assigned_to').val('');
        $('#edit_assigned_type_hidden').val(selectedType);
    });

    $('#edit_leader_assign_id').on('change', function() {
        $('#edit_assigned_to').val($(this).val());
    });

    $('#edit_dosen_assign_id').on('change', function() {
        $('#edit_assigned_to').val($(this).val());
    });

    $('#edit_profesional_assign_id').on('change', function() {
        $('#edit_assigned_to').val($(this).val());
    });

    $('#edit_mahasiswa_assign_id').on('change', function() {
        $('#edit_assigned_to').val($(this).val());
    });

    $('#formEditProgres').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        if ($(form).data('submitting')) {
            return;
        }
        
        $(form).data('submitting', true);
        
        // Clear previous error messages
        $('.invalid-feedback').text('');
        $('#edit_form_error').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');
        
        // Client-side date validation
        const status = $('#edit_status_progres').val();
        const tanggalMulai = $('#edit_tanggal_mulai_progres').val();
        const tanggalSelesai = $('#edit_tanggal_selesai_progres').val();
        
        if (status === 'In Progress') {
            const dateErrors = validateDates(tanggalMulai, tanggalSelesai, 'edit_');
            if (Object.keys(dateErrors).length > 0) {
                for (const field in dateErrors) {
                    $(`#${field}_error`).text(dateErrors[field]);
                    $(`#${field.replace('_error', '')}`).addClass('is-invalid');
                }
                $(form).data('submitting', false);
                return;
            }
        }
        
        // Get progres ID
        const progresId = $('#edit_progres_id').val();
        
        if (!progresId) {
            $('#edit_form_error').removeClass('d-none').text('ID progres tidak valid');
            $(form).data('submitting', false);
            return;
        }
        
        // Get form data
        const formData = new FormData(form);
        
        // Set loading state on submit button
        const submitBtn = $('#btnUpdateProgres');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: `/koordinator/proyek/progres-proyek/${progresId}`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Reset button state
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
                
                if (response.success) {
                    // Close the modal
                    $('#modalEditProgres').modal('hide');
                    
                    // Show success message
                    swal.successMessage('Data progres proyek berhasil diperbarui')
                    .then(() => {
                        // Reload data to show the updated item
                        loadDataProgresProyek(currentPageProgresProyek);
                    });
                    
                    // Reset form state
                    $(form).data('submitting', false);
                } else {
                    // Show error message
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
                    // Display validation errors
                    for (const field in response.errors) {
                        const errorField = `#edit_${field}_error`;
                        $(errorField).text(response.errors[field][0]);
                        $(`#edit_${field}`).addClass('is-invalid');
                    }
                    
                    $('#edit_form_error').removeClass('d-none').text('Mohon periksa kembali data yang dimasukkan.');
                } else {
                    // Display general error
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
        
        // Hide all assignment sections and date sections
        $('#edit_leader_section, #edit_dosen_section, #edit_profesional_section, #edit_mahasiswa_section').addClass('d-none');
        $('#edit_tanggal_mulai_progres_section, #edit_tanggal_selesai_progres_section').addClass('d-none');
        
        // Clear select2 selections
        if (typeof $.fn.select2 !== 'undefined') {
            try {
                $('#edit_leader_assign_id, #edit_dosen_assign_id, #edit_profesional_assign_id, #edit_mahasiswa_assign_id').val(null).trigger('change');
            } catch (e) {
                console.log('Note: Could not reset Select2 instances in edit form:', e);
            }
        }
    });

    function updatePaginationProgresProyekInfo(currentPage, perPage, total) {
        const from = total > 0 ? (currentPage - 1) * perPage + 1 : 0;
        const to = Math.min(currentPage * perPage, total);
        
        $("#progresProyekPaginationInfo").html(`Showing ${from} to ${to} of ${total} entries`);
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

    function renderProgresProyekTable(data) {
        const tableBody = $("#tableDataProgresProyek tbody");
        tableBody.empty();
        
        // Check if we have valid data
        if (!data || !Array.isArray(data) || data.length === 0) {
            const searchParam = $("#searchProgres").val() || '';
            showEmptyMessageProgresProyek(searchParam);
            return;
        }
        
        // Hide empty message when we have data
        $("#emptyDataProgresProyekMessage").addClass("d-none");
        
        // Create a string to hold all rows to avoid multiple DOM manipulations
        let tableHtml = '';
        
        // Process each progresProyek item
        data.forEach((progresProyek, index) => {
            // Ensure we have valid progresProyek ID and other required fields
            const progresProyekId = progresProyek.progres_proyek_id;
            
            if (!progresProyekId) {
                console.warn("Missing progresProyek ID for item at index", index, progresProyek);
                return; // Skip this item
            }
            
            // Use proper field names from the updated controller
            const namaProgresProyek = progresProyek.nama_progres || 'Unnamed';
            const statusProgresProyek = progresProyek.status_progres || 'Unknown';
            
            // Use the assigned_name from our controller rather than just the ID
            const assignedTo = progresProyek.assigned_name || 'Not Assigned';
            
            // Format dates
            const tanggalMulai = progresProyek.tanggal_mulai_progres ? formatDate(progresProyek.tanggal_mulai_progres) : '-';
            const tanggalSelesai = progresProyek.tanggal_selesai_progres ? formatDate(progresProyek.tanggal_selesai_progres) : '-';
            const updatedAt = progresProyek.updated_at ? formatDate(progresProyek.updated_at) : formatDate(progresProyek.created_at);
            
            // Determine badge class based on status
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
            
            // Add row HTML
            tableHtml += `
                <tr data-id="${progresProyekId}">
                    <td>${namaProgresProyek}</td>
                    <td>${statusHtml}</td>
                    <td>${progresProyek.persentase_progres}%</td>
                    <td>${assignedTo}</td>
                    <td>${tanggalMulai}</td>
                    <td>${tanggalSelesai}</td>
                    <td>${updatedAt}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-action-detail-progres" data-id="${progresProyekId}" data-bs-toggle="modal" data-bs-target="#modalEditProgres">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.586 3.586C14.367 2.805 15.633 2.805 16.414 3.586L16.414 3.586C17.195 4.367 17.195 5.633 16.414 6.414L15.621 7.207L12.793 4.379L13.586 3.586Z" fill="#3C21F7"/>
                                    <path d="M11.379 5.793L3 14.172V17H5.828L14.207 8.621L11.379 5.793Z" fill="#3C21F7"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-action-delete btn-delete-progres-proyek" data-id="${progresProyekId}">
                                <svg width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.5896 12.4848C21.5896 17.6563 17.459 21.8486 12.3636 21.8486C7.26829 21.8486 3.1377 17.6563 3.1377 12.4848C3.1377 7.31339 7.26829 3.12109 12.3636 3.12109C17.459 3.12109 21.5896 7.31339 21.5896 12.4848ZM7.56137 17.3588C7.17375 16.9654 7.17375 16.3276 7.56137 15.9342L10.9599 12.4848L7.56137 9.03551C7.17375 8.6421 7.17375 8.00426 7.56137 7.61085C7.94899 7.21744 8.57744 7.21744 8.96506 7.61085L12.3636 11.0602L15.7622 7.61085C16.1498 7.21744 16.7783 7.21744 17.1659 7.61085C17.5535 8.00426 17.5535 8.6421 17.1659 9.03551L13.7673 12.4848L17.1659 15.9342C17.5535 16.3276 17.5535 16.9654 17.1659 17.3588C16.7783 17.7522 16.1498 17.7522 15.7622 17.3588L12.3636 13.9095L8.96506 17.3588C8.57744 17.7522 7.94899 17.7522 7.56137 17.3588Z" fill="#E56F8C"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tableBody.html(tableHtml);
        attachEventHandlers();
    }

    function attachEventHandlers() {
        $('.btn-delete-progres-proyek').on('click', function() {
            const id = $(this).data('id');
            confirmDeleteProgresProyek(id);
        });

        $('.btn-action-detail-progres').off('click').on('click', function() {
            const id = $(this).data('id');
            loadProgresProyekDetail(id);
        });
    }

    function confirmDeleteProgresProyek(progresProyekId) {
        swal.confirmationDelete('Apakah Anda yakin ingin menghapus progres ini? ').then((result) => {
            if (result.isConfirmed) {
                deleteProgresProyek(progresProyekId);
            }
        });
    }
    
    function deleteProgresProyek(progresProyekId) {
        $.ajax({
            url: `/koordinator/proyek/progres-proyek/${progresProyekId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    swal.successMessage(response.message)
                    loadDataProgresProyek(currentPageProgresProyek);
                } else {
                    swal.errorMessage(response.message || 'Gagal menghapus progres proyek')
                }
            },
            error: function(xhr) {
                console.error("Error deleting progres proyek:", xhr.responseText);
                swal.errorMessage('Terjadi kesalahan saat menghapus progres proyek');
            }
        });
    }

    function initializeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            try {
                $('.select2-assign-leader').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Project Leader',
                    width: '100%'
                });
                
                $('.select2-assign-dosen').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Dosen',
                    width: '100%'
                });
                
                $('.select2-assign-profesional').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Profesional',
                    width: '100%'
                });
                
                $('.select2-assign-mahasiswa').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Mahasiswa',
                    width: '100%'
                });
            } catch (e) {
                console.error('Error initializing Select2:', e);
            }
        } else {
            console.warn('Select2 library not found');
        }
    }

    $('#modalTambahProgresFromKoor').on('shown.bs.modal', function () {
        loadTeamMembers(proyekId);
    });

    $('#assigned_type').on('change', function() {
        $('#leader_section, #dosen_section, #profesional_section, #mahasiswa_section').addClass('d-none');
        
        const selectedType = $(this).val();
        
        if (selectedType === 'leader') {
            $('#leader_section').removeClass('d-none');
        } else if (selectedType === 'dosen') {
            $('#dosen_section').removeClass('d-none');
        } else if (selectedType === 'profesional') {
            $('#profesional_section').removeClass('d-none');
        } else if (selectedType === 'mahasiswa') {
            $('#mahasiswa_section').removeClass('d-none');
        }

        $('#assigned_to').val('');
        $('#assigned_type_hidden').val(selectedType);
    });

    $('#leader_assign_id').on('change', function() {
        $('#assigned_to').val($(this).val());
    });

    $('#dosen_assign_id').on('change', function() {
        $('#assigned_to').val($(this).val());
    });

    $('#profesional_assign_id').on('change', function() {
        $('#assigned_to').val($(this).val());
    });

    $('#mahasiswa_assign_id').on('change', function() {
        $('#assigned_to').val($(this).val());
    });

    $('#btnTambahkanKeDaftarProgres').on('click', function() {
        console.log('=== DEBUG VALIDASI TANGGAL ===');
        console.log('proyekData:', proyekData);
        
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('#form_progres_error').addClass('d-none').text('');
        
        const namaProgres = $('#nama_progres').val();
        const statusProgres = $('#status_progres').val();
        let persentaseProgres = $('#persentase_progres').val();
        const deskripsiProgres = $('#deskripsi_progres').val();
        const assignedType = $('#assigned_type').val();
        const assignedTo = $('#assigned_to').val();
        const tanggalMulai = $('#tanggal_mulai_progres').val();
        const tanggalSelesai = $('#tanggal_selesai_progres').val();
        
        console.log('Status:', statusProgres);
        console.log('Tanggal Mulai:', tanggalMulai);
        console.log('Tanggal Selesai:', tanggalSelesai);
        
        let isValid = true;
        
        // Validasi field wajib
        if (!namaProgres) {
            $('#nama_progres').addClass('is-invalid');
            $('#nama_progres_error').text('Nama progres harus diisi');
            isValid = false;
        }
        
        if (!statusProgres) {
            $('#status_progres').addClass('is-invalid');
            $('#status_progres_error').text('Status progres harus dipilih');
            isValid = false;
        }
        
        if (!assignedType || !assignedTo) {
            $('#assigned_type').addClass('is-invalid');
            $('#assigned_type_error').text('Pilih tipe dan nama penanggung jawab');
            isValid = false;
        }

        // Validasi persentase
        if (persentaseProgres !== '') {
            if (isNaN(persentaseProgres)) {
                $('#persentase_progres').addClass('is-invalid');
                $('#persentase_progres_error').text('Persentase harus berupa angka');
                isValid = false;
            } else if (persentaseProgres < 0 || persentaseProgres > 100) {
                $('#persentase_progres').addClass('is-invalid');
                $('#persentase_progres_error').text('Persentase harus antara 0-100');
                isValid = false;
            }
        } else {
            persentaseProgres = '0';
            $('#persentase_progres').val('0');
        }
        
        // ========= VALIDASI TANGGAL YANG DIPERBAIKI =========
        if (statusProgres === 'In Progress') {
            console.log('Validating dates for In Progress status...');
            
            // Reset error messages untuk tanggal
            $('#tanggal_mulai_progres_error').text('');
            $('#tanggal_selesai_progres_error').text('');
            $('#tanggal_mulai_progres').removeClass('is-invalid');
            $('#tanggal_selesai_progres').removeClass('is-invalid');
            
            // Check apakah tanggal diisi
            if (!tanggalMulai) {
                $('#tanggal_mulai_progres').addClass('is-invalid');
                $('#tanggal_mulai_progres_error').text('Tanggal mulai harus diisi untuk status In Progress');
                isValid = false;
                console.log('ERROR: Tanggal mulai tidak diisi');
            }
            
            if (!tanggalSelesai) {
                $('#tanggal_selesai_progres').addClass('is-invalid');
                $('#tanggal_selesai_progres_error').text('Tanggal selesai harus diisi untuk status In Progress');
                isValid = false;
                console.log('ERROR: Tanggal selesai tidak diisi');
            }
            
            // Cek apakah proyekData sudah ada
            if (!proyekData) {
                console.log('ERROR: proyekData belum dimuat!');
                $('#form_progres_error').removeClass('d-none').text('Data proyek belum dimuat. Tutup dan buka kembali modal ini.');
                isValid = false;
            } else {
                console.log('proyekData tersedia:', proyekData);
                
                // Validasi tanggal dengan fungsi validateDates (tanpa prefix untuk form tambah)
                if (tanggalMulai || tanggalSelesai) {
                    console.log('Memanggil validateDates...');
                    const dateErrors = validateDates(tanggalMulai, tanggalSelesai, '');
                    console.log('Date errors:', dateErrors);
                    
                    // Check apakah ada error dari validateDates dan tampilkan pesan detail
                    if (Object.keys(dateErrors).length > 0) {
                        for (const field in dateErrors) {
                            console.log(`Setting error untuk ${field}: ${dateErrors[field]}`);
                            $(`#${field}_error`).text(dateErrors[field]);
                            $(`#${field}`).addClass('is-invalid');
                        }
                        isValid = false;
                    }
                }
            }
        } else {
            // Jika bukan In Progress, clear error messages untuk tanggal
            $('#tanggal_mulai_progres_error').text('');
            $('#tanggal_selesai_progres_error').text('');
            $('#tanggal_mulai_progres').removeClass('is-invalid');
            $('#tanggal_selesai_progres').removeClass('is-invalid');
        }
        
        console.log('isValid setelah semua validasi:', isValid);
        
        // Jika ada error, stop dan jangan tambahkan ke daftar
        if (!isValid) {
            console.log('VALIDASI GAGAL - Data tidak akan ditambahkan ke daftar');
            return;
        }
        
        console.log('VALIDASI BERHASIL - Data akan ditambahkan ke daftar');
        
        // Get assigned name for display
        let assignedName = 'Tidak ditugaskan';
        if (assignedType === 'leader') {
            assignedName = $('#leader_assign_id option:selected').text().trim();
        } else if (assignedType === 'dosen') {
            assignedName = $('#dosen_assign_id option:selected').text().trim();
        } else if (assignedType === 'profesional') {
            assignedName = $('#profesional_assign_id option:selected').text().trim();
        } else if (assignedType === 'mahasiswa') {
            assignedName = $('#mahasiswa_assign_id option:selected').text().trim();
        }
        
        // Create progress item object
        const progressItem = {
            nama_progres: namaProgres,
            status_progres: statusProgres,
            persentase_progres: persentaseProgres,
            deskripsi_progres: deskripsiProgres,
            assigned_type: assignedType,
            assigned_to: assignedTo,
            assigned_name: assignedName,
            tanggal_mulai_progres: tanggalMulai,
            tanggal_selesai_progres: tanggalSelesai
        };
        
        // Add to progress list
        progressList.push(progressItem);
        $('#progresJsonData').val(JSON.stringify(progressList));
        $('#isSingleProgres').val('0');
        
        // Update the display table
        updateProgressTable();
        
        // ========= PERBAIKAN: RESET FORM DENGAN LENGKAP =========
        // Reset form fields
        $('#nama_progres').val('');
        $('#status_progres').val('').trigger('change'); // This will hide date fields
        $('#persentase_progres').val('');
        $('#deskripsi_progres').val('');
        $('#assigned_type').val('').trigger('change'); // This will hide assignment sections
        $('#assigned_to').val('');
        $('#assigned_type_hidden').val('');
        
        // Reset tanggal fields
        $('#tanggal_mulai_progres').val('');
        $('#tanggal_selesai_progres').val('');
        
        // Hide all assignment sections explicitly
        $('#leader_section, #dosen_section, #profesional_section, #mahasiswa_section').addClass('d-none');
        
        // Reset all Select2 dropdowns explicitly
        try {
            if (typeof $.fn.select2 !== 'undefined') {
                // Reset Select2 values to empty
                $('#leader_assign_id').val(null).trigger('change');
                $('#dosen_assign_id').val(null).trigger('change');
                $('#profesional_assign_id').val(null).trigger('change');
                $('#mahasiswa_assign_id').val(null).trigger('change');
                
                // Alternative method - set to first option (empty option)
                $('#leader_assign_id').prop('selectedIndex', 0);
                $('#dosen_assign_id').prop('selectedIndex', 0);
                $('#profesional_assign_id').prop('selectedIndex', 0);
                $('#mahasiswa_assign_id').prop('selectedIndex', 0);
                
                // Force refresh Select2 display
                $('#leader_assign_id').select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Project Leader',
                    width: '100%'
                });
                
                $('#dosen_assign_id').select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Dosen',
                    width: '100%'
                });
                
                $('#profesional_assign_id').select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Profesional',
                    width: '100%'
                });
                
                $('#mahasiswa_assign_id').select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalTambahProgresFromKoor'),
                    placeholder: 'Pilih Mahasiswa',
                    width: '100%'
                });
            }
        } catch (e) {
            console.log('Note: Could not reset Select2 instances:', e);
            
            // Fallback: reset using standard jQuery methods
            $('#leader_assign_id').val('');
            $('#dosen_assign_id').val('');
            $('#profesional_assign_id').val('');
            $('#mahasiswa_assign_id').val('');
        }
        
        // Clear any remaining error messages
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('#form_progres_error').addClass('d-none');
        
        console.log('=== FORM BERHASIL DIRESET ===');
        console.log('=== SELESAI DEBUG ===');
    });

    function resetFormProgresCompletely() {
        // Reset semua field input
        $('#nama_progres').val('');
        $('#status_progres').val('').trigger('change');
        $('#persentase_progres').val('');
        $('#deskripsi_progres').val('');
        $('#assigned_type').val('').trigger('change');
        $('#assigned_to').val('');
        $('#assigned_type_hidden').val('');
        $('#tanggal_mulai_progres').val('');
        $('#tanggal_selesai_progres').val('');
        
        // Hide all sections
        $('#leader_section, #dosen_section, #profesional_section, #mahasiswa_section').addClass('d-none');
        $('#tanggal_mulai_progres_section, #tanggal_selesai_progres_section').addClass('d-none');
        
        // Reset Select2 dropdowns
        try {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#leader_assign_id, #dosen_assign_id, #profesional_assign_id, #mahasiswa_assign_id').each(function() {
                    $(this).val(null).trigger('change');
                    $(this).prop('selectedIndex', 0);
                });
            }
        } catch (e) {
            console.log('Could not reset Select2:', e);
            $('#leader_assign_id, #dosen_assign_id, #profesional_assign_id, #mahasiswa_assign_id').val('');
        }
        
        // Clear errors
        $('.invalid-feedback').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('#form_progres_error').addClass('d-none');
    }


    $('#formTambahDataProgres').on('submit', function(e) {
        e.preventDefault();
        
        const self = this;
        
        // Prevent multiple submissions
        if ($(self).data('submitting')) {
            return;
        }
        
        $(self).data('submitting', true);
        
        // Clear existing error messages
        $('.invalid-feedback').text('');
        $('#form_progres_error').addClass('d-none').text('');
        
        // Get form data
        const formData = new FormData(this);
        
        // Check if any progress items are in the list when in multi mode
        if ($('#isSingleProgres').val() === '0' && progressList.length === 0) {
            $('#form_progres_error').removeClass('d-none').text('Belum ada progres yang ditambahkan ke daftar.');
            $(self).data('submitting', false);
            return;
        }
        
        // Show loading indicator inside the submit button
        const submitBtn = $('#btnSimpanProgres');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '/koordinator/proyek/progres-proyek',
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
                    $('#modalTambahProgresFromKoor').modal('hide');

                    // Show success message
                    swal.successMessage(response.message || 'Data progres berhasil disimpan').then(() => {
                        resetProgresProyekForm();
                        progressList = [];
                        $("#progresJsonData").val('[]');
                        $("#isSingleProgres").val("1");
                        
                        setTimeout(function() {
                            currentPageProgresProyek = 1;
                            loadDataProgresProyek(currentPageProgresProyek);
                        }, 300);
                    });
                    
                    $(self).data('submitting', false);
    
                } else {
                    $("#form_progres_error").removeClass('d-none').text(response.message || 'Terjadi kesalahan');
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
                        const errorField = `#${field}_error`;
                        $(errorField).text(response.errors[field][0]);
                        $(`#${field}`).addClass('is-invalid');
                    }
                    
                    swal.errorMessage('Mohon periksa kembali data yang dimasukkan.');
                } else {
                    swal.errorMessage('Terjadi kesalahan saat menyimpan data.');
                }
                
                $(self).data('submitting', false);
            }
        });
    });

    function updateProgressTable() {
        const tableBody = $('#daftarProgres');
        
        tableBody.empty();
        
        if (progressList.length === 0) {
            tableBody.append(`
                <tr id="emptyRowProgres">
                    <td colspan="7" class="text-center">Belum ada progres yang ditambahkan ke daftar</td>
                </tr>
            `);
        } else {
            progressList.forEach((item, index) => {
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
                            <button type="button" class="btn btn-sm btn-danger remove-progres" data-index="${index}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
        
        // Re-attach event handlers for remove buttons
        $('.remove-progres').on('click', function() {
            const index = $(this).data('index');
            progressList.splice(index, 1);
            $('#progresJsonData').val(JSON.stringify(progressList));
            
            // If list is empty, set to single mode
            if (progressList.length === 0) {
                $('#isSingleProgres').val('1');
            }
            
            updateProgressTable();
        });
    }

    // Function to load team members from API
    function loadTeamMembers(proyekId) {
        if (!proyekId) {
            console.error('Project ID not found');
            return;
        }

        $('#leader_assign_id, #dosen_assign_id, #profesional_assign_id, #mahasiswa_assign_id').html('<option value="">Loading...</option>');
        
        $.ajax({
            url: `/koordinator/proyek/${proyekId}/team-members`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Store project data if available
                    if (response.proyek) {
                        proyekData = response.proyek;
                    }
                    
                    // Reset all selects with default option - corrected ID selectors to match HTML
                    $('#leader_assign_id').empty().append('<option value="">Pilih Project Leader</option>');
                    $('#dosen_assign_id').empty().append('<option value="">Pilih Dosen</option>');
                    $('#profesional_assign_id').empty().append('<option value="">Pilih Profesional</option>');
                    $('#mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                    
                    // Add leader if exists
                    if (data.leader) {
                        $('#leader_assign_id').append(`<option value="${data.leader.project_leader_id}">${data.leader.nama}</option>`)
                    }
                    
                    // Add dosen members
                    if (data.dosen && data.dosen.length > 0) {
                        data.dosen.forEach(dosen => {
                            $('#dosen_assign_id').append(`<option value="${dosen.project_member_dosen_id}">${dosen.nama_dosen}</option>`);
                        });
                    } else {
                        $('#dosen_assign_id').append('<option value="" disabled>Tidak ada dosen</option>');
                    }
                    
                    // Add profesional members
                    if (data.profesional && data.profesional.length > 0) {
                        data.profesional.forEach(profesional => {
                            $('#profesional_assign_id').append(`<option value="${profesional.project_member_profesional_id}">${profesional.nama_profesional}</option>`);
                        });
                    } else {
                        $('#profesional_assign_id').append('<option value="" disabled>Tidak ada profesional</option>');
                    }
                    
                    if (data.mahasiswa && data.mahasiswa.length > 0) {
                        // First, empty the select element to ensure a clean state
                        $('#mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                        
                        data.mahasiswa.forEach((mahasiswa, index) => {
                            const option = document.createElement('option');
                            option.value = mahasiswa.project_member_mahasiswa_id;
                            option.textContent = mahasiswa.nama_mahasiswa;
                            document.getElementById('mahasiswa_assign_id').appendChild(option);
                        });
                    } else {
                        $('#mahasiswa_assign_id').empty().append('<option value="">Pilih Mahasiswa</option>');
                        $('#mahasiswa_assign_id').append('<option value="" disabled>Tidak ada mahasiswa</option>');
                    }
                    
                    try {
                        if (typeof $.fn.select2 !== 'undefined') {
                            $('.select2-assign-leader, .select2-assign-dosen, .select2-assign-profesional, .select2-assign-mahasiswa').select2('destroy');
                        }
                    } catch (e) {
                        console.log('Note: Could not destroy Select2 instances, may not exist yet:', e);
                    }

                    setTimeout(function() {
                        if (typeof $.fn.select2 !== 'undefined') {
                            try {
                                // Initialize each Select2 individually
                                $('.select2-assign-leader').select2({
                                    theme: 'bootstrap-5',
                                    dropdownParent: $('#modalTambahProgresFromKoor'),
                                    placeholder: 'Pilih Project Leader',
                                    width: '100%'
                                });
                                
                                $('.select2-assign-dosen').select2({
                                    theme: 'bootstrap-5',
                                    dropdownParent: $('#modalTambahProgresFromKoor'),
                                    placeholder: 'Pilih Dosen',
                                    width: '100%'
                                });
                                
                                $('.select2-assign-profesional').select2({
                                    theme: 'bootstrap-5',
                                    dropdownParent: $('#modalTambahProgresFromKoor'),
                                    placeholder: 'Pilih Profesional',
                                    width: '100%'
                                });
                                
                                // Initialize mahasiswa select
                                $('.select2-assign-mahasiswa').select2({
                                    theme: 'bootstrap-5',
                                    dropdownParent: $('#modalTambahProgresFromKoor'),
                                    placeholder: 'Pilih Mahasiswa',
                                    width: '100%'
                                });
                                
                                // Force a refresh for mahasiswa Select2
                                try {
                                    $('.select2-assign-mahasiswa').select2('close');
                                    $('.select2-assign-mahasiswa').select2('open');
                                    $('.select2-assign-mahasiswa').select2('close');
                                } catch (e) {
                                    console.error('Error refreshing mahasiswa Select2:', e);
                                }
                            } catch (e) {
                                console.error('Error initializing Select2:', e);
                            }
                        } else {
                            console.warn('Select2 library not found');
                        }
                    }, 100); 
                } else {
                    console.error('Failed to get team data:', response.message);
                    $('#leader_assign_id, #dosen_assign_id, #profesional_assign_id, #mahasiswa_assign_id').html('<option value="">Error loading data</option>');
                }
            },
            error: function(error) {
                console.error('Error fetching team members:', error);
                $('#leader_assign_id, #dosen_assign_id, #profesional_assign_id, #mahasiswa_assign_id').html('<option value="">Error loading data</option>');
                alert('Gagal mengambil data tim proyek. Silakan coba lagi.');
            }
        });
    }

    function resetProgresProyekForm() {
        $("#nama_progres").val('');
        $("#status_progres").val('').trigger('change'); // This will hide date sections
        $("#persentase_progres").val('');
        $("#assigned_type").val('').trigger('change');
        $("#deskripsi_progres").val('');
        resetFormErrors();
    }

    function resetFormErrors() {
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").empty(); 
        $("#form_progres_error").addClass('d-none').empty(); 
    }

    $('#modalTambahProgresFromKoor').on('hidden.bs.modal', function () {
        $('#formTambahDataProgres')[0].reset();
        resetFormProgresCompletely();
        progressList = [];
        $('#progresJsonData').val('[]');
        $('#isSingleProgres').val('1');
        updateProgressTable();
    });
});