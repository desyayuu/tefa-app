document.addEventListener('DOMContentLoaded', function() {
    $('#jenis_proyek, #mitra_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#exampleModalToggle')
    });

    $('.select2-dosen, .select2-profesional').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#exampleModalToggle'),
        placeholder: 'Cari nama...',
        allowClear: true
    });

    $('#leader_type').change(function() {
        const selectedType = $(this).val();
        
        $('#dosen_leader_section, #profesional_leader_section').hide();
        
        $('#dosen_leader_id, #profesional_leader_id').val(null).trigger('change');
        
        if (selectedType === 'Dosen') {
            $('#dosen_leader_section').show();
            $('#profesional_leader_id').prop('disabled', true).prop('required', false);
            $('#dosen_leader_id').prop('disabled', false).prop('required', true);
        } else if (selectedType === 'Profesional') {
            $('#profesional_leader_section').show();
            $('#dosen_leader_id').prop('disabled', true).prop('required', false);
            $('#profesional_leader_id').prop('disabled', false).prop('required', true);
        }
    });

    $('form').submit(function(event) {
        const leaderType = $('#leader_type').val();
        let leaderId;
        
        if (leaderType === 'Dosen') {
            leaderId = $('#dosen_leader_id').val();
        } else if (leaderType === 'Profesional') {
            leaderId = $('#profesional_leader_id').val();
        }
        
        if (leaderType && !leaderId) {
            event.preventDefault();
            alert('Silakan pilih nama Project Leader');
        }
    });

    if ($('#addLeaderModal').length) {
        $('#namaPL').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addLeaderModal'),
            placeholder: 'Cari nama...',
            allowClear: true,
            width: '100%'
        });
        
        $('#addLeaderModal').on('shown.bs.modal', function() {
            $('#namaPL').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addLeaderModal'),
                placeholder: 'Cari nama...',
                allowClear: true,
                width: '100%'
            });
        });
    }
    
    $('#tanggal_mulai, #tanggal_selesai').change(function() {
        const tanggalMulai = $('#tanggal_mulai').val();
        const tanggalSelesai = $('#tanggal_selesai').val();
        
        if (tanggalMulai && tanggalSelesai && new Date(tanggalMulai) > new Date(tanggalSelesai)) {
            alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
            $('#tanggal_selesai').val('');
        }
    });

    document.querySelectorAll('.currency-format').forEach(function(input) {
        input.addEventListener('input', function(e) {
            // Hapus semua karakter non-numerik
            let value = this.value.replace(/\D/g, '');
            
            // Format dengan separator ribuan
            if (value !== '') {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            
            this.value = value;
        });
    });
});