@extends('admin.layouts.app')

@section('title', 'Dashboard - MyBDSMS Admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_users'] ?? 0 }}</h3>
                <p>Total Users</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-sms"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_sms'] ?? 0 }}</h3>
                <p>SMS Sent Today</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <a href="{{ route('admin.verifications.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="text-dark">{{ $stats['pending_verifications'] ?? 0 }}</h3>
                    <p class="text-muted">Pending Verifications</p>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['pending_contacts'] ?? 0 }}</h3>
                <p>Pending Contacts</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Contact Messages -->
    <div class="col-lg-7">
        <div class="content-card">
            <div class="card-header">
                <h4><i class="fas fa-envelope me-2"></i>Recent Contact Messages</h4>
            </div>
            <div class="card-body p-0">
                @if(isset($recentContacts) && $recentContacts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentContacts as $contact)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2">
                                                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $contact->name }}</div>
                                                    <small class="text-muted">{{ $contact->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $contact->service ?? 'N/A' }}</span></td>
                                        <td><small>{{ $contact->created_at->diffForHumans() }}</small></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-envelope-open"></i>
                        <p>No contact messages yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-lg-5">
        <div class="content-card mb-4">
            <div class="card-header">
                <h4><i class="fas fa-chart-pie me-2"></i>Content Overview</h4>
            </div>
            <div class="card-body">
                <div class="content-stats">
                    <div class="content-stat-item">
                        <div class="stat-icon-small bg-primary">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div class="stat-text">
                            <h4>{{ $stats['banners'] ?? 0 }}</h4>
                            <p>Active Banners</p>
                        </div>
                    </div>
                    <div class="content-stat-item">
                        <div class="stat-icon-small bg-success">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="stat-text">
                            <h4>{{ $stats['services'] ?? 0 }}</h4>
                            <p>Services</p>
                        </div>
                    </div>
                    <div class="content-stat-item">
                        <div class="stat-icon-small bg-info">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-text">
                            <h4>{{ $stats['pricing_plans'] ?? 0 }}</h4>
                            <p>Pricing Plans</p>
                        </div>
                    </div>
                    <div class="content-stat-item">
                        <div class="stat-icon-small bg-warning">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <div class="stat-text">
                            <h4>{{ $stats['testimonials'] ?? 0 }}</h4>
                            <p>Testimonials</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="content-card">
            <div class="card-header">
                <h4><i class="fas fa-history me-2"></i>Recent Activity</h4>
            </div>
            <div class="card-body">
                @if(isset($recentActivity) && is_array($recentActivity) && count($recentActivity) > 0)
                    <div class="activity-list">
                        @foreach($recentActivity as $activity)
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas {{ $activity['icon'] ?? 'fa-circle' }}"></i>
                                </div>
                                <div class="activity-content">
                                    <p>{{ $activity['message'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] ?? 'Just now' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state mini">
                        <i class="fas fa-inbox"></i>
                        <p>No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary {
        background: linear-gradient(135deg, #34BD93 0%, #2db184 100%);
    }

    .stat-icon.bg-success {
        background: linear-gradient(135deg, #FAA03C 0%, #FDA537 100%);
    }

    .stat-icon.bg-info {
        background: linear-gradient(135deg, #43CC84 0%, #34BD93 100%);
    }

    .stat-icon.bg-warning {
        background: linear-gradient(135deg, #FDA537 0%, #ffb84d 100%);
    }

    .stat-content h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #1a1a2e;
    }

    .stat-content p {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
        font-weight: 500;
    }

    .content-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }

    .card-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }

    .card-header h4 {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #1a1a2e;
    }

    .card-body {
        padding: 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state.mini {
        padding: 40px 20px;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.4;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-bottom: 2px solid #f0f0f0;
        font-weight: 600;
        font-size: 13px;
        color: #6c757d;
        text-transform: uppercase;
        padding: 15px 20px;
    }

    .table tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f8f9fa;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #34BD93, #2db184);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    /* Content Stats */
    .content-stats {
        display: grid;
        gap: 15px;
    }

    .content-stat-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .content-stat-item:hover {
        background: #f0f0f0;
        transform: translateX(5px);
    }

    .stat-icon-small {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon-small.bg-primary {
        background: linear-gradient(135deg, #34BD93, #2db184);
    }

    .stat-icon-small.bg-success {
        background: linear-gradient(135deg, #FAA03C, #FDA537);
    }

    .stat-icon-small.bg-info {
        background: linear-gradient(135deg, #43CC84, #34BD93);
    }

    .stat-icon-small.bg-warning {
        background: linear-gradient(135deg, #FDA537, #ffb84d);
    }

    .stat-text h4 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 2px;
        color: #1a1a2e;
    }

    .stat-text p {
        font-size: 12px;
        color: #6c757d;
        margin: 0;
    }

    /* Activity List */
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.3s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: #f8f9fa;
    }

    .activity-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: linear-gradient(135deg, #34BD93, #2db184);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-content p {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #1a1a2e;
    }

    .activity-content small {
        font-size: 12px;
        color: #6c757d;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .btn-outline-primary {
        border-color: #34BD93;
        color: #34BD93;
    }

    .btn-outline-primary:hover {
        background: #34BD93;
        border-color: #34BD93;
        color: white;
    }
</style>
@endsection
