import swal from '../components';
$(document).ready(function() {
    let editModal;
    setTimeout(function() {
        try {
            const editModalElement = document.getElementById('modalEditData');
            if (editModalElement && typeof bootstrap !== 'undefined') {
                editModal = new bootstrap.Modal(editModalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
            }
        } catch (error) {
            console.warn('Bootstrap modal initialization failed:', error);
            editModal = null;
        }
    }, 100);

    // Load data untuk dropdown saat modal dibuka
    $('#modalTambahData').on('show.bs.modal', function() {
        console.log('Modal tambah dibuka');
        loadJenisTransaksi('#jenis_transaksi_id');
        loadJenisKeuanganTefa('#jenis_keuangan_tefa_id');
    });

    $('#modalEditData').on('show.bs.modal', function() {
        console.log('Modal edit dibuka');
        loadJenisTransaksi('#edit_jenis_transaksi_id');
        loadJenisKeuanganTefa('#edit_jenis_keuangan_tefa_id');
    });

    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        console.log('Edit button clicked, ID:', id);
        
        if (!id) {
            swal.errorMessage('ID tidak ditemukan');
            return;
        }

        // Load data untuk edit
        $.ajax({
            url: `/koordinator/data-sub-kategori-transaksi/get-sub-jenis-kategori/${id}`,
            type: 'GET',
            beforeSend: function() {
                console.log('Loading edit data...');
            },
            success: function(response) {
                console.log('Edit data response:', response);
                
                if (response.status === 'success') {
                    const data = response.data;
                    
                    // Clear form first
                    $('#formEditData')[0].reset();
                    
                    // Populate form fields
                    $('#edit_id').val(data.sub_jenis_transaksi_id);
                    $('#edit_nama_sub_jenis_transaksi').val(data.nama_sub_jenis_transaksi);
                    $('#edit_keterangan').val(data.deskripsi_sub_jenis_transaksi || '');
                    
                    // Load dropdowns
                    Promise.all([
                        loadJenisTransaksi('#edit_jenis_transaksi_id'),
                        loadJenisKeuanganTefa('#edit_jenis_keuangan_tefa_id')
                    ]).then(function() {
                        // Set selected values setelah dropdown loaded
                        setTimeout(function() {
                            $('#edit_jenis_transaksi_id').val(data.jenis_transaksi_id);
                            $('#edit_jenis_keuangan_tefa_id').val(data.jenis_keuangan_tefa_id);
                            console.log('Dropdown values set:', {
                                jenis_transaksi_id: data.jenis_transaksi_id,
                                jenis_keuangan_tefa_id: data.jenis_keuangan_tefa_id
                            });
                        }, 300);
                    });
                    
                    // Set form action
                    $('#formEditData').attr('action', `/koordinator/data-sub-kategori-transaksi/update-sub-jenis-kategori/${id}`);
                    
                    // Show modal dengan fallback
                    try {
                        if (editModal) {
                            editModal.show();
                        } else {
                            $('#modalEditData').modal('show');
                        }
                    } catch (modalError) {
                        console.warn('Modal show error:', modalError);
                        // Fallback: toggle modal manually
                        $('#modalEditData').addClass('show').css('display', 'block');
                        $('body').addClass('modal-open');
                        $('.modal-backdrop').remove();
                        $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                    }
                    
                } else {
                    swal.errorMessage('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading edit data:', xhr);
                let errorMessage = 'Error loading data';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMessage = 'Data tidak ditemukan';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error';
                }
                
                swal.errorMessage(errorMessage);
            }
        });
    });

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
        swal.confirmationDelete('Apakah Anda yakin ingin menghapus data ini?')
        .then((result) => {
            if (result.isConfirmed) {
                performDelete(id);
            }
        });
    });

    function performDelete(id) {
        $.ajax({
            url: `/koordinator/data-sub-kategori-transaksi/delete-sub-jenis-kategori/${id}`,
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
                    swal.successMessage('Data Sub Jenis Transaksi berhasil dihapus');
                    // PERBAIKAN: Reload halaman setelah sukses delete
                    setTimeout(() => {
                        window.location.reload();
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
        if (modalId === 'modalEditData') {
            try {
                if (editModal) {
                    editModal.hide();
                } else {
                    $('#modalEditData').modal('hide');
                }
            } catch (error) {
                // Fallback manual close
                $('#modalEditData').removeClass('show').css('display', 'none');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }
        }
    });

    // Handle form submit untuk tambah data
    $('#formTambahData').on('submit', function(e) {
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
                if (response.status === 'success') {
                    $('#modalTambahData').modal('hide');
                    swal.successMessage('Data Sub Jenis Transaksi berhasil ditambahkan');
                    // PERBAIKAN: Reload halaman setelah sukses tambah
                    setTimeout(() => {
                        window.location.reload();
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

    // PERBAIKAN: Handle form submit untuk update data
    $('#formEditData').on('submit', function(e) {
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
                    try {
                        if (editModal) {
                            editModal.hide();
                        } else {
                            $('#modalEditData').modal('hide');
                        }
                    } catch (error) {
                        $('#modalEditData').removeClass('show').css('display', 'none');
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                    }
                    
                    // Show success message
                    swal.successMessage('Data Sub Jenis Transaksi berhasil diperbarui');
                    
                    // PERBAIKAN: Reload halaman untuk menampilkan data terbaru
                    setTimeout(() => {
                        window.location.reload();
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
});

// Function untuk load jenis transaksi (return Promise)
function loadJenisTransaksi(selector) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/koordinator/data-sub-kategori-transaksi/get-jenis-transaksi`, 
            type: 'GET',
            success: function(response) {
                $(selector).empty();
                $(selector).append('<option value="">Pilih Jenis Transaksi</option>');
                
                if (response.status === 'success') {
                    $.each(response.data, function(index, item) {
                        $(selector).append('<option value="' + item.jenis_transaksi_id + '">' + item.nama_jenis_transaksi + '</option>');
                    });
                    console.log('Jenis transaksi loaded for', selector);
                    resolve(response);
                } else {
                    reject('Failed to load jenis transaksi');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading jenis transaksi:', error);
                swal.errorMessage('Error loading jenis transaksi');
                reject(error);
            }
        });
    });
}

// Function untuk load jenis keuangan tefa (return Promise)
function loadJenisKeuanganTefa(selector) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: `/koordinator/data-sub-kategori-transaksi/get-jenis-keuangan-tefa`,
            type: 'GET',
            success: function(response) {
                $(selector).empty();
                $(selector).append('<option value="">Pilih Kategori Transaksi</option>');
                
                if (response.status === 'success') {
                    $.each(response.data, function(index, item) {
                        $(selector).append('<option value="' + item.jenis_keuangan_tefa_id + '">' + item.nama_jenis_keuangan_tefa + '</option>');
                    });
                    console.log('Jenis keuangan tefa loaded for', selector);
                    resolve(response);
                } else {
                    reject('Failed to load jenis keuangan tefa');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading kategori transaksi:', error);
                swal.errorMessage('Error loading kategori transaksi');
                reject(error);
            }
        });
    });
}