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
                        <h2 class="content-header-title mb-0">Coupons</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Coupons</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Coupon
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── Stats bar ────────────────────────────────────────────────── --}}
            <div class="row mb-2">
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card">
                        <div class="stat-label">Total coupons</div>
                        <div class="stat-value">{{ $coupons->total() }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--green">
                        <div class="stat-label">Active</div>
                        <div class="stat-value">{{ $activeCount }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--orange">
                        <div class="stat-label">Expired</div>
                        <div class="stat-value">{{ $expiredCount }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--blue">
                        <div class="stat-label">Total uses</div>
                        <div class="stat-value">{{ $totalUses }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <h4 class="card-title mb-0">All Coupons</h4>
                            <form method="GET" action="{{ route('admin.coupons.index') }}"
                                  class="d-flex align-items-center gap-1">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control form-control-sm"
                                       placeholder="Search code…"
                                       style="max-width:200px;">
                                <select name="filter" class="form-select form-select-sm" style="max-width:130px;">
                                    <option value="">All</option>
                                    <option value="active"   {{ request('filter') === 'active'   ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="expired"  {{ request('filter') === 'expired'  ? 'selected' : '' }}>Expired</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search" style="font-size:13px;"></i>
                                </button>
                                @if(request('search') || request('filter'))
                                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary btn-sm">
                                        <i data-feather="x" style="width:13px;height:13px;"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                        <div class="card-datatable table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Discount</th>
                                        <th>Min order</th>
                                        <th>Uses</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($coupons as $coupon)
                                        @php
                                            $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                                            $isFull    = $coupon->max_uses && $coupon->used_count >= $coupon->max_uses;
                                        @endphp
                                        <tr>
                                            {{-- Code --}}
                                            <td data-label="Code">
                                                <span class="coupon-code-pill">{{ $coupon->code }}</span>
                                            </td>

                                            {{-- Discount --}}
                                            <td data-label="Discount">
                                                @if($coupon->discount_type === 'percent')
                                                    <span class="badge badge-light-primary">
                                                        {{ number_format($coupon->value, 0) }}% off
                                                    </span>
                                                    @if($coupon->max_discount_amount)
                                                        <div class="text-muted mt-25" style="font-size:0.75rem;">
                                                            max ₦{{ number_format($coupon->max_discount_amount, 0) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light-success">
                                                        ₦{{ number_format($coupon->value, 0) }} off
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Min order --}}
                                            <td data-label="Min order">
                                                @if($coupon->min_order_amount > 0)
                                                    ₦{{ number_format($coupon->min_order_amount, 0) }}
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>

                                            {{-- Uses --}}
                                            <td data-label="Uses">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="{{ $isFull ? 'text-danger fw-bold' : '' }}">
                                                        {{ $coupon->used_count }}
                                                    </span>
                                                    @if($coupon->max_uses)
                                                        <span class="text-muted">/ {{ $coupon->max_uses }}</span>
                                                        <div class="usage-bar">
                                                            <div class="usage-bar__fill {{ $isFull ? 'usage-bar__fill--full' : '' }}"
                                                                 style="width:{{ min(100, ($coupon->used_count / $coupon->max_uses) * 100) }}%">
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted" style="font-size:0.75rem;">unlimited</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Expires --}}
                                            <td data-label="Expires">
                                                @if($coupon->expires_at)
                                                    <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}"
                                                          style="font-size:0.85rem;">
                                                        {{ $coupon->expires_at->format('d M Y') }}
                                                    </span>
                                                    @if(!$isExpired)
                                                        <div class="text-muted" style="font-size:0.72rem;">
                                                            in {{ $coupon->expires_at->diffForHumans() }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td data-label="Status">
                                                @if($isExpired)
                                                    <span class="badge bg-secondary">Expired</span>
                                                @elseif($isFull)
                                                    <span class="badge bg-warning text-dark">Limit reached</span>
                                                @elseif($coupon->is_active)
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td data-label="Actions">
                                                <div class="d-flex flex-wrap gap-1">
                                                    {{-- Toggle active --}}
                                                    <form method="POST"
                                                          action="{{ route('admin.coupons.toggleActive', $coupon) }}"
                                                          class="d-inline-flex align-items-center">
                                                        @csrf @method('PATCH')
                                                        <div class="form-check form-switch m-0">
                                                            <input type="checkbox" class="form-check-input" role="switch"
                                                                   {{ $coupon->is_active ? 'checked' : '' }}
                                                                   onchange="this.form.submit()"
                                                                   title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                        </div>
                                                    </form>

                                                    {{-- Edit --}}
                                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit">
                                                        <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                                    </a>

                                                    {{-- Delete --}}
                                                    <form method="POST"
                                                          action="{{ route('admin.coupons.destroy', $coupon) }}"
                                                          onsubmit="return confirm('Delete coupon {{ $coupon->code }}? This cannot be undone.')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                title="Delete">
                                                            <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                No coupons found.
                                                <a href="{{ route('admin.coupons.create') }}">Create one</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($coupons->hasPages())
                            <div class="card-footer d-flex justify-content-end">
                                {{ $coupons->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #6c757d;
    border-radius: 8px;
    padding: 0.85rem 1rem;
}
.stat-card--green  { border-left-color: #28c76f; }
.stat-card--orange { border-left-color: #ff9f43; }
.stat-card--blue   { border-left-color: #00cfe8; }
.stat-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.06em; color: #6e84a3; font-weight: 600; }
.stat-value { font-size: 1.75rem; font-weight: 700; color: #2c3e50; line-height: 1.2; }

.coupon-code-pill {
    display: inline-block;
    background: #f0f4ff;
    color: #3451b2;
    border: 1px dashed #a5b4fc;
    border-radius: 5px;
    padding: 3px 10px;
    font-family: monospace;
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.usage-bar {
    width: 56px;
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}
.usage-bar__fill {
    height: 100%;
    background: #28c76f;
    border-radius: 3px;
    transition: width 0.3s;
}
.usage-bar__fill--full { background: #ea5455; }

.mt-25 { margin-top: 0.25rem; }

@media (max-width: 768px) {
    .table thead { display: none; }
    .table tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 1rem;
        background: #fff;
    }
    .table td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        border: none;
        border-bottom: 1px solid #f0f0f0;
    }
    .table td:last-child { border-bottom: none; }
    .table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6c757d;
        flex-shrink: 0;
        margin-right: 0.75rem;
    }
    .table td[data-label="Actions"] { justify-content: flex-end; }
    .table td[data-label="Actions"]::before { display: none; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endsection