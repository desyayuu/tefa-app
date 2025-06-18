<div class="content-table">
    <div class="card-data-dosen" style="font-size: 14px;">
        <div class="card-body">
            <!-- Alert untuk menampilkan error -->
            @include('components.handling_error')

            <!-- Form dengan ID yang jelas -->
            <form id="formEditDosen" action="{{ route('dosen.updateProfilDosen') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Hidden field untuk dosen ID -->
                <input type="hidden" id="dosen_id" name="dosen_id" value="{{ $dosen->dosen_id }}">
                
                <div class="title-table d-flex justify-content-between align-items-center mb-3">
                    <h5>Profil Akun</h5>
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-add">
                                <i class="bi bi-check-circle me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Error container untuk JavaScript - posisi setelah title -->
                <div id="form_dosen_error" class="alert alert-danger d-none" role="alert"></div>
                
                <div class="row">
                    <div class="col-md-12">
                        <!-- Nama dan NIM -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="profile-image-container mb-3 text-center">
                                    @if($dosen->profile_img_dosen)
                                        <img src="{{ asset($dosen->profile_img_dosen) }}" alt="{{ $dosen->nama_dosen }}" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @endif
                                </div>
                                <label for="profile_img_dosen" class="form-label">Foto Profil</label>
                                <input type="file" 
                                       class="form-control @error('profile_img_dosen') is-invalid @enderror" 
                                       id="profile_img_dosen" 
                                       name="profile_img_dosen" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="invalid-feedback" id="profile_img_dosen_error">
                                    @error('profile_img_dosen'){{ $message }}@enderror
                                </div>
                                <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>
                            </div>
                            <div class="col-md-4" style="margin-top:5.5rem">
                                <label for="nama_dosen" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nama_dosen') is-invalid @enderror" 
                                       id="nama_dosen" 
                                       name="nama_dosen" 
                                       value="{{ old('nama_dosen', $dosen->nama_dosen) }}"
                                       data-original-value="{{ $dosen->nama_dosen }}"
                                       required>
                                <div class="invalid-feedback" id="nama_dosen_error">
                                    @error('nama_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4" style="margin-top:5.5rem">
                                <label for="nidn_dosen" class="form-label">NIP/NIDN <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nidn_dosen') is-invalid @enderror" 
                                       id="nidn_dosen" 
                                       name="nidn_dosen" 
                                       value="{{ old('nidn_dosen', $dosen->nidn_dosen) }}"
                                       data-original-value="{{ $dosen->nidn_dosen }}"
                                       pattern="[0-9]{10}"
                                       maxlength="10"
                                       required>
                                <div class="invalid-feedback" id="nidn_dosen_error">
                                    @error('nidn_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Kelamin, Tanggal Lahir -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="jenis_kelamin_dosen" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin_dosen') is-invalid @enderror" 
                                        id="jenis_kelamin_dosen" 
                                        name="jenis_kelamin_dosen"
                                        data-original-value="{{ $dosen->jenis_kelamin_dosen }}">
                                    <option value="" {{ old('jenis_kelamin_dosen', $dosen->jenis_kelamin_dosen) == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin_dosen', $dosen->jenis_kelamin_dosen) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_dosen', $dosen->jenis_kelamin_dosen) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback" id="jenis_kelamin_dosen_error">
                                    @error('jenis_kelamin_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_lahir_dosen" class="form-label">Tanggal Lahir</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_lahir_dosen') is-invalid @enderror" 
                                       id="tanggal_lahir_dosen" 
                                       name="tanggal_lahir_dosen" 
                                       value="{{ old('tanggal_lahir_dosen', $dosen->tanggal_lahir_dosen) }}"
                                       data-original-value="{{ $dosen->tanggal_lahir_dosen }}">
                                <div class="invalid-feedback" id="tanggal_lahir_dosen_error">
                                    @error('tanggal_lahir_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Email, Password, dan Telepon -->
                        <div class="row">
                            <div class="col-md-4">
                                <label for="email_dosen" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       style="font-size:14px;" 
                                       class="form-control @error('email_dosen') is-invalid @enderror" 
                                       id="email_dosen" 
                                       name="email_dosen" 
                                       value="{{ old('email_dosen', $dosen->email) }}"
                                       data-original-value="{{ $dosen->email }}"
                                       required>
                                <div class="invalid-feedback" id="email_dosen_error">
                                    @error('email_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="password_dosen" class="form-label">
                                    Password 
                                    <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password_dosen') is-invalid @enderror" 
                                           id="password_dosen" 
                                           name="password_dosen" 
                                           placeholder="Biarkan kosong jika tidak ingin mengubah">
                                </div>
                                <div class="invalid-feedback" id="password_dosen_error">
                                    @error('password_dosen'){{ $message }}@enderror
                                </div>
                                <small class="text-muted">Jika kosong, password akan menggunakan NIM</small>
                            </div>
                            <div class="col mb-4">
                                <label for="telepon_dosen" class="form-label">Telepon</label>
                                <input type="text" 
                                       class="form-control @error('telepon_dosen') is-invalid @enderror" 
                                       id="telepon_dosen" 
                                       name="telepon_dosen" 
                                       value="{{ old('telepon_dosen', $dosen->telepon_dosen) }}"
                                       data-original-value="{{ $dosen->telepon_dosen }}">
                                <div class="invalid-feedback" id="telepon_dosen_error">
                                    @error('telepon_dosen'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                    </div>
                </div> 
            </form>
        </div>
    </div>
</div>