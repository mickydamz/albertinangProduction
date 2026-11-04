@extends('layouts.adminlayout')
@section('content')
<h2>Stores & collection points</h2>
<p>Each store belongs to a state and is a collection point. Assign products to stores; delivery areas and charges are managed separately.</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<p><a href="{{ route('admin.pickup-points.create') }}" class="btn btn-primary">Add a store</a> <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline-primary">Delivery settings</a></p>
@php($activeTab = request('tab') === 'transfers' ? 'transfers' : 'stores')
<nav class="nav nav-tabs mb-3" aria-label="Store administration">
<a class="nav-link {{ $activeTab === 'stores' ? 'active' : '' }}" href="{{ route('admin.shopping-locations.index') }}" @if($activeTab === 'stores') aria-current="page" @endif>Stores &amp; collection points <span class="badge bg-secondary">{{ $points->count() }}</span></a>
<a class="nav-link {{ $activeTab === 'transfers' ? 'active' : '' }}" href="{{ route('admin.shopping-locations.index',['tab'=>'transfers']) }}" @if($activeTab === 'transfers') aria-current="page" @endif>Collection transfer prices <span class="badge bg-secondary">{{ $rates->count() }}</span></a>
<a class="nav-link" href="{{ route('admin.store-locations.index') }}">Store locator entries</a>
</nav>
@if($activeTab === 'stores')
@if($unassigned->count())<div class="alert alert-warning"><strong>{{ $unassigned->count() }} published products need store assignments.</strong><ul>@foreach($unassigned as $item)<li><a href="{{ route('admin.products.edit',$item) }}">{{ $item->name }}</a></li>@endforeach</ul></div>@endif
@foreach($points as $point)
<details class="card mb-2"><summary class="card-header" style="cursor:pointer;padding:16px;font-weight:600">{{ $point->name }} — {{ $point->state?->name ?? 'State needs review' }} <small>({{ $point->is_active ? 'Active' : 'Inactive' }})</small></summary><div class="card-body">
<p>{{ \Illuminate\Support\Facades\DB::table('product_pickup_point')->where('pickup_point_id',$point->id)->count() }} assigned products. Local collection is free; transfers are priced per unit. Existing orders keep their original details.</p>
<form method="POST" action="{{ route('admin.shopping-locations.update',$point) }}">@csrf @method('PUT')
<label class="form-label" for="store-name-{{ $point->id }}">Store name</label><input id="store-name-{{ $point->id }}" class="form-control mb-1" name="name" value="{{ $point->name }}" required maxlength="255">
<label class="form-label" for="store-state-{{ $point->id }}">State</label><select id="store-state-{{ $point->id }}" name="state_id" class="form-select mb-1" required><option value="">Choose state</option>@foreach(\App\Models\State::orderBy('name')->get() as $state)<option value="{{ $state->id }}" @selected($point->state_id == $state->id)>{{ $state->name }}</option>@endforeach</select>
<label class="form-label" for="store-address-{{ $point->id }}">Collection / dispatch address</label><input id="store-address-{{ $point->id }}" class="form-control mb-1" name="address" value="{{ $point->address }}" required maxlength="500">
<label class="form-label" for="store-hours-{{ $point->id }}">Opening hours</label><input id="store-hours-{{ $point->id }}" class="form-control mb-1" name="hours" value="{{ $point->hours }}" maxlength="255">
<label class="form-label" for="store-status-{{ $point->id }}">Store status</label><select id="store-status-{{ $point->id }}" class="form-select mb-1" name="is_active"><option value="1" @selected($point->is_active)>Active</option><option value="0" @selected(!$point->is_active)>Inactive</option></select>
<input type="hidden" name="store_location_id" value="{{ $point->store_location_id }}">
<p>Delivery areas are selected once per state in <a href="{{ route('admin.shipping.index') }}">Delivery settings</a> and apply automatically to stores in that state.</p><button class="btn btn-primary mt-1">Save store</button></form></div></details>
@endforeach
@else
<div class="card card-body"><h3>Collection transfer prices</h3><p>Charged per unit, including repeat quantities. Direction matters. Product override takes precedence over the route default. Missing routes require a quote and block payment.</p>
<form method="POST" action="{{ route('admin.collection-transfer-rates.store') }}">@csrf
<label>Supplying point<select name="origin_id" required>@foreach($points as $p)<option value="{{ $p->id }}">{{ $p->state?->name }} — {{ $p->name }}</option>@endforeach</select></label>
<label>Collection destination<select name="destination_id" required>@foreach($points as $p)<option value="{{ $p->id }}">{{ $p->state?->name }} — {{ $p->name }}</option>@endforeach</select></label>
<label>Product override (optional)<select name="product_id"><option value="">Route default for all products</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></label>
<label>NGN per unit<input name="fee_ngn" type="number" min="0" step="0.01" required></label><button class="btn btn-primary">Save price</button></form>
<div class="table-responsive"><table class="table"><thead><tr><th>From</th><th>To</th><th>Product</th><th>NGN per unit</th><th>Actions</th></tr></thead><tbody>
@forelse($rates as $rate)<tr>
<td>{{ $points->firstWhere('id',$rate->origin_id)?->name }}</td><td>{{ $points->firstWhere('id',$rate->destination_id)?->name }}</td>
<td>{{ $rate->product_id ? ($products->firstWhere('id',$rate->product_id)?->name ?? 'Product '.$rate->product_id) : 'Route default' }}</td>
<td><form id="rate-{{ $rate->id }}" method="POST" action="{{ route('admin.collection-transfer-rates.update',$rate->id) }}">@csrf @method('PUT')<label class="visually-hidden" for="fee-{{ $rate->id }}">Price per unit</label><input class="form-control" style="min-width:110px" id="fee-{{ $rate->id }}" name="fee_ngn" type="number" min="0" max="9999999999.99" step="0.01" value="{{ $rate->fee_ngn }}" required></form></td>
<td><div style="display:flex;gap:8px;flex-wrap:wrap"><button form="rate-{{ $rate->id }}" class="btn btn-primary btn-sm">Save changes</button><form method="POST" action="{{ route('admin.collection-transfer-rates.destroy',$rate->id) }}" onsubmit="return confirm('Delete this transfer price? Routes without a default or product price will require a quote. Existing orders keep their charges.');">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Delete</button></form></div></td></tr>
@empty<tr><td colspan="5">No transfer prices configured.</td></tr>@endforelse
</tbody></table></div></div>
@endif
@endsection
