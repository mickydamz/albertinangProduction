@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Category</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="requires_truck" name="requires_truck" value="1" {{ old('requires_truck') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_truck">Requires Truck Delivery</label>
                            </div>
                            <small class="text-muted d-block mt-1">Check this for categories like refrigerators and cooling units. A higher truck delivery fee will be applied at checkout.</small>
                            @error('requires_truck')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Create Category</button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection