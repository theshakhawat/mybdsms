@extends('admin.layouts.app')

@section('title', 'Testimonials - MyBDSMS Admin')

@section('page-title', 'Testimonials Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Testimonials</h4>
        <p class="text-muted mb-0">Manage client testimonials displayed on the website</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testimonialModal">
        <i class="fas fa-plus me-2"></i>Add Testimonial
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="testimonialsTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Client</th>
                        <th>Company</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testimonial)
                        <tr data-id="{{ $testimonial->id }}" draggable="true">
                            <td>
                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($testimonial->client_image)
                                        <img src="{{ asset($testimonial->client_image) }}" alt="{{ $testimonial->client_name }}"
                                             class="rounded-circle me-2" width="40" height="40">
                                    @else
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($testimonial->client_name) }}&background=random&color=fff"
                                             alt="{{ $testimonial->client_name }}"
                                             class="rounded-circle me-2" width="40" height="40">
                                    @endif
                                    <div>
                                        <strong>{{ $testimonial->client_name }}</strong>
                                        @if($testimonial->client_position)
                                            <br><small class="text-muted">{{ $testimonial->client_position }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($testimonial->client_company)
                                    <small class="text-muted">{{ $testimonial->client_company }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($testimonial->message, 60) }}</small>
                            </td>
                            <td>
                                @if($testimonial->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $testimonial->id }}"
                                            data-client_name="{{ $testimonial->client_name }}"
                                            data-client_position="{{ $testimonial->client_position }}"
                                            data-client_company="{{ $testimonial->client_company }}"
                                            data-message="{{ $testimonial->message }}"
                                            data-client_image="{{ $testimonial->client_image }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $testimonial->is_active ? 'warning' : 'success' }} toggle-btn"
                                            data-id="{{ $testimonial->id }}">
                                        <i class="fas fa-{{ $testimonial->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $testimonial->id }}"
                                            data-name="{{ $testimonial->client_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="testimonialModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="testimonialForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="testimonialModalTitle">Add Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="testimonialId" name="id">

                <div class="mb-3">
                    <label for="client_name" class="form-label">Client Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="client_name" name="client_name" required>
                </div>

                <div class="mb-3">
                    <label for="client_position" class="form-label">Position</label>
                    <input type="text" class="form-control" id="client_position" name="client_position" placeholder="CEO, Marketing Director, etc.">
                </div>

                <div class="mb-3">
                    <label for="client_company" class="form-label">Company</label>
                    <input type="text" class="form-control" id="client_company" name="client_company" placeholder="Company Name">
                </div>

                <div class="mb-3">
                    <label for="client_image" class="form-label">Client Image</label>
                    <input type="file" class="form-control" id="client_image" name="client_image" accept="image/*">
                    <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                        <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" width="100" height="100">
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImage">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    <input type="hidden" id="existing_image" name="existing_image">
                    <small class="text-muted">Leave empty to use default avatar</small>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let testimonialModal;

    testimonialModal = new bootstrap.Modal(document.getElementById('testimonialModal'));

    $('#testimonialForm').on('submit', function(e) {
        e.preventDefault();

        const testimonialId = $('#testimonialId').val();
        const url = testimonialId
            ? '{{ route('admin.testimonials.update', ':id') }}'.replace(':id', testimonialId)
            : '{{ route('admin.testimonials.store') }}';

        const method = testimonialId ? 'PUT' : 'POST';

        const formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        // Add _method for PUT requests
        if (testimonialId) {
            formData.append('_method', 'PUT');
        }

        // If editing and no new image selected, keep existing image
        if (testimonialId && $('#client_image')[0].files.length === 0) {
            formData.delete('client_image');
        }

        $.ajax({
            url: url,
            type: 'POST', // Always use POST with FormData
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message);
                testimonialModal.hide();
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
            }
        });
    });

    $('.edit-btn').on('click', function() {
        const btn = $(this);
        $('#testimonialId').val(btn.data('id'));
        $('#client_name').val(btn.data('client_name'));
        $('#client_position').val(btn.data('client_position'));
        $('#client_company').val(btn.data('client_company'));
        $('#message').val(btn.data('message'));

        // Handle image preview
        const imagePath = btn.data('client_image');
        if (imagePath) {
            // Construct the full URL for the preview
            const imageUrl = window.location.origin + '/' + imagePath;
            $('#imagePreview').attr('src', imageUrl);
            $('#imagePreviewContainer').show();
            $('#existing_image').val(imagePath);
        } else {
            $('#imagePreviewContainer').hide();
            $('#existing_image').val('');
        }

        $('#testimonialModalTitle').text('Edit Testimonial');
        testimonialModal.show();
    });

    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.testimonials.toggle', ':id') }}'.replace(':id', id),
            type: 'PATCH',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                toastr.success(response.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function() {
                toastr.error('An error occurred');
            }
        });
    });

    $('.delete-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const name = btn.data('name');

        if (confirm(`Are you sure you want to delete testimonial from "${name}"?`)) {
            $.ajax({
                url: '{{ route('admin.testimonials.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function() {
                    toastr.error('An error occurred');
                }
            });
        }
    });

    // Image preview on file select
    $('#client_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewContainer').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove image button
    $('#removeImage').on('click', function() {
        $('#client_image').val('');
        $('#imagePreviewContainer').hide();
        $('#imagePreview').attr('src', '');
        $('#existing_image').val('');
    });

    $('#testimonialModal').on('hidden.bs.modal', function() {
        $('#testimonialId').val('');
        $('#testimonialForm')[0].reset();
        $('#imagePreviewContainer').hide();
        $('#imagePreview').attr('src', '');
        $('#existing_image').val('');
    });

    // Drag and drop reorder
    let draggedItem = null;

    $('#testimonialsTable tbody').on('dragstart', 'tr', function(e) {
        draggedItem = $(this);
        $(this).addClass('dragging');
    });

    $('#testimonialsTable tbody').on('dragend', 'tr', function() {
        $(this).removeClass('dragging');
        draggedItem = null;

        const items = [];
        $('#testimonialsTable tbody tr').each(function() {
            items.push($(this).data('id'));
        });

        $.ajax({
            url: '{{ route('admin.testimonials.reorder') }}',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}', items: items},
            success: function(response) {
                toastr.success(response.message);
            }
        });
    });

    $('#testimonialsTable tbody').on('dragover', 'tr', function(e) {
        e.preventDefault();
        if (draggedItem && draggedItem !== $(this)) {
            const allRows = $('#testimonialsTable tbody tr');
            const draggedIndex = allRows.index(draggedItem);
            const targetIndex = allRows.index($(this));

            if (draggedIndex < targetIndex) {
                $(this).after(draggedItem);
            } else {
                $(this).before(draggedItem);
            }
        }
    });
});
</script>
@endpush
@endsection
