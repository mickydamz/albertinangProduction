@extends('emails.layout')

@section('title', 'Transaction Confirmation — Albertina Nigeria')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <rect x="18" y="22" width="28" height="20" rx="3" stroke="#abeb73" stroke-width="2.5" fill="none"/>
        <path d="M18 30h28" stroke="#abeb73" stroke-width="2.5"/>
        <path d="M24 38h6" stroke="#abeb73" stroke-width="2" stroke-linecap="round"/>
    </svg>
@endsection

@section('header_title', 'Transaction Confirmation')

@section('header_sub')
    Your transaction has been recorded.<br>
    Please review the details below.
@endsection

@section('body')

    <p class="greeting">
        Dear <strong>{{ $userName }}</strong>,<br><br>
        We are pleased to confirm that your transaction has been successfully processed. Please see the details below.
    </p>

    <div class="info-block" style="margin-bottom:28px;">
        <div class="info-block-title">Transaction Details</div>
        <div class="info-row">
            <span class="info-label">Username</span>
            <span class="info-value">{{ $userName }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email</span>
            <span class="info-value">{{ $userEmail }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment</span>
            <span class="info-value">
                @if($paymentMethod == 'bank') Full Payment
                @elseif($paymentMethod == 'Credit Card') Half Payment
                @elseif($paymentMethod == 'Crypto') Escrow
                @else {{ $paymentMethod }}
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Amount</span>
            <span class="info-value" style="font-weight:800; color:#2d7010;">₦{{ number_format($total_amount, 2) }}</span>
        </div>
        @if(!empty($country))
        <div class="info-row">
            <span class="info-label">Country</span>
            <span class="info-value">{{ $country }}</span>
        </div>
        @endif
        @if(!empty($cryptoCurrency))
        <div class="info-row">
            <span class="info-label">Cryptocurrency</span>
            <span class="info-value">{{ $cryptoCurrency }}</span>
        </div>
        @endif
    </div>

    <div class="help-box">
        <p>
            If you have any questions about this transaction, please contact our support team.<br>
            📞 <a href="tel:+2348064066170">+234 806 406 6170</a> &nbsp;·&nbsp;
            📧 <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a>
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">Albertina Nigeria</strong>.
    </p>

@endsection
