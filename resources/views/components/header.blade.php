{{-- Header / Navigation --}}
<header id="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#hero">
                <img src="{{ $settings->company_logo ?? asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo" class="brand-logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#hero">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>

                <!-- Verify Button (Mobile & Desktop) -->
                <button class="btn btn-verify me-2" data-bs-toggle="modal" data-bs-target="#verificationModal">
                    <i class="fas fa-shield-alt me-1"></i> Verify Account
                </button>

                <!-- Mobile: Direct Buttons -->
                <div class="d-flex flex-column gap-2 w-100 mt-4 d-lg-none">
                    <a href="{{ $settings->account_panel_url ?? '/account/dashboard' }}" class="navbar-btn-mobile w-100 text-center">
                        <i class="fas fa-user-circle"></i> Account Panel
                    </a>
                    <a href="{{ $settings->sms_panel_url ?? '/sms/dashboard' }}" class="navbar-btn-mobile w-100 text-center">
                        <i class="fas fa-sms"></i> SMS Panel
                    </a>
                </div>

                <!-- Desktop: User Dropdown -->
                <div class="dropdown d-none d-lg-flex">
                    <button class="user-icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                        <li>
                            <a class="dropdown-item user-dropdown-item" href="{{ $settings->account_panel_url ?? '/account/dashboard' }}">
                                <i class="fas fa-user-circle me-2"></i>Account Panel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item user-dropdown-item" href="{{ $settings->sms_panel_url ?? '/sms/dashboard' }}">
                                <i class="fas fa-sms me-2"></i>SMS Panel
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>

{{-- Verification Modal --}}
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">
                    <i class="fas fa-shield-alt me-2"></i>Verify Your Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="verificationForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="nid_front" class="form-label">NID Front <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="nid_front" name="nid_front" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                        <img id="nid_front_preview" class="mt-2 preview-image" style="max-width: 200px; display: none;">
                        <div id="nid_front_info" class="mt-1 file-info"></div>
                    </div>
                    <div class="mb-3">
                        <label for="nid_back" class="form-label">NID Back <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="nid_back" name="nid_back" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                        <img id="nid_back_preview" class="mt-2 preview-image" style="max-width: 200px; display: none;">
                        <div id="nid_back_info" class="mt-1 file-info"></div>
                    </div>
                    <div class="mb-3">
                        <label for="trade_license" class="form-label">Trade License (Optional)</label>
                        <input type="file" class="form-control" id="trade_license" name="trade_license" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                        <img id="trade_license_preview" class="mt-2 preview-image" style="max-width: 200px; display: none;">
                        <div id="trade_license_info" class="mt-1 file-info"></div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Your documents will be reviewed by our team. You'll receive an email once verification is complete.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="verificationForm" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Submit
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-verify {
        background: linear-gradient(135deg, #FAA03C 0%, #FDA537 100%);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 4px 15px rgba(250, 160, 60, 0.35);
    }
    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(250, 160, 60, 0.45);
        color: white;
        background: linear-gradient(135deg, #FDA537 0%, #ffb84d 100%);
    }
    .preview-image {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 5px;
    }
    .file-info {
        font-size: 13px;
        color: #6c757d;
    }
    .file-error {
        color: #dc3545;
        font-size: 13px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File validation and preview functionality
        ['nid_front', 'nid_back', 'trade_license'].forEach(field => {
            document.getElementById(field).addEventListener('change', function(e) {
                const preview = document.getElementById(field + '_preview');
                const fileInfo = document.getElementById(field + '_info');
                const file = e.target.files[0];

                // Reset previous
                preview.style.display = 'none';
                fileInfo.innerHTML = '';
                fileInfo.className = 'mt-1 file-info';

                if (file) {
                    // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        fileInfo.innerHTML = '<i class="fas fa-exclamation-circle"></i> File too large! Max 2MB allowed.';
                        fileInfo.className = 'mt-1 file-error';
                        this.value = '';
                        return;
                    }

                    // Show file info
                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    fileInfo.innerHTML = '<i class="fas fa-file"></i> ' + file.name + ' (' + sizeMB + ' MB)';

                    // Preview for images only
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        fileInfo.innerHTML += ' <i class="fas fa-file-pdf text-danger"></i>';
                    }
                }
            });
        });

        // Form submission
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

            const formData = new FormData(this);

            fetch('{{ route('verification.submit') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification request submitted successfully! We will review your documents.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('verificationModal'));
                    modal.hide();
                    this.reset();
                    document.querySelectorAll('.preview-image').forEach(img => img.style.display = 'none');
                    document.querySelectorAll('.file-info').forEach(div => div.innerHTML = '');
                } else {
                    alert(data.message || 'Something went wrong!');
                }
            })
            .catch(error => {
                alert('Error submitting form. Please try again.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit';
            });
        });
    });
</script>
