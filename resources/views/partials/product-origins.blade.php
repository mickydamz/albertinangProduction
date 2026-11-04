@php
 $originPoints=\App\Models\PickupPoint::with('state')->orderBy('name')->get();
 $selectedOrigins=old('pickup_points',isset($defaults) ? ($defaults['pickup_points'] ?? []) : (isset($product) && $product->exists ? $product->pickupPoints()->pluck('pickup_points.id')->all() : []));
@endphp
<div class="form-group" style="margin:18px 0;padding:15px;background:#f4f8ee;border:1px solid #d4e2c9;border-radius:8px">
 <label class="form-label"><strong>Available at stores</strong></label>
 <p style="font-size:13px">Select the stores that hold or fulfil this product. Delivery areas are managed separately. Customers only see goods assigned to their selected store. Quantity does not control availability.</p>
 <input type="hidden" name="origins_present" value="1">
 @foreach($originPoints as $origin)
 <label style="display:block;padding:5px 0"><input type="checkbox" name="pickup_points[]" value="{{ $origin->id }}" {{ in_array($origin->id,$selectedOrigins) ? 'checked' : '' }}> {{ $origin->state?->name ?? 'State not assigned' }} → {{ $origin->name }} — {{ $origin->address }}{{ !$origin->is_active ? ' (inactive)' : '' }}</label>
 @endforeach
 @error('pickup_points')<p class="text-danger">{{ $message }}</p>@enderror
 <p style="font-size:12px">Manage addresses, active stores and delivery coverage in <a href="/admin/shopping-locations">Stores & collection points</a>.</p>
</div>
