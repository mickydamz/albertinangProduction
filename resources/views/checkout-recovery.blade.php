@extends('layouts.simslayout')
@section('content')
<main class="container py-5" style="max-width:720px">
    <h1>Payment progress</h1>
    <p>Reference: <strong>{{ $checkout->reference }}</strong></p>
    <ol aria-label="Payment progress">
        <li>{{ $checkout->payment_confirmed_at ? 'Payment confirmed' : 'Awaiting transfer or payment confirmation' }}</li>
        <li>{{ $order ? 'Order created: '.$order->order_number : 'Order creation pending' }}</li>
    </ol>
    @if(session('error'))<p role="alert">{{ session('error') }}</p>@endif
    @if(session('success'))<p role="status">{{ session('success') }}</p>@endif
    @if($order)
        <a href="{{ route('account.orders.invoice', $order) }}">View order invoice</a>
    @else
        <p>Confirmation can take a little longer. Keep this reference. Check again before starting another payment.</p>
        <form method="POST" action="{{ route('checkout.recover', $checkout) }}">@csrf<button type="submit" class="btn btn-primary">Check payment and recover order</button></form>
    @endif
    <p><a href="{{ route('account.orders') }}">Your orders</a></p>
</main>
@endsection
