@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Add FAQ</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-50 text-primary"></i> New FAQ
                            </h4>
                        </div>
                        <div class="card-body pt-2">

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.faqs.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Question <span class="text-danger">*</span></label>
                                    <input type="text" name="question" class="form-control @error('question') is-invalid @enderror"
                                           value="{{ old('question') }}" placeholder="e.g. How do I place an order?" required>
                                    @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Answer <span class="text-danger">*</span></label>
                                    <textarea name="answer" rows="6"
                                              class="form-control @error('answer') is-invalid @enderror"
                                              placeholder="Provide a clear, helpful answer…" required>{{ old('answer') }}</textarea>
                                    @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                            @foreach(['general' => 'General', 'orders' => 'Orders & Payment', 'shipping' => 'Shipping & Delivery', 'returns' => 'Returns & Refunds', 'products' => 'Products', 'account' => 'Account'] as $val => $label)
                                                <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control"
                                               value="{{ old('sort_order', 0) }}" min="0">
                                        <div class="form-text">Lower = appears first</div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active"
                                               id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive">Visible on storefront</label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-50"></i> Save FAQ
                                    </button>
                                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
