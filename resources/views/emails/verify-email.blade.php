@extends('emails.layout')

@section('title', 'Verify Your Email — Albertina Nigeria')

@section('header_title', 'Verify Your Email')
@section('header_sub', 'One quick step to activate your account.')

@section('body')

    <p class="greeting">
        Hello{{ isset($notifiable->name) ? ' ' . $notifiable->name : '' }}!<br><br>
        Thanks for signing up with Albertina Nigeria. Please confirm your email address by clicking the button below.
    </p>

    {{-- Verify button --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:8px 0 28px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td align="center" bgcolor="#2d7010" style="border-radius:8px;">
                            <a href="{{ $url }}" target="_blank"
                               style="display:inline-block; padding:13px 36px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px;">
                                Verify Email Address
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="help-box">
        <p>
            If you did not create an account, no further action is required.
        </p>
    </div>

    <p class="greeting" style="margin-bottom:0;">
        Regards,<br>
        <strong>Albertina Nigeria</strong>
    </p>

    <div class="divider"></div>

    {{-- Fallback link --}}
    <p style="font-size:12px; color:#7a9a60; line-height:1.6;">
        If you&rsquo;re having trouble clicking the &ldquo;Verify Email Address&rdquo; button, copy and paste the URL below into your web browser:
    </p>
    <p style="font-size:12px; line-height:1.5; word-break:break-all; margin-top:6px;">
        <a href="{{ $url }}" target="_blank" style="color:#3d7018; text-decoration:underline;">{{ $url }}</a>
    </p>

@endsection