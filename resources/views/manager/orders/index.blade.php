@extends('layouts.managerlayout')
@section('content')
<div class="container-xxl">
 <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"><div><h2>Order workspace</h2><p class="text-muted mb-0">Manage orders containing your products, including mixed-brand orders, and orders assigned to you.</p></div><a href="{{ route('manager.orders.create') }}" class="btn btn-primary">Add order</a></div>
 @include('manager.orders.alerts')
 <div class="row">@foreach($stats as $label=>$count)<div class="col-6 col-md-3 col-xl mb-2"><a class="card h-100 text-reset" href="{{ route('manager.orders.index',['status'=>$label]) }}"><div class="card-body"><strong class="d-block h3">{{ $count }}</strong>{{ ucwords(str_replace('_',' ',$label)) }}</div></a></div>@endforeach</div>
 <div class="card"><div class="card-body">
 <form class="row g-1 mb-2" method="get"><div class="col-md-7"><label class="form-label" for="order-search">Find order</label><input id="order-search" class="form-control" name="search" value="{{ $search }}" placeholder="Order number, customer email or mobile"></div><div class="col-md-3"><label class="form-label" for="order-status">Status</label><select id="order-status" class="form-select" name="status"><option value="">All statuses</option>@foreach(array_keys($stats) as $s)<option value="{{ $s }}" @selected($status===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach<option value="cancelled" @selected($status==='cancelled')>Cancelled</option><option value="refunded" @selected($status==='refunded')>Refunded</option></select></div><div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Search</button></div></form>
 <div class="table-responsive"><table class="table" style="height:auto"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Fulfilment</th><th>Status</th></tr></thead><tbody>
 @forelse($orders as $order)<tr><td><a href="{{ route('manager.orders.show',$order) }}">{{ $order->order_number ?: 'Order #'.$order->id }}</a><small class="d-block text-muted">{{ $order->created_at->format('d M Y H:i') }}</small></td><td>{{ $order->customer_email ?: $order->user?->email ?: 'Not recorded' }}<small class="d-block">{{ $order->delivery_mobile }}</small></td><td>₦{{ number_format($order->total,2) }}</td><td>{{ $order->paymentConfirmed()?'Confirmed':'Awaiting verification' }}</td><td>{{ $order->fulfillment_method ? ucfirst($order->fulfillment_method) : 'Not recorded' }}</td><td>{{ ucwords(str_replace('_',' ',$order->status)) }}</td></tr>
 @empty<tr><td colspan="6">No matching orders. Orders containing your products appear automatically; admin can also assign orders to you.</td></tr>@endforelse
 </tbody></table></div>{{ $orders->links() }}
 </div></div><p class="text-muted">You can confirm offline payments, process fulfilment and review cancellations and returns. Updates apply to the whole order, including mixed-brand orders.</p>
</div>
@endsection
