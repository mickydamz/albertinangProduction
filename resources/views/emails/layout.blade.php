<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $appStoreName ?? 'AlbertinaNG')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: #f0f4eb;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1a2410;
        }
        a { text-decoration: none; }
        img { border: 0; display: block; }

        .email-wrapper { width: 100%; background-color: #f0f4eb; padding: 32px 16px; }
        .email-container {
            max-width: 600px; margin: 0 auto; background: #ffffff;
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 24px rgba(22,46,5,0.10);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1a2e0a 0%, #2d5610 60%, #3d7018 100%);
            padding: 32px 40px 30px; text-align: center;
        }
        .brand-logo-img { height: 34px; width: auto; margin: 0 auto 18px; display: block; }
        .header-icon { margin-bottom: 14px; }
        .header-title { font-size: 25px; font-weight: 800; color: #ffffff; margin-bottom: 6px; }
        .header-sub { font-size: 14px; color: rgba(255,255,255,0.72); line-height: 1.5; }

        /* Meta bar */
        .meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #5a8030; margin-bottom: 4px; }
        .meta-value { font-size: 13.5px; font-weight: 700; color: #1a2410; }

        /* Body */
        .email-body { padding: 32px 40px; }
        .greeting { font-size: 15px; color: #2d4a1a; margin-bottom: 24px; line-height: 1.6; }
        .greeting strong { color: #1a2410; }

        /* Items */
        .items-title {
            font-size: 13px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: #5a8030; margin-bottom: 12px;
        }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .item-row { border-bottom: 1px solid #eaf2e0; }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-size: 13.5px; font-weight: 600; color: #1a2410; margin-bottom: 3px; }
        .item-meta { font-size: 11.5px; color: #7a9a60; }
        .item-price { font-size: 14px; font-weight: 800; color: #2d7010; }
        .item-qty { font-size: 11px; color: #7a9a60; margin-top: 2px; }
        .item-installation {
            font-size: 11px; color: #5a8030; background: #eef5e6;
            border-radius: 4px; padding: 2px 6px; display: inline-block; margin-top: 3px;
        }

        /* Info blocks */
        .info-block {
            background: #f7faf3; border: 1px solid #dcefd0;
            border-radius: 10px; padding: 16px;
        }
        .info-block-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.2px; color: #5a8030; margin-bottom: 8px;
        }
        .info-block-content { font-size: 13px; color: #2d4a1a; line-height: 1.6; }
        .info-block-content strong { color: #1a2410; font-weight: 700; }

        .divider { height: 1px; background: #eaf2e0; margin: 28px 0; }

        /* Info rows (used in contact / admin notification emails) */
        .section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #5a8030; margin-bottom: 12px; }
        .info-row { padding: 9px 0; border-bottom: 1px solid #eaf2e0; font-size: 13.5px; display: flex; gap: 12px; }
        .info-row:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-weight: 700; color: #2d4a1a; min-width: 90px; flex-shrink: 0; }
        .info-value { color: #1a2410; }
        .info-value a { color: #3d7018; font-weight: 600; }
        .message-box { background: #f7faf3; border: 1px solid #dcefd0; border-radius: 10px; padding: 20px; margin-bottom: 28px; }
        .message-text { font-size: 14px; color: #1a2410; line-height: 1.7; white-space: pre-wrap; }

        /* Help / callout box */
        .help-box {
            background: #f7faf3; border-left: 3px solid #abeb73;
            border-radius: 0 8px 8px 0; padding: 14px 18px; margin-bottom: 28px;
        }
        .help-box p { font-size: 13px; color: #4a6a30; line-height: 1.6; }
        .help-box a { color: #3d7018; font-weight: 600; }

        /* Footer */
        .email-footer { background: #1a2e0a; padding: 28px 40px; text-align: center; }
        .footer-brand { font-size: 16px; font-weight: 800; color: #ffffff; margin-bottom: 6px; }
        .footer-brand span { color: #abeb73; }
        .footer-tagline { font-size: 12px; color: rgba(255,255,255,0.5); margin-bottom: 16px; }
        .footer-links { margin-bottom: 16px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.6); margin: 0 10px; }
        .footer-copy { font-size: 11px; color: rgba(255,255,255,0.35); line-height: 1.6; }

        @media only screen and (max-width: 600px) {
            .email-body { padding: 24px 20px; }
            .email-header { padding: 28px 20px 24px; }
            .email-footer { padding: 24px 20px; }
            .header-title { font-size: 21px; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="email-wrapper">
<div class="email-container">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="email-header">
        {{-- Text branding renders reliably without remote image loading. --}}
        @php $emailStoreName = $appStoreName ?? 'AlbertinaNG'; @endphp
        <div style="font-size:24px;font-weight:700;color:#ffffff;margin:0 auto 18px;">{{ $emailStoreName }}</div>


        @hasSection('header_icon')
            <div class="header-icon">@yield('header_icon')</div>
        @endif

        <div class="header-title">@yield('header_title', $emailStoreName)</div>
        @hasSection('header_sub')
            <p class="header-sub">@yield('header_sub')</p>
        @endif
    </div>

    {{-- ══════════════ ORDER META BAR ══════════════ --}}
    @isset($order)
    @php
        $statusLabel  = $statusLabel  ?? ucwords(str_replace('_', ' ', $order->status));
        $statusBg     = $statusBg     ?? '#eff6ff';
        $statusColor  = $statusColor  ?? '#1d4ed8';
        $statusBorder = $statusBorder ?? '#bfdbfe';
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; background:#f7faf3; border-bottom:1px solid #dcefd0;">
        <tr>
            <td style="text-align:center; padding:16px 12px; border-right:1px solid #dcefd0;">
                <div class="meta-label">Order No.</div>
                <div class="meta-value">{{ $order->order_number }}</div>
            </td>
            <td style="text-align:center; padding:16px 12px; border-right:1px solid #dcefd0;">
                <div class="meta-label">Date</div>
                <div class="meta-value">{{ $order->created_at->format('d M Y') }}</div>
            </td>
            <td style="text-align:center; padding:16px 12px; border-right:1px solid #dcefd0;">
                <div class="meta-label">Payment</div>
                <div class="meta-value">{{ ucfirst($order->payment_method ?? 'N/A') }}</div>
            </td>
            <td style="text-align:center; padding:16px 12px;">
                <div class="meta-label">{{ $statusHeading ?? 'Status' }}</div>
                <div>
                    <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:700; background:{{ $statusBg }}; color:{{ $statusColor }}; border:1px solid {{ $statusBorder }};">
                        {{ $statusLabel }}
                    </span>
                </div>
            </td>
        </tr>
    </table>
    @endisset

    {{-- ══════════════ BODY ══════════════ --}}
    <div class="email-body">
        @yield('body')
    </div>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <div class="email-footer">
        <div class="footer-brand">{{ $emailStoreName }}</div>
        <div class="footer-tagline">Premium Electronics &amp; Home Appliances · Nigeria</div>
        <div class="footer-links">
            <a href="{{ config('app.url') }}">Shop</a>
            <a href="{{ config('app.url') }}/about">About</a>
            <a href="mailto:{{ $storeEmail ?? 'Info@Albertinang.com' }}">Support</a>
        </div>
        <div class="footer-copy">
            © {{ date('Y') }} {{ $emailStoreName }}. All rights reserved.<br>
            You're receiving this because you placed an order with us.
            @isset($order)
                <br>This email was sent to {{ $order->customer_email ?? $order->user?->email }}.
            @endisset
        </div>
    </div>

</div>
</div>
</body>
</html>