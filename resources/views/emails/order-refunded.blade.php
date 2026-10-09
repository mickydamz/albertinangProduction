@extends('emails.layout')
@php
    $refundProgress = $refundProgress ?? \App\Support\RefundProgress::forOrder($order) ?? \App\Support\RefundProgress::forStatus('unknown');
    $statusLabel = $refundProgress['label'];
    $statusHeading = 'Refund status';
@endphp
@section('title', $refundProgress['label'].' – AlbertinaNG')
@section('header_title', $refundProgress['label'])
@section('body')
<p class="greeting">Hi {{ $order->user?->name ?? 'Customer' }},</p>
<div class="info-block">
    <div class="info-block-title">Refund details</div>
    <div class="info-block-content">
        <strong>Order:</strong> #{{ $order->order_number }}<br>
        <strong>Refund amount:</strong> ₦{{ number_format($order->total, 2) }}<br>
        {{ $refundProgress['message'] }}
    </div>
</div>
<p class="greeting">This update describes your refund separately from your cancellation or return. You can check its latest status on your orders page.</p>
<p style="text-align:center"><a class="btn" href="{{ url('/account/orders') }}">View My Orders</a></p>
<p class="greeting">If your bank has not credited your account within 10 business days after processing, contact our support team with your order number.</p>
@endsection
