@extends('user.layouts.app')

@section('title', 'KYC Verification - MyBDSMS')

@section('page-title', 'KYC Verification')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Identity Verification (KYC)</h4>
                    <p class="text-muted mb-0">Government regulations require identity verification to activate SMS sending capabilities.</p>
                </div>
                <div>
                    @if($kyc && $kyc->status === 'approved')
                        <span class="badge bg-success px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-check-circle me-1"></i> KYC Approved
                        </span>
                    @elseif($kyc && $kyc->status === 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-clock me-1"></i> Verification Under Review
                        </span>
                    @elseif($kyc && $kyc->status === 'rejected')
                        <span class="badge bg-danger px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-times-circle me-1"></i> Verification Rejected
                        </span>
                    @else
                        <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-info-circle me-1"></i> Not Submitted
                        </span>
                    @endif
                </div>
            </div>

            @if($kyc && $kyc->status === 'rejected')
                <div class="alert alert-danger border-0 rounded-3 mt-3 mb-0">
                    <strong>Rejection Reason:</strong> {{ $kyc->rejection_reason ?? 'Your uploaded documents were not valid or clear. Please resubmit below.' }}
                </div>
            @endif
        </div>

        @if($kyc && $kyc->status === 'approved')
            <!-- Verified Success View -->
            <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
                <div class="mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-shield-alt fa-3x"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-2">Account Verified!</h3>
                <p class="text-muted max-w-md mx-auto mb-4">
                    Your KYC documents have been reviewed and approved by the administrator. Your account is fully active.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('user.buy_package') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-shopping-cart me-2"></i> Buy SMS Packages
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @else
            <!-- KYC Submission Form -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form id="userKycForm" enctype="multipart/form-data">
                    @csrf
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>
                        {{ $kyc ? 'Resubmit Verification Documents' : 'Submit Verification Documents' }}
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" placeholder="e.g. 01700000000" value="{{ $kyc->phone ?? '' }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">NID Front Side <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="nid_front" name="nid_front" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                            <img id="nid_front_preview" class="mt-2 rounded border p-1" style="max-width: 150px; display: none;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">NID Back Side <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="nid_back" name="nid_back" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                            <img id="nid_back_preview" class="mt-2 rounded border p-1" style="max-width: 150px; display: none;">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-medium">Trade License <span class="text-muted small">(Optional for Business Accounts)</span></label>
                            <input type="file" class="form-control" id="trade_license" name="trade_license" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted">JPG, PNG, PDF (Max 2MB)</small>
                            <img id="trade_license_preview" class="mt-2 rounded border p-1" style="max-width: 150px; display: none;">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="submitKycBtn">
                            <i class="fas fa-paper-plane me-2"></i> Submit for Review
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Image Preview
    ['nid_front', 'nid_back', 'trade_license'].forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('change', function(e) {
                const preview = document.getElementById(field + '_preview');
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            });
        }
    });

    // Form submit
    $('#userKycForm').on('submit', function(e) {
        e.preventDefault();
        const submitBtn = $('#submitKycBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');

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
                toastr.success('KYC documents submitted successfully! Admin will review shortly.');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                toastr.error(data.message || 'Something went wrong');
            }
        })
        .catch(err => {
            toastr.error('Error uploading documents. Please check file size and formats.');
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i> Submit for Review');
        });
    });
</script>
@endpush

