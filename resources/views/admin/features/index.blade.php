@extends('admin.layouts.app')

@section('title', 'Features - MyBDSMS Admin')

@section('page-title', 'Features Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Features</h4>
        <p class="text-muted mb-0">Manage features displayed on the website</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#featureModal">
        <i class="fas fa-plus me-2"></i>Add Feature
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="featuresTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($features as $index => $feature)
                        <tr data-id="{{ $feature->id }}" draggable="true">
                            <td>
                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                            </td>
                            <td>
                                <strong>{{ $feature->title }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($feature->description, 80) }}</small>
                            </td>
                            <td>
                                @if($feature->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $feature->id }}"
                                            data-title="{{ $feature->title }}"
                                            data-description="{{ $feature->description }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $feature->is_active ? 'warning' : 'success' }} toggle-btn"
                                            data-id="{{ $feature->id }}">
                                        <i class="fas fa-{{ $feature->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $feature->id }}"
                                            data-title="{{ $feature->title }}">
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
<div class="modal fade" id="featureModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="featureForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="featureModalTitle">Add Feature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="featureId" name="id">

                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
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

@push('scripts')
<script>
$(document).ready(function() {
    let featureModal;

    featureModal = new bootstrap.Modal(document.getElementById('featureModal'));

    $('#featureForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const featureId = $('#featureId').val();

        const url = featureId
            ? '{{ route('admin.features.update', ':id') }}'.replace(':id', featureId)
            : '{{ route('admin.features.store') }}';

        const method = featureId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                title: $('#title').val(),
                description: $('#description').val()
            },
            success: function(response) {
                toastr.success(response.message);
                featureModal.hide();
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
        $('#featureId').val(btn.data('id'));
        $('#title').val(btn.data('title'));
        $('#description').val(btn.data('description'));
        $('#featureModalTitle').text('Edit Feature');
        featureModal.show();
    });

    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.features.toggle', ':id') }}'.replace(':id', id),
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
        const title = btn.data('title');

        if (confirm(`Are you sure you want to delete "${title}"?`)) {
            $.ajax({
                url: '{{ route('admin.features.destroy', ':id') }}'.replace(':id', id),
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

    $('#featureModal').on('hidden.bs.modal', function() {
        $('#featureId').val('');
        $('#featureForm')[0].reset();
    });

    // Drag and drop reorder
    let draggedItem = null;

    $('#featuresTable tbody').on('dragstart', 'tr', function(e) {
        draggedItem = $(this);
        $(this).addClass('dragging');
    });

    $('#featuresTable tbody').on('dragend', 'tr', function() {
        $(this).removeClass('dragging');
        draggedItem = null;

        const items = [];
        $('#featuresTable tbody tr').each(function() {
            items.push($(this).data('id'));
        });

        $.ajax({
            url: '{{ route('admin.features.reorder') }}',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}', items: items},
            success: function(response) {
                toastr.success(response.message);
            }
        });
    });

    $('#featuresTable tbody').on('dragover', 'tr', function(e) {
        e.preventDefault();
        if (draggedItem && draggedItem !== $(this)) {
            const allRows = $('#featuresTable tbody tr');
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
