@extends('emails.layout')

@section('title', 'Order Refunded – Albertina Nigeria')

{{-- Override header gradient for the blue refund tone --}}
@push('styles')
<style>
    .email-header {
        background: linear-gradient(135deg, #1a3a1e 0%, #2d7010 60%, #3a8c14 100%) !important;
    }
</style>
@endpush

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#93c5fd" stroke-width="2" fill="rgba(147,197,253,0.12)"/>
        <path d="M20 28 L20 20 L36 20" stroke="#93c5fd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        <path d="M20 20 L28 12" stroke="#93c5fd" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        <path d="M20 28 C20 38 30 44 40 40" stroke="#93c5fd" stroke-width="2.5" stroke-linecap="round" fill="none"/>
        <circle cx="40" cy="40" r="3" fill="#93c5fd"/>
    </svg>
@endsection

@section('header_title', 'Refund Processed 💙')

@section('header_sub')
    Your refund has been successfully initiated.<br>
    The amount will reflect in your account shortly.
@endsection

@php
    $statusLabel  = 'Refunded';
    $statusBg     = '#eff6ff';
    $statusColor  = '#1d4ed8';
    $statusBorder = '#93c5fd';
@endphp

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        We've successfully processed a refund for your order. The full amount
        will be returned to your original payment method. Please allow
        3–5 business days for it to reflect depending on your bank.
    </p>

    {{-- Refund Summary Box --}}
    <div style="background:#eff6ff; border:1.5px solid #93c5fd; border-radius:12px; padding:24px; margin-bottom:28px; text-align:center;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#1d4ed8; margin-bottom:8px;">Refund Amount</div>
        <div style="font-size:36px; font-weight:800; color:#1e3a5f; margin-bottom:4px;">₦{{ number_format($order->total, 0) }}</div>
        @if($order->total_usd)
        <div style="font-size:12px; color:#3b82f6;">≈ ${{ number_format($order->total_usd, 2) }} USD</div>
        @endif
        <div style="margin-top:14px; padding-top:14px; border-top:1px solid #bfdbfe;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:12px; color:#1e3a5f; text-align:left;">Payment Method</td>
                    <td style="font-size:12px; font-weight:700; color:#1e3a5f; text-align:right;">{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
                </tr>
                <!-- @if($order->reference)
                <tr>
                    <td style="font-size:12px; color:#1e3a5f; text-align:left; padding-top:4px;">Reference</td>
                    <td style="font-size:12px; font-weight:700; color:#1e3a5f; text-align:right; font-family:monospace; padding-top:4px;">{{ $order->reference }}</td>
                </tr>
                @endif -->
                <tr>
                    <td style="font-size:12px; color:#1e3a5f; text-align:left; padding-top:4px;">Timeline</td>
                    <td style="font-size:12px; font-weight:700; color:#1e3a5f; text-align:right; padding-top:4px;">3–5 Business Days</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Refunded items --}}
    <div class="items-title">Refunded Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        @foreach($order->items as $item)
        <tr style="border-bottom:1px solid #eaf2e0;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;filter:grayscale(40%);"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name">{{ $item->name }}</div>
                @if($item->sku)<div class="item-meta">SKU: {{ $item->sku }}</div>@endif
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div style="font-size:14px; font-weight:800; color:#6b7280;">₦{{ number_format($item->price * $item->quantity, 0) }}</div>
                <div class="item-qty">Qty: {{ $item->quantity }}</div>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    {{-- What happens next --}}
    <div class="info-block" style="margin-bottom:28px;">
        <div class="info-block-title">What Happens Next</div>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">1</div>
                </td>
                <td style="padding:0 0 12px 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Refund Initiated</strong> — We've sent the refund to your payment provider today.
                </td>
            </tr>
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">2</div>
                </td>
                <td style="padding:0 0 12px 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Processing</strong> — Your bank or card provider processes the refund (3–5 business days).
                </td>
            </tr>
            <tr>
                <td style="width:28px; vertical-align:top; padding-top:2px;">
                    <div style="width:22px; height:22px; border-radius:50%; background:#3d7018; color:#fff; font-size:11px; font-weight:700; text-align:center; line-height:22px;">3</div>
                </td>
                <td style="padding:0 0 0 10px; font-size:13px; color:#2d4a1a; line-height:1.5;">
                    <strong>Funds Returned</strong> — The amount reflects in your account automatically.
                </td>
            </tr>
        </table>
    </div>

    {{-- Shop again CTA --}}
    <div style="text-align:center; margin-bottom:28px;">
        <p style="font-size:13px; color:#4a6a30; margin-bottom:14px;">We hope to see you again. Browse our store for great deals.</p>
        <a href="{{ config('app.url') }}" style="display:inline-block; background:linear-gradient(135deg,#3d7018,#2d5610); color:#ffffff; font-size:14px; font-weight:700; padding:14px 36px; border-radius:8px; letter-spacing:0.2px;">
            Shop Again →
        </a>
    </div>

    <div style="background:#eff6ff; border-left:3px solid #93c5fd; border-radius:0 8px 8px 0; padding:14px 18px; margin-bottom:28px;">
        <p style="font-size:13px; color:#1e3a5f; line-height:1.6;">
            <strong>Refund not received after 5 days?</strong> We'll look into it.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170" style="color:#1d4ed8; font-weight:600;">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com" style="color:#1d4ed8; font-weight:600;">support@albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for your patience. <strong style="color:#2d5610;">Albertina Nigeria</strong><br>
        is committed to making every experience right.
    </p>

@endsection