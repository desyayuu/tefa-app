document.addEventListener('DOMContentLoaded', function() {
    $('.select2-basic').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    const danaPendanaan = document.getElementById('dana_pendanaan');
    if (danaPendanaan) {
        danaPendanaan.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            if (value !== '') {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            
            this.value = value;
        });
    }
    
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    
    if (tanggalMulai && tanggalSelesai) {
        tanggalMulai.addEventListener('change', validateDates);
        tanggalSelesai.addEventListener('change', validateDates);
        
        function validateDates() {
            if (tanggalMulai.value && tanggalSelesai.value) {
                const startDate = new Date(tanggalMulai.value);
                const endDate = new Date(tanggalSelesai.value);
                
                if (endDate < startDate) {
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
                    tanggalSelesai.value = '';
                }
            }
        }
    }
    

    const formProyek = document.getElementById('formProyek');
    if (formProyek) {
        formProyek.addEventListener('submit', function(event) {
            if (danaPendanaan && danaPendanaan.value) {
                const formattedValue = danaPendanaan.value;
                danaPendanaan.value = formattedValue.replace(/\./g, '');
            }
        });
    }
});