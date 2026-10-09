@extends('emails.layout')

@section('title', 'Order Complete — AlbertinaNG')

@section('header_title', 'Order Complete')

@php
    $statusLabel  = 'Completed';
    $statusBg     = '#e6f4ea';
    $statusColor  = '#2e7d32';
    $statusBorder = '#c8e6c9';
@endphp

@section('body')

    <p class="greeting">
        Hello {{ $order->user->name ?? 'there' }},<br><br>
        Your order <strong>{{ $order->order_number }}</strong> is now complete. We hope you're happy with your purchase!
    </p>

    <div class="items-title">Order Items</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        @foreach($order->items as $item)
            <tr class="item-row">
                {{-- Product thumbnail --}}
                <td width="64" valign="top" style="padding:12px 0;">
                    <img src="{{ $item->image_url }}"
                         alt="{{ $item->name }}"
                         width="56" height="56"
                         style="width:56px; height:56px; object-fit:contain; border:1px solid #dcefd0; border-radius:8px; background:#f7faf3; display:block;">
                </td>
                {{-- Name + qty --}}
                <td valign="top" style="padding:12px 0 12px 12px;">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-qty">Qty: {{ $item->quantity }}</div>
                    @if($item->installation_option)
                        <div class="item-installation">
                            {{ $item->installation_option }}@if($item->installation_extra_ngn > 0) (+₦{{ number_format($item->installation_extra_ngn, 0) }})@endif
                        </div>
                    @endif
                </td>
                {{-- Line price --}}
                <td valign="top" style="text-align:right; padding:12px 0;">
                    <span class="item-price">₦{{ number_format($item->price * $item->quantity, 0) }}</span>
                </td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    {{-- Download Invoice Button (Signed URL) --}}
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ URL::signedRoute('guest.orders.invoice.download', ['order' => $order->id]) }}" 
           style="background-color: #2e600d; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">
           Download Invoice
        </a>
    </div>

    <div class="help-box">
        <p>
            Enjoyed your purchase? We'd love to hear from you — leave a review on the product page to help other shoppers.
        </p>
    </div>

    <p class="greeting" style="margin-bottom:0;">
        Regards,<br>
        <strong>AlbertinaNG</strong>
    </p>

@endsection