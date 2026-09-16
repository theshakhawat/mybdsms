@extends('user.layouts.app')

@section('title', 'My Invoices - MyBDSMS')

@section('page-title', 'Invoices')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Billing & Invoices</h4>
            <p class="text-muted mb-0">View all your purchase bills, payment status and receipts</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.buy_package') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-cart-plus me-1"></i> New Package
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <span class="text-muted small fw-semibold me-2">Filter by Status:</span>
        <a href="{{ route('user.invoices.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-3">
            All ({{ \App\Models\Invoice::where('user_id', Auth::id())->count() }})
        </a>
        <a href="{{ route('user.invoices.index', ['status' => 'paid']) }}" class="btn btn-sm {{ request('status') === 'paid' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3">
            Paid ({{ \App\Models\Invoice::where('user_id', Auth::id())->where('status', 'paid')->count() }})
        </a>
        <a href="{{ route('user.invoices.index', ['status' => 'unpaid']) }}" class="btn btn-sm {{ request('status') === 'unpaid' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3">
            Unpaid ({{ \App\Models\Invoice::where('user_id', Auth::id())->where('status', 'unpaid')->count() }})
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Invoice #</th>
                    <th>Package / Description</th>
                    <th>SMS Credits</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>
                            <strong class="text-primary font-monospace">#{{ $invoice->invoice_number }}</strong>
                            @if($invoice->created_by === 'admin')
                                <span class="badge bg-info-subtle text-info border border-info-subtle ms-1" style="font-size: 10px;">Admin Billed</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $invoice->package_name }}</div>
                            @if($invoice->plan_type)
                                <small class="text-muted text-capitalize">{{ $invoice->plan_type }} SMS</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ number_format($invoice->sms_count) }} SMS</span>
                        </td>
                        <td>
                            <strong class="text-dark fs-6">৳{{ number_format($invoice->amount, 2) }}</strong>
                        </td>
                        <td>
                            @if($invoice->payment_method === 'nagad')
                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1.5 text-uppercase fw-semibold">
                                    <i class="fas fa-wallet me-1"></i> Nagad
                                </span>
                            @elseif($invoice->payment_method === 'bank')
                                <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5 fw-semibold">
                                    <i class="fas fa-university me-1"></i> Bank Transfer
                                </span>
                            @elseif($invoice->payment_method === 'office_cash')
                                <span class="badge bg-success-subtle text-success px-2.5 py-1.5 fw-semibold">
                                    <i class="fas fa-money-bill-wave me-1"></i> Office Cash
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5 fw-semibold">
                                    {{ ucfirst($invoice->payment_method) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($invoice->status === 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Paid
                                </span>
                            @elseif($invoice->status === 'unpaid')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill">
                                    <i class="fas fa-clock me-1"></i> Unpaid
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $invoice->created_at->format('M d, Y') }}<br>
                            <span class="text-muted" style="font-size: 11px;">Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('user.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-eye me-1"></i> View / Print
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">No invoices found.</p>
                            <a href="{{ route('user.buy_package') }}" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-3">Purchase Package</a>
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
@endsection

