
<div class="content-table">
    <div class="card-data-profesional" style="font-size: 14px;">
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

            <!-- Form dengan ID yang jelas -->
            <form id="formEditProfesional" action="{{ route('profesional.updateProfilProfesional', $profesional->profesional_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Hidden field untuk profesional ID -->
                <input type="hidden" id="profesional_id" name="profesional_id" value="{{ $profesional->profesional_id }}">
                
                <!-- Error container untuk JavaScript -->
                <div id="form_profesional_error" class="alert alert-danger d-none" role="alert"></div>
                
                <div class="title-table d-flex justify-content-between align-items-center mb-3">
                    <h5>Data Profesional</h5>
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
                                    @if($profesional->profile_img_profesional)
                                        <img src="{{ asset($profesional->profile_img_profesional) }}" alt="{{ $profesional->nama_profesional }}" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile" class="img-thumbnail rounded-circle" 
                                        style="width: 70px; height: 70px; object-fit: cover;">
                                    @endif
                                </div>
                                <label for="profile_img_profesional" class="form-label">Foto Profil</label>
                                <input type="file" 
                                       class="form-control @error('profile_img_profesional') is-invalid @enderror" 
                                       id="profile_img_profesional" 
                                       name="profile_img_profesional" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="invalid-feedback" id="profile_img_profesional_error">
                                    @error('profile_img_profesional'){{ $message }}@enderror
                                </div>
                                <small class="text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</small>
                            </div>
                            <div class="col-md-4" style="margin-top:5.5rem">
                                <label for="nama_profesional" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('nama_profesional') is-invalid @enderror" 
                                       id="nama_profesional" 
                                       name="nama_profesional" 
                                       value="{{ old('nama_profesional', $profesional->nama_profesional) }}"
                                       data-original-value="{{ $profesional->nama_profesional }}"
                                       required>
                                <div class="invalid-feedback" id="nama_profesional_error">
                                    @error('nama_profesional'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Kelamin, Tanggal Lahir, Status -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="jenis_kelamin_profesional" class="form-label">Jenis Kelamin</label>
                                <select class="form-select @error('jenis_kelamin_profesional') is-invalid @enderror" 
                                        id="jenis_kelamin_profesional" 
                                        name="jenis_kelamin_profesional"
                                        data-original-value="{{ $profesional->jenis_kelamin_profesional }}">
                                    <option value="" {{ old('jenis_kelamin_profesional', $profesional->jenis_kelamin_profesional) == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                    <option value="Laki-Laki" {{ old('jenis_kelamin_profesional', $profesional->jenis_kelamin_profesional) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin_profesional', $profesional->jenis_kelamin_profesional) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback" id="jenis_kelamin_profesional_error">
                                    @error('jenis_kelamin_profesional'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_lahir_profesional" class="form-label">Tanggal Lahir</label>
                                <input type="date" 
                                       class="form-control @error('tanggal_lahir_profesional') is-invalid @enderror" 
                                       id="tanggal_lahir_profesional" 
                                       name="tanggal_lahir_profesional" 
                                       value="{{ old('tanggal_lahir_profesional', $profesional->tanggal_lahir_profesional) }}"
                                       data-original-value="{{ $profesional->tanggal_lahir_profesional }}">
                                <div class="invalid-feedback" id="tanggal_lahir_profesional_error">
                                    @error('tanggal_lahir_profesional'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Email, Password, dan Telepon -->
                        <div class="row">
                            <div class="col-md-4">
                                <label for="email_profesional" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" 
                                       style="font-size:14px;" 
                                       class="form-control @error('email_profesional') is-invalid @enderror" 
                                       id="email_profesional" 
                                       name="email_profesional" 
                                       value="{{ old('email_profesional', $profesional->email) }}"
                                       data-original-value="{{ $profesional->email }}"
                                       required>
                                <div class="invalid-feedback" id="email_profesional_error">
                                    @error('email_profesional'){{ $message }}@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="password_profesional" class="form-label">
                                    Password 
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password_profesional') is-invalid @enderror" 
                                           id="password_profesional" 
                                           name="password_profesional" 
                                           placeholder="Biarkan kosong jika tidak ingin mengubah">
                                </div>
                                <div class="invalid-feedback" id="password_profesional_error">
                                    @error('password_profesional'){{ $message }}@enderror
                                </div>
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                            </div>
                            <div class="col mb-4">
                                <label for="telepon_profesional" class="form-label">Telepon</label>
                                <input type="text" 
                                       class="form-control @error('telepon_profesional') is-invalid @enderror" 
                                       id="telepon_profesional" 
                                       name="telepon_profesional" 
                                       value="{{ old('telepon_profesional', $profesional->telepon_profesional) }}"
                                       data-original-value="{{ $profesional->telepon_profesional }}">
                                <div class="invalid-feedback" id="telepon_profesional_error">
                                    @error('telepon_profesional'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </form>
        </div>
    </div>
</div>