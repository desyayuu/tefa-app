import swal from '../components';
$(document).ready(function() {
    // Auto-scroll to portfolio section if needed
    const urlParams = new URLSearchParams(window.location.search);
    const scrollTo = urlParams.get('scroll_to');
    
    if (scrollTo === 'portofolio') {
        setTimeout(function() {
            const portfolioSection = document.getElementById('section-portofolio');
            if (portfolioSection) {
                portfolioSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 500);
    }
    let editModal;
    
    // Initialize modal
    setTimeout(function() {
        try {
            const editModalElement = document.getElementById('modalEditPortofolio');
            
            if (editModalElement && typeof bootstrap !== 'undefined') {
                editModal = new bootstrap.Modal(editModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
            } else {
                console.warn('Bootstrap not available or modal element not found');
                editModal = null;
            }
        } catch (error) {
            console.error('Bootstrap modal initialization failed:', error);
            editModal = null;
        }
    }, 100);

    // Handle edit button click
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        
        if (!id) {
            console.error('ID tidak ditemukan');
            swal.errorMessage('ID tidak ditemukan');
            return;
        }

        // Load data untuk edit
        $.ajax({
            url: `/mahasiswa/portofolio/detail/${id}`,
            type: 'GET',
            success: function(response) {   
                if (response.status === 'success') {
                    const data = response.data;
                    const modalElement = $('#modalEditPortofolio');
                    
                    if (modalElement.length === 0) {
                        console.error('Modal element not found in DOM');
                        swal.errorMessage('Modal tidak ditemukan');
                        return;
                    }
                    
                    // Clear form first
                    $('#formEditPortofolio')[0].reset();
                    
                    // Populate form fields
                    $('#edit_id').val(data.portofolio_id);
                    $('#edit_nama_kegiatan').val(data.nama_kegiatan);
                    $('#edit_jenis_kegiatan').val(data.jenis_kegiatan);
                    $('#edit_penyelenggara').val(data.penyelenggara || '');
                    $('#edit_tingkat_kegiatan').val(data.tingkat_kegiatan);
                    $('#edit_peran_dalam_kegiatan').val(data.peran_dalam_kegiatan || '');
                    $('#edit_link_kegiatan').val(data.link_kegiatan || '');
                    $('#edit_deskripsi_kegiatan').val(data.deskripsi_kegiatan || '');

                    
                    // Set form action untuk mahasiswa
                    const formAction = `/mahasiswa/portofolio/update/${id}`;
                    $('#formEditPortofolio').attr('action', formAction);
                    
                    // Show modal
                    showEditModal();
                    
                } else {
                    console.error('Response error:', response.message);
                    swal.errorMessage('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error details:', {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'Error loading data';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'Data tidak ditemukan atau Anda tidak memiliki akses';
                } else if (xhr.status === 401) {
                    errorMessage = 'Sesi telah berakhir. Silakan login kembali';
                    // Redirect to login
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error';
                } else if (xhr.status === 0) {
                    errorMessage = 'Network error atau route tidak ditemukan';
                }
                
                swal.errorMessage(errorMessage);
            }
        });
    });

    // Function to show edit modal
    function showEditModal() {
        let modalShown = false;
        
        // Method 1: Bootstrap Modal API
        if (editModal && !modalShown) {
            try {
                editModal.show();
                modalShown = true;
            } catch (error) {
                console.error('Bootstrap modal show error:', error);
            }
        }
        
        // Method 2: jQuery modal
        if (!modalShown) {
            try {
                $('#modalEditPortofolio').modal('show');
                modalShown = true;
            } catch (error) {
                console.error('jQuery modal show error:', error);
            }
        }
        
        if (!modalShown) {
            console.error('All modal show methods failed');
            swal.errorMessage('Gagal menampilkan modal edit');
        }
    }

    // Handle delete button click
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        
        if (!id) {
            swal.errorMessage('ID tidak ditemukan');
            return;
        }

        // Show confirmation
        swal.confirmationDelete('Apakah Anda yakin ingin menghapus data portofolio ini?')
        .then((result) => {
            if (result.isConfirmed) {
                performDelete(id);
            }
        });
    });

    // Function untuk perform delete
    function performDelete(id) {
        // Show loading on delete button
        $(`.btn-delete[data-id="${id}"]`).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: `/mahasiswa/portofolio/delete/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    swal.successMessage('Data portofolio berhasil dihapus');
                    setTimeout(() => {
                        const currentUrl = new URL(window.location);
                        currentUrl.searchParams.set('scroll_to', 'portofolio');
                        window.location.href = currentUrl.toString();
                    }, 1500);
                } else {
                    swal.errorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting data:', xhr);
                
                let errorMessage = 'Error deleting data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 401) {
                    errorMessage = 'Sesi telah berakhir. Silakan login kembali';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                }
                
                swal.errorMessage(errorMessage);
            },
            complete: function() {
                // Reset button state
                $(`.btn-delete[data-id="${id}"]`).prop('disabled', false).html('<i class="fas fa-trash"></i>');
            }
        });
    }

    // Handle manual modal close
    $(document).on('click', '[data-bs-dismiss="modal"]', function() {
        const modalId = $(this).closest('.modal').attr('id');
        if (modalId === 'modalEditPortofolio') {
            closeEditModal();
        }
    });
    
    // Function to properly close edit modal
    function closeEditModal() {
        try {
            if (editModal) {
                editModal.hide();
            } else {
                $('#modalEditPortofolio').modal('hide');
            }
        } catch (error) {
            $('#modalEditPortofolio').removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.modal-backdrop').remove();
        }
    }

    // Handle form submit untuk tambah data
    $('#formTambahPortofolio').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                // Clear previous alerts
                $('#alertContainerPortofolio').html('');
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#modalTambahPortofolio').modal('hide');
                    swal.successMessage('Data portofolio berhasil ditambahkan');
                    // Reload halaman setelah sukses tambah dengan scroll ke section portofolio
                    setTimeout(() => {
                        const currentUrl = new URL(window.location);
                        currentUrl.searchParams.set('scroll_to', 'portofolio');
                        window.location.href = currentUrl.toString();
                    }, 1500);
                } else {
                    swal.errorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving data:', xhr);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Kesalahan Validasi:\n';
                    for (let field in errors) {
                        errorMessage += '• ' + errors[field][0] + '\n';
                    }
                    swal.errorMessage(errorMessage);
                } else if (xhr.status === 401) {
                    swal.errorMessage('Sesi telah berakhir. Silakan login kembali');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    swal.errorMessage('Error saving data: ' + (xhr.responseJSON?.message || error));
                }
            },
            complete: function() {
                // Reset submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle form submit untuk update data
    $('#formEditPortofolio').on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    // Close modal first
                    closeEditModal();
                    
                    // Show success message
                    swal.successMessage('Data portofolio berhasil diperbarui');
                    
                    // Reload halaman untuk menampilkan data terbaru dengan scroll ke section portofolio
                    setTimeout(() => {
                        const currentUrl = new URL(window.location);
                        currentUrl.searchParams.set('scroll_to', 'portofolio');
                        window.location.href = currentUrl.toString();
                    }, 1500);
                    
                } else {
                    swal.errorMessage(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating data:', xhr);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Kesalahan Validasi:\n';
                    for (let field in errors) {
                        errorMessage += '• ' + errors[field][0] + '\n';
                    }
                    swal.errorMessage(errorMessage);
                } else if (xhr.status === 401) {
                    swal.errorMessage('Sesi telah berakhir. Silakan login kembali');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    swal.errorMessage('Error updating data: ' + (xhr.responseJSON?.message || error));
                }
            },
            complete: function() {
                // Reset submit button
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Clear form when modal tambah is closed
    $('#modalTambahPortofolio').on('hidden.bs.modal', function() {
        $('#formTambahPortofolio')[0].reset();
        // Reset submit button if needed
        $(this).find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
    });

    // Clear form when modal edit is closed
    $('#modalEditPortofolio').on('hidden.bs.modal', function() {
        $('#formEditPortofolio')[0].reset();
        // Reset submit button if needed
        $(this).find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan Perubahan');
    });

    // Handle search form auto-submit on Enter
    $('#searchPortofolioForm input[name="search_portofolio"]').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $(this).closest('form').submit();
        }
    });

    // Auto-focus on modal open
    $('#modalTambahPortofolio').on('shown.bs.modal', function() {
        $('#nama_kegiatan').focus();
    });

    $('#modalEditPortofolio').on('shown.bs.modal', function() {
        $('#edit_nama_kegiatan').focus();
    });
});