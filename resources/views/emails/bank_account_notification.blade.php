@extends('emails.layout')

@section('title') {{ $title ?? 'Payment Details' }} — AlbertinaNG @endsection

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <rect x="16" y="24" width="32" height="20" rx="3" stroke="#abeb73" stroke-width="2.5" fill="none"/>
        <path d="M16 20 L32 14 L48 20" stroke="#abeb73" stroke-width="2.5" stroke-linejoin="round" fill="none"/>
        <path d="M26 34h4M34 34h4" stroke="#abeb73" stroke-width="2" stroke-linecap="round"/>
    </svg>
@endsection

@section('header_title') {{ $title ?? 'Payment Details Generated' }} @endsection

@section('header_sub')
    Your payment details have been successfully generated.<br>
    Please complete payment within 24 hours.
@endsection

@section('body')

    <p class="greeting">
        We are pleased to inform you that your payment details have been successfully generated.
        Please review the information below and complete your payment promptly.
    </p>

    <div class="section-title">Payment Details</div>
    <div class="message-box">
        <pre style="font-size:14px; color:#1a2410; line-height:1.7; white-space:pre-wrap; word-wrap:break-word; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">{!! nl2br(e($details)) !!}</pre>
    </div>

    <div style="background:#fef9e7; border:1px solid #fde68a; border-left:4px solid #f59e0b; border-radius:10px; padding:16px 20px; margin-bottom:28px;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#92400e; margin-bottom:6px;">⚠️ Important Notice</div>
        <p style="font-size:13px; color:#78350f; line-height:1.6;">
            These payment details will <strong>expire after 24 hours</strong>. If payment is not completed within this timeframe, you will need to regenerate new payment details.
        </p>
    </div>

    <div class="help-box">
        <p>
            If you have any questions or need assistance, please contact our support team.<br>
            📞 <a href="tel:+2348064066170">+234 806 406 6170</a> &nbsp;·&nbsp;
            📧 <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a>
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">AlbertinaNG</strong>.
    </p>

@endsection
