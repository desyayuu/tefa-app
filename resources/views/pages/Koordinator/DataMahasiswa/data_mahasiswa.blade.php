<div class="content-table">
    <div class="card-data-mahasiswa">
        <div class="card-body">
            <form action="{{ route('koordinator.updateDataMahasiswa', $mahasiswa->mahasiswa_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Profile Image Column -->
                    <div class="col-md-3 text-center mb-1">
                        <div class="profile-image-container mb-3">
                            @if($mahasiswa->profile_img_mahasiswa)
                                <img src="{{ asset($mahasiswa->profile_img_mahasiswa) }}" alt="{{ $mahasiswa->nama_mahasiswa }}" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default-profile.jpg') }}" alt="Default Profile" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <input type="file" class="form-control" id="profile_img_mahasiswa" name="profile_img_mahasiswa" accept="image/*">
                            <div class="invalid-feedback" id="profile_img_mahasiswa_error"></div>
                            <small class="text-muted">Format: jpeg, png, jpg, gif. Maks 2MB</small>
                        </div>
                    </div>
                    
                    <!-- Student Details Form Column -->
                    <div class="col-md-9">  
                        <!-- Nama dan NIM -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama_mahasiswa" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" value="{{ $mahasiswa->nama_mahasiswa }}">
                                <div class="invalid-feedback" id="nama_mahasiswa_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="nim_mahasiswa" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="nim_mahasiswa" name="nim_mahasiswa" value="{{ $mahasiswa->nim_mahasiswa }}">
                                <div class="invalid-feedback" id="nim_mahasiswa_error"></div>
                            </div>
                        </div>
                        
                        <!-- Jenis Kelamin, Tanggal Lahir, Status -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="jenis_kelamin_mahasiswa" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin_mahasiswa" name="jenis_kelamin_mahasiswa">
                                    <option value="" {{ $mahasiswa->jenis_kelamin_mahasiswa == null ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                                    <option value="Laki-Laki" {{ $mahasiswa->jenis_kelamin_mahasiswa == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ $mahasiswa->jenis_kelamin_mahasiswa == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="invalid-feedback" id="jenis_kelamin_mahasiswa_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_lahir_mahasiswa" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir_mahasiswa" name="tanggal_lahir_mahasiswa" value="{{ $mahasiswa->tanggal_lahir_mahasiswa }}">
                                <div class="invalid-feedback" id="tanggal_lahir_mahasiswa_error"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="status_akun_mahasiswa" class="form-label">Status Akun</label>
                                <select class="form-select" id="status_akun_mahasiswa" name="status_akun_mahasiswa">
                                    <option value="Active" {{ $mahasiswa->status == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Pending" {{ $mahasiswa->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Rejected" {{ $mahasiswa->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="Disabled" {{ $mahasiswa->status == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                                <div class="invalid-feedback" id="status_akun_mahasiswa_error"></div>
                            </div>
                        </div>
                        
                        <!-- Email dan Password -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email_mahasiswa" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email_mahasiswa" name="email_mahasiswa" value="{{ $mahasiswa->email }}">
                                <div class="invalid-feedback" id="email_mahasiswa_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_mahasiswa" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password_mahasiswa" name="password_mahasiswa">
                                <div class="invalid-feedback" id="password_mahasiswa_error"></div>
                            </div>
                        </div>
                        
                        <!-- Telepon -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telepon_mahasiswa" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon_mahasiswa" name="telepon_mahasiswa" value="{{ $mahasiswa->telepon_mahasiswa }}">
                                <div class="invalid-feedback" id="telepon_mahasiswa_error"></div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('koordinator.dataMahasiswa') }}" class="btn btn-tutup me-2">Kembali</a>
                                <button type="submit" class="btn btn-add">Simpan Perubahan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>