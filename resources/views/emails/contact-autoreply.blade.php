@extends('emails.layout')

@section('title', 'We Got Your Message — AlbertinaNG')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M20 33L28 41L44 24" stroke="#abeb73" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection

@section('header_title', 'Message Received! ✅')

@section('header_sub')
    Thank you for reaching out to us.<br>
    We'll get back to you within 24 hours.
@endsection

@section('body')

    <p class="greeting">
        Hi <strong>{{ $name }}</strong>,<br><br>
        Thank you for contacting AlbertinaNG. We've received your message and a member of our team will respond to you as soon as possible — usually within 24 hours on business days.
    </p>

    <div class="section-title">Your Message</div>
    <div class="message-box">
        <p class="message-text">{{ $userMessage }}</p>
    </div>

    <div class="info-block" style="margin-bottom:24px;">
        <div class="info-block-title">What Happens Next</div>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">1</div>
                </td>
                <td style="padding:0 0 12px 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Message Received</strong> — Your enquiry has been forwarded to our support team.
                </td>
            </tr>
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">2</div>
                </td>
                <td style="padding:0 0 12px 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Review</strong> — Our team will review your message and prepare a response.
                </td>
            </tr>
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">3</div>
                </td>
                <td style="padding:0 0 0 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Response</strong> — We'll reply to <strong>{{ $email }}</strong> within 24 hours on business days.
                </td>
            </tr>
        </table>
    </div>

    <div class="help-box">
        <p>
            <strong>Need immediate help?</strong><br>
            📞 Call or WhatsApp: <a href="tel:+2348064066170">+234 806 406 6170</a> / <a href="tel:+2347037680738">+234 703 768 0738</a><br>
            📧 Email: <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a><br><br>
            <strong>Showroom hours:</strong> Mon–Sat 8AM–6PM &nbsp;·&nbsp; Sun 10AM–4PM
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">AlbertinaNG</strong>.<br>
        Premium Electronics &amp; Home Appliances.
    </p>

@endsection
