@extends('layouts.adminlayout')

@section('content')

@php
    // Where to go after saving / cancelling. Set when the edit was opened from
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
                        <h2 class="content-header-title mb-0">Edit Location</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Locations</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="location-edit-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Edit Location: {{ $location->name }}</h4>
                                {{-- Current status badge --}}
                                <span class="badge {{ $location->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                                    <i class="fas {{ $location->is_active ? 'fa-check-circle' : 'fa-ban' }} me-50"></i>
                                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('admin.locations.update', $location->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="redirect_to" value="{{ $returnTo }}">

                                    {{-- Location Name --}}
                                    <div class="mb-1">
                                        <label class="form-label" for="name">Location Name <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter location name"
                                            value="{{ old('name', $location->name) }}"
                                            required
                                        />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- State --}}
                                    <div class="mb-1">
                                        <label class="form-label" for="state_id">State</label>
                                        <select id="state_id" name="state_id" class="form-select @error('state_id') is-invalid @enderror">
                                            <option value="">— No state —</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}" {{ old('state_id', $location->state_id) == $state->id ? 'selected' : '' }}>
                                                    {{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Assign to a state so it appears in the correct delivery dropdown at checkout.</small>
                                    </div>

                                    {{-- Delivery Fees --}}
                                    <div class="mb-1">
                                        <label class="form-label" for="shipping_cost">Standard Delivery Fee (₦)</label>
                                        <input type="number" id="shipping_cost" name="shipping_cost"
                                            class="form-control @error('shipping_cost') is-invalid @enderror"
                                            placeholder="e.g. 2000"
                                            value="{{ old('shipping_cost', $location->shipping_cost) }}"
                                            min="0" step="0.01" />
                                        @error('shipping_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Delivery fee for regular products to this location.</small>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label" for="truck_shipping_cost">Truck Delivery Fee (₦)</label>
                                        <input type="number" id="truck_shipping_cost" name="truck_shipping_cost"
                                            class="form-control @error('truck_shipping_cost') is-invalid @enderror"
                                            placeholder="e.g. 8000"
                                            value="{{ old('truck_shipping_cost', $location->truck_shipping_cost) }}"
                                            min="0" step="0.01" />
                                        @error('truck_shipping_cost')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Applied when the cart contains refrigerator/cooling products that require a delivery truck.</small>
                                    </div>

                                    {{-- Active / Inactive Toggle --}}
                                    <div class="mb-2">
                                        <label class="form-label d-block">Status</label>
                                        <div class="form-check form-switch form-check-success">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="is_active"
                                                name="is_active"
                                                value="1"
                                                {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                                            />
                                            <label class="form-check-label" for="is_active">
                                                <span class="switch-icon-left"><i class="fas fa-check"></i></span>
                                                <span class="switch-icon-right"><i class="fas fa-times"></i></span>
                                                Mark as Active
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-50"></i>
                                            Inactive locations are hidden from the public and cannot be selected at checkout.
                                        </small>
                                    </div>

                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary me-1">
                                            <i class="fas fa-save me-50"></i> Update Location
                                        </button>
                                        <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                                            Cancel
                                        </a>
                                    </div>
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