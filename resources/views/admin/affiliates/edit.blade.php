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
                        <h2 class="content-header-title mb-0">Edit Affiliate</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.affiliates.index') }}">Affiliates</a></li>
                            <li class="breadcrumb-item active">Edit Affiliate</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.affiliates.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-50"></i> Back to Affiliates
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- <h1>Edit Affiliate Details</h1> -->

            <!-- Affiliate Edit Form -->
            <div class="container" style="max-width: 900px;"> <!-- Limit form width for large screens -->
                <form action="{{ route('admin.affiliates.update', $affiliate->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-1">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $affiliate->name) }}" required>
                    </div>

                    <!-- Email -->
                    <div class="mb-1">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $affiliate->email) }}" required>
                    </div>

                    <!-- Total Clicks -->
                    <div class="mb-1">
                        <label for="total_clicks" class="form-label">Total Clicks</label>
                        <input type="number" class="form-control" name="total_clicks" value="{{ old('total_clicks', $affiliate->total_clicks) }}">
                    </div>

                    <!-- Total Bounties -->
                    <div class="mb-1">
                        <label for="total_bounties" class="form-label">Total Bounties</label>
                        <input type="number" class="form-control" name="total_bounties" value="{{ old('total_bounties', $affiliate->total_bounties) }}">
                    </div>

                    <!-- Total Items Shipped -->
                    <div class="mb-1">
                        <label for="total_items_shipped" class="form-label">Total Items Shipped</label>
                        <input type="number" class="form-control" name="total_items_shipped" value="{{ old('total_items_shipped', $affiliate->total_items_shipped) }}">
                    </div>

                    <!-- Total Earnings -->
                    <div class="mb-1">
                        <label for="total_earnings" class="form-label">Total Earnings</label>
                        <input type="number" step="0.01" class="form-control" name="total_earnings" value="{{ old('total_earnings', $affiliate->total_earnings) }}">
                    </div>

                    <!-- Total Orders -->
                    <div class="mb-1">
                        <label for="total_orders" class="form-label">Total Orders</label>
                        <input type="number" class="form-control" name="total_orders" value="{{ old('total_orders', $affiliate->total_orders) }}">
                    </div>

                    <!-- Clicks -->
                    <div class="mb-1">
                        <label for="clicks" class="form-label">Clicks</label>
                        <input type="number" class="form-control" name="clicks" value="{{ old('clicks', $affiliate->clicks) }}">
                    </div>

                    <!-- Conversions -->
                    <div class="mb-1">
                        <label for="conversions" class="form-label">Conversions</label>
                        <input type="number" class="form-control" name="conversions" value="{{ old('conversions', $affiliate->conversions) }}">
                    </div>

                    <!-- Approved Checkbox -->
                    <div class="mb-1 form-check">
                        <input type="checkbox" class="form-check-input" id="is_approved" name="is_approved" {{ $affiliate->is_approved ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_approved">Approved</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Update Affiliate</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
