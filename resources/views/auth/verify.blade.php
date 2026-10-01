@extends('layouts.authlayout')

@section('title', 'Verify Your Email — Albertina Nigeria')

@section('content')

    <h4>Verify Your Email ✉️</h4>
    <p class="subtitle">Almost there! Check your inbox for a verification link to activate your account.</p>

    @if(session('resent'))
        <div class="alert alert-success">
            A fresh verification link has been sent to your email address.
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Info box --}}
    <div style="
        background: var(--g50);
        border: 1px solid var(--border2);
        border-radius: var(--radius);
        padding: 16px 18px;
        margin-bottom: 24px;
        font-size: 13.5px;
        color: var(--ink2);
        line-height: 1.6;
    ">
        <i class="fas fa-envelope-open-text" style="color:var(--g500); margin-right:8px;"></i>
        Before proceeding, please check your email for a verification link. Be sure to check your spam or junk folder if you don't see it.
    </div>

    {{-- Resend form --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary">Resend Verification Email</button>
    </form>

    <div class="auth-divider"><span>Done verifying?</span></div>

    <p class="auth-card__foot">
        <a href="{{ route('login') }}">← Back to sign in</a>
        &nbsp;·&nbsp;
        <a href="/logout" style="color:var(--ink3);">Sign out</a>
    </p>

@endsection