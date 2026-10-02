<section class="card p-3 my-3">
<h2>Request a refund</h2>
<p>Refunds are confirmed by the gateway. Keep the request reference for recovery.</p>
<form action="{{ route('admin.orders.refund', $order) }}" method="POST"
      data-refund-guard
      data-amount-selector="#refund_amount"
      data-gateway="{{ $order->payment_method }}"
      data-currency="₦"
      data-email="{{ $order->user->email ?? $order->customer_email ?? 'the customer' }}">
@csrf
<input type="hidden" name="request_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
<label for="refund_amount">Amount to refund (NGN)</label>
<input class="form-control" id="refund_amount" name="amount" type="number" min="0.01" step="0.01" max="{{ $order->total }}" required>
@error('amount')<p role="alert">{{ $message }}</p>@enderror
<button type="submit" class="btn btn-primary mt-2">Request refund</button>
</form>
@include('admin.partials.refund-confirm')
</section>
