@extends('user.layouts.app')

@section('title', 'SMS Packages - MyBDSMS')

@section('page-title', 'SMS Packages')

@section('content')
<div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Available SMS Packages</h4>
            <p class="text-muted mb-0">Choose the perfect bulk SMS bundle for your business needs</p>
        </div>
        <a href="{{ route('user.buy_package') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-cart-plus me-1"></i> Buy Package
        </a>
    </div>
</div>

<div class="row g-4">
    @forelse($plans as $plan)
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white d-flex flex-column justify-content-between text-center position-relative overflow-hidden">
                @if($plan->is_featured)
                    <div class="position-absolute top-0 end-0 bg-warning text-dark fw-bold px-3 py-1 small rounded-bottom-start">
                        Popular
                    </div>
                @endif
                <div>
                    @if($plan->icon)
                        <div class="mb-3 text-primary">
                            <i class="{{ $plan->icon }} fa-3x"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold text-dark mb-2">{{ $plan->name }}</h5>
                    <h2 class="fw-bold text-primary mb-2">{{ $plan->price }} <span class="fs-6 text-muted">/ SMS</span></h2>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-light text-dark text-capitalize border">{{ $plan->plan_type }}</span>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                            Min Order: ৳{{ $plan->min_order_amount ?? '500' }}
                        </span>
                    </div>

                    @if($plan->description)
                        <p class="text-muted small mb-3">{{ $plan->description }}</p>
                    @endif

                    <ul class="list-unstyled text-start small text-muted mb-4 space-y-2">
                        @if($plan->features)
                            @foreach(is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? [] as $feature)
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>{{ $feature }}</li>
                            @endforeach
                        @else
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Instant Delivery</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Dynamic API Access</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Masking / Non-Masking</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Lifetime Validity</li>
                        @endif
                    </ul>
                </div>

                <a href="{{ route('user.buy_package', ['plan' => $plan->id]) }}" class="btn btn-outline-primary rounded-pill w-100 py-2">
                    <i class="fas fa-shopping-cart me-1"></i> Choose Package
                </a>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i>
            <p>No pricing plans currently available.</p>
        </div>
    @endforelse
</div>
@endsection

