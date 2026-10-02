@extends('emails.layout')
@section('title', 'Order Cancelled — Albertina Nigeria')
@section('header_title', 'Order Cancelled')
@section('header_sub', 'Your order has been successfully cancelled.')
@section('body')
    <p class="greeting">
        Hi {{ $order->user->name ?? 'Customer' }},<br><br>
        Your cancellation request for <strong>Order #{{ $order->order_number }}</strong> has been processed and your order is now cancelled.
    </p>

    {{-- Cancellation summary --}}
    <div class="info-block" style="margin-bottom:24px;">
        <div class="info-block-title">Cancellation Details</div>
        <div class="info-block-content">
            <strong>Order:</strong> #{{ $order->order_number }}<br>
            <strong>Date placed:</strong> {{ $order->created_at->format('d M Y') }}<br>
            <strong>Order total:</strong> ₦{{ number_format($order->total, 2) }}<br>
            <strong>Reason:</strong> {{ $cancellation->reason }}
        </div>
    </div>

    @php

        $refundProgress = \App\Support\RefundProgress::forOrder($order);

    @endphp
    @if($refundProgress)
        <div class="info-block"><div class="info-block-title">{{ $refundProgress['label'] }}</div>
        <div class="info-block-content">{{ $refundProgress['message'] }}</div></div>
    @elseif($order->payment_method === 'paystack')
        <p class="greeting">Our team is reviewing whether a refund is due. No completed refund has been confirmed yet.</p>
    @endif

    {{-- View orders button --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:8px 0 24px;">
        <tr>
            <td align="center">
                <table cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                        <td align="center" bgcolor="#2d7010" style="border-radius:8px;">
                            <a href="{{ url('/account/orders') }}" target="_blank"
                               style="display:inline-block; padding:13px 36px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px;">
                                View My Orders
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="greeting" style="margin-bottom:0;">
        Thanks,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection