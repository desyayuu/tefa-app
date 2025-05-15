export default {

    successMessage(message) {
        return Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: message,
            confirmButtonText: 'OK'
        });
    },


    errorMessage(message) {
        return Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'OK'
        });
    },

    warningMessage(message) {
        return Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: message,
            confirmButtonText: 'OK'
        });
    },

    confirmationDelete(message) {
        return Swal.fire({
            title: 'Konfirmasi Hapus',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        })
    }

}; 
