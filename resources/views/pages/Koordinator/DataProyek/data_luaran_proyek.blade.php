<div id="data-luaran-container" class="detail-luaran flex-grow-1 pb-3">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0">Luaran Proyek</h4>
                <button type="button" class="btn btn-add" id="btnSaveChanges">Simpan Perubahan</button>
            </div>

            <form id="formLuaranProyek" enctype="multipart/form-data" 
                  data-luaran-url="{{ route('koordinator.updateDataLuaran') }}"
                  data-dokumentasi-url="{{ route('koordinator.addDokumentasi') }}">
                @csrf
                <input type="hidden" name="proyek_id" value="{{ $proyek->proyek_id }}">
                <input type="hidden" name="luaran_proyek_id" value="{{ $luaranProyek->luaran_proyek_id ?? '' }}">

                <div class="mb-3 row align-items-center">
                    <label for="poster_proyek" class="col-md-2 col-form-label">Poster Proyek</label>
                    <div class="col-md-10">
                        @if(isset($luaranProyek) && $luaranProyek->poster_proyek)
                            <div class="d-flex align-items-center mb-2">
                                <span class="me-2">File saat ini:</span>
                                @php
                                    $extension = pathinfo($luaranProyek->poster_proyek, PATHINFO_EXTENSION);
                                @endphp
                                
                                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                    <a href="{{ asset($luaranProyek->poster_proyek) }}" 
                                    class="text-primary small" target="_blank">
                                        <img src="{{ asset($luaranProyek->poster_proyek) }}" 
                                            class="img-thumbnail" height="40" width="40">
                                        Lihat poster
                                    </a>
                                @endif
                            </div>
                        @endif
                        <input type="file" class="form-control" id="poster_proyek" name="poster_proyek" accept="image/*">
                        <div class="invalid-feedback" id="poster_proyek_error"></div>
                        <small class="text-muted">Format: jpeg, png, jpg</small>
                        
                        {{-- Container preview poster - awalnya tersembunyi --}}
                        <div id="posterPreviewContainer" class="dokumentasi-preview-container mt-3" style="display: none;">
                            <p class="dokumentasi-section-title">Preview Poster Baru</p>
                            <div id="posterPreview"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 row align-items-center">
                    <label for="link_proyek" class="col-md-2 col-form-label">Link Proyek</label>
                    <div class="col-md-10">
                        <input type="url" class="form-control" id="link_proyek" name="link_proyek" 
                               value="{{ $luaranProyek->link_proyek ?? '' }}" placeholder="www.linkproyek.com">
                        <div class="invalid-feedback" id="link_proyek_error"></div>
                    </div>
                </div>

                <div class="mb-3 row align-items-center">
                    <label for="deskripsi_luaran" class="col-md-2 col-form-label">Deskripsi Proyek</label>
                    <div class="col-md-10">
                        <textarea class="form-control form-selection" id="deskripsi_luaran" name="deskripsi_luaran" rows="3">{{ $luaranProyek->deskripsi_luaran ?? $proyek->deskripsi_proyek ?? '' }}</textarea>
                        <div class="invalid-feedback" id="deskripsi_luaran_error"></div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label class="col-md-2 col-form-label">Dokumentasi Proyek</label>
                    <div class="col-md-10">
                        <div class="mb-2 d-flex align-items-center dokuemntasi-uploaded">
                            <!-- Hidden file input -->
                            <input type="file" class="d-none" id="dokumentasi" name="dokumentasi[]" accept=".jpg,.jpeg,.png" multiple>
                            <button type="button" class="btn btn-upload" id="btnUploadDokumentasi">Upload</button>
                        </div>
                        
                        <!-- Container untuk dokumentasi yang sudah ada di database -->
                        @if(isset($dokumentasiProyek) && $dokumentasiProyek->count() > 0)
                            <p class="dokumentasi-section-title">Dokumentasi Tersimpan</p>
                            <div class="dokumentasi-gallery">
                                @foreach($dokumentasiProyek as $dok)
                                <div class="dokumentasi-item position-relative">
                                    <img src="{{ asset($dok->path_file) }}" 
                                        alt="{{ $dok->nama_file }}">
                                    <button type="button" 
                                            class="btn btn-hapus btn-delete-dokumentasi" 
                                            data-id="{{ $dok->dokumentasi_proyek_id }}">
                                        <svg width="16" height="16" viewBox="0 0 19 19" fill="white" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.7851 9.48484C18.7851 14.6563 14.6051 18.8486 9.44866 18.8486C4.29227 18.8486 0.112183 14.6563 0.112183 9.48484C0.112183 4.31339 4.29227 0.121094 9.44866 0.121094C14.6051 0.121094 18.7851 4.31339 18.7851 9.48484ZM12.8922 4.61499L12.968 4.54584C13.3602 4.22524 13.9388 4.24842 14.3043 4.61499C14.6698 4.98156 14.6929 5.56186 14.3733 5.9552L14.3043 6.03127L10.8608 9.48484L14.3043 12.9384C14.6942 13.3295 14.6942 13.9636 14.3043 14.3546C13.9143 14.7457 13.2821 14.7457 12.8921 14.3546L9.44866 10.9011L6.00519 14.3546C5.61524 14.7457 4.98299 14.7457 4.59304 14.3546C4.20309 13.9636 4.20309 13.3295 4.59304 12.9384L8.03651 9.48484L4.59299 6.03127L4.52404 5.9552C4.20437 5.56186 4.22749 4.98157 4.59299 4.61499C4.9585 4.24842 5.5371 4.22524 5.9293 4.54584L6.00514 4.61499L9.44866 8.06857L12.8922 4.61499Z"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="dokumentasi-gallery"></div>
                        @endif
                        
                        <!-- Container untuk preview file yang akan diupload -->
                        <div id="dokumentasiPreviewContainer" class="dokumentasi-preview-container mt-3" style="display: none;">
                            <p class="dokumentasi-section-title">Preview Dokumentasi Baru</p>
                            <div id="dokumentasiPreviewItems"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Dokumentasi Preview Modal -->
<div class="modal fade" id="dokumentasiPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentasi Proyek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="dokumentasiPreviewImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="posterPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dokumentasi Proyek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="posterPreviewImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>
