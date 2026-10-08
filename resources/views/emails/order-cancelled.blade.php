@extends('emails.layout')

@section('title', 'Order Cancelled – AlbertinaNG')

{{-- Override header gradient for the dark/neutral cancellation tone --}}
@push('styles')
/*<style>*/
/*    .email-header {*/
/*        background: linear-gradient(135deg, #1c1c1c 0%, #2d2d2d 60%, #3a3a3a 100%) !important;*/
/*    }*/
/*</style>*/
@endpush

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#fca5a5" stroke-width="2" fill="rgba(252,165,165,0.10)"/>
        <path d="M22 22L42 42M42 22L22 42" stroke="#fca5a5" stroke-width="3" stroke-linecap="round"/>
    </svg>
@endsection

@section('header_title', 'Order Cancelled')

@section('header_sub')
    Your order has been cancelled.<br>
    If this was a mistake, please contact us right away.
@endsection

@php
    $statusLabel  = 'Cancelled';
    $statusBg     = '#fef2f2';
    $statusColor  = '#dc2626';
    $statusBorder = '#fca5a5';
@endphp

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        We're sorry to let you know that your order has been cancelled.
        If you did not request this cancellation or have any concerns,
        please reach out to us immediately and we'll sort it out.
    </p>

    {{-- Cancelled items summary --}}
    <div class="items-title">Cancelled Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        @foreach($order->items as $item)
        <tr style="border-bottom:1px solid #eaf2e0; opacity:0.75;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;filter:grayscale(60%);"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name" style="text-decoration:line-through; color:#6b7280;">{{ $item->name }}</div>
                @if($item->sku)<div class="item-meta">SKU: {{ $item->sku }}</div>@endif
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div style="font-size:14px; font-weight:800; color:#6b7280;">₦{{ number_format($item->price * $item->quantity, 0) }}</div>
                <div class="item-qty">Qty: {{ $item->quantity }}</div>
            </td>
        </tr>
        @endforeach
    </table>

    <div style="background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:18px 20px; margin-top:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:14px; font-weight:700; color:#7f1d1d;">Order Total</td>
                <td style="font-size:14px; font-weight:700; color:#7f1d1d; text-align:right;">₦{{ number_format($order->total, 0) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:6px; font-size:12px; color:#dc2626;">
                    {{ (\App\Support\RefundProgress::forOrder($order)['message'] ?? 'Cancellation does not confirm a completed refund. We will notify you of any refund updates.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    {{-- Refund notice --}}
    <div class="info-block" style="margin-bottom:28px;">
        <div class="info-block-title">💳 Refund Information</div>
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:13px; color:#2d4a1a; padding:4px 0;">Payment Method</td>
                <td style="font-size:13px; font-weight:700; color:#1a2410; text-align:right;">{{ ucfirst($order->payment_method ?? 'N/A') }}</td>
            </tr>
            <!-- @if($order->reference)
            <tr>
                <td style="font-size:13px; color:#2d4a1a; padding:4px 0;">Reference</td>
                <td style="font-size:13px; font-weight:700; color:#1a2410; text-align:right; font-family:monospace;">{{ $order->reference }}</td>
            </tr>
            @endif -->
            <tr>
                <td colspan="2" style="padding-top:10px; font-size:12px; color:#7a9a60; border-top:1px solid #dcefd0; padding-top:10px;">
                    After the payment provider confirms processing, your bank may take up to 10 business days to credit the funds.
                </td>
            </tr>
        </table>
    </div>

    {{-- Shop again CTA --}}
    <div style="text-align:center; margin-bottom:28px;">
        <p style="font-size:13px; color:#4a6a30; margin-bottom:14px;">We'd love to have you back. Browse our store and find something you'll love.</p>
        <a href="{{ config('app.url') }}" style="display:inline-block; background:linear-gradient(135deg,#3d7018,#2d5610); color:#ffffff; font-size:14px; font-weight:700; padding:14px 36px; border-radius:8px; letter-spacing:0.2px;">
            Shop Again →
        </a>
    </div>

    <div style="background:#fff7f7; border-left:3px solid #fca5a5; border-radius:0 8px 8px 0; padding:14px 18px; margin-bottom:28px;">
        <p style="font-size:13px; color:#7f1d1d; line-height:1.6;">
            <strong>Didn't request this cancellation?</strong> Contact us immediately.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170" style="color:#dc2626; font-weight:600;">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com" style="color:#dc2626; font-weight:600;">support@albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        We're sorry for any inconvenience. <strong style="color:#2d5610;">AlbertinaNG</strong><br>
        is always here to make things right.
    </p>

@endsection