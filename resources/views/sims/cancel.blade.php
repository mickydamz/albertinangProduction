@extends('layouts.simslayout')

@push('styles')
<style>
    .cancel-wrap {
        max-width: 640px;
        margin: 0 auto;
        padding: 36px var(--gutter) 64px;
    }
    .cancel-back {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; color: var(--ink3); font-weight: 500;
        margin-bottom: 28px; transition: color .2s, gap .2s;
    }
    .cancel-back i { font-size: 11px; transition: transform .2s; }
    .cancel-back:hover { color: var(--g600); gap: 8px; }
    .cancel-back:hover i { transform: translateX(-2px); }

    .cancel-header {
        margin-bottom: 28px; padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }
    .cancel-header__eyebrow {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; font-weight: 700; letter-spacing: 1.2px;
        text-transform: uppercase; color: #c0392b; margin-bottom: 8px;
    }
    .cancel-header h1 {
        font-family: var(--fh); font-size: 1.55rem; font-weight: 700;
        color: var(--ink); margin-bottom: 6px; line-height: 1.2;
    }
    .order-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--surf3); border: 1px solid var(--border2);
        border-radius: 6px; padding: 3px 10px; font-size: 12px;
        font-weight: 600; color: var(--ink2); font-family: var(--fh);
    }
    .order-badge i { color: var(--g500); font-size: 10px; }

    /* Status card */
    .cancel-status-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: var(--r-xl); overflow: hidden; box-shadow: var(--sh-sm);
    }
    .cancel-status-card__top {
        display: flex; align-items: flex-start; gap: 14px;
        padding: 22px 24px; border-bottom: 1px solid var(--border);
    }
    .cancel-status-icon {
        width: 44px; height: 44px; border-radius: 11px;
        background: var(--surf3); border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .cancel-status-card__title { font-family: var(--fh); font-size: 15px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
    .cancel-status-card__sub { font-size: 13px; color: var(--ink3); }
    .cancel-status-card__body { padding: 20px 24px; }
    .cancel-status-card__footer {
        padding: 14px 24px; background: var(--surf2);
        border-top: 1px solid var(--border);
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; color: var(--ink3);
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px; font-size: 12px;
        font-weight: 700; letter-spacing: .3px; text-transform: capitalize;
    }
    .status-pill--pending  { background: #fff8e1; color: #b45309; border: 1px solid #fcd34d; }
    .status-pill--approved { background: var(--g50); color: var(--g600); border: 1px solid var(--g200); }
    .status-pill--rejected { background: #fff5f5; color: #c0392b; border: 1px solid #fca5a5; }
    .status-pill i { font-size: 9px; }

    .cancel-admin-note {
        margin-top: 14px; background: var(--surf2); border: 1px solid var(--border);
        border-left: 3px solid #e05a1a; border-radius: 0 var(--r) var(--r) 0;
        padding: 12px 14px; font-size: 13px; color: var(--ink2); line-height: 1.5;
    }
    .cancel-admin-note strong {
        display: flex; align-items: center; gap: 5px; font-size: 11px;
        font-weight: 700; letter-spacing: .8px; text-transform: uppercase;
        color: var(--ink3); margin-bottom: 5px;
    }

    /* Form card */
    .cancel-form-card {
        background: var(--surface); border: 1px solid var(--border);
        border-radius: 0; overflow: hidden;
    }
    .cancel-form-card__head {
        padding: 20px 24px; border-bottom: 1px solid var(--border);
        background: var(--surf2); display: flex; align-items: center; gap: 10px;
    }
    .cancel-form-card__head i { color: #c0392b; font-size: 14px; }
    .cancel-form-card__head span { font-size: 13px; font-weight: 600; color: var(--ink2); }
    .cancel-form-card__body { padding: 28px 24px; }

    .cancel-notice {
        display: flex; gap: 10px; align-items: flex-start;
        background: #fff8e1; border: 1px solid #fcd34d;
        border-radius: var(--r); padding: 12px 14px; margin-bottom: 24px;
        font-size: 12.5px; color: var(--ink2); line-height: 1.5;
    }
    .cancel-notice i { color: #b45309; font-size: 13px; margin-top: 1px; flex-shrink: 0; }

    .form-group { margin-bottom: 22px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 7px; }
    .form-label .req { color: #c0392b; margin-left: 2px; }
    .form-hint { font-size: 11.5px; color: var(--ink3); margin-top: 6px; display: flex; align-items: center; gap: 5px; }
    .form-hint i { font-size: 10px; }

    .form-textarea {
        width: 100%; border: 1.5px solid var(--border); border-radius: var(--r);
        padding: 12px 14px; font-size: 14px; font-family: var(--fb);
        color: var(--ink); background: var(--surface); resize: vertical;
        min-height: 140px; transition: border-color .2s, box-shadow .2s;
        outline: none; line-height: 1.6;
    }
    .form-textarea:focus { border-color: #e05a1a; box-shadow: 0 0 0 3px rgba(224,90,26,.12); }
    .form-textarea.has-error { border-color: #c0392b; }
    .form-textarea::placeholder { color: var(--ink3); }

    .char-counter { text-align: right; font-size: 11px; color: var(--ink3); margin-top: 5px; }
    .char-counter.warn  { color: #b45309; }
    .char-counter.limit { color: #c0392b; }

    .form-error { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #c0392b; margin-top: 6px; }
    .form-error i { font-size: 10px; }

    .cancel-form-card__foot {
        padding: 20px 24px; border-top: 1px solid var(--border);
        background: var(--surf2); display: flex; align-items: center;
        justify-content: space-between; gap: 12px; flex-wrap: wrap;
    }
    .cancel-form-card__foot-note { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ink3); }
    .cancel-form-card__foot-note i { color: var(--ink3); font-size: 11px; }

    .btn-submit-cancel {
        display: inline-flex; align-items: center; gap: 8px;
        background: #c0392b; color: #fff; padding: 11px 24px;
        border-radius: var(--r); font-size: 13.5px; font-weight: 600;
        font-family: var(--fb); border: none; cursor: pointer;
        transition: background .2s, transform .15s;
    }
    .btn-submit-cancel:hover { background: #a93226; transform: translateY(-1px); }
    .btn-submit-cancel i { font-size: 12px; }

    .flash-success {
        padding: 12px 16px; background: var(--g50); border: 1px solid var(--g200);
        border-left: 4px solid var(--g500); border-radius: var(--r);
        font-size: 13px; color: var(--g700); margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }
    .flash-error {
        padding: 12px 16px; background: #fff5f5; border: 1px solid #fca5a5;
        border-left: 4px solid #c0392b; border-radius: var(--r);
        font-size: 13px; color: #7f1d1d; margin-bottom: 20px;
        display: flex; align-items: center; gap: 8px;
    }
</style>
@endpush

@section('content')
<div class="cancel-wrap">

    <a href="{{ route('account.orders') }}" class="cancel-back">
        <i class="fas fa-arrow-left"></i> Back to orders
    </a>

    @if(session('success'))
        <div class="flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="cancel-header">
        <div class="cancel-header__eyebrow">
            <i class="fas fa-ban"></i> Cancellation Request
        </div>
        <h1>Cancel Order</h1>
        <div>
            <span class="order-badge">
                <i class="fas fa-receipt"></i>
                Order #{{ $order->order_number }}
            </span>
        </div>
    </div>

    @if($existingCancellation)

        <div class="cancel-status-card">
            <div class="cancel-status-card__top">
                <div class="cancel-status-icon">
                    @if($existingCancellation->status === 'approved')
                        <i class="fas fa-check-circle" style="font-size:18px;color:var(--g600);"></i>
                    @elseif($existingCancellation->status === 'rejected')
                        <i class="fas fa-times-circle" style="font-size:18px;color:#c0392b;"></i>
                    @else
                        <i class="fas fa-clock" style="font-size:18px;color:#b45309;"></i>
                    @endif
                </div>
                <div>
                    <div class="cancel-status-card__title">Cancellation already submitted</div>
                    <div class="cancel-status-card__sub">We've received your request and are reviewing it.</div>
                </div>
            </div>

            <div class="cancel-status-card__body">
                <div style="margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">Current Status</div>
                    @php
                        $icons = ['approved' => 'fa-circle-check', 'rejected' => 'fa-circle-xmark', 'pending' => 'fa-circle-half-stroke'];
                    @endphp
                    <span class="status-pill status-pill--{{ $existingCancellation->status }}">
                        <i class="fas {{ $icons[$existingCancellation->status] ?? 'fa-circle' }}"></i>
                        {{ ucfirst($existingCancellation->status) }}
                    </span>
                </div>
                @if($existingCancellation->admin_notes)
                    <div class="cancel-admin-note">
                        <strong><i class="fas fa-comment-dots"></i> Admin Note</strong>
                        {{ $existingCancellation->admin_notes }}
                    </div>
                @endif
            </div>

            <div class="cancel-status-card__footer">
                <i class="fas fa-clock"></i>
                Submitted {{ $existingCancellation->created_at->diffForHumans() }}
            </div>
        </div>

    @else

        <div class="cancel-form-card">
            <div class="cancel-form-card__head">
                <i class="fas fa-ban"></i>
                <span>Tell us why you want to cancel this order</span>
            </div>

            <form method="POST" action="{{ route('account.orders.cancel.submit', $order) }}" id="cancelForm">
                @csrf

                <div class="cancel-form-card__body">

                    <div class="cancel-notice">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span>Cancellations are only possible for orders with <strong>pending</strong> or <strong>processing</strong> status. Once dispatched, you'll need to request a return instead. Requests are reviewed within <strong>1–2 business days</strong>.</span>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="reason">
                            Reason for cancellation <span class="req">*</span>
                        </label>
                        <textarea
                            name="reason"
                            id="reason"
                            rows="6"
                            required
                            minlength="20"
                            maxlength="1000"
                            class="form-textarea {{ $errors->has('reason') ? 'has-error' : '' }}"
                            placeholder="Please tell us why you'd like to cancel — e.g., ordered by mistake, found a better price, changed my mind, etc."
                            oninput="updateCharCount(this)"
                        >{{ old('reason') }}</textarea>
                        <div class="char-counter" id="charCounter">0 / 1000</div>
                        @error('reason')
                            <div class="form-error">
                                <i class="fas fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                        <div class="form-hint">
                            <i class="fas fa-pen-to-square"></i>
                            Minimum 20 characters.
                        </div>
                    </div>

                </div>

                <div class="cancel-form-card__foot">
                    <div class="cancel-form-card__foot-note">
                        <i class="fas fa-lock"></i>
                        Your request is secure and confidential
                    </div>
                    <button type="submit" class="btn-submit-cancel">
                        <i class="fas fa-ban"></i>
                        Submit cancellation request
                    </button>
                </div>

            </form>
        </div>

    @endif

</div>
@endsection

@push('scripts')
<script>
    function updateCharCount(el) {
        var len = el.value.length;
        var counter = document.getElementById('charCounter');
        if (!counter) return;
        counter.textContent = len + ' / 1000';
        counter.className = 'char-counter' + (len > 900 ? ' limit' : len > 700 ? ' warn' : '');
    }
    var reasonEl = document.getElementById('reason');
    if (reasonEl && reasonEl.value) updateCharCount(reasonEl);
</script>
@endpush