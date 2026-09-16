@extends('user.layouts.app')

@section('title', 'User Dashboard - MyBDSMS')

@section('page-title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-4 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1">Welcome back, {{ $user->name }}! 👋</h4>
                        <p class="text-muted mb-0">Here is your account overview and SMS service summary.</p>
                    </div>
                </div>
                <div>
                    @if($kyc && $kyc->status === 'approved')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-shield-alt me-1"></i> KYC Verified
                        </span>
                    @elseif($kyc && $kyc->status === 'pending')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fs-6">
                            <i class="fas fa-clock me-1"></i> KYC Pending Review
                        </span>
                    @elseif($kyc && $kyc->status === 'rejected')
                        <a href="{{ route('user.kyc') }}" class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fs-6 text-decoration-none">
                            <i class="fas fa-exclamation-circle me-1"></i> KYC Rejected (Resubmit)
                        </a>
                    @else
                        <a href="{{ route('user.kyc') }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                            <i class="fas fa-shield-alt me-1"></i> Complete KYC
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notice Bar if KYC not approved -->
@if(!$kyc || $kyc->status !== 'approved')
    <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-info-circle fa-lg text-warning"></i>
            <div>
                <strong>Identity Verification Notice:</strong>
                @if(!$kyc)
                    Please complete your KYC verification with NID / Trade License so admin can review and approve your account for sending SMS.
                @elseif($kyc->status === 'pending')
                    Your KYC document verification request is under review by our admin team.
                @elseif($kyc->status === 'rejected')
                    Your KYC request was rejected. Reason: <em>{{ $kyc->rejection_reason ?? 'Document unreadable or invalid.' }}</em>
                @endif
            </div>
        </div>
        <a href="{{ route('user.kyc') }}" class="btn btn-sm btn-dark rounded-pill px-3">
            Go to KYC <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
@endif

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-semibold text-uppercase">SMS Balance</span>
                <div class="p-2.5 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-comment-sms fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ number_format($user->sms_balance ?? 0) }}</h3>
            <span class="text-muted small"><i class="fas fa-arrow-up text-success me-1"></i>Total SMS Remaining</span>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Orders</span>
                <div class="p-2.5 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-shopping-bag fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark">{{ $totalOrders ?? 0 }}</h3>
            <span class="text-muted small">Package purchases</span>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-semibold text-uppercase">Total Spent</span>
                <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="fas fa-receipt fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark">৳ {{ number_format($totalSpent ?? 0, 2) }}</h3>
            <span class="text-muted small">Paid in recharge</span>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-semibold text-uppercase">Account Status</span>
                <div class="p-2.5 rounded-3 bg-info bg-opacity-10 text-info">
                    <i class="fas fa-shield-alt fa-lg"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-success fs-4">{{ $user->status ? 'Active' : 'Suspended' }}</h3>
            <span class="text-muted small">Member since {{ $user->created_at->format('M Y') }}</span>
        </div>
    </div>
</div>

<!-- Quick Actions & Packages -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recommended Packages</h5>
                <a href="{{ route('user.packages') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
            </div>
            <div class="row g-3">
                @forelse($plans as $plan)
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 text-center h-100 d-flex flex-column justify-content-between hover-shadow transition">
                            <div>
                                @if($plan->icon)
                                    <div class="mb-2 text-primary">
                                        <i class="{{ $plan->icon }} fa-2x"></i>
                                    </div>
                                @endif
                                <h6 class="fw-bold text-dark mb-1">{{ $plan->name }}</h6>
                                <h4 class="fw-bold text-primary mb-2">{{ $plan->price }}</h4>
                                <span class="badge bg-light text-dark mb-3 text-capitalize">{{ $plan->plan_type }}</span>
                            </div>
                            <a href="{{ route('user.buy_package', ['plan' => $plan->id]) }}" class="btn btn-sm btn-primary rounded-pill w-100">
                                <i class="fas fa-shopping-cart me-1"></i> Choose Package
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        No packages available right now.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-3">Recent Orders</h5>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-box-open fa-2x mb-2 text-secondary"></i>
                <p class="mb-0">No purchase history found.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Shortcuts -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3">Quick Navigation</h5>
            <div class="d-grid gap-2">
                <a href="{{ route('user.buy_package') }}" class="btn btn-outline-primary rounded-3 text-start p-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-cart-plus me-2 text-primary"></i> Buy SMS Package</span>
                    <i class="fas fa-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('user.kyc') }}" class="btn btn-outline-warning rounded-3 text-start p-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-id-card me-2 text-warning"></i> Verification / KYC</span>
                    <i class="fas fa-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('user.payment_history') }}" class="btn btn-outline-success rounded-3 text-start p-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-history me-2 text-success"></i> Payment History</span>
                    <i class="fas fa-chevron-right small text-muted"></i>
                </a>
                <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary rounded-3 text-start p-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-user-edit me-2 text-secondary"></i> Edit Profile</span>
                    <i class="fas fa-chevron-right small text-muted"></i>
                </a>
            </div>
        </div>

        <!-- Support Info -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-gradient text-white" style="background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);">
            <h5 class="fw-bold mb-2">Need Assistance?</h5>
            <p class="small mb-3 text-white text-opacity-75">Our customer support team is available 24/7 to help you with your SMS gateway integration and account issues.</p>
            <a href="{{ route('home') }}#contact" class="btn btn-light rounded-pill btn-sm fw-bold px-3 align-self-start text-dark">
                <i class="fas fa-headset me-1"></i> Contact Support
            </a>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        border-color: var(--primary-color) !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
@endsection
