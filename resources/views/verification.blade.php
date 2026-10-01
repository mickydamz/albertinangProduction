@extends('layouts.simslayout')

@section('title', 'Verify Your Identity')

@push('styles')
<style>
.kyc-wrap {
    min-height: 60vh;
    display: flex; align-items: center; justify-content: center;
    padding: 48px 16px 60px;
    background: #f5f5f5;
}
.kyc-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 40px 40px 36px;
    max-width: 520px; width: 100%;
}
.kyc-card-center { text-align: center; }
.kyc-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; font-size: 28px;
}
.kyc-icon--green  { background: #f0faf0; border: 2px solid var(--g500); color: var(--g600); }
.kyc-icon--amber  { background: #fff8e1; border: 2px solid #f5a623; color: #f5a623; }
.kyc-icon--red    { background: #fef2f2; border: 2px solid #dc2626; color: #dc2626; }
.kyc-icon--blue   { background: #eff6ff; border: 2px solid #3b82f6; color: #3b82f6; }
.kyc-title { font-size: 20px; font-weight: 800; color: var(--ink); margin-bottom: 10px; font-family: var(--fh); letter-spacing: -.2px; }
.kyc-sub   { font-size: 14px; color: var(--ink3); line-height: 1.65; margin-bottom: 28px; }
.kyc-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--g500); color: #fff;
    padding: 12px 28px; border-radius: 8px;
    font-size: 14px; font-weight: 700; font-family: var(--fh);
    text-decoration: none; transition: background .2s; letter-spacing: -.1px;
}
.kyc-btn:hover { background: var(--g700); color: #fff; text-decoration: none; }

/* Form */
.kyc-form-head {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.kyc-form-title { font-size: 20px; font-weight: 800; color: var(--ink); font-family: var(--fh); letter-spacing: -.2px; }
.kyc-form-sub   { font-size: 13.5px; color: var(--ink3); margin-top: 4px; }

.kyc-field { margin-bottom: 18px; }
.kyc-field label {
    display: block; font-size: 13px; font-weight: 600;
    color: var(--ink2); margin-bottom: 6px;
}
.kyc-field input[type="text"],
.kyc-field input[type="email"],
.kyc-field input[type="file"] {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid var(--border); border-radius: 8px;
    font-size: 14px; font-family: var(--fb); color: var(--ink);
    background: var(--surf2); outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.kyc-field input[type="text"]:focus,
.kyc-field input[type="email"]:focus {
    border-color: var(--g400);
    box-shadow: 0 0 0 3px rgba(78,122,26,.10);
    background: #fff;
}
.kyc-field input[type="file"] { padding: 8px 12px; cursor: pointer; }
.kyc-field .field-error { font-size: 12px; color: #dc2626; margin-top: 4px; }

.kyc-submit {
    width: 100%; padding: 13px;
    background: var(--g500); color: #fff; border: none;
    border-radius: 8px; font-size: 14px; font-weight: 700;
    font-family: var(--fh); cursor: pointer;
    transition: background .2s; letter-spacing: -.1px;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    margin-top: 24px;
}
.kyc-submit:hover { background: var(--g700); }

.kyc-notice {
    background: #fff8e1; border: 1px solid #f5a623;
    border-radius: 8px; padding: 12px 16px;
    font-size: 13px; color: #92400e;
    margin-bottom: 22px; display: flex; align-items: flex-start; gap: 10px;
}
.kyc-notice i { color: #f5a623; font-size: 15px; flex-shrink: 0; margin-top: 1px; }
</style>
@endpush

@section('content')
<div class="kyc-wrap">
    <div class="kyc-card {{ (auth()->user()->idverification && in_array(auth()->user()->idverification->status, [1, 2])) ? 'kyc-card-center' : '' }}">

    @if(auth()->user()->idverification && auth()->user()->idverification->status === 1)
        {{-- ── Approved ── --}}
        <div class="kyc-icon kyc-icon--green"><i class="fas fa-check"></i></div>
        <div class="kyc-title">Verification Complete</div>
        <div class="kyc-sub">Your identity has been successfully verified. You now have full access to your account.</div>
        <a href="{{ route('dashboard') }}" class="kyc-btn"><i class="fas fa-home"></i> Go to Dashboard</a>

    @elseif(auth()->user()->idverification && auth()->user()->idverification->status === 2)
        {{-- ── Pending ── --}}
        <div class="kyc-icon kyc-icon--amber"><i class="fas fa-clock"></i></div>
        <div class="kyc-title">Documents Under Review</div>
        <div class="kyc-sub">Your documents have been submitted and are being reviewed. You will be notified by email or SMS once verification is complete.</div>
        <a href="{{ route('dashboard') }}" class="kyc-btn"><i class="fas fa-home"></i> Go to Dashboard</a>

    @else
        {{-- ── Declined or not yet submitted — show form ── --}}
        @if(auth()->user()->idverification && auth()->user()->idverification->status === 3)
            <div class="kyc-icon kyc-icon--red" style="margin-bottom:20px;"><i class="fas fa-times"></i></div>
            <div class="kyc-form-head">
                <div class="kyc-form-title">Verification Declined — Please Resubmit</div>
                <div class="kyc-form-sub">Your previous submission was declined. Please ensure your documents are clear, valid, and legible before resubmitting.</div>
            </div>
        @else
            <div class="kyc-form-head">
                <div class="kyc-form-title">Verify Your Identity</div>
                <div class="kyc-form-sub">Please provide your details and upload valid photo ID documents to complete KYC verification.</div>
            </div>
        @endif

        <div class="kyc-notice">
            <i class="fas fa-info-circle"></i>
            <span>Please make sure all documents are clear and legible. Blurry or cropped images will delay your verification.</span>
        </div>

        <form action="{{ route('await') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="kyc-field">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" placeholder="Enter your full legal name" required>
                @error('fullname') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" placeholder="your@email.com" required>
                @error('email') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="address">Home Address</label>
                <input type="text" name="address" id="address" placeholder="Street address" required>
                @error('address') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="phone_no">Phone Number</label>
                <input type="text" name="phone_no" id="phone_no" placeholder="+234…" required>
                @error('phone_no') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="dob">Date of Birth</label>
                <input type="text" name="dob" id="dob" placeholder="DD/MM/YYYY" required>
                @error('dob') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="front">Valid ID — Front (image)</label>
                <input type="file" name="front" id="front" accept="image/*" required>
                @error('front') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="kyc-field">
                <label for="back">Valid ID — Back (image)</label>
                <input type="file" name="back" id="back" accept="image/*" required>
                @error('back') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="kyc-submit">
                <i class="fas fa-paper-plane"></i> Submit Documents
            </button>
        </form>
    @endif

    </div>
</div>
@endsection
