                <div class="mb-4">
                    <label class="form-label fw-bold">Bahasa Pemrograman:</label>
                    <div class="dropdown position-relative mb-3">
                        <div class="dropdown-bahasa-pemrograman" id="dropdownBahasaPemrograman">
                            <div class="d-flex align-items-center justify-content-between">
                                <input type="text" 
                                    class="search-input" 
                                    id="searchBahasaPemrograman"
                                    placeholder="Cari atau pilih bahasa pemrograman...">
                                <i class="fas fa-chevron-down" id="dropdownIconBahasa"></i>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-bahasa position-absolute w-100 mt-1" id="dropdownMenuBahasa" style="display: none; z-index: 1000;">
                            <!-- Dynamic content akan diisi di sini -->
                        </div>
                    </div>

                    <!-- Area untuk menampilkan bahasa pemrograman yang dipilih -->
                    <div class="mb-3">
                        <label class="form-label">Bahasa Pemrograman Terpilih:</label>
                        <div class="selected-bahasa-container" id="selectedBahasaContainer">
                            <div class="empty-state" id="emptyStateBahasa">
                                <i class="fas fa-plus-circle me-2"></i>
                                Belum ada bahasa pemrograman yang dipilih
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info counter bahasa pemrograman -->
                    <div class="text-end mb-2">
                        <small class="text-muted">
                            <span id="bahasaCounter">0</span> bahasa pemrograman dipilih
                        </small>
                    </div>
                </div>