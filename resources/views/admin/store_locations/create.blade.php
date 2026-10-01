@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Add Store Location</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.store-locations.index') }}">Store Locations</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.store-locations.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Location Details</h4></div>
                        <div class="card-body">
                            <form action="{{ route('admin.store-locations.store') }}" method="POST" id="slForm">
                                @csrf

                                <div class="mb-1">
                                    <label class="form-label" for="name">Store Name <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="e.g. Enugu Showroom (HQ)"
                                           value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-1">
                                    <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                                    <textarea id="address" name="address" rows="2"
                                              class="form-control @error('address') is-invalid @enderror"
                                              placeholder="Full street address" required>{{ old('address') }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" for="phone">Phone</label>
                                        <input type="text" id="phone" name="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               placeholder="e.g. +234 806 406 6170"
                                               value="{{ old('phone') }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="email" id="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="e.g. Info@Albertinang.com"
                                               value="{{ old('email') }}">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8 mb-1">
                                        <label class="form-label" for="hours">Opening Hours</label>
                                        <input type="text" id="hours" name="hours"
                                               class="form-control @error('hours') is-invalid @enderror"
                                               placeholder="e.g. Mon–Sat: 8:00 AM – 6:00 PM · Sun: Closed"
                                               value="{{ old('hours') }}">
                                        @error('hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <label class="form-label" for="sort_order">Sort Order</label>
                                        <input type="number" id="sort_order" name="sort_order" min="0"
                                               class="form-control @error('sort_order') is-invalid @enderror"
                                               value="{{ old('sort_order', 0) }}">
                                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_hq" name="is_hq" value="1"
                                               {{ old('is_hq') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_hq">Mark as Headquarters (HQ)</label>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card" style="position:sticky;top:80px;">
                        <div class="card-body d-flex flex-column gap-1">
                            <button type="submit" form="slForm" class="btn btn-primary w-100">
                                <i class="fas fa-save me-50"></i> Save Location
                            </button>
                            <a href="{{ route('admin.store-locations.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
