@extends('emails.layout')

@section('title', 'Cancellation Request Update')

@section('header_title', 'Cancellation Not Approved')

@php
    // Override the meta bar status pill to a "rejected" amber/red treatment.
    $statusLabel  = 'Request Rejected';
    $statusBg     = '#fff0f0';
    $statusColor  = '#b91c1c';
    $statusBorder = '#fecaca';
@endphp

@section('body')
    <p class="greeting">
        Hello{{ optional($order->user)->name ? ' ' . $order->user->name : '' }},
    </p>

    <p class="greeting" style="margin-bottom:20px;">
        We’ve reviewed your request to cancel order
        <strong>#{{ $order->order_number }}</strong>, and after looking into it we’re
        unable to cancel it at this stage. Your order remains active and will be
        processed as normal.
    </p>

    @if(optional($order->cancellation)->admin_notes)
        <div class="info-block" style="margin-bottom:24px;">
            <div class="info-block-title">Note From Our Team</div>
            <div class="info-block-content">{{ $order->cancellation->admin_notes }}</div>
        </div>
    @endif

    <div class="help-box">
        <p>
            If you have questions or believe this was a mistake, just reply to this
            email or reach us at
            <a href="mailto:Info@Albertinang.com">Info@Albertinang.com</a> and we’ll
            be glad to help.
        </p>
    </div>
@endsection