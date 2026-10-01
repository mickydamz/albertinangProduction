@extends('layouts.adminlayout')

@section('content')

@php
    // Where to go after saving / cancelling. Set when this form was opened from
    // another page (e.g. the State edit page) so we return there instead of the
    // locations list. Falls back to the locations index.
    $returnTo = old('redirect_to', request('return'));
    $backUrl  = $returnTo ?: route('admin.locations.index');
@endphp

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Location</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Locations</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="location-create-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">New location Information</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.locations.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ $returnTo }}">
                                    <div class="mb-1">
                                        <label class="form-label" for="name">Location Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter location name" value="{{ old('name') }}" required />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="state_id">State</label>
                                        <select id="state_id" name="state_id" class="form-select @error('state_id') is-invalid @enderror">
                                            <option value="">— No state —</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}" {{ old('state_id', request('state_id')) == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Assign to a state so it appears in the correct delivery dropdown at checkout.</small>
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="shipping_cost">Standard Delivery Fee (₦)</label>
                                        <input type="number" id="shipping_cost" class="form-control @error('shipping_cost') is-invalid @enderror" name="shipping_cost" placeholder="e.g. 2000" value="{{ old('shipping_cost') }}" min="0" step="0.01" />
                                        @error('shipping_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Delivery fee for regular products to this location.</small>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label" for="truck_shipping_cost">Truck Delivery Fee (₦)</label>
                                        <input type="number" id="truck_shipping_cost" class="form-control @error('truck_shipping_cost') is-invalid @enderror" name="truck_shipping_cost" placeholder="e.g. 8000" value="{{ old('truck_shipping_cost') }}" min="0" step="0.01" />
                                        @error('truck_shipping_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Higher delivery fee applied when the cart contains refrigerator/cooling products that require a delivery truck.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-1">
                                        {{-- Using Font Awesome for save icon, as feather icons require JS initialization --}}
                                        <i class="fas fa-save me-50"></i> Save location
                                    </button>
                                    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
