@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Create Coupon</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-6">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">New Coupon</h4>
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

                            <form method="POST" action="{{ route('admin.coupons.store') }}">
                                @csrf

                                {{-- Code --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">
                                        Coupon code <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text"
                                               name="code"
                                               value="{{ old('code') }}"
                                               class="form-control text-uppercase @error('code') is-invalid @enderror"
                                               placeholder="e.g. SAVE20"
                                               style="letter-spacing:0.08em;font-family:monospace;font-weight:700;"
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn"
                                                title="Generate random code">
                                            <i data-feather="refresh-cw" style="width:14px;height:14px;"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Customers enter this exactly. Saved in uppercase.</small>
                                    @error('code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Discount type --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">
                                        Discount type <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-2">
                                        <label class="discount-type-card {{ old('discount_type', 'percent') === 'percent' ? 'selected' : '' }}"
                                               for="type_percent">
                                            <input type="radio" id="type_percent" name="discount_type"
                                                   value="percent"
                                                   {{ old('discount_type', 'percent') === 'percent' ? 'checked' : '' }}
                                                   onchange="updateDiscountType()">
                                            <i data-feather="percent" style="width:20px;height:20px;"></i>
                                            <span>Percentage</span>
                                        </label>
                                        <label class="discount-type-card {{ old('discount_type') === 'fixed' ? 'selected' : '' }}"
                                               for="type_fixed">
                                            <input type="radio" id="type_fixed" name="discount_type"
                                                   value="fixed"
                                                   {{ old('discount_type') === 'fixed' ? 'checked' : '' }}
                                                   onchange="updateDiscountType()">
                                            <i data-feather="tag" style="width:20px;height:20px;"></i>
                                            <span>Fixed (₦)</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Value --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold" id="valueLabel">
                                        Discount value <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="valuePrefix">%</span>
                                        <input type="number"
                                               name="value"
                                               value="{{ old('value') }}"
                                               class="form-control @error('value') is-invalid @enderror"
                                               step="0.01" min="0.01"
                                               placeholder="e.g. 20"
                                               required>
                                    </div>
                                    @error('value')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Max discount (percent only) --}}
                                <div class="mb-1" id="maxDiscountRow"
                                     style="{{ old('discount_type') === 'fixed' ? 'display:none;' : '' }}">
                                    <label class="form-label fw-bold">Max discount amount (₦)</label>
                                    <input type="number"
                                           name="max_discount_amount"
                                           value="{{ old('max_discount_amount') }}"
                                           class="form-control @error('max_discount_amount') is-invalid @enderror"
                                           step="1" min="0"
                                           placeholder="Leave blank for no cap">
                                    <small class="text-muted">Caps the maximum ₦ saved. e.g. 20% off but max ₦5,000.</small>
                                    @error('max_discount_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Min order --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Minimum order amount (₦)</label>
                                    <input type="number"
                                           name="min_order_amount"
                                           value="{{ old('min_order_amount', 0) }}"
                                           class="form-control @error('min_order_amount') is-invalid @enderror"
                                           step="1" min="0"
                                           placeholder="0 = no minimum">
                                    @error('min_order_amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Max uses --}}
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Total redemption limit</label>
                                    <input type="number"
                                           name="max_uses"
                                           value="{{ old('max_uses') }}"
                                           class="form-control @error('max_uses') is-invalid @enderror"
                                           step="1" min="1"
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
                                        <label class="discount-type-card {{ old('multi_use') ? '' : 'selected' }}"
                                               for="use_single">
                                            <input type="radio" id="use_single" name="multi_use"
                                                   value="0" {{ old('multi_use') ? '' : 'checked' }}
                                                   onchange="updateUsageType()">
                                            <i data-feather="user-check" style="width:20px;height:20px;"></i>
                                            <span>Once per customer</span>
                                        </label>
                                        <label class="discount-type-card {{ old('multi_use') ? 'selected' : '' }}"
                                               for="use_multi">
                                            <input type="radio" id="use_multi" name="multi_use"
                                                   value="1" {{ old('multi_use') ? 'checked' : '' }}
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
                                           value="{{ old('expires_at') }}"
                                           class="form-control @error('expires_at') is-invalid @enderror"
                                           min="{{ now()->format('Y-m-d\TH:i') }}">
                                    <small class="text-muted">Leave blank for no expiry.</small>
                                    @error('expires_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Active toggle --}}
                                <div class="mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_active" id="is_active" value="1"
                                               {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active (customers can use this coupon immediately)
                                        </label>
                                    </div>
                                </div>

                                {{-- Preview --}}
                                <div class="coupon-preview mb-2" id="couponPreview">
                                    <div class="coupon-preview__label">Preview</div>
                                    <div class="coupon-preview__body">
                                        <span class="coupon-code-pill" id="previewCode">—</span>
                                        <span class="coupon-preview__desc" id="previewDesc">—</span>
                                    </div>
                                </div>

                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="save" style="width:14px;height:14px;"></i>
                                        Create Coupon
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

@include('admin.coupons._form_styles_scripts', ['mode' => 'create'])
@endsection