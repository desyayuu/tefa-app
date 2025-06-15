<!-- Interface Data Mahasiswa dengan 2 Kolom -->
<div class="content-table">
    <div class="card-data-mahasiswa" style="font-size: 14px;">
        <div class="card-body">
            <!-- Alert untuk menampilkan error -->
            @include('components.handling_error')
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Terdapat kesalahan pada form:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="formEditMahasiswa" action="{{ route('koordinator.updateDataMahasiswa', $mahasiswa->mahasiswa_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <input type="hidden" id="mahasiswa_id" name="mahasiswa_id" value="{{ $mahasiswa->mahasiswa_id }}">
                
                <!-- Error container untuk JavaScript -->
                <div id="form_mahasiswa_error" class="alert alert-danger d-none" role="alert"></div>
                
                <!-- Header dengan tombol simpan -->
                <div class="title-table d-flex justify-content-between align-items-center mb-4">
                    <h5>Profil Mahasiswa</h5>
                    <button type="submit" class="btn btn-add">
                        <i class="bi bi-check-circle me-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
                
                <!-- Layout 2 Kolom -->
                <div class="row">
                    <!-- KOLOM 1: DATA PRIBADI -->
                    <div class="col-md-6">
                        <div class="card h-100" style="border: 2px solid #e9ecef; border-radius: 10px;">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Data Pribadi</h6>
                            </div>
                            <div class="card-body">
                                <!-- Foto Profil -->
                                <div class="profile-image-container mb-3 text-center">
                                    @if($mahasiswa->profile_img_mahasiswa)
                                        <img src="{{ asset($mahasiswa->profile_img_mahasiswa) }}" alt="{{ $mahasiswa->nama_mahasiswa }}" class="img-thumbnail rounded-circle" 
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile" class="img-thumbnail rounded-circle" 
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_img_mahasiswa" class="form-label">Foto Profil</label>
                                    <input type="file" 
                                           class="form-control @error('profile_img_mahasiswa') is-invalid @enderror" 
                                           id="profile_img_mahasiswa" 
                                           name="profile_img_mahasiswa" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif">
                                    <div class="invalid-feedback" id="profile_img_mahasiswa_error">
                                        @error('profile_img_mahasiswa'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="mb-3">
                                    <label for="nama_mahasiswa" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_mahasiswa') is-invalid @enderror" 
                                           id="nama_mahasiswa" 
                                           name="nama_mahasiswa" 
                                           value="{{ old('nama_mahasiswa', $mahasiswa->nama_mahasiswa) }}"
                                           data-original-value="{{ $mahasiswa->nama_mahasiswa }}"
                                           required>
                                    <div class="invalid-feedback" id="nama_mahasiswa_error">
                                        @error('nama_mahasiswa'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- NIM -->
                                <div class="mb-3">
                                    <label for="nim_mahasiswa" class="form-label">NIM <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nim_mahasiswa') is-invalid @enderror" 
                                           id="nim_mahasiswa" 
                                           name="nim_mahasiswa" 
                                           value="{{ old('nim_mahasiswa', $mahasiswa->nim_mahasiswa) }}"
                                           data-original-value="{{ $mahasiswa->nim_mahasiswa }}"
                                           pattern="[0-9]{10}"
                                           maxlength="10"
                                           required>
                                    <div class="invalid-feedback" id="nim_mahasiswa_error">
                                        @error('nim_mahasiswa'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- Row untuk 2 kolom -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="jenis_kelamin_mahasiswa" class="form-label">Jenis Kelamin</label>
                                        <select class="form-select @error('jenis_kelamin_mahasiswa') is-invalid @enderror" 
                                                id="jenis_kelamin_mahasiswa" 
                                                name="jenis_kelamin_mahasiswa"
                                                data-original-value="{{ $mahasiswa->jenis_kelamin_mahasiswa }}">
                                            <option value="" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == null ? 'selected' : '' }}>Pilih</option>
                                            <option value="Laki-Laki" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback" id="jenis_kelamin_mahasiswa_error">
                                            @error('jenis_kelamin_mahasiswa'){{ $message }}@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_lahir_mahasiswa" class="form-label">Tanggal Lahir</label>
                                        <input type="date" 
                                               class="form-control @error('tanggal_lahir_mahasiswa') is-invalid @enderror" 
                                               id="tanggal_lahir_mahasiswa" 
                                               name="tanggal_lahir_mahasiswa" 
                                               value="{{ old('tanggal_lahir_mahasiswa', $mahasiswa->tanggal_lahir_mahasiswa) }}"
                                               data-original-value="{{ $mahasiswa->tanggal_lahir_mahasiswa }}">
                                        <div class="invalid-feedback" id="tanggal_lahir_mahasiswa_error">
                                            @error('tanggal_lahir_mahasiswa'){{ $message }}@enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email_mahasiswa" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email_mahasiswa') is-invalid @enderror" 
                                           id="email_mahasiswa" 
                                           name="email_mahasiswa" 
                                           value="{{ old('email_mahasiswa', $mahasiswa->email) }}"
                                           data-original-value="{{ $mahasiswa->email }}"
                                           required>
                                    <div class="invalid-feedback" id="email_mahasiswa_error">
                                        @error('email_mahasiswa'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- Telepon -->
                                <div class="mb-3">
                                    <label for="telepon_mahasiswa" class="form-label">Telepon</label>
                                    <input type="text" 
                                           class="form-control @error('telepon_mahasiswa') is-invalid @enderror" 
                                           id="telepon_mahasiswa" 
                                           name="telepon_mahasiswa" 
                                           value="{{ old('telepon_mahasiswa', $mahasiswa->telepon_mahasiswa) }}"
                                           data-original-value="{{ $mahasiswa->telepon_mahasiswa }}">
                                    <div class="invalid-feedback" id="telepon_mahasiswa_error">
                                        @error('telepon_mahasiswa'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- GitHub & LinkedIn -->
                                <div class="mb-3">
                                    <label for="github" class="form-label">GitHub</label>
                                    <input type="url" 
                                           class="form-control @error('github') is-invalid @enderror" 
                                           id="github" 
                                           name="github" 
                                           value="{{ old('github', $mahasiswa->github) }}"
                                           data-original-value="{{ $mahasiswa->github }}"
                                           placeholder="https://github.com/username">
                                    <div class="invalid-feedback" id="github_error">
                                        @error('github'){{ $message }}@enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="linkedin" class="form-label">LinkedIn</label>
                                    <input type="url" 
                                           class="form-control @error('linkedin') is-invalid @enderror" 
                                           id="linkedin" 
                                           name="linkedin" 
                                           value="{{ old('linkedin', $mahasiswa->linkedin) }}"
                                           data-original-value="{{ $mahasiswa->linkedin}}"
                                           placeholder="https://linkedin.com/in/username">
                                    <div class="invalid-feedback" id="linkedin_error">
                                        @error('linkedin'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- Status Akun -->
                                <div class="mb-3">
                                    <label for="status_akun_mahasiswa" class="form-label">Status Akun <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status_akun_mahasiswa') is-invalid @enderror" 
                                            id="status_akun_mahasiswa" 
                                            name="status_akun_mahasiswa"
                                            data-original-value="{{ $mahasiswa->status }}"
                                            required>
                                        <option value="Active" {{ old('status_akun_mahasiswa', $mahasiswa->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Pending" {{ old('status_akun_mahasiswa', $mahasiswa->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Rejected" {{ old('status_akun_mahasiswa', $mahasiswa->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="Disabled" {{ old('status_akun_mahasiswa', $mahasiswa->status) == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                    </select>
                                    <div class="invalid-feedback" id="status_akun_mahasiswa_error">
                                        @error('status_akun_mahasiswa'){{ $message }}@enderror
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password_mahasiswa" class="form-label">
                                        Password 
                                        <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password_mahasiswa') is-invalid @enderror" 
                                           id="password_mahasiswa" 
                                           name="password_mahasiswa" 
                                           placeholder="Biarkan kosong jika tidak ingin mengubah">
                                    <div class="invalid-feedback" id="password_mahasiswa_error">
                                        @error('password_mahasiswa'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Jika kosong, password akan menggunakan NIM</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM 2: PROFIL DIRI & DOKUMEN -->
                    <div class="col-md-6">
                        <!-- BARIS 1: PROFIL DIRI -->
                        <div class="card mb-3" style="border: 2px solid #e9ecef; border-radius: 10px;">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Profil Diri</h6>
                            </div>
                            <div class="card-body">
                                <!-- Deskripsi Diri -->
                                <div class="mb-3">
                                    <label for="deskripsi_diri" class="form-label">Deskripsi Diri</label>
                                    <textarea class="form-control ckeditor @error('deskripsi_diri') is-invalid @enderror" 
                                              id="deskripsi_diri" 
                                              name="deskripsi_diri" 
                                              rows="6">{{ old('deskripsi_diri', $mahasiswa->deskripsi_diri ?? '') }}</textarea>
                                    <div class="invalid-feedback" id="deskripsi_diri_error">
                                        @error('deskripsi_diri'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Jelaskan tentang kepribadian, motivasi, dan tujuan Anda dengan formatting yang menarik</small>
                                </div>

                                <!-- Kelebihan -->
                                <div class="mb-3">
                                    <label for="kelebihan_diri" class="form-label">Kelebihan & Keahlian</label>
                                    <textarea class="form-control ckeditor @error('kelebihan_diri') is-invalid @enderror" 
                                              id="kelebihan_diri" 
                                              name="kelebihan_diri" 
                                              rows="5">{{ old('kelebihan_diri', $mahasiswa->kelebihan_diri ?? '') }}</textarea>
                                    <div class="invalid-feedback" id="kelebihan_error">
                                        @error('kelebihan_diri'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Sebutkan 3 kelebihan</small>
                                </div>

                                <!-- Kekurangan -->
                                <div class="mb-3">
                                    <label for="kekurangan_diri" class="form-label">Area yang Ingin Dikembangkan</label>
                                    <textarea class="form-control ckeditor @error('kekurangan_diri') is-invalid @enderror" 
                                              id="kekurangan_diri" 
                                              name="kekurangan_diri" 
                                              rows="5">{{ old('kekurangan_diri', $mahasiswa->kekurangan_diri ?? '') }}</textarea>
                                    <div class="invalid-feedback" id="kekurangan_error">
                                        @error('kekurangan_diri'){{ $message }}@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARIS 2: DOKUMEN -->
                        <div class="card" style="border: 2px solid #e9ecef; border-radius: 10px;">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Dokumen Mahasiswa</h6>
                            </div>
                            <div class="card-body">
                                <!-- CV -->
                                <div class="mb-3">
                                    <label for="doc_cv" class="form-label">CV (Curriculum Vitae)</label>
                                    @if($mahasiswa->doc_cv)
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                                File saat ini: <a href="{{ asset($mahasiswa->doc_cv) }}" target="_blank" class="text-decoration-none">Lihat CV</a>
                                            </small>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control @error('doc_cv') is-invalid @enderror" 
                                           id="doc_cv" 
                                           name="doc_cv" 
                                           accept=".pdf,.doc,.docx">
                                    <div class="invalid-feedback" id="doc_cv_error">
                                        @error('doc_cv'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Format: PDF, DOC, DOCX. Maksimal 2MB</small>
                                </div>

                                <!-- KTP -->
                                <div class="mb-3">
                                    <label for="doc_ktp" class="form-label">KTP (Kartu Tanda Penduduk)</label>
                                    @if($mahasiswa->doc_ktp)
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="bi bi-file-earmark-image"></i>
                                                File saat ini: <a href="{{ asset($mahasiswa->doc_ktp) }}" target="_blank" class="text-decoration-none">Lihat KTP</a>
                                            </small>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control @error('doc_ktp') is-invalid @enderror" 
                                           id="doc_ktp" 
                                           name="doc_ktp" 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="invalid-feedback" id="doc_ktp_error">
                                        @error('doc_ktp'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</small>
                                </div>

                                <!-- KTM -->
                                <div class="mb-3">
                                    <label for="doc_ktm" class="form-label">KTM (Kartu Tanda Mahasiswa)</label>
                                    @if($mahasiswa->doc_ktm)
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="bi bi-file-earmark-image"></i>
                                                File saat ini: <a href="{{ asset($mahasiswa->doc_ktm) }}" target="_blank" class="text-decoration-none">Lihat KTM</a>
                                            </small>
                                        </div>
                                    @endif
                                    <input type="file" 
                                           class="form-control @error('doc_ktm') is-invalid @enderror" 
                                           id="doc_ktm" 
                                           name="doc_ktm" 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="invalid-feedback" id="doc_ktm_error">
                                        @error('doc_ktm'){{ $message }}@enderror
                                    </div>
                                    <small class="text-muted">Format: PDF, JPG, JPEG, PNG. Maksimal 2MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CKEditor Script -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<style>
.card-data-mahasiswa .card {
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-data-mahasiswa .card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card-header {
    border-bottom: 2px solid #e9ecef;
}

.card-header h6 {
    font-weight: 600;
    color: #495057;
}

textarea {
    resize: vertical;
}

.text-success a {
    color: #198754 !important;
    font-weight: 500;
}

.text-success a:hover {
    color: #146c43 !important;
}

/* CKEditor Custom Styling */
.ck-editor__editable {
    min-height: 150px;
    border-radius: 0.375rem;
}

.ck.ck-editor {
    border-radius: 0.375rem;
    border: 2px solid #e9ecef;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.ck.ck-editor:focus-within {
    border-color: #4c6ef5;
    box-shadow: 0 0 0 0.2rem rgba(76, 110, 245, 0.25);
}

.ck.ck-toolbar {
    border-radius: 0.375rem 0.375rem 0 0;
    background: #f8f9fa;
}

.ck.ck-content {
    border-radius: 0 0 0.375rem 0.375rem;
}

/* Custom CKEditor button colors */
.ck.ck-button:not(.ck-disabled):hover {
    background: rgba(76, 110, 245, 0.1);
}

.ck.ck-button.ck-on {
    background: rgba(76, 110, 245, 0.2);
    border-color: #4c6ef5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editorConfig = {
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'link', '|',
                'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
        },
        language: 'id'
    };

    // Initialize CKEditor for Deskripsi Diri
    ClassicEditor
        .create(document.querySelector('#deskripsi_diri'), editorConfig)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                document.querySelector('#deskripsi_diri').value = editor.getData();
            });
        })
        .catch(error => {
            console.error('Error initializing CKEditor for deskripsi_diri:', error);
        });

    // Initialize CKEditor for Kelebihan
    ClassicEditor
        .create(document.querySelector('#kelebihan_diri'), editorConfig)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                document.querySelector('#kelebihan_diri').value = editor.getData();
            });
        })
        .catch(error => {
            console.error('Error initializing CKEditor for kelebihan_diri:', error);
        });

    // Initialize CKEditor for Kekurangan
    ClassicEditor
        .create(document.querySelector('#kekurangan_diri'), editorConfig)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                document.querySelector('#kekurangan_diri').value = editor.getData();
            });
        })
        .catch(error => {
            console.error('Error initializing CKEditor for kekurangan_diri:', error);
        });

    // Form validation untuk CKEditor
    document.querySelector('#formEditMahasiswa').addEventListener('submit', function(e) {
        // Update hidden textarea values before submit
        document.querySelectorAll('.ck-editor__editable').forEach(function(editable) {
            const editor = editable.ckeditorInstance;
            if (editor) {
                const textareaId = editor.sourceElement.id;
                document.querySelector('#' + textareaId).value = editor.getData();
            }
        });
    });
});
</script>