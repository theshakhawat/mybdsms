@extends('admin.layouts.app')

@section('title', 'About Section - MyBDSMS Admin')

@section('page-title', 'About Section Management')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="mb-1">About Section</h4>
        <p class="text-muted mb-0">Manage homepage about section content</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">About Section Content</h5>
            </div>
            <div class="card-body">
                <form id="aboutForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="badge" class="form-label">Section Badge</label>
                                <input type="text" class="form-control" id="badge" name="badge" value="{{ $about->badge ?? 'About Us' }}" placeholder="e.g., About Us">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ $about->title ?? '' }}" placeholder="e.g., Why Choose MyBDSMS?">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subtitle" class="form-label">Subtitle</label>
                        <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ $about->subtitle ?? '' }}" placeholder="e.g., A trusted SMS service provider...">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter detailed description">{{ $about->description ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Key Features</label>
                        <div id="featuresContainer">
                            @forelse(($about->features ?? []) as $index => $feature)
                                <div class="feature-row mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Enter feature">
                                        <button type="button" class="btn btn-outline-danger remove-feature" onclick="this.closest('.feature-row').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="feature-row mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                                        <button type="button" class="btn btn-outline-danger remove-feature" onclick="this.closest('.feature-row').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addFeatureBtn">
                            <i class="fas fa-plus me-1"></i>Add Feature
                        </button>
                        <small class="text-muted d-block mt-1">Add up to 5-6 key features/benefits</small>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Section Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Recommended: 600x500px, JPG/PNG, max 2MB</small>

                        @if($about->image)
                            <div id="existingImageContainer" class="mt-2">
                                <img src="{{ asset($about->image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        @endif
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').show();
                $('#imagePreview img').attr('src', e.target.result);
                // Hide existing image when new image is selected
                $('#existingImageContainer').hide();
            };
            reader.readAsDataURL(file);
        } else {
            // If no file selected, show existing image if editing
            $('#imagePreview').hide();
            $('#existingImageContainer').show();
        }
    });

    // Add feature button
    $('#addFeatureBtn').on('click', function() {
        const featureRow = `
            <div class="feature-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                    <button type="button" class="btn btn-outline-danger remove-feature" onclick="this.closest('.feature-row').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        $('#featuresContainer').append(featureRow);
    });

    // Form submission
    $('#aboutForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();

        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: '{{ route('admin.about.update') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message);
                btn.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection
