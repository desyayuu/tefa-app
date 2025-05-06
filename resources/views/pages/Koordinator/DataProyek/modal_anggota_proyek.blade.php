<!-- Modal untuk Project Leader -->
<div class="modal fade" id="addLeaderModal" tabindex="-1" aria-labelledby="addLeaderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaderModalLabel">Pilih Project Leader</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLeader" action="{{ route('koordinator.updateProjectLeader', $proyek->proyek_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenisPL" class="form-label">Jenis Project Leader:</label>
                        <select id="jenisPL" name="leader_type" class="form-select" onchange="tampilkanNamaPL()" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Profesional">Profesional</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="namaPL" class="form-label">Nama Project Leader:</label>
                        <select id="namaPL" name="leader_id" class="form-select" required style="width: 100%;">
                            <option value="">-- Pilih Nama --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan jQuery tersedia
    if (typeof jQuery !== 'undefined') {
        // Inisialisasi Select2 pada dropdown
        $('#namaPL').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#addLeaderModal'), // Penting untuk modal
            placeholder: '-- Pilih Nama --',
            allowClear: true,
            width: '100%'
        });
        
        window.tampilkanNamaPL = function() {
            const jenisPL = document.getElementById('jenisPL').value;
            const namaPLDropdown = $('#namaPL');
            
            // Hapus opsi yang ada sebelum menambahkan yang baru
            namaPLDropdown.empty().append('<option value="">-- Pilih Nama --</option>');
            
            if (jenisPL === 'Dosen') {
                // Data dosen
                @foreach($dataDosen as $dosen)
                namaPLDropdown.append(new Option("{{ $dosen->nama_dosen }}", "{{ $dosen->dosen_id }}"));
                @endforeach
            } else if (jenisPL === 'Profesional') {
                // Data profesional
                @foreach($dataProfesional as $profesional)
                namaPLDropdown.append(new Option("{{ $profesional->nama_profesional }}", "{{ $profesional->profesional_id }}"));
                @endforeach
            }
            
            // Penting: Refresh Select2 setelah menambahkan opsi baru
            namaPLDropdown.trigger('change');
        };
        
        // Modal event handlers untuk reinisialisasi Select2
        $('#addLeaderModal').on('shown.bs.modal', function() {
            $('#namaPL').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addLeaderModal'),
                placeholder: '-- Pilih Nama --',
                allowClear: true,
                width: '100%'
            });
        });
        
        // Pre-select nilai jika sudah ada project leader
        const existingLeaderType = "{{ $projectLeader->leader_type ?? '' }}";
        const existingLeaderId = "{{ $projectLeader->leader_id ?? '' }}";
        
        if (existingLeaderType) {
            document.getElementById('jenisPL').value = existingLeaderType;
            tampilkanNamaPL(); // Populasi dropdown nama
            
            // Set nilai leader_id jika ada
            if (existingLeaderId) {
                setTimeout(() => {
                    $('#namaPL').val(existingLeaderId).trigger('change');
                }, 100);
            }
        }
        
        // Form validation
        document.getElementById('formLeader').addEventListener('submit', function(event) {
            const jenisPL = document.getElementById('jenisPL').value;
            const namaPL = $('#namaPL').val();
            
            if (!jenisPL || !namaPL) {
                event.preventDefault();
                alert('Mohon lengkapi semua field!');
            }
        });
    } else {
        console.error('jQuery tidak ditemukan! Select2 membutuhkan jQuery.');
    }
});
</script>