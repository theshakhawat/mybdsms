@extends('admin.layouts.app')

@section('title', 'FAQs - MyBDSMS Admin')

@section('page-title', 'FAQs Management')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Frequently Asked Questions</h4>
        <p class="text-muted mb-0">Manage FAQs displayed on the website</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqModal">
        <i class="fas fa-plus me-2"></i>Add FAQ
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="faqsTable">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Status</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($faqs as $faq)
                        <tr data-id="{{ $faq->id }}" draggable="true">
                            <td>
                                <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                            </td>
                            <td>
                                <strong>{{ $faq->question }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($faq->answer, 100) }}</small>
                            </td>
                            <td>
                                @if($faq->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary edit-btn"
                                            data-id="{{ $faq->id }}"
                                            data-question="{{ $faq->question }}"
                                            data-answer="{{ $faq->answer }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $faq->is_active ? 'warning' : 'success' }} toggle-btn"
                                            data-id="{{ $faq->id }}">
                                        <i class="fas fa-{{ $faq->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger delete-btn"
                                            data-id="{{ $faq->id }}"
                                            data-question="{{ $faq->question }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="faqForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="faqModalTitle">Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="faqId" name="id">

                <div class="mb-3">
                    <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="question" name="question" required placeholder="What is...?">
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="answer" name="answer" rows="5" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let faqModal;

    faqModal = new bootstrap.Modal(document.getElementById('faqModal'));

    $('#faqForm').on('submit', function(e) {
        e.preventDefault();

        const faqId = $('#faqId').val();
        const url = faqId
            ? '{{ route('admin.faqs.update', ':id') }}'.replace(':id', faqId)
            : '{{ route('admin.faqs.store') }}';

        const method = faqId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: {
                _token: '{{ csrf_token() }}',
                question: $('#question').val(),
                answer: $('#answer').val()
            },
            success: function(response) {
                toastr.success(response.message);
                faqModal.hide();
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred';
                toastr.error(error);
            }
        });
    });

    $('.edit-btn').on('click', function() {
        const btn = $(this);
        $('#faqId').val(btn.data('id'));
        $('#question').val(btn.data('question'));
        $('#answer').val(btn.data('answer'));
        $('#faqModalTitle').text('Edit FAQ');
        faqModal.show();
    });

    $('.toggle-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');

        $.ajax({
            url: '{{ route('admin.faqs.toggle', ':id') }}'.replace(':id', id),
            type: 'PATCH',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                toastr.success(response.message);
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function() {
                toastr.error('An error occurred');
            }
        });
    });

    $('.delete-btn').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const question = btn.data('question');

        if (confirm(`Are you sure you want to delete this FAQ?`)) {
            $.ajax({
                url: '{{ route('admin.faqs.destroy', ':id') }}'.replace(':id', id),
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function() {
                    toastr.error('An error occurred');
                }
            });
        }
    });

    $('#faqModal').on('hidden.bs.modal', function() {
        $('#faqId').val('');
        $('#faqForm')[0].reset();
    });

    // Drag and drop reorder
    let draggedItem = null;

    $('#faqsTable tbody').on('dragstart', 'tr', function(e) {
        draggedItem = $(this);
        $(this).addClass('dragging');
    });

    $('#faqsTable tbody').on('dragend', 'tr', function() {
        $(this).removeClass('dragging');
        draggedItem = null;

        const items = [];
        $('#faqsTable tbody tr').each(function() {
            items.push($(this).data('id'));
        });

        $.ajax({
            url: '{{ route('admin.faqs.reorder') }}',
            type: 'POST',
            data: {_token: '{{ csrf_token() }}', items: items},
            success: function(response) {
                toastr.success(response.message);
            }
        });
    });

    $('#faqsTable tbody').on('dragover', 'tr', function(e) {
        e.preventDefault();
        if (draggedItem && draggedItem !== $(this)) {
            const allRows = $('#faqsTable tbody tr');
            const draggedIndex = allRows.index(draggedItem);
            const targetIndex = allRows.index($(this));

            if (draggedIndex < targetIndex) {
                $(this).after(draggedItem);
            } else {
                $(this).before(draggedItem);
            }
        }
    });
});
</script>
@endpush
@endsection
