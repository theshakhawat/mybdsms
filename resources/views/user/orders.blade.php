@extends('user.layouts.app')

@section('title', 'My Orders - MyBDSMS')

@section('page-title', 'Orders')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Order History</h4>
            <p class="text-muted mb-0">Track and view your SMS package purchase orders</p>
        </div>
        <a href="{{ route('user.buy_package') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-cart-plus me-1"></i> New Order
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Package</th>
                    <th>SMS Credits</th>
                    <th>Price</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong class="text-dark">#{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $order->package_name }}</div>
                            @if($order->plan_type)
                                <small class="text-muted text-capitalize">{{ $order->plan_type }} SMS</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ number_format($order->sms_count) }} SMS</span>
                        </td>
                        <td>
                            <strong>৳{{ number_format($order->amount, 2) }}</strong>
                            <div class="text-muted small">@ ৳{{ $order->price_per_sms }}/SMS</div>
                        </td>
                        <td>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1 text-uppercase fw-semibold">
                                <i class="fas fa-wallet me-1"></i> {{ $order->payment_method }}
                            </span>
                        </td>
                        <td>
                            @if($order->status === 'completed')
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">Completed</span>
                            @elseif($order->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">Pending</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">Cancelled</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ $order->created_at->format('M d, Y') }}<br>
                            {{ $order->created_at->format('h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-shopping-bag fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">No orders found yet.</p>
                            <a href="{{ route('user.buy_package') }}" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-3">Buy your first package</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection

