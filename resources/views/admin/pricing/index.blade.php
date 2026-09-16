@extends('admin.layouts.app')

@section('title', 'Pricing - MyBDSMS Admin')

@section('page-title', 'Pricing Plans Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Pricing Plans</h4>
        <p class="text-muted mb-0">Manage pricing packages displayed on the website</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planModal">
        <i class="fas fa-plus me-2"></i>Add Plan
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="plansTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Icon</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Min Order</th>
                        <th>Type</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                        <tr data-id="{{ $plan->id }}" draggable="true">
                            <td>
                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                            </td>
                            <td>
                                @if($plan->icon)
                                    <div class="plan-icon-preview">
                                        <i class="{{ $plan->icon }}"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $plan->name }}</strong>
                                @if($plan->is_featured)
                                    <span class="badge bg-warning text-dark ms-1">Featured</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-success fw-bold">{{ $plan->price }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">৳{{ $plan->min_order_amount ?? '500' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $plan->plan_type }}</span>
                            </td>
                            <td>
                                @if($plan->is_featured)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-muted"></i>
                                @endif
                            </td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $plan->id }}"
                                            data-name="{{ $plan->name }}"
                                            data-price="{{ $plan->price }}"
                                            data-min_order_amount="{{ $plan->min_order_amount ?? '500' }}"
                                            data-description="{{ $plan->description }}"
                                            data-icon="{{ $plan->icon }}"
                                            data-plan_type="{{ $plan->plan_type }}"
                                            data-button_link="{{ $plan->button_link }}"
                                            data-features='@json($plan->features)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning featured-btn"
                                            data-id="{{ $plan->id }}"
                                            title="{{ $plan->is_featured ? 'Remove Featured' : 'Make Featured' }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $plan->is_active ? 'secondary' : 'success' }} toggle-btn"
                                            data-id="{{ $plan->id }}">
                                        <i class="fas fa-{{ $plan->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $plan->id }}"
                                            data-name="{{ $plan->name }}">
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
<div class="modal fade" id="planModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="planForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="planModalTitle">Add Pricing Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="planId" name="id">

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Starter">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price per SMS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" required placeholder="৳0.34">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="min_order_amount" class="form-label">Min Order Amount (৳) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="min_order_amount" name="min_order_amount" required placeholder="500">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="Perfect for small businesses">
                </div>

                <div class="mb-3">
                    <label for="button_link" class="form-label">Button Link</label>
                    <input type="url" class="form-control" id="button_link" name="button_link"
                        placeholder="https://example.com">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="icon" class="form-label">Font Awesome Icon</label>
                            <input type="text" class="form-control" id="icon" name="icon" placeholder="fas fa-sms">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="plan_type" class="form-label">Plan Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="plan_type" name="plan_type" required>
                                <option value="non-masking">Non-Masking</option>
                                <option value="masking">Masking</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Features <span class="text-danger">*</span></label>
                    <div id="featuresContainer">
                        <div class="feature-item input-group mb-2">
                            <input type="text" class="form-control feature-input" placeholder="Enter feature" required>
                            <button type="button" class="btn btn-outline-danger remove-feature">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addFeature">
                        <i class="fas fa-plus me-1"></i>Add Feature
                    </button>
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
    .plan-icon-preview {
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
    let planModal;
    let features = [];

    planModal = new bootstrap.Modal(document.getElementById('planModal'));

    // Add feature input
    $('#addFeature').on('click', function() {
        const html = `
            <div class="feature-item input-group mb-2">
                <input type="text" class="form-control feature-input" placeholder="Enter feature" required>
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        $('#featuresContainer').append(html);
    });

    // Remove feature input
    $(document).on('click', '.remove-feature', function() {
        if ($('#featuresContainer .feature-item').length > 1) {
            $(this).closest('.feature-item').remove();
        }
    });

    $('#planForm').on('submit', function(e) {
        e.preventDefault();

        // Collect features
        features = [];
        $('.feature-input').each(function() {
            if ($(this).val().trim()) {
                features.push($(this).val().trim());
            }
        });

        const planId = $('#planId').val();
        const url = planId
            ? '{{ route('admin.pricing.update', ':id') }}'.replace(':id', planId)
            : '{{ route('admin.pricing.store') }}';

        const method = planId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                name: $('#name').val(),
                price: $('#price').val(),
                min_order_amount: $('#min_order_amount').val(),
                description: $('#description').val(),
                 button_link: $('#button_link').val(),
                button_link: $('#button_link').val(),
                icon: $('#icon').val(),
                plan_type: $('#plan_type').val(),
                features: features
            },
            success: function(response) {
                toastr.success(response.message);
                planModal.hide();
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

        // Get features - jQuery auto-parses JSON, but we need to handle object vs array
        let featuresData = btn.data('features') || [];

        // Convert object to array if needed (in case DB stores as object with numeric keys)
        if (!Array.isArray(featuresData) && typeof featuresData === 'object') {
            featuresData = Object.values(featuresData);
        }

        console.log('Features data:', featuresData); // Debug log

        $('#planId').val(btn.data('id'));
        $('#name').val(btn.data('name'));
        $('#price').val(btn.data('price'));
        $('#min_order_amount').val(btn.data('min_order_amount') || '500');
        $('#description').val(btn.data('description'));
            $('#button_link').val(btn.data('button_link'));
        $('#button_link').val(btn.data('button_link'));
        $('#icon').val(btn.data('icon'));
        $('#plan_type').val(btn.data('plan_type'));

        // Load features
        $('#featuresContainer').empty();
        if (Array.isArray(featuresData) && featuresData.length > 0) {
            featuresData.forEach(function(feature) {
                const html = `
                    <div class="feature-item input-group mb-2">
                        <input type="text" class="form-control feature-input" value="${feature}" placeholder="Enter feature" required>
                        <button type="button" class="btn btn-outline-danger remove-feature">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                $('#featuresContainer').append(html);
            });
        } else {
            // Add one empty input if no features or not an array
            $('#addFeature').click();
        }

        $('#planModalTitle').text('Edit Pricing Plan');
        planModal.show();
    });

    $('.featured-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.pricing.featured', ':id') }}'.replace(':id', id),
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

    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.pricing.toggle', ':id') }}'.replace(':id', id),
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

        if (confirm(`Are you sure you want to delete "${name}"?`)) {
            $.ajax({
                url: '{{ route('admin.pricing.destroy', ':id') }}'.replace(':id', id),
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

    $('#planModal').on('hidden.bs.modal', function() {
        $('#planId').val('');
        $('#planForm')[0].reset();
        $('#featuresContainer').empty();
        $('#addFeature').click();
    });

    // Drag and drop reorder
    let draggedItem = null;

    $('#plansTable tbody').on('dragstart', 'tr', function(e) {
        draggedItem = $(this);
        $(this).addClass('dragging');
    });

    $('#plansTable tbody').on('dragend', 'tr', function() {
        $(this).removeClass('dragging');
        draggedItem = null;

        const items = [];
        $('#plansTable tbody tr').each(function() {
            items.push($(this).data('id'));
        });

        $.ajax({
            url: '{{ route('admin.pricing.reorder') }}',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}', items: items},
            success: function(response) {
                toastr.success(response.message);
            }
        });
    });

    $('#plansTable tbody').on('dragover', 'tr', function(e) {
        e.preventDefault();
        if (draggedItem && draggedItem !== $(this)) {
            const allRows = $('#plansTable tbody tr');
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
