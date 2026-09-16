@extends('user.layouts.app')

@section('title', 'Buy SMS Package - MyBDSMS')

@section('page-title', 'Buy SMS Package')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1">Purchase SMS Package</h4>
                    <p class="text-muted mb-0">Select your preferred package and payment method to recharge your SMS balance</p>
                </div>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-shield-alt me-1"></i> Secure Checkout
                </span>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <form action="{{ route('user.nagad.pay') }}" method="POST" id="buyPackageForm" enctype="multipart/form-data">
                @csrf
                
                <!-- Package Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Select SMS Package <span class="text-danger">*</span></label>
                    <select name="package_id" class="form-select form-select-lg" id="packageSelect" required>
                        <option value="">-- Choose an SMS Plan --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}"
                                    data-price="{{ $plan->price }}"
                                    data-name="{{ $plan->name }}"
                                    data-type="{{ ucfirst($plan->plan_type) }}"
                                    data-min-order="{{ $plan->min_order_amount ?? '500' }}"
                                    {{ (isset($selectedPlanId) && $selectedPlanId == $plan->id) ? 'selected' : '' }}>
                                {{ $plan->name }} (৳{{ $plan->price }}/SMS) - Min ৳{{ $plan->min_order_amount ?? '500' }}
                            </option>
                        @endforeach
                    </select>
                    @error('package_id')
                        <p class="text-danger small mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        <!-- Nagad -->
                        <div class="col-md-4 col-12">
                            <label class="border rounded-4 p-1 text-center d-block cursor-pointer payment-option active" data-target="nagad">
                                <input type="radio" name="payment_method" value="nagad" class="d-none" checked>
                                <img src="{{ asset('assets/images/Nagad-Logo.wine.svg') }}" alt="Nagad" class="img-fluid mb-1" style="max-height: 100px;">
                            </label>
                        </div>
                        <!-- Dgepay -->
                        <div class="col-md-4 col-12">
                            <label class="border rounded-4 p-1 text-center d-block cursor-pointer payment-option" data-target="dgepay">
                                <input type="radio" name="payment_method" value="dgepay" class="d-none">
                                <img src="{{ asset('assets/images/dgepay.png') }}" alt="Dgepay" class="img-fluid mb-1" style="max-height: 100px;">
                            </label>
                        </div>

                        <!-- Bank Transfer -->
                        <div class="col-md-4 col-12">
                            <label class="border rounded-4 p-3 text-center d-block cursor-pointer payment-option" data-target="bank">
                                <input type="radio" name="payment_method" value="bank" class="d-none">
                                <img src="{{ asset('assets/images/bank-svgrepo-com.svg') }}" alt="Bank Transfer" class="img-fluid mb-1" style="max-height:50px;">
                                <span class="fw-bold fs-6 mt-1 text-dark d-block">Bank Transfer</span>
                            </label>
                        </div>

                        <!-- Office Cash -->
                        <div class="col-md-4 col-12">
                            <label class="border rounded-4 p-3 text-center d-block cursor-pointer payment-option" data-target="office_cash">
                                <input type="radio" name="payment_method" value="office_cash" class="d-none">
                                <img src="{{ asset('assets/images/cash-on-delivery.png') }}" alt="Office Cash" class="img-fluid mb-1" style="max-height:50px;">
                                <span class="fw-bold fs-6 mt-1 text-dark d-block">Office Cash</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Details (Dynamic) -->
                <div id="bankSection" class="payment-details-section d-none mb-4">
                    <div class="card border border-primary-subtle bg-primary-subtle bg-opacity-10 rounded-4 p-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-university text-primary fs-5"></i>
                            <h6 class="fw-bold mb-0 text-primary">আমাদের অফিসিয়াল ব্যাংক একাউন্ট তথ্য</h6>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">Bank Name:</span>
                                    <strong class="text-dark">{{ $siteSettings->bank_name ?? 'Dutch-Bangla Bank PLC' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">Account Name:</span>
                                    <strong class="text-dark">{{ $siteSettings->bank_account_name ?? 'MyBDSMS Limited' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted d-block">Account Number:</span>
                                        <strong class="text-dark font-monospace" id="accNumberText">{{ $siteSettings->bank_account_number ?? '123.120.456789' }}</strong>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2" data-clipboard-target="#accNumberText" title="Copy Account Number">
                                        <i class="far fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">Branch:</span>
                                    <strong class="text-dark">{{ $siteSettings->bank_branch ?? 'Motijheel Branch, Dhaka' }}</strong>
                                </div>
                            </div>
                            @if(!empty($siteSettings->bank_routing_number))
                            <div class="col-12">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">Routing Number:</span>
                                    <strong class="text-dark font-monospace">{{ $siteSettings->bank_routing_number }}</strong>
                                </div>
                            </div>
                            @endif
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1 text-primary"></i>
                            {{ $siteSettings->bank_instructions ?? 'ব্যাংক অ্যাকাউন্টে টাকা জমা/ট্রান্সফার করে নিচের বক্সে আপনার তথ্য প্রদান করুন এবং মানি রিসিট/স্লিপ ছবি আপলোড করুন।' }}
                        </p>
                    </div>

                    <div class="card border rounded-4 p-3 bg-white">
                        <h6 class="fw-bold mb-3 text-dark">আপনার ব্যাংক পেমেন্টের তথ্য দিন:</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">আপনার ব্যাংক নাম (Sender Bank)</label>
                                <input type="text" name="sender_bank_name" class="form-control" placeholder="যেমন: DBBL, City Bank, Brac Bank">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">আপনার একাউন্ট / মোবাইল নম্বর</label>
                                <input type="text" name="sender_account" class="form-control" placeholder="যে একাউন্ট থেকে টাকা পাঠিয়েছেন">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Transaction ID / রেফারেন্স নম্বর <span class="text-danger">*</span></label>
                                <input type="text" name="trx_id" id="bankTrxId" class="form-control" placeholder="ব্যাংক ট্রানজেকশন আইডি">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">পেমেন্ট স্লিপ / রসিদের ছবি (ঐচ্ছিক)</label>
                                <input type="file" name="slip_image" class="form-control" accept="image/*,.pdf">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">অতিরিক্ত নোট (Optional)</label>
                                <textarea name="customer_notes" rows="2" class="form-control" placeholder="পেমেন্ট সংক্রান্ত কোনো মন্তব্য বা নির্দেশনা থাকলে লিখুন..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Office Cash Details (Dynamic) -->
                <div id="officeCashSection" class="payment-details-section d-none mb-4">
                    <div class="card border border-success-subtle bg-success-subtle bg-opacity-10 rounded-4 p-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-building text-success fs-5"></i>
                            <h6 class="fw-bold mb-0 text-success">অফিস ক্যাশ পেমেন্ট নির্দেশনা</h6>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-12">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">অফিসের ঠিকানা:</span>
                                    <strong class="text-dark">{{ $siteSettings->company_address ?? 'Level 5, Concord Tower, Motijheel, Dhaka-1000' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">অফিস সময়:</span>
                                    <strong class="text-dark">{{ $siteSettings->company_hours ?? 'শনি - বৃহস্পতি: সকাল ৯:০০ - সন্ধ্যা ৬:০০' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 bg-white rounded-3 border">
                                    <span class="text-muted d-block">যোগাযোগ নম্বর:</span>
                                    <strong class="text-dark">{{ $siteSettings->company_phone ?? '+880 1700-000000' }}</strong>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1 text-success"></i>
                            {{ $siteSettings->office_payment_instructions ?? 'আমাদের অফিসে এসে সরাসরি ক্যাশ কাউন্টারে টাকা পরিশোধ করুন। ক্যাশ গ্রহণের সাথে সাথে আপনার অ্যাকাউন্টে প্যাকেজ সক্রিয় হয়ে যাবে।' }}
                        </p>
                    </div>

                    <div class="card border rounded-4 p-3 bg-white">
                        <label class="form-label small fw-semibold">আপনার মন্তব্য / যোগাযোগের নম্বর (ঐচ্ছিক)</label>
                        <textarea name="office_notes" rows="2" class="form-control" placeholder="কখন অফিসে আসবেন বা কোনো বিশেষ তথ্য থাকলে লিখুন..."></textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="p-3 bg-light rounded-4 mb-4 border">
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">Selected Plan:</span>
                        <strong id="summaryPlanName">--</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">SMS Rate:</span>
                        <strong id="summaryRate" class="text-dark">--</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">Approximate SMS:</span>
                        <strong id="summarySmsCount" class="text-success fw-bold">--</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">Minimum Order Amount:</span>
                        <strong class="text-warning text-dark fw-bold" id="summaryMinOrder">৳500</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-1">
                        <span class="text-muted fw-bold">Payable Amount:</span>
                        <strong class="text-primary fs-4" id="summaryTotal">৳500.00</strong>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold">
                    <i class="fas fa-lock me-2"></i> Pay with Nagad
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .payment-option {
        transition: all 0.25s ease;
    }
    .payment-option:hover {
        border-color: #aaa !important;
        transform: translateY(-2px);
    }
    .payment-option.active {
        border-color: var(--primary-color) !important;
        background-color: rgba(52, 189, 147, 0.08);
        box-shadow: 0 4px 12px rgba(52, 189, 147, 0.15);
    }
</style>
@endsection

@push('scripts')
<script>
    const nagadAction = "{{ route('user.nagad.pay') }}";
    const manualAction = "{{ route('user.manual_payment') }}";
    const dgepayAction = "{{ route('user.dgepay.pay') }}";

    function updateSummary() {
        const option = $('#packageSelect').find('option:selected');
        const price = parseFloat(option.data('price')) || 0;
        const name = option.data('name') || '--';
        const type = option.data('type') || '';
        const minOrder = parseFloat(option.data('min-order')) || 500;

        if ($('#packageSelect').val()) {
            $('#summaryPlanName').text(name + (type ? ' (' + type + ')' : ''));
            $('#summaryRate').text('৳' + price.toFixed(4) + ' / SMS');
            $('#summaryMinOrder').text('৳' + minOrder.toFixed(2));
            $('#summaryTotal').text('৳' + minOrder.toFixed(2));
            const smsCount = price > 0 ? Math.floor(minOrder / price) : 0;
            $('#summarySmsCount').text(smsCount.toLocaleString() + ' SMS');
        } else {
            $('#summaryPlanName').text('--');
            $('#summaryRate').text('--');
            $('#summarySmsCount').text('--');
            $('#summaryMinOrder').text('৳500');
            $('#summaryTotal').text('৳0.00');
        }
    }

    $('#packageSelect').on('change', updateSummary);
    if ($('#packageSelect').val()) {
        updateSummary();
    }

    $('.payment-option').on('click', function() {
        $('.payment-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true);

        const method = $(this).data('target');
        $('.payment-details-section').addClass('d-none');

        if (method === 'nagad') {
            $('#buyPackageForm').attr('action', nagadAction);
            $('#bankTrxId').removeAttr('required');
            $('#submitBtn').html('<i class="fas fa-lock me-2"></i> Pay with Nagad').removeClass('btn-success').addClass('btn-primary');
        } else if (method === 'bank') {
            $('#buyPackageForm').attr('action', manualAction);
            $('#bankSection').removeClass('d-none');
            $('#bankTrxId').attr('required', 'required');
            $('#submitBtn').html('<i class="fas fa-paper-plane me-2"></i> Submit Bank Payment').removeClass('btn-primary').addClass('btn-success');
        } else if (method === 'office_cash') {
            $('#buyPackageForm').attr('action', manualAction);
            $('#officeCashSection').removeClass('d-none');
            $('#bankTrxId').removeAttr('required');
            $('#submitBtn').html('<i class="fas fa-check-circle me-2"></i> Submit Cash Order').removeClass('btn-primary').addClass('btn-success');
        } else if (method === 'dgepay') {
            $('#buyPackageForm').attr('action', dgepayAction);
            $('#bankTrxId').removeAttr('required');
            $('#submitBtn').html('<i class="fas fa-lock me-2"></i> Pay with Dgepay').removeClass('btn-success').addClass('btn-primary');
        }
    });

    // Copy to clipboard
    $(document).on('click', '.copy-btn', function() {
        const target = $(this).data('clipboard-target');
        const text = $(target).text().trim();
        navigator.clipboard.writeText(text).then(() => {
            const icon = $(this).find('i');
            icon.removeClass('far fa-copy').addClass('fas fa-check text-success');
            setTimeout(() => {
                icon.removeClass('fas fa-check text-success').addClass('far fa-copy');
            }, 1500);
        });
    });
</script>
@endpush
