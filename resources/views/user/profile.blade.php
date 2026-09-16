@extends('user.layouts.app')

@section('title', 'My Profile - MyBDSMS')

@section('page-title', 'Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h4 class="fw-bold mb-1">Edit Profile</h4>
            <p class="text-muted mb-0">Update your account information and password</p>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form method="POST" action="{{ route('user.profile.update') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-medium">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="01700000000">
                </div>

                <hr class="my-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-lock me-2 text-primary"></i> Change Password</h5>
                <p class="text-muted small mb-3">Leave these fields empty if you don't want to change your password.</p>

                <div class="mb-3">
                    <label class="form-label fw-medium">New Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Minimum 8 characters">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium">Confirm New Password</label>
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Repeat new password">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
