
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
                <div class="title-table d-flex justify-content-between align-items-center mb-3">
                    <h5>Data Mahasiswa</h5>
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-add">
                                <i class="bi bi-check-circle me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <!-- Nama dan NIM -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="profile-image-container mb-3 text-center">
                                    @if($mahasiswa->profile_img_mahasiswa)
                                        <img src="{{ asset($mahasiswa->profile_img_mahasiswa) }}" alt="{{ $mahasiswa->nama_mahasiswa }}" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @endif
                                </div>
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
                            <div class="col-md-4" style="margin-top:5.5rem">
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
                            <div class="col-md-4" style="margin-top:5.5rem">
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
                        </div>

                        <!-- Jenis Kelamin, Tanggal Lahir, Status -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="jenis_kelamin_mahasiswa" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin_mahasiswa') is-invalid @enderror" 
                                        id="jenis_kelamin_mahasiswa" 
                                        name="jenis_kelamin_mahasiswa"
                                        data-original-value="{{ $mahasiswa->jenis_kelamin_mahasiswa }}">
                                    <option value="" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_mahasiswa', $mahasiswa->jenis_kelamin_mahasiswa) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback" id="jenis_kelamin_mahasiswa_error">
                                    @error('jenis_kelamin_mahasiswa'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-4">
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
                        </div>

                        <!-- Email, Password, dan Telepon -->
                        <div class="row">
                            <div class="col-md-4">
                                <label for="email_mahasiswa" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       style="font-size:14px;" 
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
                            <div class="col-md-4">
                                <label for="password_mahasiswa" class="form-label">
                                    Password 
                                    <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password_mahasiswa') is-invalid @enderror" 
                                           id="password_mahasiswa" 
                                           name="password_mahasiswa" 
                                           placeholder="Biarkan kosong jika tidak ingin mengubah">
                                </div>
                                <div class="invalid-feedback" id="password_mahasiswa_error">
                                    @error('password_mahasiswa'){{ $message }}@enderror
                                </div>
                                <small class="text-muted">Jika kosong, password akan menggunakan NIM</small>
                            </div>
                            <div class="col mb-4">
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
                        </div>

                        <!-- Github & LinkedIn -->
                        <div class="row">
                            <div class="col-md-6">
                                <label for="github" class="form-label">Github</label>
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
                            <div class="col-md-6">
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
                        </div>

                        <!-- File Uploads: CV, KTP, KTM -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="doc_cv" class="form-label">CV</label>
                                @if($mahasiswa->doc_cv)
                                    <div class="mb-2">
                                        <small class="text-success">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            File saat ini: <a href="{{ asset($mahasiswa->doc_cv) }}" target="_blank">Lihat CV</a>
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
                            <div class="col-md-4">
                                <label for="doc_ktp" class="form-label">KTP</label>
                                @if($mahasiswa->doc_ktp)
                                    <div class="mb-2">
                                        <small class="text-success">
                                            <i class="bi bi-file-earmark-image"></i>
                                            File saat ini: <a href="{{ asset($mahasiswa->doc_ktp) }}" target="_blank">Lihat KTP</a>
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
                            <div class="col-md-4">
                                <label for="doc_ktm" class="form-label">KTM</label>
                                @if($mahasiswa->doc_ktm)
                                    <div class="mb-2">
                                        <small class="text-success">
                                            <i class="bi bi-file-earmark-image"></i>
                                            File saat ini: <a href="{{ asset($mahasiswa->ktm) }}" target="_blank">Lihat KTM</a>
                                        </small>
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('ktm') is-invalid @enderror" 
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
            </form>
        </div>
    </div>
</div>