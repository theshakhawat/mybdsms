@extends('admin.layouts.app')

@section('title', 'Messages - MyBDSMS Admin')

@section('page-title', 'Contact Messages')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Contact Messages</h4>
        <p class="text-muted mb-0">
            @if($unreadCount > 0)
                <span class="badge bg-danger">{{ $unreadCount }} unread</span>
            @else
                <span class="text-muted">All messages are read</span>
            @endif
        </p>
    </div>
    @if($unreadCount > 0)
        <button type="button" class="btn btn-outline-primary" id="markAllReadBtn">
            <i class="fas fa-envelope-open me-2"></i>Mark All Read
        </button>
    @endif
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr class="{{ !$message->is_read ? 'table-primary' : '' }}" data-id="{{ $message->id }}">
                            <td>
                                @if(!$message->is_read)
                                    <span class="badge bg-danger">New</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $message->name }}</strong>
                            </td>
                            <td>
                                <a href="mailto:{{ $message->email }}" class="text-decoration-none">
                                    {{ $message->email }}
                                </a>
                            </td>
                            <td>
                                @if($message->phone)
                                    <small>{{ $message->phone }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($message->service)
                                    <span class="badge bg-info">{{ ucfirst($message->service) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($message->message, 50) }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $message->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary view-btn"
                                            data-id="{{ $message->id }}"
                                            title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(!$message->is_read)
                                        <button type="button" class="btn btn-outline-success mark-read-btn"
                                                data-id="{{ $message->id }}"
                                                title="Mark as Read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $message->id }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No messages yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="text-muted small">From:</label>
                    <div>
                        <strong id="msgName"></strong>
                    </div>
                    <div id="msgEmail" class="text-muted"></div>
                    <div id="msgPhone" class="text-muted"></div>
                </div>

                <div class="mb-3 d-none" id="serviceContainer">
                    <label class="text-muted small">Interested Service:</label>
                    <span id="msgService" class="badge bg-info"></span>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Message:</label>
                    <div id="msgMessage" class="p-3 bg-light rounded"></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Received:</label>
                    <div id="msgDate" class="text-muted"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="replyEmail" class="btn btn-primary">
                    <i class="fas fa-reply me-2"></i>Reply via Email
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let messageModal;

    messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

    // View message
    $('.view-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.messages.show', ':id') }}'.replace(':id', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const msg = response.data;
                    $('#msgName').text(msg.name);
                    $('#msgEmail').text(msg.email);
                    $('#msgPhone').text(msg.phone || '');
                    if (msg.phone) {
                        $('#msgPhone').show();
                    } else {
                        $('#msgPhone').hide();
                    }
                    $('#msgService').text(msg.service ? msg.service.charAt(0).toUpperCase() + msg.service.slice(1) : '');
                    if (msg.service) {
                        $('#serviceContainer').removeClass('d-none');
                    } else {
                        $('#serviceContainer').addClass('d-none');
                    }
                    $('#msgMessage').text(msg.message);
                    $('#msgDate').text(new Date(msg.created_at).toLocaleString());
                    $('#replyEmail').attr('href', 'mailto:' + msg.email);
                    messageModal.show();

                    // Mark as read automatically
                    if (!msg.is_read) {
                        $.ajax({
                            url: '{{ route('admin.messages.read', ':id') }}'.replace(':id', id),
                            type: 'PATCH',
                            data: {_token: '{{ csrf_token() }}'},
                            success: function() {
                                // Update UI
                                btn.closest('tr').removeClass('table-primary');
                                btn.closest('tr').find('.badge.bg-danger').remove();
                                btn.siblings('.mark-read-btn').remove();
                                updateUnreadCount();
                            }
                        });
                    }
                }
            },
            error: function() {
                toastr.error('Failed to load message');
            }
        });
    });

    // Mark as read
    $('.mark-read-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.messages.read', ':id') }}'.replace(':id', id),
            type: 'PATCH',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                toastr.success(response.message);
                btn.closest('tr').removeClass('table-primary');
                btn.closest('tr').find('.badge.bg-danger').fadeOut();
                btn.remove();
                updateUnreadCount();
            },
            error: function() {
                toastr.error('An error occurred');
            }
        });
    });

    // Mark all as read
    $('#markAllReadBtn').on('click', function() {
        if (confirm('Are you sure you want to mark all messages as read?')) {
            $.ajax({
                url: '{{ route('admin.messages.mark-all-read') }}',
                type: 'POST',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function() {
                    toastr.error('An error occurred');
                }
            });
        }
    });

    // Delete message
    $('.delete-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        if (confirm('Are you sure you want to delete this message?')) {
            $.ajax({
                url: '{{ route('admin.messages.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    toastr.success(response.message);
                    btn.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                    updateUnreadCount();
                },
                error: function() {
                    toastr.error('An error occurred');
                }
            });
        }
    });

    function updateUnreadCount() {
        $.get('{{ route('admin.messages.unread-count') }}', function(response) {
            if (response.success) {
                $('.unread-badge').text(response.count);
                if (response.count === 0) {
                    $('.unread-badge').hide();
                    $('#markAllReadBtn').remove();
                }
            }
        });
    }
});
</script>
@endpush
@endsection
