@extends('layouts.adminlayout')
@section('content')
<div class="container py-4">
<h1>Payment recovery</h1>
<h2>Verified payments without orders</h2>
@foreach($checkouts as $checkout)
<p>{{ $checkout->reference }} — {{ $checkout->customer_email }} — {{ $checkout->recovery_error ?: 'Waiting for order creation' }}</p>
<form method="POST" action="{{ route('admin.payment-recover', $checkout) }}">@csrf<button type="submit">Verify payment and recover order</button></form>
@endforeach
{{ $checkouts->links() }}
<h2>Refunds requiring attention</h2>
@foreach($refunds as $refund)
<article class="card p-3 mb-2">
<p>Order {{ $refund->order_id }} — {{ $refund->request_key }} — {{ $refund->status }} — ₦{{ number_format($refund->amount, 2) }}</p>
<p>{{ $refund->failure_reason }}</p>
@if($refund->gateway_id)
<form method="POST" action="{{ route('admin.refunds.reconcile', $refund) }}">@csrf<button type="submit">Check gateway status</button></form>
@else
<p>Check the gateway dashboard with this reference before issuing another refund.</p>
@endif
</article>
@endforeach
</div>
@endsection
