@extends('admin.layouts.app')

@section('title', 'Users Management - MyBDSMS Admin')

@section('page-title', 'Users Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Users</h4>
        <p class="text-muted mb-0">Manage registered users, review KYC documents, and control account status</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
        <i class="fas fa-user-plus me-2"></i>Add New User
    </button>
</div>

<!-- Filters & Search Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive / Pending</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>KYC Status</th>
                        <th>Account Status</th>
                        <th>Joined Date</th>
                        <th width="150" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr id="user-row-{{ $user->id }}">
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->profile_image)
                                        <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="avatar-circle object-fit-cover">
                                    @else
                                        <div class="avatar-circle">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary' }} px-2 py-0.5 small rounded-pill text-capitalize">
                                            {{ $user->role }}
                                        </span>
                                        @if(auth()->id() === $user->id)
                                            <span class="badge bg-info-subtle text-info small" style="font-size: 10px;">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-medium">{{ $user->email }}</div>
                                <div class="text-muted small">{{ $user->phone ?? 'N/A' }}</div>
                            </td>
                            <td id="user-kyc-container-{{ $user->id }}">
                                @if($user->kyc_status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> KYC Verified
                                    </span>
                                @elseif($user->kyc_status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Pending Review
                                    </span>
                                @elseif($user->kyc_status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill" title="{{ $user->rejection_reason }}">
                                        <i class="fas fa-times-circle me-1"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill">
                                        Not Submitted
                                    </span>
                                @endif

                                @if($user->nid_front || $user->nid_back || $user->trade_license)
                                    <button type="button" class="btn btn-sm btn-link text-primary p-0 d-block mt-1 small view-user-docs-btn"
                                            data-name="{{ $user->name }}"
                                            data-nid-front="{{ $user->nid_front ? asset($user->nid_front) : '' }}"
                                            data-nid-back="{{ $user->nid_back ? asset($user->nid_back) : '' }}"
                                            data-trade-license="{{ $user->trade_license ? asset($user->trade_license) : '' }}">
                                        <i class="fas fa-id-card me-1"></i> View KYC Docs
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-status-btn" type="checkbox" role="switch"
                                           data-id="{{ $user->id }}"
                                           {{ $user->status ? 'checked' : '' }}
                                           {{ auth()->id() === $user->id ? 'disabled title="You cannot change your own status"' : '' }}>
                                    <label class="form-check-label status-label small ms-1 {{ $user->status ? 'text-success' : 'text-danger' }}">
                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.invoices.index', ['user_id' => $user->id]) }}" class="btn btn-outline-info" title="User Invoices">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-primary edit-user-btn"
                                            data-id="{{ $user->id }}"
                                            title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                        <button type="button" class="btn btn-outline-danger delete-user-btn"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}"
                                                title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-users-slash fa-3x mb-3 text-secondary"></i>
                                    <p class="mb-0 fs-6">No users found.</p>
                                    @if(request()->hasAny(['search', 'role', 'status']))
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mt-2">Clear filters</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                <div class="text-muted small">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- View User KYC Documents Modal -->
<div class="modal fade" id="userDocsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDocsModalTitle">User KYC Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border p-3 text-center">
                            <h6 class="fw-bold mb-2">NID Front</h6>
                            <div id="modalNidFront" class="d-flex align-items-center justify-content-center" style="min-height: 200px;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border p-3 text-center">
                            <h6 class="fw-bold mb-2">NID Back</h6>
                            <div id="modalNidBack" class="d-flex align-items-center justify-content-center" style="min-height: 200px;"></div>
                        </div>
                    </div>
                    <div class="col-12" id="tradeLicenseContainer">
                        <div class="card border p-3 text-center">
                            <h6 class="fw-bold mb-2">Trade License</h6>
                            <div id="modalTradeLicense" class="d-flex align-items-center justify-content-center" style="min-height: 150px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create / Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="userForm">
            @csrf
            <input type="hidden" id="userId" name="userId">
            <input type="hidden" id="formMethod" value="POST">

            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="userName" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="userName" name="name" placeholder="John Doe" required>
                </div>

                <div class="mb-3">
                    <label for="userEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="userEmail" name="email" placeholder="user@example.com" required>
                </div>

                <div class="mb-3">
                    <label for="userPhone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="userPhone" name="phone" placeholder="01700000000">
                </div>

                <div class="mb-3">
                    <label for="userPassword" class="form-label" id="passwordLabel">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="userPassword" name="password" placeholder="Minimum 8 characters">
                    <small class="text-muted" id="passwordHelp" style="display: none;">Leave blank if you don't want to change the password.</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="userRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="userStatus" class="form-label">Account Status</label>
                        <select class="form-select" id="userStatus" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive / Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveUserBtn">
                    <i class="fas fa-save me-1"></i> Save User
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .avatar-circle {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        flex-shrink: 0;
    }
    .form-switch .form-check-input {
        cursor: pointer;
        width: 2.2em;
        height: 1.2em;
    }
    .form-switch .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
</style>
@endsection

@push('scripts')
<script>
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const userDocsModal = new bootstrap.Modal(document.getElementById('userDocsModal'));
    const userForm = document.getElementById('userForm');

    function openCreateModal() {
        userForm.reset();
        document.getElementById('userId').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('userModalTitle').textContent = 'Add New User';
        document.getElementById('passwordLabel').innerHTML = 'Password <span class="text-danger">*</span>';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordHelp').style.display = 'none';
    }

    // View User KYC Documents
    $(document).on('click', '.view-user-docs-btn', function() {
        const name = $(this).data('name');
        const front = $(this).data('nid-front');
        const back = $(this).data('nid-back');
        const trade = $(this).data('trade-license');

        document.getElementById('userDocsModalTitle').textContent = 'KYC Documents: ' + name;

        // Front
        if (front) {
            if (front.endsWith('.pdf')) {
                $('#modalNidFront').html(`<a href="${front}" target="_blank" class="btn btn-outline-danger"><i class="fas fa-file-pdf fa-2x d-block mb-1"></i>View PDF</a>`);
            } else {
                $('#modalNidFront').html(`<img src="${front}" class="img-fluid rounded border" style="max-height: 250px;">`);
            }
        } else {
            $('#modalNidFront').html('<span class="text-muted">Not provided</span>');
        }

        // Back
        if (back) {
            if (back.endsWith('.pdf')) {
                $('#modalNidBack').html(`<a href="${back}" target="_blank" class="btn btn-outline-danger"><i class="fas fa-file-pdf fa-2x d-block mb-1"></i>View PDF</a>`);
            } else {
                $('#modalNidBack').html(`<img src="${back}" class="img-fluid rounded border" style="max-height: 250px;">`);
            }
        } else {
            $('#modalNidBack').html('<span class="text-muted">Not provided</span>');
        }

        // Trade License
        if (trade) {
            $('#tradeLicenseContainer').show();
            if (trade.endsWith('.pdf')) {
                $('#modalTradeLicense').html(`<a href="${trade}" target="_blank" class="btn btn-outline-danger"><i class="fas fa-file-pdf fa-2x d-block mb-1"></i>View PDF</a>`);
            } else {
                $('#modalTradeLicense').html(`<img src="${trade}" class="img-fluid rounded border" style="max-height: 250px;">`);
            }
        } else {
            $('#tradeLicenseContainer').hide();
        }

        userDocsModal.show();
    });

    // Open Edit Modal & Fetch User
    $(document).on('click', '.edit-user-btn', function() {
        const id = $(this).data('id');

        $.get(`{{ url('admin/users') }}/${id}`, function(response) {
            if (response.success) {
                const user = response.user;
                document.getElementById('userId').value = user.id;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userPhone').value = user.phone || '';
                document.getElementById('userRole').value = user.role;
                document.getElementById('userStatus').value = user.status ? '1' : '0';

                document.getElementById('userPassword').value = '';
                document.getElementById('userPassword').required = false;
                document.getElementById('passwordLabel').innerHTML = 'Password <span class="text-muted small">(Optional)</span>';
                document.getElementById('passwordHelp').style.display = 'block';

                document.getElementById('userModalTitle').textContent = 'Edit User: ' + user.name;
                userModal.show();
            }
        }).fail(function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Failed to fetch user details');
        });
    });

    // Handle Form Submit (Create / Update)
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#userId').val();
        const method = $('#formMethod').val();
        const submitBtn = $('#saveUserBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        const url = method === 'POST' ? '{{ route('admin.users.store') }}' : `{{ url('admin/users') }}/${id}`;

        const data = {
            _token: '{{ csrf_token() }}',
            name: $('#userName').val(),
            email: $('#userEmail').val(),
            phone: $('#userPhone').val(),
            role: $('#userRole').val(),
            status: $('#userStatus').val(),
        };

        if ($('#userPassword').val()) {
            data.password = $('#userPassword').val();
        }

        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    userModal.hide();
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    toastr.error(response.message || 'Something went wrong');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error processing request');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save User');
            }
        });
    });

    // Toggle Status
    $(document).on('change', '.toggle-status-btn', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const label = checkbox.closest('.form-check').find('.status-label');

        $.ajax({
            url: `{{ url('admin/users') }}/${id}/toggle`,
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.status) {
                        label.text('Active').removeClass('text-danger').addClass('text-success');
                        checkbox.prop('checked', true);
                        if (response.kyc_status === 'approved') {
                            $(`#user-kyc-container-${id}`).find('.badge').first()
                                .removeClass('bg-warning-subtle text-warning border-warning-subtle bg-danger-subtle text-danger border-danger-subtle')
                                .addClass('bg-success-subtle text-success border-success-subtle')
                                .html('<i class="fas fa-check-circle me-1"></i> KYC Verified');
                        }
                    } else {
                        label.text('Inactive').removeClass('text-success').addClass('text-danger');
                        checkbox.prop('checked', false);
                    }
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to update user status');
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });

    // Delete User
    $(document).on('click', '.delete-user-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        if (confirm(`Are you sure you want to delete user "${name}"? This action cannot be undone.`)) {
            $.ajax({
                url: `{{ url('admin/users') }}/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $(`#user-row-${id}`).fadeOut(400, function() {
                            $(this).remove();
                        });
                    } else {
                        toastr.error(response.message || 'Failed to delete user');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting user');
                }
            });
        }
    });
</script>
@endpush
