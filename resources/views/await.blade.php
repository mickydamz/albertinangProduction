@extends('layouts.simslayout')

@section('title', 'Documents Under Review')

@push('styles')
<style>
.kyc-wrap {
    min-height: 60vh;
    display: flex; align-items: center; justify-content: center;
    padding: 48px 16px;
    background: #f5f5f5;
}
.kyc-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 0;
    padding: 48px 40px 44px;
    max-width: 460px; width: 100%;
    text-align: center;
}
.kyc-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; font-size: 28px;
}
.kyc-icon--amber { background: #fff8e1; border: 2px solid #f5a623; color: #f5a623; }
.kyc-title { font-size: 20px; font-weight: 800; color: var(--ink); margin-bottom: 10px; font-family: var(--fh); letter-spacing: -.2px; }
.kyc-sub   { font-size: 14px; color: var(--ink3); line-height: 1.65; margin-bottom: 28px; max-width: 340px; margin-left: auto; margin-right: auto; }
.kyc-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--g500); color: #fff;
    padding: 12px 28px; border-radius: 8px;
    font-size: 14px; font-weight: 700; font-family: var(--fh);
    text-decoration: none; transition: background .2s; letter-spacing: -.1px;
}
.kyc-btn:hover { background: var(--g700); color: #fff; text-decoration: none; }
</style>
@endpush

@section('content')
<div class="kyc-wrap">
    <div class="kyc-card">
        <div class="kyc-icon kyc-icon--amber"><i class="fas fa-clock"></i></div>
        <div class="kyc-title">Documents Under Review</div>
        <div class="kyc-sub">
            Your KYC documents have been submitted and are currently being reviewed.
            You will receive a notification by email or SMS once your identity has been verified. Thank you for your patience.
        </div>
        <a href="{{ route('dashboard') }}" class="kyc-btn"><i class="fas fa-home"></i> Go to Dashboard</a>
    </div>
</div>
@endsection
