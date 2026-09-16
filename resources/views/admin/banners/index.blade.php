@extends('admin.layouts.app')

@section('title', 'Banners - MyBDSMS Admin')

@section('page-title', 'Banner Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Banner Management</h4>
        <p class="text-muted mb-0">Manage homepage banners and slides</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" id="addBannerBtn">
        <i class="fas fa-plus me-2"></i>Add New Banner
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="80">Image</th>
                        <th>Title</th>
                        <th>Badge</th>
                        <th>Button</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody id="bannersTable">
                    @forelse($banners as $banner)
                        <tr data-id="{{ $banner->id }}" data-sort="{{ $banner->sort_order }}">
                            <td>
                                @if($banner->image)
                                    <img src="{{ asset($banner->image) }}" alt="Banner" class="img-thumbnail" style="max-width: 80px; max-height: 60px;">
                                @else
                                    <span class="text-muted small">No image</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $banner->title ?? '-' }}</strong>
                                @if($banner->subtitle)
                                    <br><small class="text-muted">{{ Str::limit($banner->subtitle, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($banner->badge)
                                    <span class="badge bg-primary">{{ $banner->badge }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($banner->button_text)
                                    <span class="badge bg-info">{{ $banner->button_text }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $banner->sort_order }}</span>
                            </td>
                            <td>
                                @if($banner->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $banner->id }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success toggle-btn"
                                            data-id="{{ $banner->id }}"
                                            title="Toggle Status">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $banner->id }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No banners yet. Click "Add New Banner" to create one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bannerModalTitle">Add New Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bannerForm">
                    @csrf
                    <input type="hidden" id="banner_id" name="banner_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Connect with Customers Through |Powerful SMS|">
                                <small class="text-muted">Use |text| to highlight with yellow color. Or use HTML: &lt;span&gt;text&lt;/span&gt;</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="badge" class="form-label">Badge Text</label>
                                <input type="text" class="form-control" id="badge" name="badge" placeholder="e.g., Bangladesh's Trusted SMS Partner">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subtitle" class="form-label">Subtitle</label>
                        <textarea class="form-control" id="subtitle" name="subtitle" rows="3" placeholder="Enter banner subtitle/description"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_text" class="form-label">Button Text</label>
                                <input type="text" class="form-control" id="button_text" name="button_text" placeholder="e.g., Get Started">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="button_link" class="form-label">Button Link</label>
                                <input type="text" class="form-control" id="button_link" name="button_link" placeholder="e.g., #pricing">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" @if(isset($banner)) @else required @endif>
                        <small class="text-muted">Recommended: 1920x1080px, JPG/PNG, max 2MB</small>

                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                        <div id="existingImageContainer" class="mt-2" style="display: none;">
                            <img id="existingImage" src="" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBannerBtn">
                    <i class="fas fa-save me-2"></i>Save Banner
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let bannerModal;

    bannerModal = new bootstrap.Modal(document.getElementById('bannerModal'));

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
            if ($('#banner_id').val()) {
                $('#imagePreview').hide();
                $('#existingImageContainer').show();
            }
        }
    });

    // Add banner button
    $('#addBannerBtn').on('click', function() {
        $('#bannerModalTitle').text('Add New Banner');
        $('#bannerForm')[0].reset();
        $('#banner_id').val('');
        $('#image').prop('required', true);
        $('#imagePreview').hide();
        $('#existingImageContainer').hide();
    });

    // Edit banner
    $('.edit-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '/admin/banners/' + id,
            type: 'GET',
            success: function(response) {
                $('#bannerModalTitle').text('Edit Banner');
                $('#banner_id').val(response.id);
                $('#title').val(response.title || '');
                $('#badge').val(response.badge || '');
                $('#subtitle').val(response.subtitle || '');
                $('#button_text').val(response.button_text || '');
                $('#button_link').val(response.button_link || '');
                $('#image').prop('required', false);

                // Reset both image containers first
                $('#imagePreview').hide();
                $('#existingImageContainer').hide();
                $('#imagePreview img').attr('src', '');
                $('#existingImage').attr('src', '');

                // Clear file input
                $('#image').val('');

                // Show existing image if available
                if (response.image) {
                    $('#existingImage').attr('src', '/' + response.image);
                    $('#existingImageContainer').show();
                }

                bannerModal.show();
            },
            error: function() {
                toastr.error('Failed to load banner');
            }
        });
    });

    // Save banner
    $('#saveBannerBtn').on('click', function() {
        const form = $('#bannerForm')[0];
        const formData = new FormData(form);
        const btn = $(this);
        const id = $('#banner_id').val();
        const originalText = btn.html();

        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: id ? '/admin/banners/' + id : '/admin/banners',
            type: id ? 'POST' : 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-HTTP-Method-Override': id ? 'PUT' : 'POST'
            },
            success: function(response) {
                toastr.success(response.message);
                bannerModal.hide();
                location.reload();
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Toggle status
    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '/admin/banners/' + id + '/toggle',
            type: 'PATCH',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                toastr.success(response.message);
                location.reload();
            },
            error: function() {
                toastr.error('An error occurred');
            }
        });
    });

    // Delete banner
    $('.delete-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        if (confirm('Are you sure you want to delete this banner?')) {
            $.ajax({
                url: '/admin/banners/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    toastr.success(response.message);
                    btn.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function() {
                    toastr.error('An error occurred');
                }
            });
        }
    });

    // Make table rows sortable
    $('tbody').sortable({
        handle: 'tr',
        axis: 'y',
        update: function(event, ui) {
            const items = [];
            $('tbody tr').each(function(index) {
                items.push({
                    id: $(this).data('id'),
                    sort_order: index
                });
            });

            $.ajax({
                url: '/admin/banners/reorder',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    items: items
                },
                success: function(response) {
                    // Update order badges
                    $('tbody tr').each(function(index) {
                        $(this).find('td:nth-child(5) .badge').text(index);
                    });
                }
            });
        }
    });
});
</script>
@endpush
@endsection
