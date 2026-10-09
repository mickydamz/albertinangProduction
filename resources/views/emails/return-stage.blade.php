@extends('emails.layout')
@section('title', $label.' – AlbertinaNG')
@section('header_title', $label)
@section('body')
<p class="greeting">Hi <strong>{{ $return->user?->name ?? 'Valued Customer' }}</strong>,<br><br>
Here is the latest update for your return on <strong>Order #{{ $order->order_number }}</strong>.</p>
<div class="info-block" style="margin-bottom:24px;">
    <div class="info-block-title">Return details</div>
    <div class="info-block-content" style="white-space:pre-line;">{{ $stageMessage ?: 'You can view the latest return progress in My Orders.' }}</div>
</div>
@if(str_starts_with($label, 'Return approved'))
<p class="greeting">Please follow the instructions above to return the goods. We will notify you when we receive them and when inspection is completed.</p>
@endif
<p class="greeting">This update does not confirm a completed refund. Approval, receipt of goods and inspection are separate from your refund. Any Paystack refund progress will be confirmed in a separate update and shown in My Orders.</p>
<p style="text-align:center;margin-bottom:24px;"><a class="btn" href="{{ route('account.orders') }}">View your order and return progress</a></p>
<div class="help-box"><p><strong>Need help with your return?</strong><br>Email <a href="mailto:support@albertinang.com">support@albertinang.com</a> or call/WhatsApp <a href="tel:+2348064066170">+2348064066170</a>, quoting your order number.</p></div>
@endsection
