@extends('layouts.managerlayout')
@section('content')
<div class="container-xxl"><div class="d-flex justify-content-between mb-2"><h2>Add order</h2><a href="{{ route('manager.orders.index') }}" class="btn btn-outline-secondary">Back to orders</a></div>
@include('manager.orders.alerts')
<div class="alert alert-info p-2">Orders start pending payment. Catalogue prices and delivery charges are calculated automatically. Stock is checked again before processing.</div>
@if($products->isEmpty())<div class="card"><div class="card-body">No active products are assigned to you. Ask admin to assign products before creating an order.</div></div>@else
<form method="post" action="{{ route('manager.orders.store') }}">@csrf
<div class="card"><div class="card-body"><h4>Customer and payment</h4><div class="row g-2">
<div class="col-md-6"><label class="form-label" for="customer-email">Customer email</label><input id="customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}" required class="form-control"><small>Use the customer's exact email; an existing customer account is linked automatically.</small></div>
<div class="col-md-6"><label class="form-label" for="delivery-mobile">Collection / delivery number</label><input id="delivery-mobile" name="delivery_mobile" value="{{ old('delivery_mobile') }}" required class="form-control" placeholder="08012345678"><small>Nigerian mobile numbers only.</small></div>
<div class="col-md-6"><label class="form-label" for="payment-method">Payment method</label><select id="payment-method" name="payment_method" class="form-select">@foreach(['cash'=>'Cash','pos'=>'POS','bank_transfer'=>'Bank transfer'] as $key=>$label)<option value="{{ $key }}" @selected(old('payment_method')===$key)>{{ $label }}</option>@endforeach</select><small>Admin verifies the receipt or transfer before fulfilment.</small></div>
</div></div></div>
<div class="card"><div class="card-body"><h4>Order items</h4><div id="order-items"></div><button type="button" id="add-item" class="btn btn-outline-primary">Add another item</button><p class="mt-1 mb-0">Prices include current catalogue markups and discounts. Custom discounts and coupons can be handled by admin in this release.</p></div></div>
<div class="card"><div class="card-body"><h4>Delivery or collection</h4><div class="row g-2">
<div class="col-md-4"><label for="fulfilment" class="form-label">Method</label><select id="fulfilment" name="fulfillment_method" class="form-select"><option value="pickup" @selected(old('fulfillment_method')==='pickup')>Pickup</option><option value="delivery" @selected(old('fulfillment_method')==='delivery')>Delivery</option></select></div>
<div class="col-md-8" id="pickup-fields"><label for="pickup-point" class="form-label">Pickup point</label><select id="pickup-point" name="pickup_point_id" class="form-select"><option value="">Choose pickup point</option>@foreach($points as $p)<option value="{{ $p->id }}" @selected(old('pickup_point_id')==$p->id)>{{ $p->name }} — {{ $p->location?->name }}</option>@endforeach</select></div>
<div class="col-md-8" id="delivery-fields"><label for="delivery-location" class="form-label">Delivery location</label><select id="delivery-location" name="delivery_location_id" class="form-select"><option value="">Choose delivery location</option>@foreach($locations as $l)<option value="{{ $l->id }}" @selected(old('delivery_location_id')==$l->id)>{{ $l->state?->name }} — {{ $l->name }} (₦{{ number_format($l->shipping_cost) }} / truck ₦{{ number_format($l->truck_shipping_cost) }})</option>@endforeach</select><label for="shipping-address" class="form-label mt-1">Delivery address</label><textarea id="shipping-address" class="form-control" name="shipping_address" maxlength="500">{{ old('shipping_address') }}</textarea></div>
<div class="col-12"><label for="notes" class="form-label">Internal note (optional)</label><textarea id="notes" class="form-control" name="notes" maxlength="1000">{{ old('notes') }}</textarea></div>
</div><button class="btn btn-primary mt-2">Create pending order</button></div></div>
<label>Dispatch store (required for delivery)</label><select name="dispatch_point_id" class="form-select"><option value="">Choose dispatch store</option>@foreach($points as $point)<option value="{{ $point->id }}">{{ $point->state?->name }} → {{ $point->name }} — {{ $point->address }}</option>@endforeach</select></form>
<template id="item-template"><div class="row g-1 mb-2 border-bottom pb-2 item-row"><div class="col-md-6"><label class="form-label">Product</label><select class="form-select product-select" required><option value="">Choose assigned product</option>@foreach($products as $p)<option value="{{ $p->id }}" data-options="{{ json_encode($p->installation_options??[]) }}">{{ $p->name }} — ₦{{ number_format($p->sell_price,2) }} (stock {{ $p->stock }})</option>@endforeach</select></div><div class="col-md-2"><label class="form-label">Quantity</label><input class="form-control item-qty" type="number" min="1" max="1000" value="1" required></div><div class="col-md-3"><label class="form-label">Installation</label><select class="form-select item-installation"><option value="">No installation</option></select></div><div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-outline-danger remove-item" aria-label="Remove item">×</button></div></div></template>
@endif</div>
@endsection
@push('scripts')
<script>
(function(){
const list=document.getElementById('order-items'); if(!list)return; let next=0;
function add(data={}){const row=document.getElementById('item-template').content.firstElementChild.cloneNode(true);const i=next++;const product=row.querySelector('.product-select'),qty=row.querySelector('.item-qty'),install=row.querySelector('.item-installation');
product.name=`items[${i}][product_id]`;qty.name=`items[${i}][quantity]`;install.name=`items[${i}][installation_option]`;
for(const [el,key] of [[product,'product'],[qty,'quantity'],[install,'installation']]){el.id=`${key}-${i}`;el.previousElementSibling.htmlFor=el.id;}
function options(){install.replaceChildren(new Option('No installation',''));try{for(const o of JSON.parse(product.selectedOptions[0]?.dataset.options||'[]'))if(o.label)install.add(new Option(`${o.label} (+₦${Number(o.price||0).toLocaleString()})`,o.label));}catch(e){}}
product.addEventListener('change',options);row.querySelector('.remove-item').addEventListener('click',()=>{if(list.children.length>1)row.remove();});list.append(row);product.value=data.product_id||'';qty.value=data.quantity||1;options();install.value=data.installation_option||'';}
const previous=@json(array_values(old('items',[]))); if(previous.length)previous.forEach(add);else add();document.getElementById('add-item').addEventListener('click',()=>add());
const method=document.getElementById('fulfilment');function toggle(){const pickup=method.value==='pickup';document.getElementById('pickup-fields').hidden=!pickup;document.getElementById('delivery-fields').hidden=pickup;document.getElementById('pickup-point').disabled=!pickup;document.getElementById('delivery-location').disabled=pickup;document.getElementById('shipping-address').disabled=pickup;}method.addEventListener('change',toggle);toggle();
})();
</script>
@endpush
