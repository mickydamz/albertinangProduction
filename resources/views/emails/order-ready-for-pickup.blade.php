@extends('emails.layout')

@section('title', 'Ready for Pickup – AlbertinaNG')

{{-- Status pill colours for the meta bar (amber) --}}
@php
    $statusLabel  = 'Ready for Pickup';
    $statusBg     = '#fef9e7';
    $statusColor  = '#b45309';
    $statusBorder = '#fde68a';
@endphp

@section('header_icon')
    <svg width="60" height="60" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin:0 auto;">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M22 33 L29 40 L43 24" stroke="#abeb73" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection

@section('header_title', 'Your Order Is Ready! 🎉')
@section('body')

    <p class="greeting">
        Hi <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
        Your order has been prepared and is now ready for collection. Please head to the pickup
        point below at your convenience, and remember to bring a valid ID and your order number.
    </p>

    {{-- Progress: Confirmed → Processing → Ready for Pickup --}}
    @include('emails.partials.progress', ['stage' => 'ready'])

    {{-- PICKUP HERO --}}
    <div style="background:linear-gradient(135deg,#eef7e3 0%,#f7faf3 100%); border:2px solid #abeb73; border-radius:14px; padding:24px; margin:8px 0 28px; text-align:center;">
        <div style="font-size:32px; margin-bottom:10px;">📍</div>
        <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.4px; color:#5a8030; margin-bottom:8px;">Collect Your Order At</div>
        @if($order->pickup_point_name)
            <div style="font-size:19px; font-weight:800; color:#1a2410; margin-bottom:6px;">{{ $order->pickup_point_name }}</div>
            @if($order->pickup_point_address)
                <div style="font-size:13.5px; color:#4a6a30; line-height:1.6;">{{ $order->pickup_point_address }}</div>
            @endif
            @if($order->pickup_location)
                <div style="font-size:12.5px; color:#7a9a60; margin-top:4px;">{{ $order->pickup_location }}</div>
            @endif
        @elseif($order->pickup_location)
            <div style="font-size:19px; font-weight:800; color:#1a2410; margin-bottom:6px;">{{ $order->pickup_location }}</div>
            <div style="font-size:13.5px; color:#4a6a30; line-height:1.6;">Please contact us for the exact collection address.</div>
        @else
            <div style="font-size:19px; font-weight:800; color:#1a2410; margin-bottom:6px;">Our Store</div>
            <div style="font-size:13.5px; color:#4a6a30; line-height:1.6;">17-18 Zik's Avenue, Uwani, Enugu</div>
        @endif
    </div>

    {{-- What to bring --}}
    <div class="help-box">
        <p>
            <strong>✅ What to bring</strong><br>
            • Your order number: <strong>{{ $order->order_number }}</strong><br>
            • A valid government-issued photo ID<br>
            • This email (printed or on your phone)
        </p>
    </div>

    <div class="divider"></div>

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
            @if($order->hasCoupon())
            <tr>
                <td style="padding:4px 0; font-size:13px; color:#3d7018;">Discount ({{ $order->coupon_code_used }})</td>
                <td style="padding:4px 0; font-size:13px; color:#3d7018; text-align:right; font-weight:600;">
                    −₦{{ number_format($order->coupon_discount_ngn, 0) }}
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

    {{-- Pickup point + Payment --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td width="48%" valign="top" style="padding-right:8px;">
                <div class="info-block">
                    <div class="info-block-title">📍 Pickup Point</div>
                    <div class="info-block-content">
                        @if($order->pickup_point_name)
                            <strong>{{ $order->pickup_point_name }}</strong><br>
                            @if($order->pickup_point_address)
                                <span style="font-size:12px; color:#7a9a60;">{{ $order->pickup_point_address }}</span><br>
                            @endif
                            @if($order->pickup_location)
                                <span style="font-size:12px; color:#7a9a60;">{{ $order->pickup_location }}</span>
                            @endif
                        @elseif($order->pickup_location)
                            <strong>{{ $order->pickup_location }}</strong>
                        @else
                            <strong>Our Store</strong>
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

    {{-- Help --}}
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