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
                        <h2 class="content-header-title mb-0">Edit Pickup Point</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.pickup-points.index') }}">Pickup Points</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to Pickup Points
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="pickup-point-edit">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit: {{ $pickupPoint->name }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.pickup-points.update', $pickupPoint->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-1">
                                        <label class="form-label" for="location_id">Location <span class="text-danger">*</span></label>
                                        <select id="location_id" name="location_id"
                                                class="form-select @error('location_id') is-invalid @enderror" required>
                                            <option value="">— Select a location —</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('location_id', $pickupPoint->location_id) == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('location_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="name">Pickup Point Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name"
                                               class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $pickupPoint->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                                        <input type="text" id="address" name="address"
                                               class="form-control @error('address') is-invalid @enderror"
                                               value="{{ old('address', $pickupPoint->address) }}" required>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label" for="hours">Opening Hours</label>
                                        <input type="text" id="hours" name="hours"
                                               class="form-control @error('hours') is-invalid @enderror"
                                               value="{{ old('hours', $pickupPoint->hours) }}">
                                        @error('hours')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary me-1">
                                        <i class="fas fa-save me-50"></i> Update Pickup Point
                                    </button>
                                    <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-outline-secondary">Cancel</a>
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