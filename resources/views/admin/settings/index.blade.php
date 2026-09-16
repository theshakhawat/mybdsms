@extends('admin.layouts.app')

@section('title', 'Settings - MyBDSMS Admin')

@section('page-title', 'Site Settings')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="mb-1">Site Settings</h4>
        <p class="text-muted mb-0">Manage company information and contact details</p>
    </div>
</div>

<form id="settingsForm" class="row">
    @csrf

    <!-- Company Logo -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Company Logo</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Current Logo</label>
                    <div id="currentLogoContainer" class="text-center p-3 border rounded bg-light">
                        @if($settingsFlat['company_logo'] ?? false)
                            <img src="{{ $settingsFlat['company_logo'] }}" alt="Company Logo" class="img-fluid" style="max-height: 100px;">
                        @else
                            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Default Logo" class="img-fluid" style="max-height: 100px;">
                            <p class="text-muted small mb-0 mt-2">Default logo</p>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label for="logoUpload" class="form-label">Upload New Logo</label>
                    <input type="file" class="form-control" id="logoUpload" accept="image/*">
                    <input type="hidden" id="company_logo" name="company_logo" value="{{ $settingsFlat['company_logo'] ?? '' }}">
                    <small class="text-muted">Recommended: PNG or JPG, max 2MB</small>
                </div>

                <div id="logoPreview" class="mb-3" style="display: none;">
                    <label class="form-label">New Logo Preview</label>
                    <div class="text-center p-3 border rounded bg-light">
                        <img src="" alt="Logo Preview" class="img-fluid" style="max-height: 100px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="company_address" class="form-label">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>Company Address
                    </label>
                    <textarea class="form-control" id="company_address" name="company_address" rows="3" placeholder="Enter full company address">{{ $settingsFlat['company_address'] ?? '' }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_email" class="form-label">
                                <i class="fas fa-envelope text-primary me-1"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="company_email" name="company_email"
                                   placeholder="info@mybdsms.com"
                                   value="{{ $settingsFlat['company_email'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_phone" class="form-label">
                                <i class="fas fa-phone text-success me-1"></i>Phone Number
                            </label>
                            <input type="text" class="form-control" id="company_phone" name="company_phone"
                                   placeholder="+88 09611-778371"
                                   value="{{ $settingsFlat['company_phone'] ?? '' }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="company_hours" class="form-label">
                        <i class="fas fa-clock text-warning me-1"></i>Working Hours
                    </label>
                    <input type="text" class="form-control" id="company_hours" name="company_hours"
                           placeholder="Sat - Thu: 9:00 AM - 6:00 PM"
                           value="{{ $settingsFlat['company_hours'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media Links -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Social Media Links</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="social_facebook" class="form-label">
                                <i class="fab fa-facebook text-primary me-1"></i>Facebook
                            </label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook"
                                   placeholder="https://facebook.com/mybdsms"
                                   value="{{ $settingsFlat['social_facebook'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="social_twitter" class="form-label">
                                <i class="fab fa-twitter text-info me-1"></i>Twitter
                            </label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter"
                                   placeholder="https://twitter.com/mybdsms"
                                   value="{{ $settingsFlat['social_twitter'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="social_linkedin" class="form-label">
                                <i class="fab fa-linkedin text-primary me-1"></i>LinkedIn
                            </label>
                            <input type="url" class="form-control" id="social_linkedin" name="social_linkedin"
                                   placeholder="https://linkedin.com/company/mybdsms"
                                   value="{{ $settingsFlat['social_linkedin'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="social_youtube" class="form-label">
                                <i class="fab fa-youtube text-danger me-1"></i>YouTube
                            </label>
                            <input type="url" class="form-control" id="social_youtube" name="social_youtube"
                                   placeholder="https://youtube.com/@mybdsms"
                                   value="{{ $settingsFlat['social_youtube'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel URLs -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-link me-2"></i>Panel URLs</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="account_panel_url" class="form-label">
                                <i class="fas fa-user-circle text-primary me-1"></i>Account Panel URL
                            </label>
                            <input type="url" class="form-control" id="account_panel_url" name="account_panel_url"
                                   placeholder="/account/dashboard"
                                   value="{{ $settingsFlat['account_panel_url'] ?? '/account/dashboard' }}">
                            <small class="text-muted">The URL for the Account Panel button in header</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sms_panel_url" class="form-label">
                                <i class="fas fa-sms text-success me-1"></i>SMS Panel URL
                            </label>
                            <input type="url" class="form-control" id="sms_panel_url" name="sms_panel_url"
                                   placeholder="/sms/dashboard"
                                   value="{{ $settingsFlat['sms_panel_url'] ?? '/sms/dashboard' }}">
                            <small class="text-muted">The URL for the SMS Panel button in header</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank & Manual Payment Settings -->
    <div class="col-lg-12 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="fas fa-university me-2 text-warning"></i>Bank & Manual Payment Settings</h5>
                <small class="text-light">ইউজার প্যাকেজ কেনার সময় এই তথ্যগুলো দেখতে পাবে</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label fw-semibold">Bank Name (ব্যাংকের নাম)</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name"
                                   placeholder="Dutch-Bangla Bank PLC"
                                   value="{{ $settingsFlat['bank_name'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_account_name" class="form-label fw-semibold">Account Name (হিসাবের নাম)</label>
                            <input type="text" class="form-control" id="bank_account_name" name="bank_account_name"
                                   placeholder="MyBDSMS Limited"
                                   value="{{ $settingsFlat['bank_account_name'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_account_number" class="form-label fw-semibold">Account Number (হিসাব নম্বর)</label>
                            <input type="text" class="form-control" id="bank_account_number" name="bank_account_number"
                                   placeholder="123.120.456789"
                                   value="{{ $settingsFlat['bank_account_number'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_branch" class="form-label fw-semibold">Branch Name (শাখা)</label>
                            <input type="text" class="form-control" id="bank_branch" name="bank_branch"
                                   placeholder="Motijheel Branch, Dhaka"
                                   value="{{ $settingsFlat['bank_branch'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_routing_number" class="form-label fw-semibold">Routing Number (রাউটিং নম্বর)</label>
                            <input type="text" class="form-control" id="bank_routing_number" name="bank_routing_number"
                                   placeholder="090271234"
                                   value="{{ $settingsFlat['bank_routing_number'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_instructions" class="form-label fw-semibold">Bank Transfer Instructions (ব্যাংক ট্রান্সফার নির্দেশাবলী)</label>
                            <textarea class="form-control" id="bank_instructions" name="bank_instructions" rows="3"
                                      placeholder="ব্যাংক অ্যাকাউন্টে টাকা জমা/ট্রান্সফার করে নিচের বক্সে আপনার তথ্য প্রদান করুন...">{{ $settingsFlat['bank_instructions'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="office_payment_instructions" class="form-label fw-semibold">Office Cash Instructions (অফিস ক্যাশ নির্দেশাবলী)</label>
                            <textarea class="form-control" id="office_payment_instructions" name="office_payment_instructions" rows="3"
                                      placeholder="আমাদের অফিসে এসে সরাসরি ক্যাশ কাউন্টারে টাকা পরিশোধ করুন...">{{ $settingsFlat['office_payment_instructions'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="col-12">
        <div class="card">
            <div class="card-body text-end">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Save All Settings
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Logo upload with preview
    $('#logoUpload').on('change', function() {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').show();
            $('#logoPreview img').attr('src', e.target.result);
            // Hide current logo when previewing new one
            $('#currentLogoContainer').hide();
        };
        reader.readAsDataURL(file);

        // Upload to server
        $.ajax({
            url: '{{ route('admin.settings.upload-image') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#company_logo').val(response.path);
                toastr.success(response.message);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Upload failed';
                toastr.error(error);
                $('#logoPreview').hide();
                $('#currentLogoContainer').show();
                $('#logoUpload').val('');
            }
        });
    });

    // Settings form submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: '{{ route('admin.settings.update') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success(response.message);
                submitBtn.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection
