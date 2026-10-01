@extends('emails.layout')

@section('title') {!! strip_tags($title ?? 'Notification') !!} — Albertina Nigeria @endsection

@section('header_title') {!! $title ?? 'Notification' !!} @endsection

@section('header_sub', 'An important message from Albertina Nigeria.')

@section('body')

    <div class="message-box">
        <pre style="font-size:14px; color:#1a2410; line-height:1.7; white-space:pre-wrap; word-wrap:break-word; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">{!! $messager !!}</pre>
    </div>

    <div class="help-box">
        <p>
            If you have any questions, please contact our support team.<br>
            📞 <a href="tel:+2348064066170">+234 806 406 6170</a> &nbsp;·&nbsp;
            📧 <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a>
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">Albertina Nigeria</strong>.
    </p>

@endsection
