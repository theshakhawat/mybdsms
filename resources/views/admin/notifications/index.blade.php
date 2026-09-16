@extends('admin.layouts.app')

@section('title', 'Notifications - MyBDSMS Admin')

@section('page-title', 'Notifications')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">All Notifications</h4>
        <p class="text-muted mb-0">Track orders, user registrations, and system events</p>
    </div>
    @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.mark_all_read') }}">
            @csrf
            <button type="submit" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-check-double me-1"></i> Mark All as Read ({{ $unreadCount }})
            </button>
        </form>
    @endif
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
    <div class="d-flex gap-2 flex-wrap align-items-center justify-content-between">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm {{ !request('status') && !request('type') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-3">
                All
            </a>
            <a href="{{ route('admin.notifications.index', ['status' => 'unread']) }}" class="btn btn-sm {{ request('status') === 'unread' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
                Unread ({{ $unreadCount }})
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'order']) }}" class="btn btn-sm {{ request('type') === 'order' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('admin.notifications.index', ['type' => 'user_register']) }}" class="btn btn-sm {{ request('type') === 'user_register' ? 'btn-info' : 'btn-outline-info' }} rounded-pill px-3">
                <i class="fas fa-user-plus me-1"></i> Registrations
            </a>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <div class="list-group-item p-3 p-md-4 {{ !$notification->is_read ? 'bg-light bg-opacity-75' : '' }} d-flex align-items-start gap-3 transition" id="notification-{{ $notification->id }}">
                <div class="p-3 rounded-4 bg-white border shadow-sm flex-shrink-0">
                    <i class="{{ $notification->icon ?: 'fas fa-bell text-primary' }} fa-lg"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                        <h6 class="fw-bold mb-0 text-dark">
                            {{ $notification->title }}
                            @if(!$notification->is_read)
                                <span class="badge bg-danger ms-2" style="font-size: 9px; vertical-align: middle;">NEW</span>
                            @endif
                        </h6>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('M d, h:i A') }})</small>
                    </div>

                    <p class="text-secondary mb-2 small">{{ $notification->message }}</p>

                    <div class="d-flex align-items-center gap-2">
                        @if(!empty($notification->action_url))
                            <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1">
                                View Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @endif

                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1">
                                    <i class="fas fa-check me-1"></i> Mark as Read
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-sm btn-link text-danger delete-notif-btn ms-auto p-0" data-id="{{ $notification->id }}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0">No notifications found.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="p-3 border-top">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.delete-notif-btn', function() {
        const id = $(this).data('id');
        if (!confirm('Delete this notification?')) return;

        $.ajax({
            url: "{{ url('admin/notifications') }}/" + id,
            type: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                if (response.success) {
                    $('#notification-' + id).fadeOut(300, function() { $(this).remove(); });
                }
            }
        });
    });
</script>
@endpush

