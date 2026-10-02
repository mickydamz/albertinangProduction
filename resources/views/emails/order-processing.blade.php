@extends('emails.layout')

@section('title', 'Order Processing – Albertina Nigeria')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M18 32 L26 26 L26 38 Z M30 26 L44 32 L30 38 Z" fill="#abeb73"/>
    </svg>
@endsection

@section('header_title', "We're Processing Your Order ⚙️")

@section('header_sub')
    Your order is now being prepared by our team.<br>
    @if($order->fulfillment_method === 'pickup') We'll notify you when it is ready for pickup. @else We'll notify you as soon as it ships. @endif
@endsection

@php
    $statusLabel  = 'Processing';
    $statusBg     = '#eff6ff';
    $statusColor  = '#1d4ed8';
    $statusBorder = '#bfdbfe';
@endphp

@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        Good news — our team has picked up your order and it's now being processed.
        @if($order->fulfillment_method === 'pickup') We'll send you another update when your order is ready for collection. @else Everything is on track and we'll send you another update once your item(s) are on their way. @endif
    </p>

    {{-- Order Items --}}
    <div class="items-title">Your Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        @foreach($order->items as $item)
        <tr class="item-row" style="border-bottom:1px solid #eaf2e0;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="{{ $item->image_url }}"
                     alt="{{ $item->name }}"
                     width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name">{{ $item->name }}</div>
                @if($item->sku)
                    <div class="item-meta">SKU: {{ $item->sku }}</div>
                @endif
                @if($item->installation_option)
                    <div style="font-size:11px; color:#5a8030; background:#eef5e6; border-radius:4px; padding:2px 6px; display:inline-block; margin-top:3px;">
                        + Installation included
                        @if($item->installation_extra_ngn > 0)
                            (₦{{ number_format($item->installation_extra_ngn, 0) }})
                        @endif
                    </div>
                @endif
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div class="item-price">₦{{ number_format($item->price * $item->quantity, 0) }}</div>
                <div class="item-qty">Qty: {{ $item->quantity }} × ₦{{ number_format($item->price, 0) }}</div>
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Total --}}
    <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30;">Subtotal</td>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30; text-align:right; font-weight:600;">
                    ₦{{ number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 0) }}
                </td>
            </tr>
            @php $installationTotal = $order->items->sum(fn($i) => $i->installation_extra_ngn ?? 0); @endphp
            @if($installationTotal > 0)
            <tr>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30;">Installation</td>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30; text-align:right; font-weight:600;">
                    ₦{{ number_format($installationTotal, 0) }}
                </td>
            </tr>
            @endif
            <tr><td colspan="2" style="padding:8px 0 0;"><hr style="border:none; border-top:1.5px solid #dcefd0; margin:0;"></td></tr>
            <tr>
                <td style="padding:10px 0 0; font-size:16px; font-weight:800; color:#1a2410;">Total</td>
                <td style="padding:10px 0 0; font-size:16px; font-weight:800; color:#2d7010; text-align:right;">
                    ₦{{ number_format($order->total, 0) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    {{-- Pickup / Delivery --}}
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
                            <span style="font-size:12px; color:#7a9a60;">Your order will be ready for collection soon.</span>
                        @elseif($order->shipping_address)
                            <strong>{{ $order->shipping_address }}</strong><br>
                            @php $loc = collect([$order->delivery_location_name, $order->delivery_state_name])->filter()->implode(', '); @endphp
                            @if($loc)<span style="font-size:12px; color:#7a9a60;">{{ $loc }}</span><br>@endif
                            <span style="font-size:12px; color:#7a9a60;">Tracking info will follow once shipped.</span>
                        @else
                            We'll deliver to your registered address.<br>
                            <span style="font-size:12px; color:#7a9a60;">Tracking info will follow once shipped.</span>
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