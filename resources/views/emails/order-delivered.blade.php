@extends('emails.layout')

@section('title', 'Order Delivered – AlbertinaNG')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M20 33L28 41L44 24" stroke="#abeb73" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection

@section('header_title', 'Order Delivered! 📦')

@section('header_sub')
    Your order has arrived. We hope you love it!<br>
    Thank you for shopping with AlbertinaNG.
@endsection

@php
    $statusLabel  = 'Delivered';
    $statusBg     = '#eef5e6';
    $statusColor  = '#3d7018';
    $statusBorder = '#c0e0a0';
@endphp

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        Your order has been successfully delivered. We hope everything arrived
        in perfect condition and meets your expectations!
    </p>

    <div class="divider"></div>

    {{-- Items --}}
    <div class="items-title">Items Delivered</div>
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
                <td style="font-size:16px; font-weight:800; color:#1a2410;">Total Paid</td>
                <td style="font-size:16px; font-weight:800; color:#2d7010; text-align:right;">₦{{ number_format($order->total, 0) }}</td>
            </tr>
        </table>
    </div>

    {{-- Where it went — helps the customer's records and any follow-up --}}
    @if($order->pickup_location || $order->pickup_point_name)
        <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:16px;">
            <div style="font-size:13px; font-weight:700; color:#1a2410; margin-bottom:6px;">📍 Collected From (Pickup)</div>
            <div style="font-size:13px; color:#4a6a30; line-height:1.6;">
                @if($order->pickup_point_name)
                    <strong>{{ $order->pickup_point_name }}</strong><br>
                    @if($order->pickup_point_address){{ $order->pickup_point_address }}<br>@endif
                    @if($order->pickup_location)<span style="color:#7a9a60;">{{ $order->pickup_location }}</span>@endif
                @else
                    {{ $order->pickup_location }}
                @endif
            </div>
        </div>
    @elseif($order->shipping_address)
        <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:16px;">
            <div style="font-size:13px; font-weight:700; color:#1a2410; margin-bottom:6px;">🚚 Delivered To</div>
            <div style="font-size:13px; color:#4a6a30; line-height:1.6;">
                {{ $order->shipping_address }}
                @php $loc = collect([$order->delivery_location_name, $order->delivery_state_name])->filter()->implode(', '); @endphp
                @if($loc)<br>{{ $loc }}@endif
            </div>
        </div>
    @endif

    <div class="divider"></div>

    {{-- Review nudge --}}
    <!-- <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:20px; text-align:center; margin-bottom:28px;">
        <div style="font-size:22px; margin-bottom:8px;">⭐⭐⭐⭐⭐</div>
        <div style="font-size:14px; font-weight:700; color:#1a2410; margin-bottom:6px;">Enjoying your purchase?</div>
        <p style="font-size:13px; color:#4a6a30; line-height:1.6;">
            We'd love to hear your feedback. Share your experience and help other customers make informed decisions.
        </p>
    </div> -->

    <div class="help-box">
        <p>
            <strong>Something not right?</strong> We'll make it good.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com">support@albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">AlbertinaNG</strong>.<br>
        We look forward to serving you again.
    </p>

@endsection