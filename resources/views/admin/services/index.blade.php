@extends('admin.layouts.app')

@section('title', 'Services - MyBDSMS Admin')

@section('page-title', 'Services Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Services</h4>
        <p class="text-muted mb-0">Manage your services displayed on the website</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal">
        <i class="fas fa-plus me-2"></i>Add Service
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="servicesTable">
                <thead>
                    <tr>
                        <th width="80">Order</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr data-id="{{ $service->id }}">
                            <td>
                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                            </td>
                            <td>
                                <div class="service-icon-preview">
                                    <i class="{{ $service->icon }}"></i>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $service->title }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($service->description, 80) }}</small>
                            </td>
                            <td>
                                @if($service->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $service->id }}"
                                            data-title="{{ $service->title }}"
                                            data-icon="{{ $service->icon }}"
                                            data-description="{{ $service->description }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $service->is_active ? 'warning' : 'success' }} toggle-btn"
                                            data-id="{{ $service->id }}"
                                            title="{{ $service->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $service->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $service->id }}"
                                            data-title="{{ $service->title }}">
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
<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="serviceForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="serviceModalTitle">Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="serviceId" name="id">

                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Font Awesome Icon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="icon" name="icon" required placeholder="fas fa-sms">
                    <small class="text-muted">Example: fas fa-sms, fas fa-shield-alt</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
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

<style>
    .service-icon-preview {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .cursor-move {
        cursor: move;
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let serviceModal;
    let table;

    // Initialize modal
    serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));

    // Add/Edit service
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const serviceId = $('#serviceId').val();

        const url = serviceId
            ? '{{ route('admin.services.update', ':id') }}'.replace(':id', serviceId)
            : '{{ route('admin.services.store') }}';

        const method = serviceId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                title: $('#title').val(),
                icon: $('#icon').val(),
                description: $('#description').val()
            },
            success: function(response) {
                toastr.success(response.message);
                serviceModal.hide();
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

    // Edit button click
    $('.edit-btn').on('click', function() {
        const btn = $(this);
        $('#serviceId').val(btn.data('id'));
        $('#title').val(btn.data('title'));
        $('#icon').val(btn.data('icon'));
        $('#description').val(btn.data('description'));
        $('#serviceModalTitle').text('Edit Service');
        serviceModal.show();
    });

    // Toggle status
    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.services.toggle', ':id') }}'.replace(':id', id),
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

    // Delete service
    $('.delete-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const title = btn.data('title');

        if (confirm(`Are you sure you want to delete "${title}"?`)) {
            $.ajax({
                url: '{{ route('admin.services.destroy', ':id') }}'.replace(':id', id),
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

    // Reset modal on open
    $('#serviceModal').on('show.bs.modal', function() {
        if (!$('#serviceId').val()) {
            $('#serviceForm')[0].reset();
            $('#serviceModalTitle').text('Add Service');
        }
    });

    $('#serviceModal').on('hidden.bs.modal', function() {
        $('#serviceId').val('');
        $('#serviceForm')[0].reset();
    });

    // Reorder functionality
    let draggedItem = null;

    $('#servicesTable tbody').on('dragstart', 'tr', function(e) {
        draggedItem = $(this);
        $(this).addClass('dragging');
    });

    $('#servicesTable tbody').on('dragend', 'tr', function() {
        $(this).removeClass('dragging');
        draggedItem = null;

        // Get new order
        const items = [];
        $('#servicesTable tbody tr').each(function() {
            items.push($(this).data('id'));
        });

        // Save new order
        $.ajax({
            url: '{{ route('admin.services.reorder') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function() {
                toastr.error('Failed to reorder');
            }
        });
    });

    $('#servicesTable tbody').on('dragover', 'tr', function(e) {
        e.preventDefault();
        if (draggedItem && draggedItem !== $(this)) {
            const allRows = $('#servicesTable tbody tr');
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
