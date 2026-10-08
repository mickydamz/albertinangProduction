@extends('layouts.adminlayout')
@section('content')
<div class="container-fluid py-3">
<h1>Refunds</h1>
@include('admin.orders.request-tabs')
<p>Track money separately from delivery and collection. Checking status never issues another refund.</p>
<form method="get" class="d-flex gap-2 mb-3">
<select name="status" class="form-select" aria-label="Refund status">
@foreach(['all','requesting','pending','processing','processed','needs-attention','failed','unknown'] as $status)
<option value="{{ $status }}" @selected(request('status','all')===$status)>{{ $status==='all'?'All refunds':\App\Support\RefundProgress::forStatus($status)['label'] }}</option>
@endforeach
</select><button class="btn btn-primary">Filter</button></form>
<div class="table-responsive"><table class="table"><thead><tr><th>Order</th><th>Type</th><th>Amount</th><th>Refund progress</th><th>Paystack reference</th><th>Last updated / checked</th><th>Action</th></tr></thead><tbody>
@forelse($refunds as $refund)
<tr><td>{{ $refund->order_number }}</td><td>{{ ucfirst($refund->request_type) }}</td><td>₦{{ number_format($refund->amount/100,2) }}</td>
<td>{{ \App\Support\RefundProgress::forStatus($refund->status)['label'] }}<br><small>{{ $refund->last_error }}</small></td>
<td>{{ $refund->refund_id ?: 'Awaiting confirmation' }}</td><td>{{ $refund->updated_at }}<br><small>Checked: {{ $refund->last_checked_at ?: 'Not checked manually' }}</small></td>
<td><form method="post" action="{{ route('admin.refunds.check',$refund->order_id) }}">@csrf<button class="btn btn-outline-primary">Check Paystack status</button></form></td></tr>
@empty<tr><td colspan="7">No matching refunds.</td></tr>@endforelse
</tbody></table></div>{{ $refunds->links() }}
</div>
@endsection
