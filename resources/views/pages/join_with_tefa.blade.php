<!-- Perbaikan minimal pada HTML form Anda -->
<section class="fifth-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Gabung Ide Proyek Bersama Kami !</h2>
        <h6 class="text-center mb-5">
            Tertarik bekerja sama dengan TEFA JTI Polinema? Beri tahu kami melalui form di bawah untuk diskusi mewujudkan ide proyek anda
        </h6>

        <!-- Alert container untuk feedback -->
        <div id="alert-container" class="mb-4"></div>

        <form id="joinProjectForm">
            @csrf
            <div class="row">
                <!-- Kolom Pertama -->
                <div class="col-md-6 mb-4">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="perusahaan" class="form-label">Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="perusahaan" name="perusahaan" placeholder="Masukkan Perusahaan Anda" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email Anda" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telepon" name="telepon" placeholder="Masukkan Telepon Anda">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4 d-flex flex-column justify-content-between h-100">
                    <div class="mb-3">
                        <label for="pesan" class="form-label">Pesan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="pesan" name="pesan" rows="8" placeholder="Tulis pesan Anda di sini..." required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-kirim px-4" id="submitBtn">
                            <span class="loading-spinner d-none spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <span id="submitText">Kirim</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('joinProjectForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.querySelector('.loading-spinner');
    const alertContainer = document.getElementById('alert-container');

    // Form submission handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous states
        clearValidationStates();
        setLoadingState(true);
        
        try {
            // Prepare form data
            const formData = new FormData(form);
            
            // Send request
            const response = await fetch('{{ route("join-proyek") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                // Success
                showAlert('success', result.message);
                form.reset();
                
                // Scroll to alert
                alertContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } else {
                // Handle validation errors
                if (result.errors) {
                    displayValidationErrors(result.errors);
                }
                showAlert('danger', result.message || 'Terjadi kesalahan saat mengirim pesan.');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('danger', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            setLoadingState(false);
        }
    });

    // Set loading state
    function setLoadingState(isLoading) {
        if (isLoading) {
            submitBtn.disabled = true;
            loadingSpinner.classList.remove('d-none');
            submitText.textContent = 'Mengirim...';
        } else {
            submitBtn.disabled = false;
            loadingSpinner.classList.add('d-none');
            submitText.textContent = 'Kirim';
        }
    }

    // Show alert message
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="${type}:">
                    <use xlink:href="#${iconClass}"/>
                </svg>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        alertContainer.innerHTML = alertHtml;
        
        // Auto hide success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    }

    // Display validation errors
    function displayValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
                }
            }
        }
    }

    // Clear validation states
    function clearValidationStates() {
        alertContainer.innerHTML = '';
        
        const inputs = form.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = '';
            }
        });
    }

    // Real-time validation clearing
    const inputs = form.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = '';
                }
            }
        });
    });

    // Phone number formatting for Indonesian numbers
    const phoneInput = document.getElementById('telepon');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Format Indonesian phone numbers
        if (value.startsWith('8') && value.length > 1) {
            value = '62' + value;
        }
        
        if (value.startsWith('62')) {
            value = '+' + value;
        }
        
        e.target.value = value;
    });
});
</script>

<!-- SVG Icons untuk alerts (tambahkan di head atau sebelum closing body) -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
    <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>
@endpush