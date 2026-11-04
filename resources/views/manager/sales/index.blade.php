@extends('layouts.managerlayout')
@section('content')
<style>
#manager-sales { max-width:1400px; margin:0 auto; padding:0 12px; font-size:1rem; line-height:1.5; }
#manager-sales * { box-sizing:border-box; }
#manager-sales .sales-heading { margin-bottom:24px; }
#manager-sales .sales-heading p { max-width:850px; color:#586174; }
#manager-sales .sales-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin-bottom:20px; }
#manager-sales .sales-stat, #manager-sales .sales-panel { padding:20px; border:1px solid #e2e5e9; border-radius:12px; background:#fff; min-width:0; }
#manager-sales .sales-stat strong { display:block; font-size:1.55rem; color:#33571f; overflow-wrap:anywhere; }
#manager-sales .sales-filters { display:grid; grid-template-columns:2fr repeat(4,minmax(0,1fr)); gap:12px; align-items:end; }
#manager-sales label { display:block; font-weight:600; margin-bottom:6px; color:#384152; }
#manager-sales input, #manager-sales select { width:100%; min-width:0; min-height:44px; }
#manager-sales .sales-actions { display:flex; gap:12px; flex-wrap:wrap; margin:16px 0; }
#manager-sales .sales-note { color:#586174; font-size:.9rem; margin:12px 0 20px; }
#manager-sales .table-responsive { overflow-x:auto; }
#manager-sales table { width:100%; height:auto; font-size:.95rem; }
#manager-sales th, #manager-sales td { padding:14px 12px; vertical-align:top; white-space:normal; }
#manager-sales th { background:#f4f7f1; color:#384152; }
#manager-sales .sales-product { min-width:170px; overflow-wrap:anywhere; }
#manager-sales .sales-money { white-space:nowrap; }
#manager-sales .sales-sub { display:block; color:#586174; font-size:.85rem; margin-top:4px; }
#manager-sales .sales-status { display:inline-block; border-radius:6px; padding:4px 8px; background:#f1f3f6; color:#384152; }
#manager-sales .sales-empty { text-align:center; padding:36px 16px; }
@media(max-width:991px) { #manager-sales .sales-filters { grid-template-columns:repeat(2,minmax(0,1fr)); } #manager-sales .sales-search { grid-column:1 / -1; } }
@media(max-width:575px) { #manager-sales .sales-stats { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; } #manager-sales .sales-stat, #manager-sales .sales-panel { padding:14px; } #manager-sales .sales-filters { grid-template-columns:minmax(0,1fr); } }
</style>
<div id="manager-sales">
 <div class="sales-heading"><h2>Product sales</h2><p>Past and current orders containing products you currently manage, including orders handled by other staff. Each row shows one of your product lines.</p></div>
 @if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
 <div class="sales-stats">
  <div class="sales-stat"><strong>{{ number_format($summary->orders_count) }}</strong>Matching orders</div>
  <div class="sales-stat"><strong>{{ number_format($summary->current_orders) }}</strong>Current orders</div>
  <div class="sales-stat"><strong>{{ number_format($summary->confirmed_units) }}</strong>Confirmed units</div>
  <div class="sales-stat"><strong>₦{{ number_format($summary->confirmed_value,2) }}</strong>Confirmed product value</div>
 </div>
 <div class="sales-panel">
  <form method="get" action="{{ route('manager.sales.index') }}">
   <div class="sales-filters">
    <div class="sales-search"><label for="sales-search">Find a product or order</label><input id="sales-search" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Product name, SKU or order number" maxlength="150"></div>
    <div><label for="sales-brand">Brand</label><select id="sales-brand" class="form-select" name="brand"><option value="">All your brands</option>@foreach($brands as $brand)<option value="{{ $brand }}" @selected(($filters['brand'] ?? '') === $brand)>{{ $brand }}</option>@endforeach</select></div>
    <div><label for="sales-status">Order status</label><select id="sales-status" class="form-select" name="status"><option value="">All statuses</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach</select></div>
    <div><label for="sales-from">From</label><input id="sales-from" class="form-control" type="date" name="from" value="{{ $filters['from'] ?? '' }}"></div>
    <div><label for="sales-to">To</label><input id="sales-to" class="form-control" type="date" name="to" value="{{ $filters['to'] ?? '' }}"></div>
   </div>
   <div class="sales-actions"><button class="btn btn-primary" type="submit">Apply filters</button><a class="btn btn-outline-secondary" href="{{ route('manager.sales.index') }}">Clear filters</a></div>
  </form>
  <p class="sales-note">Totals follow your filters. Confirmed value uses the price recorded at purchase × quantity, before discounts, shipping and installation. It excludes unpaid, cancelled, refunded and failed orders; it is not net revenue. Current orders are pending, paid, processing, ready for pickup or shipped. Older records appear when they can be safely matched to your products.</p>
  <div class="table-responsive" tabindex="0" role="region" aria-label="Product sales history"><table class="table"><thead><tr><th>Order / date</th><th>Product</th><th>Quantity</th><th>Unit price</th><th>Product value</th><th>Status / payment</th></tr></thead><tbody>
   @forelse($sales as $sale)<tr>
    <td><a href="{{ route('manager.orders.show',$sale->order_id) }}">{{ $sale->order_number ?: 'Order #'.$sale->order_id }}</a><span class="sales-sub">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y H:i') }}</span></td>
    <td class="sales-product">{{ $sale->name }}<span class="sales-sub">{{ $sale->brand ?: 'No brand' }}@if($sale->sku) · {{ $sale->sku }}@endif</span>@if($sale->installation_option)<span class="sales-sub">Installation: {{ $sale->installation_option }} (₦{{ number_format($sale->installation_extra_ngn ?? 0,2) }} per unit)</span>@endif</td>
    <td>{{ number_format($sale->quantity) }}</td><td class="sales-money">₦{{ number_format($sale->price,2) }}</td><td class="sales-money">₦{{ number_format($sale->price * $sale->quantity,2) }}</td>
    <td><span class="sales-status">{{ ucwords(str_replace('_',' ',$sale->status)) }}</span><span class="sales-sub">{{ $sale->confirmed ? 'Payment confirmed' : (in_array($sale->status,['cancelled','refunded','failed']) ? 'Excluded from confirmed totals' : 'Awaiting payment verification') }}</span></td>
   </tr>@empty<tr><td class="sales-empty" colspan="6">No sales match these filters. Sales appear here when an order includes a product assigned to you.</td></tr>@endforelse
  </tbody></table></div>
  <div class="mt-2">{{ $sales->links() }}</div>
 </div>
</div>
@endsection
