@extends('admin.layouts.app')

@section('title', 'Billing & Invoices - MyBDSMS Admin')

@section('page-title', 'Billing & Invoices')

@section('content')
<!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Invoices & User Billing</h4>
        <p class="text-muted mb-0">Manage customer billing, manual payments, and paid/unpaid invoice statuses</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
        <i class="fas fa-plus-circle me-2"></i>Create New Invoice
    </button>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Invoices</span>
                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-file-invoice fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalInvoices }}</h3>
            <span class="text-muted small">Generated to date</span>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Paid</span>
                <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-success">৳{{ number_format($totalPaidAmount, 2) }}</h3>
            <span class="text-muted small">Completed payments</span>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Total Unpaid</span>
                <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-warning">৳{{ number_format($totalUnpaidAmount, 2) }}</h3>
            <span class="text-muted small">Pending collection</span>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-semibold text-uppercase">Unpaid Bills</span>
                <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0 text-danger">{{ $unpaidCount }}</h3>
            <span class="text-muted small">Awaiting approval or payment</span>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-3 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search #Invoice, TrxID, User..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="method" class="form-select">
                    <option value="">All Methods</option>
                    <option value="nagad" {{ request('method') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                    <option value="bank" {{ request('method') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="office_cash" {{ request('method') === 'office_cash' ? 'selected' : '' }}>Office Cash</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'user_id', 'status', 'method']))
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>User</th>
                        <th>Package / Details</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status Control</th>
                        <th>Date & Due</th>
                        <th class="text-end" width="130">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr id="invoice-row-{{ $invoice->id }}">
                            <td>
                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    #{{ $invoice->invoice_number }}
                                </a>
                                @if($invoice->created_by === 'admin')
                                    <span class="badge bg-info-subtle text-info d-block mt-1" style="width: fit-content; font-size: 10px;">Admin Billed</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->user)
                                    <div class="fw-semibold text-dark">{{ $invoice->user->name }}</div>
                                    <small class="text-muted">{{ $invoice->user->email }}</small>
                                    @if($invoice->user->phone)
                                        <div class="text-muted" style="font-size: 11px;">{{ $invoice->user->phone }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">User Deleted</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $invoice->package_name }}</div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-light text-dark border">{{ number_format($invoice->sms_count) }} SMS</span>
                                    @if($invoice->plan_type)
                                        <span class="badge bg-secondary-subtle text-secondary text-capitalize">{{ $invoice->plan_type }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark fs-6">৳{{ number_format($invoice->amount, 2) }}</strong>
                                @if($invoice->price_per_sms > 0)
                                    <div class="text-muted small">@ ৳{{ $invoice->price_per_sms }}/SMS</div>
                                @endif
                            </td>
                            <td>
                                @if($invoice->payment_method === 'nagad')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 text-uppercase fw-semibold">
                                        <i class="fas fa-wallet me-1"></i> Nagad
                                    </span>
                                @elseif($invoice->payment_method === 'bank')
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1 fw-semibold">
                                        <i class="fas fa-university me-1"></i> Bank
                                    </span>
                                    @if($invoice->trx_id)
                                        <div class="small font-monospace text-muted mt-1" title="TrxID: {{ $invoice->trx_id }}">
                                            {{ Str::limit($invoice->trx_id, 12) }}
                                        </div>
                                    @endif
                                @elseif($invoice->payment_method === 'office_cash')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 fw-semibold">
                                        <i class="fas fa-money-bill-wave me-1"></i> Cash
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        {{ ucfirst($invoice->payment_method) }}
                                    </span>
                                @endif

                                @if($invoice->slip_image)
                                    <button type="button" class="btn btn-sm btn-link p-0 d-block small text-primary view-slip-btn mt-1" data-image="{{ asset($invoice->slip_image) }}" title="View Slip">
                                        <i class="fas fa-paperclip me-1"></i> Slip Preview
                                    </button>
                                @endif
                            </td>
                            <td>
                                <!-- Quick Status Switch Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle status-dropdown-btn px-2.5 py-1.5 rounded-pill fw-semibold {{ $invoice->status === 'paid' ? 'btn-success' : ($invoice->status === 'unpaid' ? 'btn-warning text-dark' : 'btn-secondary') }}" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false" 
                                            id="status-btn-{{ $invoice->id }}">
                                        @if($invoice->status === 'paid')
                                            <i class="fas fa-check-circle me-1"></i> Paid
                                        @elseif($invoice->status === 'unpaid')
                                            <i class="fas fa-clock me-1"></i> Unpaid
                                        @else
                                            <i class="fas fa-times-circle me-1"></i> Cancelled
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu shadow border-0 py-1">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 change-status-btn text-success {{ $invoice->status === 'paid' ? 'active disabled' : '' }}" 
                                               href="javascript:void(0)" 
                                               data-id="{{ $invoice->id }}" 
                                               data-status="paid">
                                                <i class="fas fa-check-circle text-success"></i> Mark as Paid
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 change-status-btn text-warning {{ $invoice->status === 'unpaid' ? 'active disabled' : '' }}" 
                                               href="javascript:void(0)" 
                                               data-id="{{ $invoice->id }}" 
                                               data-status="unpaid">
                                                <i class="fas fa-clock text-warning"></i> Mark as Unpaid
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 change-status-btn text-danger {{ $invoice->status === 'cancelled' ? 'active disabled' : '' }}" 
                                               href="javascript:void(0)" 
                                               data-id="{{ $invoice->id }}" 
                                               data-status="cancelled">
                                                <i class="fas fa-ban text-danger"></i> Cancel Invoice
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td class="text-muted small">
                                <div>{{ $invoice->created_at->format('M d, Y') }}</div>
                                @if($invoice->due_date)
                                    <span class="text-muted" style="font-size: 11px;">Due: {{ $invoice->due_date->format('M d, Y') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-outline-primary" title="View / Print Invoice">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger delete-invoice-btn" data-id="{{ $invoice->id }}" data-num="{{ $invoice->invoice_number }}" title="Delete Invoice">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No invoices found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="mt-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="createInvoiceModalLabel">
                    <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Create New Invoice / Bill User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createInvoiceForm" action="{{ route('admin.invoices.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- User Selection -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select User / Customer <span class="text-danger">*</span></label>
                        <select name="user_id" id="modalUserId" class="form-select form-select-lg" required>
                            <option value="">-- Choose User --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }}) {{ $u->phone ? ' - ' . $u->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Billing Type Radio -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Billing Option <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="typePackage" value="package" checked>
                                <label class="form-check-label fw-semibold" for="typePackage">
                                    Standard SMS Package
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="billing_type" id="typeCustom" value="custom">
                                <label class="form-check-label fw-semibold" for="typeCustom">
                                    Custom Bill / Custom Amount
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Package Select Container -->
                    <div id="packageSelectContainer" class="mb-3">
                        <label class="form-label fw-semibold">Select SMS Package <span class="text-danger">*</span></label>
                        <select name="package_id" id="modalPackageId" class="form-select">
                            <option value="">-- Choose an SMS Plan --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}"
                                        data-price="{{ $plan->price }}"
                                        data-min-order="{{ $plan->min_order_amount ?? '500' }}"
                                        data-type="{{ $plan->plan_type }}">
                                    {{ $plan->name }} (৳{{ $plan->price }}/SMS, Min: ৳{{ $plan->min_order_amount ?? '500' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Fields Container -->
                    <div id="customFieldsContainer" class="d-none border rounded-4 p-3 bg-light mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Bill / Item Title <span class="text-danger">*</span></label>
                                <input type="text" name="custom_title" id="modalCustomTitle" class="form-control" placeholder="e.g. Bulk Non-masking SMS Package">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">SMS Type</label>
                                <select name="plan_type" class="form-select">
                                    <option value="non-masking">Non-masking</option>
                                    <option value="masking">Masking</option>
                                    <option value="custom">Custom Service</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Total SMS Quantity</label>
                                <input type="number" name="sms_count" id="modalSmsCount" class="form-control" placeholder="e.g. 10000" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Rate Per SMS (৳)</label>
                                <input type="number" step="0.0001" name="price_per_sms" id="modalPricePerSms" class="form-control" placeholder="e.g. 0.4500" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Amount & Due Date -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Total Bill Amount (৳) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" name="amount" id="modalAmount" class="form-control form-control-lg fw-bold" placeholder="500.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Due Date</label>
                            <input type="date" name="due_date" class="form-control form-control-lg" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                    </div>

                    <!-- Payment Method & Initial Status -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select">
                                <option value="bank">Bank Transfer</option>
                                <option value="office_cash">Office Cash</option>
                                <option value="nagad">Nagad</option>
                                <option value="other">Other / Manual</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Initial Payment Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select fw-semibold" id="modalStatusSelect">
                                <option value="unpaid" class="text-warning fw-bold">Unpaid (বকেয়া - ইউজার পেমেন্ট করবে)</option>
                                <option value="paid" class="text-success fw-bold">Paid (পরিশোধিত - সাথে সাথে SMS ব্যালেন্স যোগ হবে)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Admin Notes / Remarks (Optional)</label>
                        <textarea name="admin_notes" rows="2" class="form-control" placeholder="কোনো বিশেষ নোট বা রেফারেন্স থাকলে লিখুন..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="createInvoiceSubmitBtn" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Slip Image Modal -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Payment Slip / Receipt Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img src="" id="slipModalImg" class="img-fluid rounded" alt="Payment Slip" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle Billing Type
    $('input[name="billing_type"]').on('change', function() {
        if ($(this).val() === 'package') {
            $('#packageSelectContainer').removeClass('d-none');
            $('#customFieldsContainer').addClass('d-none');
            $('#modalPackageId').attr('required', 'required');
            $('#modalCustomTitle').removeAttr('required');
        } else {
            $('#packageSelectContainer').addClass('d-none');
            $('#customFieldsContainer').removeClass('d-none');
            $('#modalPackageId').removeAttr('required');
            $('#modalCustomTitle').attr('required', 'required');
        }
    });

    // Auto calculate amount when package selected
    $('#modalPackageId').on('change', function() {
        const option = $(this).find('option:selected');
        const minOrder = option.data('min-order') || 500;
        $('#modalAmount').val(parseFloat(minOrder).toFixed(2));
    });

    // Change Status via AJAX
    $(document).on('click', '.change-status-btn', function() {
        const invoiceId = $(this).data('id');
        const newStatus = $(this).data('status');
        const btn = $(this);

        if (!confirm('আপনি কি এই ইনভয়েসের স্ট্যাটাস "' + newStatus.toUpperCase() + '" করতে চান?')) {
            return;
        }

        $.ajax({
            url: "{{ url('admin/invoices') }}/" + invoiceId + "/status",
            type: "PATCH",
            data: {
                _token: "{{ csrf_token() }}",
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message || 'Status update failed.');
                }
            },
            error: function() {
                toastr.error('Error updating status.');
            }
        });
    });

    // Delete Invoice via AJAX
    $(document).on('click', '.delete-invoice-btn', function() {
        const invoiceId = $(this).data('id');
        const invoiceNum = $(this).data('num');

        if (!confirm('আপনি কি নিশ্চিত যে #' + invoiceNum + ' ইনভয়েসটি ডিলিট করতে চান?')) {
            return;
        }

        $.ajax({
            url: "{{ url('admin/invoices') }}/" + invoiceId,
            type: "DELETE",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#invoice-row-' + invoiceId).fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    toastr.error(response.message || 'Delete failed.');
                }
            },
            error: function() {
                toastr.error('Error deleting invoice.');
            }
        });
    });

    // View slip image modal
    $(document).on('click', '.view-slip-btn', function() {
        const src = $(this).data('image');
        $('#slipModalImg').attr('src', src);
        $('#slipModal').modal('show');
    });

    // Function to open create invoice modal with pre-selected user (from Users list)
    window.openBillUserModal = function(userId) {
        $('#modalUserId').val(userId);
        $('#createInvoiceModal').modal('show');
    };
</script>
@endpush

