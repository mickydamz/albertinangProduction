@extends('layouts.simslayout')

@push('styles')
<style>
    /* ── Return Request Page ── */
    .return-wrap {
        max-width: 640px;
        margin: 0 auto;
        padding: 36px var(--gutter) 64px;
    }

    /* Breadcrumb back link */
    .return-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--ink3);
        font-weight: 500;
        margin-bottom: 28px;
        transition: color .2s, gap .2s;
    }
    .return-back i { font-size: 11px; transition: transform .2s; }
    .return-back:hover { color: var(--g600); gap: 8px; }
    .return-back:hover i { transform: translateX(-2px); }

    /* Page header */
    .return-header {
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }
    .return-header__eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--g600);
        margin-bottom: 8px;
    }
    .return-header__eyebrow i { font-size: 11px; }
    .return-header h1 {
        font-family: var(--fh);
        font-size: 1.55rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
        line-height: 1.2;
    }
    .return-header__meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--ink3);
    }
    .return-header__meta .order-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--surf3);
        border: 1px solid var(--border2);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink2);
        font-family: var(--fh);
    }
    .return-header__meta .order-badge i { color: var(--g500); font-size: 10px; }

    /* Existing return status card */
    .return-status-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-xl);
        overflow: hidden;
        box-shadow: var(--sh-sm);
    }
    .return-status-card__top {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 22px 24px;
        border-bottom: 1px solid var(--border);
    }
    .return-status-icon {
        width: 44px;
        height: 44px;
        border-radius: 11px;
        background: var(--surf3);
        border: 1px solid var(--border2);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .return-status-icon i { font-size: 18px; color: var(--g600); }
    .return-status-card__title {
        font-family: var(--fh);
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 4px;
    }
    .return-status-card__sub {
        font-size: 13px;
        color: var(--ink3);
    }
    .return-status-card__body { padding: 20px 24px; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: capitalize;
    }
    .status-pill--pending  { background: #fff8e1; color: #b45309; border: 1px solid #fcd34d; }
    .status-pill--approved { background: var(--g50);  color: var(--g600); border: 1px solid var(--g200); }
    .status-pill--rejected { background: #fff5f5; color: #c0392b; border: 1px solid #fca5a5; }
    .status-pill i { font-size: 9px; }

    .return-admin-note {
        margin-top: 14px;
        background: var(--surf2);
        border: 1px solid var(--border);
        border-left: 3px solid var(--g400);
        border-radius: 0 var(--r) var(--r) 0;
        padding: 12px 14px;
        font-size: 13px;
        color: var(--ink2);
        line-height: 1.5;
    }
    .return-admin-note strong {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        color: var(--ink3);
        margin-bottom: 5px;
    }
    .return-admin-note strong i { color: var(--g500); }

    .return-status-card__footer {
        padding: 14px 24px;
        background: var(--surf2);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--ink3);
    }
    .return-status-card__footer i { color: var(--ink3); font-size: 11px; }

    /* Return form card */
    .return-form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 0;
        overflow: hidden;
    }
    .return-form-card__head {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        background: var(--surf2);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .return-form-card__head i { color: var(--g500); font-size: 14px; }
    .return-form-card__head span {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink2);
    }
    .return-form-card__body { padding: 28px 24px; }

    /* Info notice */
    .return-notice {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        background: var(--g50);
        border: 1px solid var(--border2);
        border-radius: var(--r);
        padding: 12px 14px;
        margin-bottom: 24px;
        font-size: 12.5px;
        color: var(--ink2);
        line-height: 1.5;
    }
    .return-notice i { color: var(--g600); font-size: 13px; margin-top: 1px; flex-shrink: 0; }

    /* Form field */
    .form-group { margin-bottom: 22px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 7px;
    }
    .form-label .req { color: #c0392b; margin-left: 2px; }
    .form-label .opt {
        font-size: 11px;
        font-weight: 400;
        color: var(--ink3);
        margin-left: 4px;
    }
    .form-hint {
        font-size: 11.5px;
        color: var(--ink3);
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .form-hint i { font-size: 10px; }

    .form-textarea {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: var(--r);
        padding: 12px 14px;
        font-size: 14px;
        font-family: var(--fb);
        color: var(--ink);
        background: var(--surface);
        resize: vertical;
        min-height: 140px;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
        line-height: 1.6;
    }
    .form-textarea:focus {
        border-color: var(--g400);
        box-shadow: 0 0 0 3px rgba(90,171,31,.12);
    }
    .form-textarea.has-error { border-color: #e05a1a; }
    .form-textarea::placeholder { color: var(--ink3); }

    .char-counter {
        text-align: right;
        font-size: 11px;
        color: var(--ink3);
        margin-top: 5px;
        font-variant-numeric: tabular-nums;
    }
    .char-counter.warn { color: #b45309; }
    .char-counter.limit { color: #c0392b; }

    /* File upload */
    .file-upload-zone {
        border: 2px dashed var(--border);
        border-radius: var(--r);
        padding: 28px 20px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        position: relative;
        background: var(--surf2);
    }
    .file-upload-zone:hover,
    .file-upload-zone.drag-over {
        border-color: var(--g400);
        background: var(--g50);
    }
    .file-upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .file-upload-zone__icon {
        width: 44px;
        height: 44px;
        background: var(--surface);
        border: 1px solid var(--border2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }
    .file-upload-zone__icon i { font-size: 18px; color: var(--g500); }
    .file-upload-zone__title {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 4px;
    }
    .file-upload-zone__sub {
        font-size: 12px;
        color: var(--ink3);
    }
    .file-upload-zone__sub span { color: var(--g600); font-weight: 600; }

    .file-preview {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: var(--g50);
        border: 1px solid var(--border2);
        border-radius: var(--r);
        margin-top: 10px;
    }
    .file-preview.visible { display: flex; }
    .file-preview img {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 7px;
        border: 1px solid var(--border);
    }
    .file-preview__info { flex: 1; min-width: 0; }
    .file-preview__name {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .file-preview__size { font-size: 11px; color: var(--ink3); margin-top: 2px; }
    .file-preview__remove {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--ink3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        cursor: pointer;
        transition: all .2s;
        flex-shrink: 0;
    }
    .file-preview__remove:hover { background: #fff5f5; border-color: #fca5a5; color: #c0392b; }

    .form-error {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #c0392b;
        margin-top: 6px;
    }
    .form-error i { font-size: 10px; }

    /* Submit */
    .return-form-card__foot {
        padding: 20px 24px;
        border-top: 1px solid var(--border);
        background: var(--surf2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .return-form-card__foot-note {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--ink3);
    }
    .return-form-card__foot-note i { color: var(--g500); font-size: 11px; }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--g500);
        color: #fff;
        padding: 11px 24px;
        border-radius: var(--r);
        font-size: 13.5px;
        font-weight: 600;
        font-family: var(--fb);
        border: none;
        cursor: pointer;
        transition: background .2s, transform .15s;
    }
    .btn-submit:hover { background: var(--g600); transform: translateY(-1px); }
    .btn-submit:active { transform: translateY(0); }
    .btn-submit i { font-size: 12px; }
</style>
@endpush

@section('content')
<div class="return-wrap">

    {{-- Back link --}}
    <a href="{{ route('account.orders') }}" class="return-back">
        <i class="fas fa-arrow-left"></i> Back to orders
    </a>

    {{-- Page header --}}
    <div class="return-header">
        <div class="return-header__eyebrow">
            <i class="fas fa-rotate-left"></i>
            Return Request
        </div>
        <h1>Request a Return</h1>
        <div class="return-header__meta">
            <span class="order-badge">
                <i class="fas fa-receipt"></i>
                Order #{{ $order->order_number }}
            </span>
        </div>
    </div>

    @if($existingReturn)
        @if($existingReturn->refund_status)
            <div class="alert alert-info" role="status">
                Refund: {{ in_array($existingReturn->refund_status, ['pending','requesting','processing']) ? 'Processing — awaiting Paystack confirmation' : ucfirst(str_replace('-', ' ', $existingReturn->refund_status)) }}.
                @if(in_array($existingReturn->refund_status, ['unknown','failed','needs-attention'])) Our team needs to review this refund. @endif
            </div>
        @endif


        {{-- ── Existing return status ── --}}
        <div class="return-status-card">
            <div class="return-status-card__top">
                <div class="return-status-icon">
                    @if($existingReturn->status === 'approved')
                        <i class="fas fa-check-circle" style="color:var(--g600);"></i>
                    @elseif($existingReturn->status === 'rejected')
                        <i class="fas fa-times-circle" style="color:#c0392b;"></i>
                    @else
                        <i class="fas fa-clock" style="color:#b45309;"></i>
                    @endif
                </div>
                <div>
                    <div class="return-status-card__title">Return already submitted</div>
                    <div class="return-status-card__sub">We've received your request and are reviewing it.</div>
                </div>
            </div>

            <div class="return-status-card__body">
                <div style="margin-bottom:14px;">
                    <div style="font-size:12px;font-weight:600;color:var(--ink3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;">Current Status</div>
                    @php
                        $statusIcons = [
                            'approved' => 'fa-circle-check',
                            'rejected' => 'fa-circle-xmark',
                            'pending'  => 'fa-circle-half-stroke',
                        ];
                        $icon = $statusIcons[$existingReturn->status] ?? 'fa-circle';
                    @endphp
                    <span class="status-pill status-pill--{{ $existingReturn->status }}">
                        <i class="fas {{ $icon }}"></i>
                        {{ ucfirst($existingReturn->status) }}
                    </span>
                </div>

                @if($existingReturn->admin_notes)
                    <div class="return-admin-note">
                        <strong><i class="fas fa-comment-dots"></i> Admin Note</strong>
                        {{ $existingReturn->admin_notes }}
                    </div>
                @endif
            </div>

            <div class="return-status-card__footer">
                <i class="fas fa-clock"></i>
                Submitted {{ $existingReturn->created_at->diffForHumans() }}
            </div>
        </div>

    @else

        {{-- ── Return form ── --}}
        <div class="return-form-card">
            <div class="return-form-card__head">
                <i class="fas fa-clipboard-list"></i>
                <span>Fill in the details below to submit your return</span>
            </div>

            <form method="POST" action="{{ route('account.orders.return.submit', $order) }}" enctype="multipart/form-data" id="returnForm">
                @csrf

                <div class="return-form-card__body">

                    <div class="return-notice">
                        <i class="fas fa-circle-info"></i>
                        <span>Returns are reviewed within <strong>1–2 business days</strong>. Ensure your reason is clear and detailed so we can process your request promptly.</span>
                    </div>

                    {{-- Reason textarea --}}
                    <div class="form-group">
                        <label class="form-label" for="reason">
                            Reason for return <span class="req">*</span>
                        </label>
                        <textarea
                            name="reason"
                            id="reason"
                            rows="6"
                            required
                            minlength="20"
                            maxlength="1000"
                            class="form-textarea {{ $errors->has('reason') ? 'has-error' : '' }}"
                            placeholder="Describe the issue in detail — e.g., item arrived damaged, received wrong product, changed mind, etc."
                            oninput="updateCharCount(this)"
                        >{{ old('reason') }}</textarea>
                        <div class="char-counter" id="charCounter">0 / 1000</div>
                        @error('reason')
                            <div class="form-error">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="form-hint">
                            <i class="fas fa-pen-to-square"></i>
                            Minimum 20 characters. More detail helps us process your return faster.
                        </div>
                    </div>

                    {{-- File upload --}}
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">
                            Photo evidence
                            <span class="opt">(optional)</span>
                        </label>

                        <div class="file-upload-zone" id="uploadZone">
                            <input type="file" name="evidence" id="evidenceInput"
                                accept="image/jpeg,image/png,image/webp"
                                onchange="handleFileSelect(this)">
                            <div class="file-upload-zone__icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="file-upload-zone__title">Drop your photo here, or <span style="color:var(--g600);">browse</span></div>
                            <div class="file-upload-zone__sub">Supports <span>JPG, PNG, WebP</span> &nbsp;·&nbsp; Max <span>4 MB</span></div>
                        </div>

                        <div class="file-preview" id="filePreview">
                            <img id="previewImg" src="" alt="Preview">
                            <div class="file-preview__info">
                                <div class="file-preview__name" id="previewName">—</div>
                                <div class="file-preview__size" id="previewSize">—</div>
                            </div>
                            <button type="button" class="file-preview__remove" onclick="removeFile()" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @error('evidence')
                            <div class="form-error">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="return-form-card__foot">
                    <div class="return-form-card__foot-note">
                        <i class="fas fa-lock"></i>
                        Your request is secure and confidential
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Submit return request
                    </button>
                </div>

            </form>
        </div>

    @endif

</div>
@endsection

@push('scripts')
<script>
    /* ── Character counter ── */
    function updateCharCount(el) {
        var len = el.value.length;
        var counter = document.getElementById('charCounter');
        if (!counter) return;
        counter.textContent = len + ' / 1000';
        counter.className = 'char-counter' + (len > 900 ? ' limit' : len > 700 ? ' warn' : '');
    }

    // Init on load if old() value exists
    var reasonEl = document.getElementById('reason');
    if (reasonEl && reasonEl.value) updateCharCount(reasonEl);

    /* ── File upload ── */
    function handleFileSelect(input) {
        var file = input.files[0];
        if (!file) return;

        var preview   = document.getElementById('filePreview');
        var previewImg  = document.getElementById('previewImg');
        var previewName = document.getElementById('previewName');
        var previewSize = document.getElementById('previewSize');
        var zone        = document.getElementById('uploadZone');

        var mb = (file.size / 1024 / 1024).toFixed(2);
        previewName.textContent = file.name;
        previewSize.textContent = mb + ' MB';
        preview.classList.add('visible');
        zone.style.display = 'none';

        var reader = new FileReader();
        reader.onload = function(e) { previewImg.src = e.target.result; };
        reader.readAsDataURL(file);
    }

    function removeFile() {
        var input   = document.getElementById('evidenceInput');
        var preview = document.getElementById('filePreview');
        var zone    = document.getElementById('uploadZone');

        input.value = '';
        preview.classList.remove('visible');
        zone.style.display = '';
        document.getElementById('previewImg').src = '';
    }

    /* ── Drag-over highlight ── */
    var zone = document.getElementById('uploadZone');
    if (zone) {
        zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.classList.add('drag-over'); });
        zone.addEventListener('dragleave', function()  { zone.classList.remove('drag-over'); });
        zone.addEventListener('drop',      function(e){
            e.preventDefault();
            zone.classList.remove('drag-over');
            var files = e.dataTransfer.files;
            if (files.length) {
                var input = document.getElementById('evidenceInput');
                input.files = files;
                handleFileSelect(input);
            }
        });
    }
</script>
@endpush