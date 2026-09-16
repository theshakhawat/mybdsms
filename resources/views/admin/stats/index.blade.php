@extends('admin.layouts.app')

@section('title', 'Stats Section - MyBDSMS Admin')

@section('page-title', 'Stats Section Management')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="mb-1">Statistics Section</h4>
        <p class="text-muted mb-0">Manage homepage statistics counters</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistics Section</h5>
            </div>
            <div class="card-body">
                <form id="statsForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Clients Stat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="clients_count" class="form-label">Count</label>
                                                <input type="text" class="form-control" id="clients_count" name="clients_count" value="{{ $stats->clients_count }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="clients_label" class="form-label">Label</label>
                                                <input type="text" class="form-control" id="clients_label" name="clients_label" value="{{ $stats->clients_label }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-sms me-2"></i>SMS Stat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="sms_count" class="form-label">Count</label>
                                                <input type="text" class="form-control" id="sms_count" name="sms_count" value="{{ $stats->sms_count }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="sms_label" class="form-label">Label</label>
                                                <input type="text" class="form-control" id="sms_label" name="sms_label" value="{{ $stats->sms_label }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Delivery Stat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="delivery_count" class="form-label">Count</label>
                                                <input type="text" class="form-control" id="delivery_count" name="delivery_count" value="{{ $stats->delivery_count }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="delivery_label" class="form-label">Label</label>
                                                <input type="text" class="form-control" id="delivery_label" name="delivery_label" value="{{ $stats->delivery_label }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-award me-2"></i>Experience Stat</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="years_count" class="form-label">Count</label>
                                                <input type="text" class="form-control" id="years_count" name="years_count" value="{{ $stats->years_count }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="years_label" class="form-label">Label</label>
                                                <input type="text" class="form-control" id="years_label" name="years_label" value="{{ $stats->years_label }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-save me-2"></i>Save All Statistics
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#statsForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();

        btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: '{{ route('admin.stats.update') }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success(response.message);
                btn.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection
