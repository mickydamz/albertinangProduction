@extends('emails.layout')

@section('title', 'Order Shipped – Albertina Nigeria')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <text x="32" y="40" text-anchor="middle" font-size="28">🚚</text>
    </svg>
@endsection

@section('header_title', 'Your Order Is On Its Way! 🚚')

@section('header_sub')
    Great news — your order has been dispatched<br>
    and is heading your way right now.
@endsection

@php
    $statusLabel  = 'Shipped';
    $statusBg     = '#f0fdf4';
    $statusColor  = '#15803d';
    $statusBorder = '#86efac';
@endphp

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        Your order has left our facility and is on its way to you.
        Keep an eye out — delivery is just around the corner!
    </p>

    <div class="divider"></div>

    {{-- Items --}}
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

    <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:16px; font-weight:800; color:#1a2410;">Total</td>
                <td style="font-size:16px; font-weight:800; color:#2d7010; text-align:right;">₦{{ number_format($order->total, 0) }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td width="48%" valign="top" style="padding-right:8px;">
                <div class="info-block">
                    <div class="info-block-title">
                        @if($order->pickup_location) 📍 Pickup Location @else 🚚 Delivery @endif
                    </div>
                    <div class="info-block-content">
                        @if($order->pickup_location || $order->pickup_point_name)
                            @if($order->pickup_point_name)
                                <strong>{{ $order->pickup_point_name }}</strong><br>
                                @if($order->pickup_point_address)<span style="font-size:12px; color:#7a9a60;">{{ $order->pickup_point_address }}</span><br>@endif
                                @if($order->pickup_location)<span style="font-size:12px; color:#7a9a60;">{{ $order->pickup_location }}</span><br>@endif
                            @else
                                <strong>{{ $order->pickup_location }}</strong><br>
                            @endif
                            <span style="font-size:12px; color:#7a9a60;">Your order is ready for collection.</span>
                        @elseif($order->shipping_address)
                            <strong>{{ $order->shipping_address }}</strong><br>
                            @php $loc = collect([$order->delivery_location_name, $order->delivery_state_name])->filter()->implode(', '); @endphp
                            @if($loc)<span style="font-size:12px; color:#7a9a60;">{{ $loc }}</span><br>@endif
                            <span style="font-size:12px; color:#7a9a60;">Expected within 1–3 business days.</span>
                        @else
                            Your items are on the way to your address.<br>
                            <span style="font-size:12px; color:#7a9a60;">Expected within 1–3 business days.</span>
                        @endif
                    </div>
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top" style="padding-left:8px;">
                <div class="info-block">
                    <div class="info-block-title">💳 Payment</div>
                    <div class="info-block-content">
                        <strong>{{ ucfirst($order->payment_method ?? 'N/A') }}</strong><br>
                        <!-- @if($order->reference)
                            Ref: <span style="font-family:monospace; font-size:12px;">{{ $order->reference }}</span>
                        @endif -->
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="help-box">
        <p>
            <strong>Need help?</strong> Our team is available 24/7.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com">support@albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">Albertina Nigeria</strong>.<br>
        We look forward to serving you again.
    </p>

@endsection