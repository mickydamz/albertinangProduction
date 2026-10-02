@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Edit Order #{{ $order->id }} {{ $order->order_number }}</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->id }}</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Order
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">

                {{-- Edit Form --}}
                <div class="col-lg-8 col-12 mb-2">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Update Order Details</h4>
                        </div>
                        <div class="card-body pt-2">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                @csrf
                                @method('PUT')

                                {{-- Status --}}
                                <div class="mb-2">
                                    <label for="status" class="form-label fw-bold">
                                        Order Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Changing status will update the order and notify the customer by email.</small>
                                </div>

                                {{-- Pickup Point --}}
                                <div class="mb-2">
                                    <label for="pickup_point_id" class="form-label fw-bold">Pickup Point</label>
                                    <select name="pickup_point_id" id="pickup_point_id"
                                            class="form-select @error('pickup_point_id') is-invalid @enderror">
                                        <option value="">— Keep current / none —</option>
                                        @foreach ($pickupPoints as $point)
                                            <option value="{{ $point->id }}"
                                                {{ (int) old('pickup_point_id', $order->pickup_point_id) === (int) $point->id ? 'selected' : '' }}>
                                                {{ $point->name }}@if($point->location) — {{ $point->location->name }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pickup_point_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        @if($order->pickup_point_name)
                                            Currently: <strong>{{ $order->pickup_point_name }}</strong>
                                            @if($order->pickup_location) ({{ $order->pickup_location }}) @endif
                                        @else
                                            No specific pickup point is currently assigned.
                                        @endif
                                        Reassigning updates the point shown on the customer's invoice.
                                    </small>
                                </div>

                                {{-- Payment Method --}}
                                <div class="mb-2">
                                    <label for="payment_method" class="form-label fw-bold">Payment Method</label>
                                    <select
                                        name="payment_method"
                                        id="payment_method"
                                        class="form-select @error('payment_method') is-invalid @enderror"
                                    >
                                        @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'pos' => 'POS', 'paystack' => 'Paystack', 'stripe' => 'Stripe', 'other' => 'Other'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('payment_method', $order->payment_method) === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Save Changes
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4 col-12 mb-2">

                    {{-- Current Status Card --}}
                    <div class="card mb-2">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Current Status</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $statusColors = [
                                    'pending'          => 'warning',
                                    'paid'             => 'info',
                                    'processing'       => 'info',
                                    'ready_for_pickup' => 'warning',
                                    'shipped'          => 'primary',
                                    'delivered'        => 'success',
                                    'completed'        => 'success',
                                    'cancelled'        => 'danger',
                                    'refunded'         => 'secondary',
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <div class="text-center py-1">
                                <span class="badge bg-{{ $color }} fs-6 px-3 py-2">
                                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                </span>
                                <p class="text-muted mt-1 mb-0 small">
                                    Last updated: {{ $order->updated_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Order Meta Card --}}
                    <div class="card mb-2">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Order Info</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Order ID</span>
                                    <strong>#{{ $order->id }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Order Number</span>
                                    <strong>{{ $order->order_number }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Total</span>
                                    <strong class="text-success">₦{{ number_format($order->total, 2) }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Payment</span>
                                    <strong>{{ ucfirst($order->payment_method ?? 'N/A') }}</strong>
                                </li>
                                @if($order->reference)
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Reference</span>
                                    <strong style="font-size:11px;word-break:break-all;max-width:150px;text-align:right;">
                                        {{ $order->reference }}
                                    </strong>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Items</span>
                                    <strong>{{ $order->items->count() }}</strong>
                                </li>
                                @if($order->pickup_point_name || $order->pickup_location)
                                <li class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Pickup</span>
                                    <strong style="font-size:12px;max-width:160px;text-align:right;">
                                        {{ $order->pickup_point_name ?: $order->pickup_location }}
                                        @if($order->pickup_point_name && $order->pickup_location)
                                            <br><span class="text-muted fw-normal">{{ $order->pickup_location }}</span>
                                        @endif
                                    </strong>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Customer</span>
                                    <strong>{{ $order->user->name ?? 'Guest' }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Status Reference Card --}}
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Status Reference</h4>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-warning me-2" style="min-width:110px;">Pending</span>
                                    <small class="text-muted">Awaiting processing</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-info me-2" style="min-width:110px;">Paid</span>
                                    <small class="text-muted">Payment confirmed</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-info me-2" style="min-width:110px;">Processing</span>
                                    <small class="text-muted">Being prepared</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-warning me-2" style="min-width:110px;">Ready for Pickup</span>
                                    <small class="text-muted">Awaiting collection</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-primary me-2" style="min-width:110px;">Shipped</span>
                                    <small class="text-muted">On the way</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-success me-2" style="min-width:110px;">Delivered</span>
                                    <small class="text-muted">Received by customer</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-success me-2" style="min-width:110px;">Completed</span>
                                    <small class="text-muted">Order complete</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-danger me-2" style="min-width:110px;">Cancelled</span>
                                    <small class="text-muted">Order cancelled</small>
                                </li>
                                <li class="d-flex align-items-center py-1">
                                    <span class="badge bg-secondary me-2" style="min-width:110px;">Refunded</span>
                                    <small class="text-muted">Payment returned</small>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<style>
    body { font-family: 'Roboto', sans-serif; }
    .gap-2 { gap: 0.5rem; }
    .fs-6 { font-size: 1rem !important; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (!form) return;
    const label = document.createElement('label');
    label.textContent = 'Delivery tracking reference';
    label.htmlFor = 'tracking_reference';
    const input = document.createElement('input');
    input.id = 'tracking_reference'; input.name = 'tracking_reference'; input.className = 'form-control';
    input.value = @json($order->tracking_reference ?? '');
    form.append(label, input);
});
</script>
@endpush
