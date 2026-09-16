@extends('user.layouts.app')

@section('title', 'My Account - MyBDSMS')

@section('page-title', 'My Account')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
            <div class="avatar-lg mx-auto mb-3 bg-gradient rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted small mb-3">{{ $user->email }}</p>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                {{ $user->status ? 'Active Account' : 'Suspended' }}
            </span>
            <div class="border-top mt-4 pt-3 text-start small text-muted">
                <div class="d-flex justify-content-between mb-2">
                    <span>Member Since:</span>
                    <strong>{{ $user->created_at->format('d M, Y') }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Role:</span>
                    <strong class="text-capitalize">{{ $user->role }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-4">Account Details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small">Full Name</label>
                    <div class="fw-semibold">{{ $user->name }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small">Email Address</label>
                    <div class="fw-semibold">{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small">Account Type</label>
                    <div class="fw-semibold text-capitalize">{{ $user->role }} User</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small">Account Status</label>
                    <div>
                        @if($user->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Suspended</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-top mt-4 pt-3">
                <a href="{{ route('user.profile') }}" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fas fa-edit me-1"></i> Edit Account Info
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

