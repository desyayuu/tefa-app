// File: resources/js/Koordinator/data_portofolio_mahasiswa.js

import swal from '../components';

$(document).ready(function() {
    // Check if we're on the detail page by checking if modal exists
    const modalExists = document.getElementById('modalEditPortofolio');
    if (!modalExists) {
        console.log('Portfolio modal not found, skipping portfolio JavaScript initialization');
        return; // Exit if modal doesn't exist (not on detail page)
    }

    console.log('Portfolio page detected, initializing...');
    
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
                console.log('Auto-scrolled to portfolio section');
            }
        }, 500); // Delay to ensure page is fully loaded
    }
    
    let editModal;
    
    // Initialize modal setelah DOM ready dengan lebih robust checking
    setTimeout(function() {
        try {
            const editModalElement = document.getElementById('modalEditPortofolio');
            console.log('Modal element found:', editModalElement);
            
            if (editModalElement && typeof bootstrap !== 'undefined') {
                editModal = new bootstrap.Modal(editModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                console.log('Bootstrap modal initialized:', editModal);
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
        console.log('Edit button clicked, ID:', id);
        console.log('Button element:', this);
        
        if (!id) {
            console.error('ID tidak ditemukan');
            swal.errorMessage('ID tidak ditemukan');
            return;
        }

        const url = `/koordinator/data-mahasiswa/portofolio/detail/${id}`;
        console.log('Making request to:', url);

        // Load data untuk edit
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                console.log('Loading edit data...');
            },
            success: function(response) {
                console.log('Edit data response:', response);
                
                if (response.status === 'success') {
                    const data = response.data;
                    console.log('Portfolio data:', data);
                    
                    // Check if modal exists
                    const modalElement = $('#modalEditPortofolio');
                    console.log('Modal element check:', modalElement.length);
                    
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
                    
                    console.log('Form populated with data');
                    
                    // Set form action
                    const formAction = `/koordinator/data-mahasiswa/portofolio/update/${id}`;
                    $('#formEditPortofolio').attr('action', formAction);
                    console.log('Form action set to:', formAction);
                    
                    // Show modal dengan multiple fallbacks
                    console.log('Attempting to show modal...');
                    
                    let modalShown = false;
                    
                    // Method 1: Bootstrap Modal API
                    if (editModal && !modalShown) {
                        try {
                            console.log('Trying bootstrap modal.show()');
                            editModal.show();
                            modalShown = true;
                            console.log('Modal shown via bootstrap API');
                        } catch (error) {
                            console.error('Bootstrap modal show error:', error);
                        }
                    }
                    
                    // Method 2: jQuery modal
                    if (!modalShown) {
                        try {
                            console.log('Trying jQuery modal show');
                            modalElement.modal('show');
                            modalShown = true;
                            console.log('Modal shown via jQuery');
                        } catch (error) {
                            console.error('jQuery modal show error:', error);
                        }
                    }
                    
                    // Method 3: Manual show (last resort)
                    if (!modalShown) {
                        try {
                            console.log('Trying manual modal show');
                            modalElement.addClass('show').css({
                                'display': 'block',
                                'padding-right': '17px'
                            });
                            $('body').addClass('modal-open').css('padding-right', '17px');
                            
                            // Remove existing backdrops
                            $('.modal-backdrop').remove();
                            
                            // Add backdrop
                            $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                            modalShown = true;
                            console.log('Modal shown manually');
                        } catch (error) {
                            console.error('Manual modal show error:', error);
                        }
                    }
                    
                    if (!modalShown) {
                        console.error('All modal show methods failed');
                        swal.errorMessage('Gagal menampilkan modal edit');
                    }
                    
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
                    errorMessage = 'Data tidak ditemukan';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error';
                } else if (xhr.status === 0) {
                    errorMessage = 'Network error atau route tidak ditemukan';
                }
                
                swal.errorMessage(errorMessage);
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        console.log('Delete button clicked, ID:', id);
        
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
        $.ajax({
            url: `/koordinator/data-mahasiswa/portofolio/delete/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                console.log('Deleting data...');
            },
            success: function(response) {
                console.log('Delete response:', response);
                if (response.status === 'success') {
                    swal.successMessage('Data portofolio berhasil dihapus');
                    // Reload halaman setelah sukses delete dengan scroll ke section portofolio
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
                swal.errorMessage('Error deleting data: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
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
            // Fallback manual close
            console.log('Manual modal close');
            $('#modalEditPortofolio').removeClass('show').css('display', 'none');
            $('body').removeClass('modal-open').css('padding-right', '');
            $('.modal-backdrop').remove();
        }
    }

    // Handle form submit untuk tambah data
    $('#formTambahPortofolio').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Form tambah submitted');
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                console.log('Saving new data...');
            },
            success: function(response) {
                console.log('Save response:', response);
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
                    let errorMessage = 'Validation Error:\n';
                    for (let field in errors) {
                        errorMessage += '- ' + errors[field][0] + '\n';
                    }
                    swal.errorMessage(errorMessage);
                } else {
                    swal.errorMessage('Error saving data: ' + error);
                }
            }
        });
    });

    // Handle form submit untuk update data
    $('#formEditPortofolio').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Form edit submitted');
        console.log('Form action:', $(this).attr('action'));
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                console.log('Updating data...');
            },
            success: function(response) {
                console.log('Update response:', response);
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
                    let errorMessage = 'Validation Error:\n';
                    for (let field in errors) {
                        errorMessage += '- ' + errors[field][0] + '\n';
                    }
                    swal.errorMessage(errorMessage);
                } else {
                    swal.errorMessage('Error updating data: ' + error);
                }
            }
        });
    });

    // Clear form when modal tambah is closed
    $('#modalTambahPortofolio').on('hidden.bs.modal', function() {
        $('#formTambahPortofolio')[0].reset();
    });

    // Clear form when modal edit is closed
    $('#modalEditPortofolio').on('hidden.bs.modal', function() {
        $('#formEditPortofolio')[0].reset();
    });
});