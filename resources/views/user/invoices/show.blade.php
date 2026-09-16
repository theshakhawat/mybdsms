@extends('user.layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number . ' - MyBDSMS')

@section('page-title', 'Invoice Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <!-- Action Bar (hidden when printing) -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4 d-print-none">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="{{ route('user.invoices.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back to Invoices
                </a>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">
                        <i class="fas fa-print me-1"></i> Print Invoice
                    </button>
                    @if($invoice->status === 'unpaid' && $invoice->payment_method === 'nagad')
                        <a href="{{ route('user.buy_package') }}?plan={{ $invoice->package_id }}" class="btn btn-danger rounded-pill px-4">
                            <i class="fas fa-wallet me-1"></i> Pay with Nagad
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 d-print-none" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Printable Invoice Container -->
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white invoice-box position-relative">
            <!-- Paid / Unpaid Watermark Stamp -->
            <div class="invoice-stamp {{ $invoice->status === 'paid' ? 'stamp-paid' : 'stamp-unpaid' }}">
                {{ strtoupper($invoice->status) }}
            </div>

            <!-- Header -->
            <div class="row align-items-start border-bottom pb-4 mb-4">
                <div class="col-sm-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS" style="height: 48px;" class="rounded-3">
                        <span class="fs-3 fw-bold text-dark" style="letter-spacing: -0.5px;">MyBD<span style="color: #34BD93;">SMS</span></span>
                    </div>
                    <p class="text-muted small mb-0">
                        {{ $siteSettings->company_address ?? 'Dhaka, Bangladesh' }}<br>
                        Email: {{ $siteSettings->company_email ?? 'support@mybdsms.com' }} | Phone: {{ $siteSettings->company_phone ?? '+880 1700-000000' }}
                    </p>
                </div>
                <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                    <h3 class="fw-bold text-dark mb-1">INVOICE</h3>
                    <div class="fs-5 font-monospace text-primary fw-bold mb-1">#{{ $invoice->invoice_number }}</div>
                    <div class="text-muted small">
                        <strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}<br>
                        @if($invoice->due_date)
                            <strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}<br>
                        @endif
                        @if($invoice->status === 'paid' && $invoice->paid_at)
                            <strong class="text-success">Paid Date:</strong> {{ $invoice->paid_at->format('M d, Y h:i A') }}<br>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Billed To / Payment Method Info -->
            <div class="row mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Billed To:</h6>
                    <h5 class="fw-bold text-dark mb-1">{{ $invoice->user->name ?? 'Valued Customer' }}</h5>
                    <div class="text-muted small">
                        <div><i class="fas fa-envelope me-1 text-muted"></i> {{ $invoice->user->email ?? 'N/A' }}</div>
                        @if(!empty($invoice->user->phone))
                            <div><i class="fas fa-phone me-1 text-muted"></i> {{ $invoice->user->phone }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="text-muted text-uppercase small fw-bold mb-2">Payment Details:</h6>
                    <div class="small text-muted">
                        <div><strong>Payment Method:</strong> 
                            @if($invoice->payment_method === 'bank')
                                Bank Transfer
                            @elseif($invoice->payment_method === 'office_cash')
                                Office Cash
                            @else
                                {{ ucfirst($invoice->payment_method) }}
                            @endif
                        </div>
                        <div><strong>Payment Status:</strong> 
                            <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        </div>
                        @if($invoice->trx_id)
                            <div class="mt-1 font-monospace"><strong>TrxID / Ref:</strong> {{ $invoice->trx_id }}</div>
                        @endif
                        @if($invoice->sender_bank_name)
                            <div><strong>Sender Bank:</strong> {{ $invoice->sender_bank_name }}</div>
                        @endif
                        @if($invoice->sender_account)
                            <div><strong>Sender Acc:</strong> {{ $invoice->sender_account }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-center" width="130">Plan Type</th>
                            <th class="text-center" width="130">SMS Credits</th>
                            <th class="text-end" width="130">Rate/SMS</th>
                            <th class="text-end" width="150">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $invoice->package_name }}</div>
                                <small class="text-muted">SMS Gateway Recharge</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border text-capitalize">
                                    {{ $invoice->plan_type ?? 'Standard' }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">
                                {{ number_format($invoice->sms_count) }} SMS
                            </td>
                            <td class="text-end font-monospace">
                                ৳{{ number_format($invoice->price_per_sms, 4) }}
                            </td>
                            <td class="text-end fw-bold fs-6">
                                ৳{{ number_format($invoice->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row justify-content-end mb-4">
                <div class="col-sm-6 col-md-5">
                    <div class="border rounded-3 p-3 bg-light small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong class="text-dark">৳{{ number_format($invoice->amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">VAT / Tax (0%):</span>
                            <strong class="text-dark">৳0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark">Total Amount:</span>
                            <strong class="text-dark fs-5">৳{{ number_format($invoice->amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Amount Paid:</span>
                            <strong class="text-success">৳{{ number_format($invoice->paid_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-dark">Balance Due:</span>
                            <strong class="fs-5 {{ ($invoice->amount - $invoice->paid_amount) > 0 ? 'text-danger' : 'text-success' }}">
                                ৳{{ number_format(max(0, $invoice->amount - $invoice->paid_amount), 2) }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions or Slip Preview -->
            @if($invoice->status === 'unpaid')
                <div class="border rounded-3 p-3 bg-light mb-4 small">
                    @if($invoice->payment_method === 'bank')
                        <h6 class="fw-bold text-primary mb-2"><i class="fas fa-university me-1"></i> Bank Payment Instructions:</h6>
                        <p class="mb-1">Please deposit/transfer the bill amount to our official bank account:</p>
                        <ul class="mb-2 ps-3">
                            <li><strong>Bank:</strong> {{ $siteSettings->bank_name ?? 'Dutch-Bangla Bank PLC' }}</li>
                            <li><strong>Account Name:</strong> {{ $siteSettings->bank_account_name ?? 'MyBDSMS Limited' }}</li>
                            <li><strong>Account Number:</strong> <span class="font-monospace fw-bold">{{ $siteSettings->bank_account_number ?? '123.120.456789' }}</span></li>
                            <li><strong>Branch:</strong> {{ $siteSettings->bank_branch ?? 'Motijheel Branch, Dhaka' }}</li>
                        </ul>
                        <p class="text-muted mb-0">Your package will be activated after payment confirmation by our team.</p>
                    @elseif($invoice->payment_method === 'office_cash')
                        <h6 class="fw-bold text-success mb-2"><i class="fas fa-building me-1"></i> Office Cash Instructions:</h6>
                        <p class="mb-1">Please visit our office during working hours to complete your cash payment:</p>
                        <p class="mb-0 text-muted"><strong>Address:</strong> {{ $siteSettings->company_address ?? 'Dhaka, Bangladesh' }} | <strong>Hours:</strong> {{ $siteSettings->company_hours ?? 'Sat - Thu, 9 AM - 6 PM' }}</p>
                    @endif
                </div>
            @endif

            @if($invoice->slip_image)
                <div class="border rounded-3 p-3 bg-light mb-4 small">
                    <h6 class="fw-bold text-dark mb-2"><i class="fas fa-paperclip me-1"></i> Submitted Payment Receipt / Slip:</h6>
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ asset($invoice->slip_image) }}" target="_blank">
                            <img src="{{ asset($invoice->slip_image) }}" alt="Payment Slip" class="rounded border shadow-sm" style="max-height: 120px; max-width: 200px; object-fit: contain; background: #fff;">
                        </a>
                        <div>
                            <a href="{{ asset($invoice->slip_image) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-external-link-alt me-1"></i> View Full Slip
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if($invoice->customer_notes || $invoice->admin_notes)
                <div class="border-top pt-3 text-muted small">
                    @if($invoice->customer_notes)
                        <div><strong>Customer Note:</strong> {{ $invoice->customer_notes }}</div>
                    @endif
                    @if($invoice->admin_notes)
                        <div><strong>Admin Note:</strong> {{ $invoice->admin_notes }}</div>
                    @endif
                </div>
            @endif

            <!-- Footer -->
            <div class="border-top pt-3 mt-4 text-center text-muted small">
                <p class="mb-0">Thank you for choosing <strong>MyBDSMS</strong>. For questions regarding this invoice, contact us at {{ $siteSettings->company_email ?? 'support@mybdsms.com' }}.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .invoice-stamp {
        position: absolute;
        top: 25px;
        right: 25px;
        font-size: 2.2rem;
        font-weight: 900;
        letter-spacing: 4px;
        padding: 6px 18px;
        border: 4px solid;
        border-radius: 12px;
        transform: rotate(-12deg);
        opacity: 0.22;
        pointer-events: none;
        user-select: none;
        z-index: 1;
    }
    .stamp-paid {
        color: #198754;
        border-color: #198754;
    }
    .stamp-unpaid {
        color: #dc3545;
        border-color: #dc3545;
    }

    @media print {
        body {
            background: white !important;
            font-size: 12px;
        }
        .sidebar, .top-header, .d-print-none {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .invoice-box {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            padding: 20px !important;
        }
    }
</style>
@endsection

