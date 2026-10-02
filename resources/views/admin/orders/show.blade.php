@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        {{-- ══════════════ Page Header ══════════════ --}}
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">
                            Order <span class="text-primary">{{ $order->order_number }}</span>
                        </h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                                <li class="breadcrumb-item active">#{{ $order->id }}</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">

            {{-- ══════════════ Status + Action bar ══════════════ --}}
            @php
                $statusColors = [
                    'pending'          => 'warning',
                    'processing'       => 'info',
                    'shipped'          => 'primary',
                    'ready_for_pickup' => 'warning',
                    'delivered'        => 'success',
                    'cancelled'        => 'danger',
                    'refunded'         => 'secondary',
                    'paid'             => 'success',
                ];
                $color = $statusColors[$order->status] ?? 'secondary';
            @endphp
            <div class="card mb-2">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2 py-75">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar bg-light-{{ $color }} rounded">
                            <div class="avatar-content">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-0">Order #{{ $order->id }}</h5>
                            <small class="text-muted">Placed {{ $order->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="text-end me-1">
                            <span class="badge bg-{{ $color }} d-block mb-25">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                            <div class="fw-bolder fs-4 text-success lh-1">&#8358;{{ number_format($order->total, 2) }}</div>
                        </div>
                        <div class="vr d-none d-md-block" style="height:40px;"></div>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('admin.orders.invoice', $order) }}"
                               class="btn btn-success" target="_blank">
                                <i class="fas fa-file-invoice me-50"></i>Invoice
                            </a>
                            <a href="{{ route('admin.orders.edit', $order) }}"
                               class="btn btn-warning">
                                <i class="fas fa-edit me-50"></i>Edit Status
                            </a>
                            <a href="{{ route('admin.orders.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-50"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════ Top cards row ══════════════ --}}
            <div class="row g-2 mb-2">

                {{-- Order Summary --}}
                <div class="col-lg-4 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom">
                            <h4 class="card-title"><i class="fas fa-file-invoice-dollar text-primary me-50"></i>Order Summary</h4>
                        </div>
                        <div class="card-body pt-1">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Order Number</span>
                                    <strong class="font-monospace">{{ $order->order_number }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Status</span>
                                    <span class="badge bg-light-{{ $color }}">{{ ucfirst(str_replace('_',' ',$order->status)) }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Payment Method</span>
                                    <strong>{{ ucfirst($order->payment_method ?? 'N/A') }}</strong>
                                </li>
                                @if($order->reference)
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Reference</span>
                                    <strong class="font-monospace text-truncate ms-2" style="max-width:150px;">{{ $order->reference }}</strong>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Items</span>
                                    <strong>{{ $order->items->count() }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-50 border-bottom">
                                    <span class="text-muted">Created</span>
                                    <strong>{{ $order->created_at->format('d M Y, H:i') }}</strong>
                                </li>
                                <li class="d-flex justify-content-between py-50">
                                    <span class="text-muted">Last Updated</span>
                                    <strong>{{ $order->updated_at->format('d M Y, H:i') }}</strong>
                                </li>
                            </ul>

                            <hr>

                            @php
                                $installationTotal = $order->items->sum('installation_extra_ngn');
                                $itemsSubtotal = (float)$order->total - (float)$order->shipping_cost + (float)$order->coupon_discount_ngn;
                            @endphp
                            <div class="d-flex justify-content-between py-25">
                                <span class="text-muted">Items Subtotal</span>
                                <span>&#8358;{{ number_format($itemsSubtotal, 2) }}</span>
                            </div>
                            @if($installationTotal > 0)
                            <div class="d-flex justify-content-between py-25">
                                <span class="text-muted">Installation</span>
                                <span class="text-success">+&#8358;{{ number_format($installationTotal, 2) }}</span>
                            </div>
                            @endif
                            @if(($order->shipping_cost ?? 0) > 0)
                            <div class="d-flex justify-content-between py-25">
                                <span class="text-muted">Delivery Fee</span>
                                <span>+&#8358;{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            @endif
                            @if($order->hasCoupon())
                            <div class="d-flex justify-content-between py-25">
                                <span class="text-success">
                                    <i class="fas fa-tag me-25"></i>Discount
                                    <small class="text-muted">({{ $order->coupon_code_used }})</small>
                                </span>
                                <span class="text-success">&minus;&#8358;{{ number_format($order->coupon_discount_ngn, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center pt-1 mt-50 border-top">
                                <h5 class="mb-0">Order Total</h5>
                                <h4 class="mb-0 text-success">&#8358;{{ number_format($order->total, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="col-lg-4 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom">
                            <h4 class="card-title"><i class="fas fa-user text-primary me-50"></i>Customer</h4>
                        </div>
                        <div class="card-body pt-1">
                            @if ($order->user)
                                <div class="d-flex align-items-center mb-2">
                                    @if ($order->user->avatar)
                                        <img src="{{ Storage::url($order->user->avatar) }}" alt="Avatar" class="rounded-circle me-2" style="width:54px;height:54px;object-fit:cover;">
                                    @else
                                        <img src="{{ asset('app-asset/images/portrait/small/e_avatar.png') }}" alt="Avatar" class="rounded-circle me-2" style="width:54px;height:54px;object-fit:cover;">
                                    @endif
                                    <div>
                                        <h5 class="mb-0">{{ $order->user->name }}</h5>
                                        <small class="text-muted text-capitalize">{{ $order->user->role ?? 'User' }}</small>
                                    </div>
                                </div>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Email</span>
                                        <strong class="text-truncate ms-2" style="max-width:170px;">{{ $order->user->email }}</strong>
                                    </li>
                                    @if ($order->user->phone_no ?? false)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Phone</span>
                                        <strong>{{ $order->user->phone_no }}</strong>
                                    </li>
                                    @endif
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Verified</span>
                                        @if ($order->user->verified)
                                            <span class="badge bg-light-success">Verified</span>
                                        @else
                                            <span class="badge bg-light-danger">Unverified</span>
                                        @endif
                                    </li>
                                    <li class="d-flex justify-content-between py-50">
                                        <span class="text-muted">Member Since</span>
                                        <strong>{{ $order->user->created_at->format('d M Y') }}</strong>
                                    </li>
                                </ul>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.edit', $order->user) }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-external-link-alt me-50"></i>View User Profile
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                                    <p class="mb-0">Guest / Deleted User</p>
                                    @if($order->customer_email)
                                        <small>{{ $order->customer_email }}</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pickup / Fulfilment --}}
                <div class="col-lg-4 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">
                                <i class="fas fa-{{ $order->fulfillment_method === 'delivery' ? 'truck' : 'store' }} text-primary me-50"></i>
                                Fulfilment
                            </h4>
                        </div>
                        <div class="card-body pt-1">
                            @if($order->fulfillment_method === 'delivery')
                                <div class="d-flex align-items-start mb-2">
                                    <div class="avatar bg-light-primary rounded me-1">
                                        <div class="avatar-content"><i class="fas fa-truck"></i></div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $order->delivery_state_name ?? 'Delivery' }}</h5>
                                        @if($order->delivery_location_name)
                                            <small class="text-muted d-block">{{ $order->delivery_location_name }}</small>
                                        @endif
                                    </div>
                                </div>
                                <ul class="list-unstyled mb-0">
                                    @if($order->delivery_state_name)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">State</span>
                                        <strong>{{ $order->delivery_state_name }}</strong>
                                    </li>
                                    @endif
                                    @if($order->delivery_location_name)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Area</span>
                                        <strong>{{ $order->delivery_location_name }}</strong>
                                    </li>
                                    @endif
                                    @if($order->shipping_address)
                                    <li class="py-50 border-bottom">
                                        <span class="text-muted d-block mb-25">Delivery Address</span>
                                        <strong style="white-space:pre-line;">{{ $order->shipping_address }}</strong>
                                    </li>
                                    @endif
                                    @if($order->shipping_cost > 0)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Delivery Fee</span>
                                        <strong>&#8358;{{ number_format($order->shipping_cost, 2) }}</strong>
                                    </li>
                                    @endif
                                    <li class="d-flex justify-content-between py-50">
                                        <span class="text-muted">Method</span>
                                        <span class="badge bg-light-primary">Home Delivery</span>
                                    </li>
                                </ul>
                            @elseif($order->pickup_point_name || $order->pickup_location)
                                <div class="d-flex align-items-start mb-2">
                                    <div class="avatar bg-light-success rounded me-1">
                                        <div class="avatar-content"><i class="fas fa-map-pin"></i></div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $order->pickup_point_name ?: 'Pickup Point' }}</h5>
                                        @if($order->pickup_point_address)
                                            <small class="text-muted d-block">{{ $order->pickup_point_address }}</small>
                                        @endif
                                    </div>
                                </div>
                                <ul class="list-unstyled mb-0">
                                    @if($order->pickup_location)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">City / Location</span>
                                        <strong>{{ $order->pickup_location }}</strong>
                                    </li>
                                    @endif
                                    @if($order->pickup_point_id)
                                    <li class="d-flex justify-content-between py-50 border-bottom">
                                        <span class="text-muted">Point ID</span>
                                        <strong>#{{ $order->pickup_point_id }}</strong>
                                    </li>
                                    @endif
                                    <li class="d-flex justify-content-between py-50">
                                        <span class="text-muted">Method</span>
                                        <span class="badge bg-light-info">Store Pickup</span>
                                    </li>
                                </ul>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-map-marker-slash fa-2x mb-2"></i>
                                    <p class="mb-0">No fulfilment location recorded</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>{{-- /.row (top cards) --}}

            {{-- ══════════════ Order Items ══════════════ --}}
            <div class="card mb-2">
                <div class="card-header border-bottom">
                    <h4 class="card-title"><i class="fas fa-boxes text-primary me-50"></i>Order Items</h4>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Installation</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $item)
                                @php
                                    $imgSrc = null;
                                    if ($item->image) {
                                        $imgSrc = filter_var($item->image, FILTER_VALIDATE_URL)
                                            ? $item->image
                                            : Storage::url($item->image);
                                    }
                                @endphp
                                <tr>
                                    <td data-label="Image">
                                        @if ($imgSrc)
                                            <img src="{{ $imgSrc }}"
                                                 alt="{{ $item->name }}"
                                                 style="max-width:48px;border-radius:6px;object-fit:contain;background:#f9fafb;padding:2px;border:1px solid #e0e0e0;">
                                        @else
                                            <img src="{{ asset('app-asset/images/portrait/small/e_avatar.png') }}"
                                                 alt="No image"
                                                 style="max-width:48px;border-radius:6px;opacity:.4;">
                                        @endif
                                    </td>
                                    <td data-label="Product"><span class="fw-bolder">{{ \Illuminate\Support\Str::limit($item->name, 43) }}</span></td>
                                    <td data-label="SKU">
                                        @if ($item->sku)
                                            <span class="badge bg-light text-dark border font-monospace">{{ $item->sku }}</span>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td data-label="Unit Price">&#8358;{{ number_format($item->price, 2) }}</td>
                                    <td data-label="Qty"><span class="badge bg-light-secondary">{{ $item->quantity }}</span></td>
                                    <td data-label="Installation">
                                        @if ($item->installation_option)
                                            @foreach (explode(',', $item->installation_option) as $tag)
                                                <span class="badge bg-light-success border border-success-subtle d-inline-block mb-25">
                                                    <i class="fas fa-tools me-25"></i>{{ trim($tag) }}
                                                </span>
                                            @endforeach
                                            @if ($item->installation_extra_ngn > 0)
                                                <div class="text-success fw-bold mt-25" style="font-size:12px;">
                                                    +&#8358;{{ number_format($item->installation_extra_ngn, 0) }}
                                                </div>
                                            @else
                                                <div class="text-muted mt-25 fst-italic" style="font-size:12px;">Included</div>
                                            @endif
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td data-label="Subtotal" class="text-end">
                                        <strong>&#8358;{{ number_format(($item->price * $item->quantity) + ($item->installation_extra_ngn ?? 0), 2) }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No items in this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            @if ($installationTotal > 0)
                            <tr>
                                <td colspan="6" class="text-end text-muted">Installation Total:</td>
                                <td class="text-end fw-bold text-success">+&#8358;{{ number_format($installationTotal, 2) }}</td>
                            </tr>
                            @endif
                            @if(($order->shipping_cost ?? 0) > 0)
                            <tr>
                                <td colspan="6" class="text-end text-muted">Delivery Fee:</td>
                                <td class="text-end fw-bold">+&#8358;{{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->hasCoupon())
                            <tr>
                                <td colspan="6" class="text-end text-success">
                                    <i class="fas fa-tag me-25"></i>Discount ({{ $order->coupon_code_used }}):
                                </td>
                                <td class="text-end fw-bold text-success">&minus;&#8358;{{ number_format($order->coupon_discount_ngn, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="6" class="text-end fw-bold fs-5">Order Total:</td>
                                <td class="text-end fw-bold fs-5 text-success">&#8358;{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ══════════════ Return Request ══════════════ --}}
            @if($order->return)
            <div class="card mb-2">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-rotate-left text-primary me-50"></i>Return Request</h4>
                    @php
                        $returnColors = [
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'refunded' => 'secondary',
                        ];
                    @endphp
                    <span class="badge bg-{{ $returnColors[$order->return->status] ?? 'secondary' }}">
                        {{ ucfirst($order->return->status) }}
                    </span>
                </div>
                <div class="card-body pt-2">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Reason</p>
                            <p class="mb-0">{{ $order->return->reason }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Submitted</p>
                            <p class="mb-0">{{ $order->return->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($order->return->evidence_path)
                        <div class="mb-2">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Evidence</p>
                            <img src="{{ Storage::url($order->return->evidence_path) }}"
                                 alt="Return evidence"
                                 style="max-width:200px;border-radius:6px;border:1px solid #e0e0e0;">
                        </div>
                    @endif

                    @if($order->return->admin_notes)
                        <div class="mb-2">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Previous notes</p>
                            <p class="mb-0">{{ $order->return->admin_notes }}</p>
                        </div>
                    @endif

                    <hr>

                    <form method="POST" action="{{ route('admin.returns.review', $order->return) }}"
                          data-refund-guard
                          data-refund-statuses="approved,refunded"
                          data-gateway="{{ $order->payment_method }}"
                          data-amount="{{ $order->total }}"
                          data-currency="₦"
                          data-email="{{ $order->user->email ?? $order->customer_email ?? 'the customer' }}"
                          data-already-refunded="{{ !empty($order->return->refund_id) || $order->status === 'refunded' ? '1' : '0' }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-1 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Update status</label>
                                <select name="status" class="form-select">
                                    @foreach(['approved', 'rejected', 'refunded'] as $s)
                                        <option value="{{ $s }}" {{ $order->return->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label text-muted small">Notes to customer (optional)</label>
                                <input type="text" name="admin_notes" class="form-control"
                                       placeholder="e.g. Please ship the item back to our Enugu office"
                                       value="{{ $order->return->admin_notes }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Update</button>
                            </div>
                        </div>
                    </form>
                    @include('admin.partials.refund-confirm')
                </div>
            </div>
            @else
            <div class="card mb-2">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0"><i class="fas fa-rotate-left text-primary me-50"></i>Add Return</h4>
                </div>
                <div class="card-body pt-2">
                    <form method="POST" action="{{ route('admin.orders.returns.store', $order) }}">
                        @csrf
                        <label class="form-label text-muted small">Reason for return</label>
                        <textarea name="reason" class="form-control mb-2" rows="2" required minlength="10" maxlength="1000"
                                  placeholder="Why is this order being returned? (min 10 characters)">{{ old('reason') }}</textarea>
                        <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1"></i>Log Return</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- ══════════════ Cancellation Request ══════════════ --}}
            @if($order->cancellation)
            <div class="card mb-2">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-ban text-danger me-50"></i>Cancellation Request</h4>
                    @php
                        $cancelColors = [
                            'pending'  => 'warning',
                            'approved' => 'danger',
                            'rejected' => 'secondary',
                            'refunded' => 'success',
                        ];
                    @endphp
                    <span class="badge bg-{{ $cancelColors[$order->cancellation->status] ?? 'secondary' }}">
                        {{ ucfirst($order->cancellation->status) }}
                    </span>
                </div>
                <div class="card-body pt-2">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Reason</p>
                            <p class="mb-0">{{ $order->cancellation->reason }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Submitted</p>
                            <p class="mb-0">{{ $order->cancellation->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($order->cancellation->admin_notes)
                        <div class="mb-2">
                            <p class="text-muted mb-25 small fw-bolder text-uppercase">Previous notes</p>
                            <p class="mb-0">{{ $order->cancellation->admin_notes }}</p>
                        </div>
                    @endif

                    <hr>

                    <form method="POST" action="{{ route('admin.cancellations.review', $order->cancellation) }}"
                          data-refund-guard
                          data-refund-statuses="refunded"
                          data-gateway="{{ $order->payment_method }}"
                          data-amount="{{ $order->total }}"
                          data-currency="₦"
                          data-email="{{ $order->user->email ?? $order->customer_email ?? 'the customer' }}"
                          data-already-refunded="{{ !empty($order->cancellation->refund_id) || $order->status === 'refunded' ? '1' : '0' }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-1 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Update status</label>
                                <select name="status" class="form-select">
                                    @foreach(['approved', 'rejected', 'refunded'] as $s)
                                        <option value="{{ $s }}" {{ $order->cancellation->status === $s ? 'selected' : '' }}>
                                            {{ ucfirst($s) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label text-muted small">Notes to customer (optional)</label>
                                <input type="text" name="admin_notes" class="form-control"
                                       placeholder="e.g. Cancellation approved, refund in 5–10 business days"
                                       value="{{ $order->cancellation->admin_notes }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Update</button>
                            </div>
                        </div>
                    </form>
                    @include('admin.partials.refund-confirm')
                </div>
            </div>
            @else
            <div class="card mb-2">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0"><i class="fas fa-ban text-danger me-50"></i>Add Cancellation</h4>
                </div>
                <div class="card-body pt-2">
                    <form method="POST" action="{{ route('admin.orders.cancellations.store', $order) }}">
                        @csrf
                        <label class="form-label text-muted small">Reason for cancellation</label>
                        <textarea name="reason" class="form-control mb-2" rows="2" required minlength="10" maxlength="1000"
                                  placeholder="Why is this order being cancelled? (min 10 characters)">{{ old('reason') }}</textarea>
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-plus me-1"></i>Log Cancellation</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- ══════════════ Danger Zone ══════════════ --}}
            <div class="card border-danger mb-2">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
                    <div>
                        <h5 class="mb-0 text-danger"><i class="fas fa-triangle-exclamation me-50"></i>Danger Zone</h5>
                        <small class="text-muted">Deleting an order is permanent and cannot be undone.</small>
                    </div>
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete order #{{ $order->id }}? This cannot be undone.');"
                          class="mb-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-50"></i>Delete Order
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /.content-body --}}
    </div>
</div>

<style>
    .py-50 { padding-top: .5rem; padding-bottom: .5rem; }
    .py-25 { padding-top: .25rem; padding-bottom: .25rem; }
    .py-75 { padding-top: .75rem !important; padding-bottom: .75rem !important; }
    .avatar.bg-light-success .avatar-content { color: #28c76f; }
    .avatar.bg-light-warning .avatar-content,
    .avatar.bg-light-info .avatar-content,
    .avatar.bg-light-danger .avatar-content,
    .avatar.bg-light-secondary .avatar-content,
    .avatar.bg-light-primary .avatar-content { color: inherit; }

    @media (max-width: 768px) {
        .table thead { display: none; }
        .table tr { display: block; margin-bottom: 1rem; border: 1px solid #e0e0e0; border-radius: 6px; }
        .table td { display: flex; justify-content: space-between; align-items: center; text-align: right; padding: .5rem .75rem; border: none; border-bottom: 1px dashed #eee; }
        .table td:last-child { border-bottom: none; }
        .table td::before { content: attr(data-label); font-weight: bold; color: #6c757d; text-align: left; margin-right: 1rem; }
        .table tfoot td::before { content: ''; }
    }
</style>

@endsection
