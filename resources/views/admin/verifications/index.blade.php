@extends('admin.layouts.app')

@section('title', 'Verifications - Admin')
@section('page-title', 'Verification Requests')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Documents</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($verifications as $verification)
                    <tr>
                        <td>#{{ $verification->id }}</td>
                        <td>{{ $verification->name }}</td>
                        <td>{{ $verification->email }}</td>
                        <td>{{ $verification->phone }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewDocuments({{ $verification->id }})">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                        <td>
                            @if($verification->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($verification->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $verification->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($verification->status == 'pending')
                                <button class="btn btn-sm btn-success" onclick="approveRequest({{ $verification->id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="showRejectModal({{ $verification->id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                            <button class="btn btn-sm btn-secondary" onclick="deleteRequest({{ $verification->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Documents Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verification Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>NID Front</h6>
                    <div id="modalNidFront"></div>
                </div>
                <div class="mb-3">
                    <h6>NID Back</h6>
                    <div id="modalNidBack"></div>
                </div>
                <div class="mb-3" id="tradeLicenseContainer" style="display: none;">
                    <h6>Trade License</h6>
                    <div id="modalTradeLicense"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" id="rejectRequestId" name="request_id">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason (Optional)</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Store base URLs manually
const showUrl = '/admin/verifications/';
const approveUrl = '/admin/verifications/';
const rejectUrl = '/admin/verifications/';
const destroyUrl = '/admin/verifications/';

function viewDocuments(id) {
    $.get(showUrl + id, function(data) {
        renderDocument('modalNidFront', data.nid_front);
        renderDocument('modalNidBack', data.nid_back);
        if (data.trade_license) {
            renderDocument('modalTradeLicense', data.trade_license);
            $('#tradeLicenseContainer').show();
        } else {
            $('#tradeLicenseContainer').hide();
        }
        new bootstrap.Modal(document.getElementById('documentsModal')).show();
    }).fail(function() {
        toastr.error('Error loading documents.');
    });
}

function renderDocument(elementId, filePath) {
    const fullPath = '{{ asset('/') }}' + filePath;
    const extension = filePath.split('.').pop().toLowerCase();

    if (extension === 'pdf') {
        $('#' + elementId).html('<iframe src="' + fullPath + '" style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 8px;"></iframe>');
    } else {
        $('#' + elementId).html('<img src="' + fullPath + '" class="img-fluid" style="max-height: 400px;">');
    }
}

function approveRequest(id) {
    if (confirm('Are you sure you want to approve this verification request?')) {
        $.ajax({
            url: approveUrl + id + '/approve',
            method: 'POST',
            data: {
                _method: 'PATCH',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success('Verification request approved!');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error('Error approving request.');
            }
        });
    }
}

function showRejectModal(id) {
    $('#rejectRequestId').val(id);
    $('#rejectForm').attr('action', rejectUrl + id + '/reject');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function deleteRequest(id) {
    if (confirm('Are you sure you want to delete this verification request?')) {
        $.ajax({
            url: destroyUrl + id,
            method: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                toastr.success('Verification request deleted!');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error('Error deleting request.');
            }
        });
    }
}

// Handle reject form submission
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    const url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            toastr.success('Verification request rejected!');
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            toastr.error('Error rejecting request.');
        }
    });
});
</script>
@endpush
@endsection
