@extends('user.layouts.app')

@section('title', 'Payment History - MyBDSMS')

@section('page-title', 'Payment History')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Payments & Transactions</h4>
            <p class="text-muted mb-0">List of all invoice payments and balance recharges</p>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>TrxID / Invoice</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Charge</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>
                            @php
                                $displayTrx = $payment->trx_id ?? $payment->payment_ref_id;
                            @endphp
                            @if($payment->order)
                                <div class="fw-bold text-dark">
                                    <span class="text-primary">#{{ $payment->order->order_number }}</span>
                                </div>
                            @endif

                            @if($displayTrx)
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-light text-secondary border font-monospace" title="{{ $displayTrx }}">
                                        <i class="fas fa-fingerprint me-1 text-muted"></i>
                                        {{ Str::limit($displayTrx, 16, '...') }}
                                    </span>
                                    <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1 copy-btn" data-clipboard-text="{{ $displayTrx }}" title="Copy TrxID">
                                        <i class="far fa-copy fa-xs"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1 text-uppercase fw-semibold">
                                <i class="fas fa-wallet me-1"></i> {{ $payment->gateway }}
                            </span>
                        </td>
                        <td>
                            <strong class="text-dark">৳{{ number_format($payment->amount, 2) }}</strong>
                        </td>
                        <td class="text-muted">
                            ৳{{ number_format($payment->charge, 2) }}
                        </td>
                        <td>
                            @if($payment->status === 'success')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Success</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Pending</span>
                            @elseif($payment->status === 'cancelled')
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">Cancelled</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : $payment->created_at->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">No payment records found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($payments->hasPages())
        <div class="mt-4">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.copy-btn', function() {
        var text = $(this).attr('data-clipboard-text');
        if (text) {
            navigator.clipboard.writeText(text).then(() => {
                var icon = $(this).find('i');
                icon.removeClass('far fa-copy').addClass('fas fa-check text-success');
                setTimeout(() => {
                    icon.removeClass('fas fa-check text-success').addClass('far fa-copy');
                }, 1500);
            });
        }
    });
</script>
@endpush

