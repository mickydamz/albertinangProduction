@extends('emails.layout')

@section('title', 'New Cancellation Request — Albertina Nigeria')

@section('header_icon')
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#fca5a5" stroke-width="2" fill="rgba(252,165,165,0.10)"/>
        <path d="M32 20v14M32 38v2" stroke="#fca5a5" stroke-width="3" stroke-linecap="round"/>
    </svg>
@endsection

@section('header_title', 'New Cancellation Request')

@section('header_sub', 'A customer has requested to cancel their order.')

@section('body')

    <p class="greeting">
        A customer has submitted a cancellation request. Please review and take action.
    </p>

    <div class="info-block" style="margin-bottom:28px;">
        <div class="info-block-title">Order Details</div>
        <div class="info-row">
            <span class="info-label">Order</span>
            <span class="info-value" style="font-weight:700;">#{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer</span>
            <span class="info-value">{{ $order->user->name ?? 'Guest' }} &lt;{{ $order->user->email ?? 'N/A' }}&gt;</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date Placed</span>
            <span class="info-value">{{ $order->created_at->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Order Total</span>
            <span class="info-value" style="font-weight:800; color:#2d7010;">₦{{ number_format($order->total, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Reason</span>
            <span class="info-value">{{ $cancellation->reason }}</span>
        </div>
    </div>

    <div style="text-align:center; margin-bottom:28px;">
        <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:0 auto;">
            <tr>
                <td align="center" style="background:linear-gradient(135deg,#dc2626,#b91c1c); border-radius:8px;">
                    <a href="{{ url('/admin/orders/' . $order->id) }}" target="_blank"
                       style="display:inline-block; padding:13px 36px; font-size:15px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px;">
                        Review Order →
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <div class="help-box">
        <p>
            Please review the cancellation request and approve or reject it from the admin panel.
            If you approve, any applicable refund will be processed automatically.
        </p>
    </div>

@endsection
