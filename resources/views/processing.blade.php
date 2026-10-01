@extends('layouts.simslayout')

@section('title', 'Processing')

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
    box-shadow: none;
}
.kyc-icon {
    width: 72px; height: 72px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 24px; font-size: 28px;
}
.kyc-icon--amber  { background: #fff8e1; border: 2px solid #f5a623; color: #f5a623; }
.kyc-icon--green  { background: #f0faf0; border: 2px solid var(--g500); color: var(--g600); }
.kyc-icon--red    { background: #fef2f2; border: 2px solid #dc2626; color: #dc2626; }
.kyc-title { font-size: 20px; font-weight: 800; color: var(--ink); margin-bottom: 10px; font-family: var(--fh); letter-spacing: -.2px; }
.kyc-sub   { font-size: 14px; color: var(--ink3); line-height: 1.65; margin-bottom: 28px; max-width: 340px; margin-left: auto; margin-right: auto; }
.kyc-bar-wrap { height: 8px; background: var(--surf3); border-radius: 4px; overflow: hidden; margin: 0 0 28px; }
.kyc-bar      { height: 100%; width: 0; background: var(--g500); border-radius: 4px; transition: width .4s ease; }
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
        <div class="kyc-icon kyc-icon--amber"><i class="fas fa-hourglass-half"></i></div>
        <div class="kyc-title">Processing…</div>
        <div class="kyc-sub">Please wait while we process your request. This should only take a moment.</div>
        <div class="kyc-bar-wrap"><div class="kyc-bar" id="kycBar"></div></div>
        <a href="{{ route('dashboard') }}" class="kyc-btn"><i class="fas fa-home"></i> Go to Dashboard</a>
    </div>
</div>
<script>
(function(){
    var bar = document.getElementById('kycBar'), pct = 0;
    var t = setInterval(function(){
        pct += 1; if (bar) bar.style.width = pct + '%';
        if (pct >= 100) clearInterval(t);
    }, 80);
})();
</script>
@if(isset($redirect) && $redirect)
<script>setTimeout(function(){ window.location.href = 'await-success'; }, 4000);</script>
@endif
@endsection
