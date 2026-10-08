@extends('emails.layout')

@section('title', 'Ready for Pickup — AlbertinaNG')

@php
    $statusLabel  = 'Ready for Pickup';
    $statusBg     = '#fef9e7';
    $statusColor  = '#b45309';
    $statusBorder = '#fde68a';
@endphp

@section('header_icon')
    <svg width="60" height="60" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin:0 auto;">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <text x="32" y="42" text-anchor="middle" font-size="28">📦</text>
    </svg>
@endsection

@section('header_title', 'Your Order Is Ready for Pickup! 📦')

@section('header_sub')
    Great news — your order has been packed<br>
    and is waiting for you at our store.
@endsection

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        Your order is packed and ready to collect. Simply visit our store,
        quote your order number, and our team will hand it over to you.
    </p>

    <div style="background:#fefce8; border:1px solid #fde68a; border-left:4px solid #f59e0b; border-radius:10px; padding:16px 20px; margin-bottom:28px;">
        <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#92400e; margin-bottom:6px;">📍 Pickup Details</div>
        <div style="font-size:13.5px; color:#78350f; line-height:1.7;">
            <strong>Location:</strong> {{ $order->pickup_location ?? "17-18 Zik's Avenue, Uwani, Enugu" }}<br>
            <strong>Order Number:</strong> {{ $order->order_number }}<br>
            <strong>Hours:</strong> Mon – Sat, 8:00 AM – 6:00 PM<br>
            <span style="font-size:12px; color:#92400e;">Please bring a valid ID or quote your order number when collecting.</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="items-title">Your Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        @foreach($order->items as $item)
        <tr style="border-bottom:1px solid #eaf2e0;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name">{{ $item->name }}</div>
                @if($item->sku)<div class="item-meta">SKU: {{ $item->sku }}</div>@endif
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div class="item-price">₦{{ number_format($item->price * $item->quantity, 0) }}</div>
                <div class="item-qty">Qty: {{ $item->quantity }}</div>
            </td>
        </tr>
        @endforeach
    </table>

    <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:20px; margin-bottom:28px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:16px; font-weight:800; color:#1a2410;">Total</td>
                <td style="font-size:16px; font-weight:800; color:#2d7010; text-align:right;">₦{{ number_format($order->total, 0) }}</td>
            </tr>
        </table>
    </div>

    <div class="help-box">
        <p>
            <strong>Need help?</strong> Our team is available 24/7.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170">+234 806 406 6170</a>
            or email <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">AlbertinaNG</strong>.<br>
        We look forward to seeing you soon!
    </p>

@endsection
