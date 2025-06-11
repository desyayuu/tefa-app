<!-- Modal untuk Project Leader -->
<div class="modal fade" id="addLeaderModal" tabindex="-1" aria-labelledby="addLeaderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaderModalLabel">Pilih Project Leader</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formLeader" action="{{ route('profesional.updateProjectLeader', $proyek->proyek_id) }}" method="POST">
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

<!-- Modal untuk Dosen -->
<div class="modal fade" id="addDosenModal" tabindex="-1" aria-labelledby="addDosenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDosenModalLabel">Tambah Anggota Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddDosen" action="{{ route('profesional.tambahAnggotaDosen', $proyek->proyek_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="dosen_select" class="form-label">Pilih Dosen</label>
                            <select class="form-select" id="dosen_select">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dataDosen ?? [] as $dosen)
                                    @php
                                        $isLeader = ($projectLeader && 
                                                    $projectLeader->leader_type === 'Dosen' && 
                                                    $projectLeader->leader_id === $dosen->dosen_id);
                                    @endphp
                                    
                                    @if(!$isLeader)
                                        <option value="{{ $dosen->dosen_id }}" 
                                                data-nama="{{ $dosen->nama_dosen }}" 
                                                data-nidn="{{ $dosen->nidn_dosen ?? '' }}">
                                            {{ $dosen->nama_dosen }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-add" id="btnAddToList">
                                Tambahkan ke Daftar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3 mt-5">
                        <label for="selectedDosenTable" class="form-label">Daftar Dosen yang Dipilih</label>
                        <table class="table table-bordered" id="selectedDosenTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Dosen</th>
                                    <th>NIDN</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data dosen yang dipilih -->
                                <tr id="emptyRow">
                                    <td colspan="3" class="text-center">Belum ada dosen yang dipilih</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Input tersembunyi untuk menyimpan data dosen terakhir yang dipilih -->
                    <input type="hidden" name="dosen_id" id="dosen_id">
                    
                    <!-- Hidden input untuk menyimpan data dosen yang dipilih -->
                    <input type="hidden" name="selected_dosen" id="selectedDosenInput" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Profesional -->
<div class="modal fade" id="addProfesionalModal" tabindex="-1" aria-labelledby="addProfesionalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProfesionalModalLabel">Tambah Anggota Profesional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddProfesional" action="{{ route('profesional.tambahAnggotaProfesional', $proyek->proyek_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="profesional_select" class="form-label">Pilih Profesional</label>
                            <select class="form-select" id="profesional_select">
                                <option value="">-- Pilih Profesional --</option>
                                @foreach($dataProfesional ?? [] as $profesional)
                                    @php
                                        $isLeader = ($projectLeader && 
                                                    $projectLeader->leader_type === 'Profesional' && 
                                                    $projectLeader->leader_id === $profesional->profesional_id);
                                        
                                        // Get email from user table
                                        $userEmail = DB::table('d_user')
                                            ->where('user_id', $profesional->user_id)
                                            ->value('email');
                                    @endphp
                                    
                                    @if(!$isLeader)
                                        <option value="{{ $profesional->profesional_id }}" 
                                                data-nama="{{ $profesional->nama_profesional }}" 
                                                data-email="{{ $userEmail ?? '' }}">
                                            {{ $profesional->nama_profesional }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-add" id="btnAddProfesionalToList">
                                Tambahkan ke Daftar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3 mt-5">
                        <label for="selectedProfesionalTable" class="form-label">Daftar Profesional yang Dipilih</label>
                        <table class="table table-bordered" id="selectedProfesionalTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Profesional</th>
                                    <th>Email</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data profesional yang dipilih akan ditampilkan di sini -->
                                <tr id="emptyProfesionalRow">
                                    <td colspan="3" class="text-center">Belum ada profesional yang dipilih</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Input tersembunyi untuk menyimpan data profesional terakhir yang dipilih (agar backward compatible) -->
                    <input type="hidden" name="profesional_id" id="profesional_id">
                    
                    <!-- Hidden input untuk menyimpan data profesional yang dipilih -->
                    <input type="hidden" name="selected_profesional" id="selectedProfesionalInput" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Mahasiswa -->
<div class="modal fade" id="addMahasiswaModal" tabindex="-1" aria-labelledby="addMahasiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMahasiswaModalLabel">Tambah Anggota Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAddMahasiswa" action="{{ route('profesional.tambahAnggotaMahasiswa', $proyek->proyek_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="mahasiswa_select" class="form-label">Pilih Mahasiswa</label>
                            <select class="form-select" id="mahasiswa_select">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($dataMahasiswa ?? [] as $mahasiswa)
                                    <option value="{{ $mahasiswa->mahasiswa_id }}" 
                                            data-nama="{{ $mahasiswa->nama_mahasiswa }}" 
                                            data-nim="{{ $mahasiswa->nim_mahasiswa ?? '' }}">
                                        {{ $mahasiswa->nama_mahasiswa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-add" id="btnAddMahasiswaToList">
                                Tambahkan ke Daftar
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3 mt-5">
                        <label for="selectedMahasiswaTable" class="form-label">Daftar Mahasiswa yang Dipilih</label>
                        <table class="table table-bordered" id="selectedMahasiswaTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data mahasiswa yang dipilih akan ditampilkan di sini -->
                                <tr id="emptyMahasiswaRow">
                                    <td colspan="3" class="text-center">Belum ada mahasiswa yang dipilih</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Input tersembunyi untuk menyimpan data mahasiswa terakhir yang dipilih (agar backward compatible) -->
                    <input type="hidden" name="mahasiswa_id" id="mahasiswa_id">
                    
                    <!-- Hidden input untuk menyimpan data mahasiswa yang dipilih -->
                    <input type="hidden" name="selected_mahasiswa" id="selectedMahasiswaInput" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-tutup" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-add">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined') {
            $('#namaPL').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addLeaderModal'),
                placeholder: '-- Pilih Nama --',
                allowClear: true,
                width: '100%'
            });
            
            $('#dosen_select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addDosenModal'),
                placeholder: '-- Pilih Dosen --',
                allowClear: true,
                width: '100%'
            });
            
            window.tampilkanNamaPL = function() {
                const jenisPL = document.getElementById('jenisPL').value;
                const namaPLDropdown = $('#namaPL');
                
                namaPLDropdown.empty().append('<option value="">-- Pilih Nama --</option>');
                
                if (jenisPL === 'Dosen') {
                    @foreach($dataDosen as $dosen)
                    namaPLDropdown.append(new Option("{{ $dosen->nama_dosen }}", "{{ $dosen->dosen_id }}"));
                    @endforeach
                } else if (jenisPL === 'Profesional') {
                    @foreach($dataProfesional as $profesional)
                    namaPLDropdown.append(new Option("{{ $profesional->nama_profesional }}", "{{ $profesional->profesional_id }}"));
                    @endforeach
                }
                
                namaPLDropdown.trigger('change');
            };
            
            // Event handler untuk modal Project Leader
            $('#addLeaderModal').on('shown.bs.modal', function() {
                $('#namaPL').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#addLeaderModal'),
                    placeholder: '-- Pilih Nama --',
                    allowClear: true,
                    width: '100%'
                });
            });
            
            // Set nilai yang sudah ada untuk Project Leader
            const existingLeaderType = "{{ $projectLeader->leader_type ?? '' }}";
            const existingLeaderId = "{{ $projectLeader->leader_id ?? '' }}";
            
            if (existingLeaderType) {
                document.getElementById('jenisPL').value = existingLeaderType;
                tampilkanNamaPL(); 

                if (existingLeaderId) {
                    setTimeout(() => {
                        $('#namaPL').val(existingLeaderId).trigger('change');
                    }, 100);
                }
            }
            
            // Validasi form Project Leader
            document.getElementById('formLeader').addEventListener('submit', function(event) {
                const jenisPL = document.getElementById('jenisPL').value;
                const namaPL = $('#namaPL').val();
                
                if (!jenisPL || !namaPL) {
                    event.preventDefault();
                    alert('Mohon lengkapi semua field!');
                }
            });
            
            // === TAMBAHAN KODE UNTUK MULTIPLE DOSEN SELECTION ===
            
            // Array untuk menyimpan dosen yang dipilih
            var selectedDosen = [];
            
            // Fungsi untuk memeriksa apakah dosen sudah dipilih
            function isDosenSelected(dosenId) {
                return selectedDosen.some(function(dosen) {
                    return dosen.id === dosenId;
                });
            }
            
            // Fungsi untuk memperbarui tabel dan hidden input
            function updateSelectedDosenTable() {
                var tbody = $('#selectedDosenTable tbody');
                
                // Hapus semua row kecuali row kosong
                tbody.find('tr:not(#emptyRow)').remove();
                
                if (selectedDosen.length === 0) {
                    $('#emptyRow').show();
                } else {
                    $('#emptyRow').hide();
                    
                    selectedDosen.forEach(function(dosen, index) {
                        tbody.append(`
                            <tr>
                                <td>${dosen.nama}</td>
                                <td>${dosen.nidn || '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-dosen btn btn-sm btn-danger" data-index="${index}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }
                
                // Update hidden input dengan data dosen yang dipilih
                $('#selectedDosenInput').val(JSON.stringify(selectedDosen.map(d => d.id)));
                
                // Set dosen_id dengan ID terakhir yang dipilih untuk kompatibilitas
                if (selectedDosen.length > 0) {
                    $('#dosen_id').val(selectedDosen[selectedDosen.length - 1].id);
                } else {
                    $('#dosen_id').val('');
                }
            }
            
            // Event click untuk tombol "Tambahkan ke Daftar"
            $('#btnAddToList').on('click', function() {
                var select = $('#dosen_select');
                var dosenId = select.val();
                
                if (!dosenId) {
                    alert('Silakan pilih dosen terlebih dahulu!');
                    return;
                }
                
                if (isDosenSelected(dosenId)) {
                    alert('Dosen ini sudah ditambahkan ke daftar!');
                    return;
                }
                
                var option = select.find('option:selected');
                var dosenData = {
                    id: dosenId,
                    nama: option.data('nama'),
                    nidn: option.data('nidn')
                };
                
                selectedDosen.push(dosenData);
                updateSelectedDosenTable();
                
                // Reset select
                select.val('').trigger('change');
            });
            
            // Event click untuk tombol hapus dosen dari daftar
            $(document).on('click', '.btn-remove-dosen', function() {
                var index = $(this).data('index');
                selectedDosen.splice(index, 1);
                updateSelectedDosenTable();
            });
            
            // Event submit form
            $('#formAddDosen').on('submit', function(e) {
                if (selectedDosen.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu dosen!');
                    return false;
                }
            });
            
            // Inisialisasi tabel
            updateSelectedDosenTable();

            // Mahasiswa selection with search feature
            $('#mahasiswa_select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addMahasiswaModal'),
                placeholder: '-- Pilih Mahasiswa --',
                allowClear: true,
                width: '100%'
            });
            
            // === KODE UNTUK MULTIPLE MAHASISWA SELECTION ===
            
            // Array untuk menyimpan mahasiswa yang dipilih
            var selectedMahasiswa = [];
            
            // Fungsi untuk memeriksa apakah mahasiswa sudah dipilih
            function isMahasiswaSelected(mahasiswaId) {
                return selectedMahasiswa.some(function(mahasiswa) {
                    return mahasiswa.id === mahasiswaId;
                });
            }
            
            // Fungsi untuk memperbarui tabel dan hidden input
            function updateSelectedMahasiswaTable() {
                var tbody = $('#selectedMahasiswaTable tbody');
                
                // Hapus semua row kecuali row kosong
                tbody.find('tr:not(#emptyMahasiswaRow)').remove();
                
                if (selectedMahasiswa.length === 0) {
                    $('#emptyMahasiswaRow').show();
                } else {
                    $('#emptyMahasiswaRow').hide();
                    
                    selectedMahasiswa.forEach(function(mahasiswa, index) {
                        tbody.append(`
                            <tr>
                                <td>${mahasiswa.nama}</td>
                                <td>${mahasiswa.nim || '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-mahasiswa btn btn-sm btn-danger" data-index="${index}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }
                
                // Update hidden input dengan data mahasiswa yang dipilih
                $('#selectedMahasiswaInput').val(JSON.stringify(selectedMahasiswa.map(m => m.id)));
                
                // Set mahasiswa_id dengan ID terakhir yang dipilih untuk kompatibilitas
                if (selectedMahasiswa.length > 0) {
                    $('#mahasiswa_id').val(selectedMahasiswa[selectedMahasiswa.length - 1].id);
                } else {
                    $('#mahasiswa_id').val('');
                }
            }
            
            // Event click untuk tombol "Tambahkan ke Daftar"
            $('#btnAddMahasiswaToList').on('click', function() {
                var select = $('#mahasiswa_select');
                var mahasiswaId = select.val();
                
                if (!mahasiswaId) {
                    alert('Silakan pilih mahasiswa terlebih dahulu!');
                    return;
                }
                
                if (isMahasiswaSelected(mahasiswaId)) {
                    alert('Mahasiswa ini sudah ditambahkan ke daftar!');
                    return;
                }
                
                var option = select.find('option:selected');
                var mahasiswaData = {
                    id: mahasiswaId,
                    nama: option.data('nama'),
                    nim: option.data('nim')
                };
                
                selectedMahasiswa.push(mahasiswaData);
                updateSelectedMahasiswaTable();
                
                // Reset select
                select.val('').trigger('change');
            });
            
            // Event click untuk tombol hapus mahasiswa dari daftar
            $(document).on('click', '.btn-remove-mahasiswa', function() {
                var index = $(this).data('index');
                selectedMahasiswa.splice(index, 1);
                updateSelectedMahasiswaTable();
            });
            
            // Event submit form
            $('#formAddMahasiswa').on('submit', function(e) {
                if (selectedMahasiswa.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu mahasiswa!');
                    return false;
                }
            });
            
            // Inisialisasi tabel
            updateSelectedMahasiswaTable();
            
             // Profesional selection with search feature
             $('#profesional_select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#addProfesionalModal'),
                placeholder: '-- Pilih Profesional --',
                allowClear: true,
                width: '100%'
            });
            
            // === KODE UNTUK MULTIPLE PROFESIONAL SELECTION ===
            
            // Array untuk menyimpan profesional yang dipilih
            var selectedProfesional = [];
            
            // Fungsi untuk memeriksa apakah profesional sudah dipilih
            function isProfesionalSelected(profesionalId) {
                return selectedProfesional.some(function(profesional) {
                    return profesional.id === profesionalId;
                });
            }
            
            // Fungsi untuk memperbarui tabel dan hidden input
            function updateSelectedProfesionalTable() {
                var tbody = $('#selectedProfesionalTable tbody');
                
                // Hapus semua row kecuali row kosong
                tbody.find('tr:not(#emptyProfesionalRow)').remove();
                
                if (selectedProfesional.length === 0) {
                    $('#emptyProfesionalRow').show();
                } else {
                    $('#emptyProfesionalRow').hide();
                    
                    selectedProfesional.forEach(function(profesional, index) {
                        tbody.append(`
                            <tr>
                                <td>${profesional.nama}</td>
                                <td>${profesional.email || '-'}</td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-profesional btn btn-sm btn-danger" data-index="${index}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }
                
                // Update hidden input dengan data profesional yang dipilih
                $('#selectedProfesionalInput').val(JSON.stringify(selectedProfesional.map(p => p.id)));
                
                // Set profesional_id dengan ID terakhir yang dipilih untuk kompatibilitas
                if (selectedProfesional.length > 0) {
                    $('#profesional_id').val(selectedProfesional[selectedProfesional.length - 1].id);
                } else {
                    $('#profesional_id').val('');
                }
            }
            
            // Event click untuk tombol "Tambahkan ke Daftar"
            $('#btnAddProfesionalToList').on('click', function() {
                var select = $('#profesional_select');
                var profesionalId = select.val();
                
                if (!profesionalId) {
                    alert('Silakan pilih profesional terlebih dahulu!');
                    return;
                }
                
                if (isProfesionalSelected(profesionalId)) {
                    alert('Profesional ini sudah ditambahkan ke daftar!');
                    return;
                }
                
                var option = select.find('option:selected');
                var profesionalData = {
                    id: profesionalId,
                    nama: option.data('nama'),
                    email: option.data('email')
                };
                
                selectedProfesional.push(profesionalData);
                updateSelectedProfesionalTable();
                
                // Reset select
                select.val('').trigger('change');
            });
            
            // Event click untuk tombol hapus profesional dari daftar
            $(document).on('click', '.btn-remove-profesional', function() {
                var index = $(this).data('index');
                selectedProfesional.splice(index, 1);
                updateSelectedProfesionalTable();
            });
            
            // Event submit form
            $('#formAddProfesional').on('submit', function(e) {
                if (selectedProfesional.length === 0) {
                    e.preventDefault();
                    alert('Silakan pilih minimal satu profesional!');
                    return false;
                }
            });
            
            // Inisialisasi tabel
            updateSelectedProfesionalTable();
            
        } else {
            console.error('jQuery tidak ditemukan! Select2 membutuhkan jQuery.');
        }
    });
</script>