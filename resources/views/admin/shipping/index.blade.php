@extends('layouts.adminlayout')
@section('content')
<h2>Delivery settings</h2><p>Enable delivery by state, then manage delivery areas and charges. State visibility is controlled on the States page. Delivery switches do not disable collection.</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<p><a class="btn btn-outline-primary" href="{{ route('admin.states.create') }}">Add state</a> <a href="{{ route('admin.shopping-locations.index') }}">Stores & collection points</a> · <a href="{{ route('admin.weight.index') }}">Delivery weights & truck rules</a></p>
<p>Blank charges mean unconfigured. Enter 0 only for deliberately free delivery. Selected active areas are offered automatically within the shopping state. No separate store coverage selection is required.</p>
<form method="POST" action="{{ route('admin.shipping.update') }}">@csrf @method('PUT')
@foreach($states as $state)<div class="card"><div class="card-body">
<h3>{{ $state->name }}</h3>
<input type="hidden" name="states[{{ $state->id }}]" value="0"><label><input type="checkbox" name="states[{{ $state->id }}]" value="1" @checked($state->delivery_enabled)> Delivery enabled in {{ $state->name }}</label>
<p><a href="{{ route('admin.locations.create',['state_id'=>$state->id,'return'=>route('admin.shipping.index')]) }}">Add delivery area</a> · <a href="{{ route('admin.states.edit',$state) }}">Edit state name</a></p>
<div class="table-responsive"><table class="table"><thead><tr><th>Delivery area</th><th>Enabled</th><th>Standard charge (₦)</th><th>Truck charge (₦)</th><th></th></tr></thead><tbody>
@forelse($grouped[$state->name] ?? [] as $area)<tr><td>{{ $area->name }}</td><td><input type="hidden" name="areas[{{ $area->id }}]" value="0"><input type="checkbox" aria-label="Enable delivery to {{ $area->name }}" name="areas[{{ $area->id }}]" value="1" @checked($area->is_active)></td>
<td><input class="form-control" type="number" min="0" max="99999999" step="0.01" aria-label="Standard charge for {{ $area->name }}" name="shipping[{{ $area->id }}]" value="{{ old('shipping.'.$area->id,$area->shipping_cost) }}" placeholder="Not configured"></td>
<td><input class="form-control" type="number" min="0" max="99999999" step="0.01" aria-label="Truck charge for {{ $area->name }}" name="truck_shipping[{{ $area->id }}]" value="{{ old('truck_shipping.'.$area->id,$area->truck_shipping_cost) }}" placeholder="Not configured"></td>
<td><a href="{{ route('admin.locations.edit',[$area,'return'=>route('admin.shipping.index')]) }}">Edit area</a></td></tr>
@empty<tr><td colspan="5">No delivery areas added.</td></tr>@endforelse
</tbody></table></div></div></div>@endforeach
<button class="btn btn-primary">Save delivery settings</button></form>
@endsection
