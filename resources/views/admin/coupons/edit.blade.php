@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Coupon</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                                <li class="breadcrumb-item active">{{ $coupon->code }}</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-6">

                    {{-- Usage stats card --}}
                    <div class="card mb-1">
                        <div class="card-body py-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="coupon-code-pill">{{ $coupon->code }}</span>
                                    @if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at->isFuture()))
                                        <span class="badge badge-light-success">Active</span>
                                    @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                                        <span class="badge bg-secondary">Expired</span>
                                    @else
                                        <span class="badge badge-light-danger">Inactive</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 text-muted" style="font-size:0.82rem;">
                                    <span>
                                        <i data-feather="users" style="width:13px;height:13px;"></i>
                                        {{ $coupon->usages_count }} use{{ $coupon->usages_count !== 1 ? 's' : '' }}
                                    </span>
                                    <span>Created {{ $coupon->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">Edit Coupon</h4>
                        </div>
                        <div class="card-body pt-2">

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                                @csrf @method('PUT')

                                {{-- Code --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">
                                        Coupon code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="code"
                                           value="{{ old('code', $coupon->code) }}"
                                           class="form-control text-uppercase @error('code') is-invalid @enderror"
                                           style="letter-spacing:0.08em;font-family:monospace;font-weight:700;"
                                           required>
                                    @error('code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Discount type --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Discount type <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        <label class="discount-type-card {{ old('discount_type', $coupon->discount_type) === 'percent' ? 'selected' : '' }}"
                                               for="type_percent">
                                            <input type="radio" id="type_percent" name="discount_type"
                                                   value="percent"
                                                   {{ old('discount_type', $coupon->discount_type) === 'percent' ? 'checked' : '' }}
                                                   onchange="updateDiscountType()">
                                            <i data-feather="percent" style="width:20px;height:20px;"></i>
                                            <span>Percentage</span>
                                        </label>
                                        <label class="discount-type-card {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}"
                                               for="type_fixed">
                                            <input type="radio" id="type_fixed" name="discount_type"
                                                   value="fixed"
                                                   {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'checked' : '' }}
                                                   onchange="updateDiscountType()">
                                            <i data-feather="tag" style="width:20px;height:20px;"></i>
                                            <span>Fixed (₦)</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Value --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold" id="valueLabel">Discount value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="valuePrefix">
                                            {{ old('discount_type', $coupon->discount_type) === 'percent' ? '%' : '₦' }}
                                        </span>
                                        <input type="number"
                                               name="value"
                                               value="{{ old('value', $coupon->value) }}"
                                               class="form-control @error('value') is-invalid @enderror"
                                               step="0.01" min="0.01" required>
                                    </div>
                                    @error('value')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Max discount --}}
                                <div class="mb-1" id="maxDiscountRow"
                                     style="{{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'display:none;' : '' }}">
                                    <label class="form-label fw-bold">Max discount amount (₦)</label>
                                    <input type="number"
                                           name="max_discount_amount"
                                           value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                                           class="form-control @error('max_discount_amount') is-invalid @enderror"
                                           step="1" min="0"
                                           placeholder="Leave blank for no cap">
                                    @error('max_discount_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Min order --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Minimum order amount (₦)</label>
                                    <input type="number"
                                           name="min_order_amount"
                                           value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                                           class="form-control @error('min_order_amount') is-invalid @enderror"
                                           step="1" min="0">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Max uses --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Total redemption limit</label>
                                    @if($coupon->used_count > 0)
                                        <small class="text-muted ms-1">
                                            ({{ $coupon->used_count }} used so far — cannot set below this)
                                        </small>
                                    @endif
                                    <input type="number"
                                           name="max_uses"
                                           value="{{ old('max_uses', $coupon->max_uses) }}"
                                           class="form-control @error('max_uses') is-invalid @enderror"
                                           step="1" min="{{ $coupon->used_count ?: 1 }}"
                                           placeholder="Leave blank for unlimited">
                                    @error('max_uses')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Total redemptions across <strong>all</strong> customers. Leave blank for unlimited.
                                        This is separate from customer reuse below — unlimited here does <strong>not</strong>
                                        let the same customer redeem it more than once.
                                    </small>
                                </div>

                                {{-- Usage per customer --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Allow the same customer to reuse</label>
                                    <div class="d-flex gap-2">
                                        <label class="discount-type-card {{ old('multi_use', $coupon->multi_use) ? '' : 'selected' }}"
                                               for="use_single">
                                            <input type="radio" id="use_single" name="multi_use"
                                                   value="0" {{ old('multi_use', $coupon->multi_use) ? '' : 'checked' }}
                                                   onchange="updateUsageType()">
                                            <i data-feather="user-check" style="width:20px;height:20px;"></i>
                                            <span>Once per customer</span>
                                        </label>
                                        <label class="discount-type-card {{ old('multi_use', $coupon->multi_use) ? 'selected' : '' }}"
                                               for="use_multi">
                                            <input type="radio" id="use_multi" name="multi_use"
                                                   value="1" {{ old('multi_use', $coupon->multi_use) ? 'checked' : '' }}
                                                   onchange="updateUsageType()">
                                            <i data-feather="repeat" style="width:20px;height:20px;"></i>
                                            <span>Customer can reuse</span>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Once per customer: each customer may redeem it a single time.
                                        Customer can reuse: the same customer may redeem it repeatedly
                                        (still capped by the total redemption limit above, if set).
                                    </small>
                                </div>

                                {{-- Expiry --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Expiry date</label>
                                    <input type="datetime-local"
                                           name="expires_at"
                                           value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                                           class="form-control @error('expires_at') is-invalid @enderror">
                                    @error('expires_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Active --}}
                                <div class="mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_active" id="is_active" value="1"
                                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>

                                {{-- Preview --}}
                                <div class="coupon-preview mb-2" id="couponPreview">
                                    <div class="coupon-preview__label">Preview</div>
                                    <div class="coupon-preview__body">
                                        <span class="coupon-code-pill" id="previewCode">{{ $coupon->code }}</span>
                                        <span class="coupon-preview__desc" id="previewDesc">{{ $coupon->describeDiscount() }}</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-warning">
                                        <i data-feather="save" style="width:14px;height:14px;"></i>
                                        Save changes
                                    </button>
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.coupons._form_styles_scripts', ['mode' => 'edit'])
@endsection