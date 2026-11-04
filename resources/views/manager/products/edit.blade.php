@extends('layouts.managerlayout')

@section('content')


        <!-- Header -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Edit Product</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('manager.products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-edit me-1"></i> Editing: {{ $product->name }}
                    </h4>
                    <a href="{{ route('manager.products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>

                <div class="card-body">

                    <!-- Status toggle (saves instantly, separate from the form below) -->
                    <div class="d-flex align-items-center justify-content-between border rounded p-1 mb-2"
                         style="background:#f8f8f8;">
                        <div>
                            <span class="fw-bold d-block">Product Status</span>
                            <small class="text-muted">Only the administrator can publish or unpublish this product.</small>
                        </div>
                        <div class="form-check form-switch d-flex align-items-center gap-50 ps-0 m-0">
                            <span class="badge bg-light-secondary text-secondary">{{ $product->is_active ? 'Published' : 'Unpublished' }} · Admin controlled</span>
                        </div>
                    </div>

                    @include('catalogue.manager-submission')
                    <form action="{{ route('manager.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            <!-- Product Name -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $product->name) }}"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Brand (read only - manager cannot change brand) -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Brand</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $product->brand }}"
                                    disabled
                                >
                                <small class="text-muted">Brand cannot be changed.</small>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₦</span>
                                    <input
                                        type="number"
                                        name="price"
                                        step="0.01"
                                        min="0"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', $product->price) }}"
                                        required
                                    >
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input
                                    type="number"
                                    name="stock"
                                    min="0"
                                    class="form-control @error('stock') is-invalid @enderror"
                                    value="{{ old('stock', $product->stock) }}"
                                    required
                                >
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-2">
                                <label class="form-label">Description</label>
                                <textarea
                                    name="description"
                                    rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                >{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Image -->
                            @if($product->image)
                            <div class="col-12 mb-2">
                                <label class="form-label">Current Image</label>
                                <div>
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        height="120"
                                        class="rounded border"
                                    >
                                </div>
                            </div>
                            @endif

                            <!-- New Image -->
                            <div class="col-12 mb-2">
                                <label class="form-label">Update Image</label>
                                <input
                                    type="file"
                                    name="image"
                                    class="form-control @error('image') is-invalid @enderror"
                                    accept="image/*"
                                >
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- Submit -->
                        <div class="mt-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <a href="{{ route('manager.products.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .gap-50 { gap: 0.5rem !important; }
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
  });
</script>
@endpush
@endsection
