@extends('emails.layout')

@section('title', 'New Contact Message — AlbertinaNG')

@section('header_icon')
    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="28" cy="28" r="27" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <rect x="14" y="18" width="28" height="20" rx="2" stroke="#abeb73" stroke-width="2" fill="none"/>
        <path d="M14 22l14 10 14-10" stroke="#abeb73" stroke-width="2" stroke-linecap="round"/>
    </svg>
@endsection

@section('header_title', 'New Contact Message 📬')

@section('header_sub')
    Someone submitted the contact form on Albertinang.com
@endsection

@section('body')

    <div class="section-title">Sender Details</div>
    <div class="info-block">
        <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value">{{ $name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value"><a href="mailto:{{ $email }}">{{ $email }}</a></span>
        </div>
        @if(!empty($phone))
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value"><a href="tel:{{ $phone }}">{{ $phone }}</a></span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Received</span>
            <span class="info-value">{{ now()->format('d M Y, g:i A') }}</span>
        </div>
    </div>

    <div class="section-title" style="margin-top:20px;">Message</div>
    <div class="message-box">
        <p class="message-text">{{ $userMessage }}</p>
    </div>

    <div style="text-align:center; margin-bottom:28px;">
        <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto;">
            <tr>
                <td align="center" bgcolor="#2d7010" style="border-radius:8px;">
                    <a href="mailto:{{ $email }}?subject=Re: Your enquiry – AlbertinaNG"
                       style="display:inline-block; padding:13px 32px; font-size:14px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px;">
                        Reply to {{ $name }} →
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <p style="font-size:12px; color:#7a9a60; text-align:center; line-height:1.6;">
        This message was submitted via the contact form on <strong>Albertinang.com</strong>.
    </p>

@endsection
