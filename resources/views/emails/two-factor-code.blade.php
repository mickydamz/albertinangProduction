@extends('emails.layout')

@section('title', 'Verification Code — Albertina Nigeria')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M32 18 L42 23 L42 33 C42 39 37 44 32 46 C27 44 22 39 22 33 L22 23 Z" stroke="#abeb73" stroke-width="2.5" fill="none" stroke-linejoin="round"/>
        <circle cx="32" cy="32" r="3" fill="#abeb73"/>
    </svg>
@endsection

@section('header_title', 'Verification Code')

@section('header_sub')
    Use this code to complete your sign-in.<br>
    It expires in 10 minutes.
@endsection

@section('body')

    <p class="greeting">
        Hi <strong>{{ $user->name }}</strong>,<br><br>
        You requested a two-factor authentication code. Enter the code below to verify your identity and complete sign-in.
    </p>

    {{-- Code display --}}
    <div style="text-align:center; margin: 28px 0;">
        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.4px; color: #5a8030; margin-bottom: 12px;">Your Verification Code</div>
        <div style="display:inline-block; background: #f0fce8; border: 2px solid #abeb73; border-radius: 14px; padding: 20px 48px;">
            <span style="font-family: 'Courier New', monospace; font-size: 42px; font-weight: 900; letter-spacing: 10px; color: #1a2e0a; line-height: 1;">{{ $user->two_factor_code }}</span>
        </div>
    </div>

    <div class="help-box">
        <p>
            This code is valid for <strong>10 minutes</strong> from the time it was sent.
            If you did not attempt to sign in, please secure your account immediately by changing your password.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        If you didn't request this, you can safely ignore this email.<br>
        <strong style="color:#2d5610;">Albertina Nigeria</strong>
    </p>

@endsection
