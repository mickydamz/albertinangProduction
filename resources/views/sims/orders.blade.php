@extends('layouts.simslayout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ── Tokens (page-local semantic colours only) ── */
:root {
    --red: #dc2626; --red-bg: #fff0f0; --red-bd: #fecaca;
    --blue: #1a56db; --blue-bg: #eff6ff; --blue-bd: #bfdbfe;
    --amber: #d97706; --amber-bg: #fffbeb; --amber-bd: #fde68a;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--font-body); color: var(--ink); background: #f5f7f4; }

.op-header__left { display: flex; align-items: baseline; gap: 10px; }
.op-header__count { font-size: 13px; font-weight: 500; color: var(--ink3); }

/* ── Filter bar ── */
.op-filters {
    padding: 14px 0;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: center;
}
.op-filter-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--ink4);
    text-transform: uppercase;
    letter-spacing: .5px;
    white-space: nowrap;
}
.op-search {
    flex: 1;
    min-width: 180px;
    max-width: 300px;
    position: relative;
}
.op-search i {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ink4);
    font-size: 13px;
    pointer-events: none;
}
.op-search input {
    width: 100%;
    padding: 9px 12px 9px 22px;
    border: none;
    border-bottom: 1.5px solid var(--border);
    border-radius: 0;
    font-size: 14px;
    font-family: var(--font-body);
    color: var(--ink);
    background: transparent;
    outline: none;
    transition: border-color .18s;
}
.op-search input:focus { border-bottom-color: var(--g500); }
.op-search input::placeholder { color: var(--ink4); }

/* ── Filter triggers: big flat underline style ── */
.op-filters .cs-trigger {
    border: none;
    border-bottom: 1.5px solid var(--border);
    background: transparent;
    border-radius: 0;
    min-height: unset;
    padding: 9px 2px 9px 0;
    font-size: 15px;
    font-weight: 600;
    color: var(--ink);
    transition: color .15s, border-color .15s;
}
.op-filters .cs-trigger:hover {
    color: var(--g600);
    border-bottom-color: var(--border2);
}
.op-filters .cs-wrap.cs-open .cs-trigger {
    border-bottom: 1.5px solid var(--g500);
    box-shadow: none;
    background: transparent;
    color: var(--g700);
}
.op-filters .cs-dropdown {
    border-radius: 0;
    box-shadow: 0 8px 24px rgba(0,0,0,.09);
    border: none;
    border-top: 2px solid var(--g500);
    max-height: calc(5 * 44px);
    overflow-y: auto;
    scrollbar-width: none;
}
.op-filters .cs-dropdown::-webkit-scrollbar { display: none; }
.op-filters .cs-option--selected { background: transparent !important; color: var(--g700); font-weight: 700; }

/* ── Order card ── */
.op-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 0;
    margin-bottom: 6px;
    overflow: visible;
}
.op-card:last-child { margin-bottom: 0; }

/* Card header */
.op-card__head {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    padding: 15px 20px;
    cursor: pointer;
    user-select: none;
    background: transparent;
    border-bottom: 1px solid transparent;
    transition: background .15s, border-color .15s;
}
.op-card__head:hover { background: var(--surf2); }
.op-card[data-open="true"] .op-card__head { border-bottom-color: var(--border); background: transparent; }

.op-card__id-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.op-card__id {
    font-family: var(--font-head);
    font-size: 15px;
    font-weight: 800;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: 7px;
    letter-spacing: -.2px;
}
.op-card__id i { color: var(--g500); font-size: 11px; }
.op-card__order-num {
    font-size: 12px;
    font-weight: 600;
    color: var(--ink3);
    background: var(--surf3);
    border: 1px solid var(--border2);
    border-radius: var(--radius-xs);
    padding: 2px 8px;
    font-family: monospace;
    letter-spacing: .3px;
}

/* Status pill */
.op-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.op-status::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: .7; }
.op-status.delivered  { background: #e6f4ea; color: #2e7d32; border: 1px solid #b9dfbe; }
.op-status.completed  { background: #e6f4ea; color: #2e7d32; border: 1px solid #b9dfbe; }
.op-status.processing { background: #e3f2fd; color: #1565c0; border: 1px solid #93c5fd; }
.op-status.shipped    { background: #fff8e1; color: #b45309; border: 1px solid #fde68a; }
.op-status.pending    { background: #fff8e1; color: #b45309; border: 1px solid #fde68a; }
.op-status.paid       { background: #e6f4ea; color: #2e7d32; border: 1px solid #b9dfbe; }
.op-status.cancelled  { background: #ffebee; color: #c62828; border: 1px solid #fecdd3; }
.op-status.refunded   { background: #eceff1; color: #455a64; border: 1px solid #cfd8dc; }
.op-status.returned   { background: #f3e5f5; color: #6a1b9a; border: 1px solid #d8b4fe; }

.op-card__head-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-left: auto;
}
.op-card__date { font-size: 11.5px; color: var(--ink3); display: flex; align-items: center; gap: 5px; }
.op-card__date i { font-size: 10px; }
.op-card__chevron {
    display: flex; align-items: center; justify-content: center;
    padding: 6px;
    margin-left: 16px;
    color: var(--ink3);
    font-size: 13px;
    transition: transform .25s, color .15s;
    flex-shrink: 0;
}
.op-card__head:hover .op-card__chevron { color: var(--g600); }
.op-card[data-open="true"] .op-card__chevron {
    transform: rotate(180deg);
    color: var(--g600);
}

/* Collapsible body */
.op-card__body {
    display: grid;
    grid-template-rows: 0fr;
    transition: grid-template-rows .28s ease;
}
.op-card[data-open="true"] .op-card__body { grid-template-rows: 1fr; }
.op-card__body-inner { overflow: hidden; }

/* Summary strip */
.op-card__strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    border-bottom: 1px solid var(--border);
}
.op-card__strip-cell {
    padding: 11px 16px;
    border-right: 1px solid var(--border);
}
.op-card__strip-cell:last-child { border-right: none; }
.op-strip-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--ink4);
    margin-bottom: 3px;
}
.op-strip-val {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ink);
    font-family: var(--font-head);
}
.op-strip-val.green  { color: var(--g600); }
.op-strip-val.red    { color: var(--red); }
.op-strip-val.total  { font-size: 17px; letter-spacing: -.4px; }
.op-strip-val.strike {
    font-size: 11px;
    font-weight: 400;
    color: var(--ink4);
    text-decoration: line-through;
    font-family: var(--font-body);
    display: block;
    margin-bottom: 1px;
}
.op-strip-sub {
    font-size: 11px;
    color: var(--ink4);
    margin-top: 2px;
    font-family: var(--font-body);
    font-weight: 400;
    line-height: 1.35;
}

/* Coupon badge in strip */
.op-coupon-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    padding: 2px 8px;
    background: var(--amber-bg);
    border: 1px solid var(--amber-bd);
    border-radius: var(--radius-xs);
    font-size: 10.5px;
    font-weight: 700;
    color: var(--amber);
    font-family: monospace;
    letter-spacing: .3px;
    width: fit-content;
}
.op-coupon-badge i { font-size: 9px; }

/* Items */
.op-card__items { padding: 2px 0; }
.op-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--surf2);
    transition: background .13s;
}
.op-item:last-child { border-bottom: none; }
.op-item:hover { background: var(--surf2); }
.op-item__img {
    width: 68px; height: 68px;
    object-fit: cover;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    background: #f8f9fa;
    flex-shrink: 0;
}
.op-item__info { flex: 1; min-width: 0; }
.op-item__name {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.35;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.op-item__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    font-size: 12px;
    color: var(--ink3);
}
.op-item__price { font-weight: 800; color: var(--ink); }
.op-item__qty   { color: var(--ink3); }
.op-item__sku   { color: var(--ink4); font-size: 11px; }

/* Installation badge */
.op-item__install {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 5px;
    padding: 3px 9px 3px 7px;
    background: var(--g50);
    border: 1px solid var(--border2);
    border-radius: var(--radius-xs);
    font-size: 11px;
    font-weight: 600;
    color: var(--g700);
    width: fit-content;
}
.op-item__install i { font-size: 10px; color: var(--g500); }
.op-item__install-extra {
    font-weight: 700;
    color: var(--g600);
    margin-left: 3px;
}

/* Order totals breakdown */
.op-card__totals {
    padding: 12px 18px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    background: var(--surf);
}
.op-totals-table {
    min-width: 240px;
    font-size: 12.5px;
}
.op-totals-table tr td {
    padding: 4px 0;
    color: var(--ink3);
}
.op-totals-table tr td:last-child {
    text-align: right;
    font-weight: 600;
    color: var(--ink);
    padding-left: 24px;
}
.op-totals-table tr.discount td     { color: var(--g600); }
.op-totals-table tr.discount td:last-child { color: var(--g600); }
.op-totals-table tr.grand-total td  {
    padding-top: 10px;
    border-top: 2px solid var(--border);
    font-size: 14px;
    font-weight: 800;
    color: var(--ink);
    font-family: var(--font-head);
}

/* Card footer */
.op-card__foot {
    display: flex;
    gap: 8px;
    padding: 12px 18px;
    background: transparent;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.op-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    font-family: var(--font-body);
    cursor: pointer;
    border: none;
    transition: all .18s;
    text-decoration: none;
    white-space: nowrap;
}
.op-btn i { font-size: 10px; }
.op-btn--primary { background: var(--g500); color: #fff; }
.op-btn--primary:hover { background: var(--g600); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(78,122,26,.25); }
.op-btn--outline { background: var(--surf); color: var(--ink2); border: 1.5px solid var(--border); }
.op-btn--outline:hover { border-color: var(--g400); color: var(--g600); background: var(--g50); }
.op-btn--danger  { background: var(--red-bg); color: var(--red); border: 1.5px solid var(--red-bd); }
.op-btn--danger:hover  { background: #fde8e8; border-color: var(--red); }

/* ── Return / cancellation status note ── */
.op-request-note {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    padding: 11px 18px;
    font-size: 12px;
    border-top: 1px solid var(--border);
    line-height: 1.45;
}
.op-request-note i { font-size: 13px; margin-top: 1px; flex-shrink: 0; }
.op-request-note .rn-title { font-weight: 700; }
.op-request-note .rn-sub { color: var(--ink3); font-weight: 400; }
.op-request-note.pending  { background: var(--amber-bg); color: var(--amber); border-top-color: var(--amber-bd); }
.op-request-note.approved { background: #e6f4ea; color: #2e7d32; border-top-color: #b9dfbe; }
.op-request-note.rejected { background: var(--red-bg); color: var(--red); border-top-color: var(--red-bd); }
.op-request-note.refunded { background: #eceff1; color: #455a64; border-top-color: #cfd8dc; }

/* ── Empty state ── */
.op-empty {
    background: var(--surf);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 56px 24px;
    text-align: center;
}
.op-empty__icon {
    width: 68px; height: 68px;
    background: var(--g50);
    border: 1px solid var(--border2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
}
.op-empty__icon i { font-size: 26px; color: var(--g500); }
.op-empty h3 { font-family: var(--font-head); font-size: 1.1rem; font-weight: 700; color: var(--ink); margin-bottom: 7px; }
.op-empty p  { font-size: 13px; color: var(--ink3); margin-bottom: 22px; max-width: 320px; margin-left: auto; margin-right: auto; }

/* ── Animations ── */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsive ── */
@media (max-width: 600px) {
    .op-card__strip { grid-template-columns: repeat(2,1fr); }
    .op-card__strip-cell:nth-child(odd) { border-right: 1px solid var(--border); }
    .op-card__strip-cell { border-right: none; border-bottom: 1px solid var(--border); }
    .op-card__strip-cell:nth-last-child(-n+2) { border-bottom: none; }
    .op-item { gap: 10px; padding: 11px 14px; }
    .op-item__img { width: 46px; height: 46px; }
    .op-card__foot { padding: 10px 14px; }
    .op-totals-table { min-width: 200px; }
}
</style>

<div class="acct-wrap">

    {{-- Breadcrumb --}}
    <ul class="acct-breadcrumb" aria-label="Breadcrumb">
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><a href="{{ url('/account') }}">My Account</a></li>
        <li>My Orders</li>
    </ul>

    <div class="acct-layout">

        {{-- Sidebar --}}
        @include('sims.partials.account-sidebar', ['activeNav' => 'orders'])

        {{-- Main --}}
        <div class="acct-main">

            {{-- Header --}}
            <div class="acct-header">
                <div class="op-header__left">
                    <span class="acct-header__title">My Orders</span>
                    <!--@if(!empty($orders) && count($orders) > 0)-->
                    <!--    <span class="op-header__count">{{ count($orders) }} order{{ count($orders) !== 1 ? 's' : '' }}</span>-->
                    <!--@endif-->
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('account.orders') }}" class="op-filters">
                <label for="periodFilter" class="op-filter-label">Date:</label>
                <select name="period" id="periodFilter" aria-label="Order date range">
                    @foreach(['all' => 'All history', '1' => 'Last month', '3' => 'Last 3 months', '6' => 'Last 6 months', '12' => 'Last year'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('period', 'all') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <span class="op-filter-label">Filter:</span>
                <select class="op-filter-select" name="status" id="statusFilter" aria-label="Filter by status">
                    <option value="all" @selected(request('status', 'all') === 'all')>All Orders</option>
                    <option value="pending" @selected(request('status', 'all') === 'pending')>Pending</option>
                    <option value="processing" @selected(request('status', 'all') === 'processing')>Processing</option>
                    <option value="shipped" @selected(request('status', 'all') === 'shipped')>Shipped</option>
                    <option value="delivered" @selected(request('status', 'all') === 'delivered')>Delivered</option>
                    <option value="completed" @selected(request('status', 'all') === 'completed')>Completed</option>
                    <option value="paid" @selected(request('status', 'all') === 'paid')>Paid</option>
                    <option value="cancelled" @selected(request('status', 'all') === 'cancelled')>Cancelled</option>
                    <option value="refunded" @selected(request('status', 'all') === 'refunded')>Refunded</option>
                </select>
                <span class="op-filter-label">Sort:</span>
                <select class="op-filter-select" name="sort" id="sortFilter" aria-label="Sort orders">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest First</option>
                    <option value="oldest" @selected(request('sort', 'newest') === 'oldest')>Oldest First</option>
                    <option value="price-high" @selected(request('sort', 'newest') === 'price-high')>Total: High → Low</option>
                    <option value="price-low" @selected(request('sort', 'newest') === 'price-low')>Total: Low → High</option>
                </select>
                <div class="op-search">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" id="orderSearch" placeholder="Search orders…" aria-label="Search orders">
                </div>
                <button type="submit" class="op-btn op-btn--primary">Apply filters</button>
                <a href="{{ route('account.orders') }}" class="op-btn op-btn--outline">Reset</a>
            </form>
            <p style="margin-bottom:16px">{{ $orderCount }} matching orders</p>

            {{-- Orders list --}}
            @if(!empty($orders) && count($orders) > 0)
                <div id="ordersList">
                    @foreach($orders as $index => $order)
                        @php
                            $installationTotal  = $order->items->sum('installation_extra_ngn') ?? 0;
                            $itemsSubtotal      = $order->items->sum(fn($i) => $i->price * $i->quantity);
                            $couponDiscount     = (float) ($order->coupon_discount_ngn ?? 0);
                            $hasCoupon          = $order->hasCoupon();
                            // Pre-discount subtotal (items + installation, before coupon)
                            $subtotalBeforeDiscount = $itemsSubtotal + $installationTotal;
                            // Pickup display — prefer the specific point, fall back to the city/location
                            $pickupHeadline = $order->pickup_point_name ?: $order->pickup_location;
                            $statusLower = strtolower($order->status);
                            $refundProgress = \App\Support\RefundProgress::forOrder($order);
                        @endphp

                        <div class="op-card"
                             data-open="true"
                             data-status="{{ $statusLower }}"
                             data-total="{{ $order->total }}"
                             data-date="{{ $order->created_at->timestamp }}"
                             style="animation: fadeInUp .3s ease {{ $index * 0.04 }}s both;">

                            {{-- Head (accordion trigger) --}}
                            <div class="op-card__head" role="button" aria-expanded="true" tabindex="0">
                                <div class="op-card__id-row">
                                    <div class="op-card__id">
                                        <i class="fas fa-receipt"></i>
                                        Order
                                    </div>
                                    {{-- Order number shown as a monospace badge --}}
                                    <span class="op-card__order-num">{{ $order->order_number }}</span>
                                    <span class="op-status {{ $statusLower }}">
                                        Order status: {{ $order->status === 'refunded' ? ($refundProgress['label'] ?? 'Refund awaiting confirmation') : ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                    @if($refundProgress)
                                        <span class="op-status {{ $refundProgress['processed'] ? 'refunded' : 'pending' }}">
                                            Refund status: {{ $refundProgress['label'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="op-card__head-right">
                                    <span class="op-card__date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $order->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>
                                <div class="op-card__chevron" aria-hidden="true">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            {{-- Collapsible body --}}
                            <div class="op-card__body">
                                <div class="op-card__body-inner">

                                    {{-- Summary strip --}}
                                    <div class="op-card__strip">

                                        {{-- Order total cell — shows strike-through original if coupon was used --}}
                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Order Total</div>
                                            @if($hasCoupon)
                                                <span class="op-strip-val strike">₦{{ number_format($subtotalBeforeDiscount, 2) }}</span>
                                            @endif
                                            <div class="op-strip-val green total">₦{{ number_format($order->total, 2) }}</div>
                                        </div>

                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Payment</div>
                                            <div class="op-strip-val">{{ ucfirst($order->payment_method ?? '—') }}</div>
                                        </div>

                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Items</div>
                                            <div class="op-strip-val">{{ $order->items->sum('quantity') }}</div>
                                        </div>

                                        @if($installationTotal > 0)
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Installation</div>
                                                <div class="op-strip-val green">+₦{{ number_format($installationTotal, 2) }}</div>
                                            </div>
                                        @endif

                                        {{-- Coupon cell — only shown when a coupon was applied --}}
                                        @if($hasCoupon)
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Discount</div>
                                                <div class="op-strip-val red">−₦{{ number_format($couponDiscount, 2) }}</div>
                                                <div class="op-coupon-badge">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $order->coupon_code_used }}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Pickup cell — point name as headline, location + address beneath --}}
                                        @if($pickupHeadline)
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Pickup</div>
                                                <div class="op-strip-val" style="font-size:12.5px;font-family:var(--font-body);font-weight:600;">
                                                    {{ $pickupHeadline }}
                                                </div>
                                                @if($order->pickup_point_address)
                                                    <div class="op-strip-sub">{{ $order->pickup_point_address }}</div>
                                                @endif
                                                @if($order->pickup_point_name && $order->pickup_location)
                                                    <div class="op-strip-sub">{{ $order->pickup_location }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    @if($order->fulfillment_method === 'delivery')
                                    <div class="op-card__strip-cell">
                                        <div class="op-strip-label">Home delivery</div>
                                        @if($order->shipping_address)
                                            <div class="op-strip-val" style="font-size:12.5px;font-family:var(--font-body);font-weight:600;">{{ $order->shipping_address }}</div>
                                        @endif
                                        <div class="op-strip-sub">{{ $order->delivery_location_name }}, {{ $order->delivery_state_name }}</div>
                                        <div class="op-strip-sub">Delivery fee: ₦{{ number_format($order->shipping_cost, 2) }}</div>
                                    </div>
                                    @else
                                        @php $collectionPoint = $pickupHeadline ?: 'your selected collection point'; @endphp
                                        @if(in_array($order->status, ['pending', 'paid', 'processing'], true))
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Collection</div>
                                                <div class="op-strip-sub">We're preparing your order — we'll let you know when it's ready to collect at {{ $collectionPoint }}.</div>
                                            </div>
                                        @elseif($order->status === 'ready_for_pickup')
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Ready for collection</div>
                                                <div class="op-strip-sub">Bring your order number and photo ID to {{ $collectionPoint }} to collect your order.</div>
                                            </div>
                                        @elseif($order->status === 'completed')
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Collection</div>
                                                <div class="op-strip-sub">Collected from {{ $collectionPoint }}. Thank you!</div>
                                            </div>
                                        @endif
                                        {{-- cancelled / refunded / refund states: nothing to collect, so no instruction --}}
                                    @endif
                                    @if($refundProgress)
                                    <section aria-label="Refund progress" class="op-card__strip-cell">
                                        <div class="op-strip-label">Refund progress</div>
                                        <div class="op-strip-val">{{ $refundProgress['label'] }}</div>
                                        <div class="op-strip-sub">{{ $refundProgress['message'] }}</div>
                                    </section>
                                    @endif
                                    {{-- Items --}}
                                    <div class="op-card__items">
                                        @foreach($order->items as $item)
                                            <div class="op-item">
                                                <img class="op-item__img"
                                                     src="{{ $item->image_url }}"
                                                     alt="{{ $item->name }}"
                                                     loading="lazy"
                                                     onerror="this.onerror=null;this.src='https://placehold.co/56x56/eef3e8/3d8012?text=No+Image';">
                                                <div class="op-item__info">
                                                    <div class="op-item__name">{{ $item->name }}</div>
                                                    <div class="op-item__meta">
                                                        <span class="op-item__price">₦{{ number_format($item->price, 2) }}</span>
                                                        <span class="op-item__qty">× {{ $item->quantity }}</span>
                                                    </div>

                                                    {{-- Installation option badge --}}
                                                    @if($item->installation_option)
                                                        <div class="op-item__install">
                                                            <i class="fas fa-tools"></i>
                                                            {{ $item->installation_option }}
                                                            @if($item->installation_extra_ngn > 0)
                                                                <span class="op-item__install-extra">
                                                                    +₦{{ number_format($item->installation_extra_ngn, 0) }}
                                                                </span>
                                                            @else
                                                                <span style="color:var(--g500);margin-left:2px;font-weight:400;">(included)</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Totals breakdown — only shown when there's something meaningful to break down --}}
                                    @if($hasCoupon || $installationTotal > 0)
                                        <div class="op-card__totals">
                                            <table class="op-totals-table">
                                                <tr>
                                                    <td>Items subtotal</td>
                                                    <td>₦{{ number_format($itemsSubtotal, 2) }}</td>
                                                </tr>
                                                @if($installationTotal > 0)
                                                    <tr>
                                                        <td>Installation</td>
                                                        <td>₦{{ number_format($installationTotal, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if($hasCoupon)
                                                    <tr class="discount">
                                                        <td>
                                                            <i class="fas fa-tag" style="font-size:10px;margin-right:4px;"></i>
                                                            Discount
                                                            <span style="font-size:11px;font-weight:400;color:var(--ink3);margin-left:4px;">({{ $order->coupon_code_used }})</span>
                                                        </td>
                                                        <td>−₦{{ number_format($couponDiscount, 2) }}</td>
                                                    </tr>
                                                @endif
                                                <tr class="grand-total">
                                                    <td>Total</td>
                                                    <td>₦{{ number_format($order->total, 2) }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endif

                                    {{-- Return request status note --}}
                                    @if($order->return)
                                        @php
                                            $rs = strtolower($order->return->status);
                                            // Base the wording on the order's real refund state, not just the
                                            // request record — approving a return triggers the refund, so an
                                            // 'approved' record can actually mean the refund failed or is pending.
                                            $refundDone   = $order->status === 'refunded';
                                            $refundFailed = $order->status === 'refund_failed';
                                            $refundBusy   = in_array($order->status, ['refund_pending', 'partially_refunded'], true);
                                            $rnMap = [
                                                'pending'  => ['fa-clock', 'Return requested', 'We\'re reviewing your request.'],
                                                'approved' => $refundFailed
                                                    ? ['fa-exclamation-triangle', 'Return approved', 'We couldn\'t process the refund automatically — our team will sort it out.']
                                                    : ($refundBusy
                                                        ? ['fa-rotate-left', 'Refund processing', 'Your return is approved and the gateway is confirming your refund.']
                                                        : ['fa-check-circle', 'Return approved', 'Please follow the return instructions sent to you.']),
                                                'rejected' => ['fa-times-circle', 'Return rejected', $order->return->admin_notes ?: 'Contact support for details.'],
                                                'refunded' => $refundDone
                                                    ? ['fa-rotate-left', 'Return refunded', 'Your refund has been processed.']
                                                    : ($refundFailed
                                                        ? ['fa-exclamation-triangle', 'Refund failed', 'We could not confirm the refund — our team will sort it out.']
                                                        : ['fa-rotate-left', 'Refund processing', 'The gateway is confirming your refund.']),
                                            ];
                                            $rn = $rnMap[$rs] ?? ['fa-info-circle', 'Return '.$rs, ''];
                                            // Colour follows the true state: red if the refund failed, amber while
                                            // it is still confirming, otherwise the request's own status colour.
                                            $rnClass = $refundFailed ? 'rejected'
                                                     : (($refundBusy && in_array($rs, ['approved','refunded'], true)) ? 'pending'
                                                     : $rs);
                                        @endphp
                                        @if($refundProgress)
                                            @php
                                                $rn = ['fa-rotate-left', $refundProgress['label'], $refundProgress['message']];
                                            @endphp
                                            @php
                                                $rnClass = $refundProgress['processed'] ? 'refunded' : ($refundProgress['status'] === 'failed' ? 'rejected' : 'pending');
                                            @endphp
                                        @endif
                                        <div class="op-request-note {{ $rnClass }}">
                                            <i class="fas {{ $rn[0] }}"></i>
                                            <div>
                                                <span class="rn-title">{{ $rn[1] }}</span>
                                                @if($rn[2])
                                                    <span class="rn-sub"> — {{ $rn[2] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Cancellation request status note --}}
                                    @if($order->cancellation)
                                        @php
                                            $cs = strtolower($order->cancellation->status);
                                            // A cancellation marked "refunded" only means the refund was initiated.
                                            // Reflect the order's real refund state so we never claim a refund is
                                            // done while the gateway is still confirming it (mirrors the return note).
                                            $refundDone     = $order->status === 'refunded';
                                            $refundFailed   = $order->status === 'refund_failed';
                                            $cnMap = [
                                                'pending'  => ['Cancellation requested', 'We\'re reviewing your request.'],
                                                'approved' => $refundFailed
                                                    ? ['Refund failed', 'We couldn\'t process the refund automatically — our team will sort it out.']
                                                    : ['Cancellation approved', 'Your cancellation has been approved.'],
                                                'rejected' => ['Cancellation rejected',  $order->cancellation->admin_notes ?: 'Contact support for details.'],
                                                'refunded' => [
                                                    $refundDone ? 'Cancellation refunded' : ($refundFailed ? 'Refund failed' : 'Refund processing'),
                                                    $refundDone ? 'Your refund has been processed.' : ($refundFailed ? 'We could not confirm the refund — our team will sort it out.' : 'The gateway is confirming your refund.'),
                                                ],
                                            ];
                                            $cn = $cnMap[$cs] ?? ['Cancellation '.$cs, ''];
                                            // Colour follows the TRUE state: amber while processing, not green.
                                            $cnClass = $refundFailed ? 'rejected'
                                                     : (($cs === 'refunded' && !$refundDone) ? 'pending'
                                                     : (in_array($cs, ['approved','refunded'], true) ? $cs : 'pending'));
                                        @endphp
                                        @if($refundProgress)
                                            @php
                                                $cn = [$refundProgress['label'], $refundProgress['message']];
                                            @endphp
                                            @php
                                                $cnClass = $refundProgress['processed'] ? 'refunded' : ($refundProgress['status'] === 'failed' ? 'rejected' : 'pending');
                                            @endphp
                                        @endif
                                        <div class="op-request-note {{ $cnClass }}">
                                            <i class="fas fa-ban"></i>
                                            <div>
                                                <span class="rn-title">{{ $cn[0] }}</span>
                                                <span class="rn-sub"> — requested {{ $order->cancellation->created_at->format('d M Y') }}</span>
                                                @if($cn[1])
                                                    <span class="rn-sub">{{ $cn[1] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Actions --}}
                                    <div class="op-card__foot">
                                        <a href="{{ route('account.orders.invoice', $order->id) }}"
                                           class="op-btn op-btn--primary">
                                            <i class="fas fa-file-invoice"></i> Invoice
                                        </a>
                                        <!--<a href="{{ route('account.orders.invoice', $order->id) }}?print=1"-->
                                        <!--   class="op-btn op-btn--outline"-->
                                        <!--   target="_blank">-->
                                        <!--    <i class="fas fa-download"></i> Download-->
                                        <!--</a>-->
                                        
                                        
                                        <a href="{{ route('account.orders.invoice.download', $order->id) }}"
   class="op-btn op-btn--outline">
    <i class="fas fa-download"></i> Download 
</a>

                                        {{-- Cancel — available right after purchase, before the order leaves the warehouse --}}
                                        @if($order->canCancel() && !$order->cancellation)
                                            <a href="{{ url('/account/orders/' . $order->id . '/cancel') }}"
                                               class="op-btn op-btn--danger">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        @endif

                                        {{-- Return — available once the order is on its way or has arrived --}}
                                        @if($order->canReturn() && !$order->return)
                                            <a href="{{ url('/account/orders/' . $order->id . '/return') }}"
                                               class="op-btn op-btn--outline">
                                                <i class="fas fa-undo"></i> Return
                                            </a>
                                        @endif
                                    </div>

                                </div>{{-- /.op-card__body-inner --}}
                            </div>{{-- /.op-card__body --}}
                        </div>
                    @endforeach
                </div>
                <nav aria-label="Order history pages" style="margin-top:20px">{{ $orders->links() }}</nav>

            @else
                <div class="op-empty">
                    <div class="op-empty__icon"><i class="fas fa-shopping-bag"></i></div>
                    <h3>No matching orders</h3>
                    <p>Try another date range or reset your filters to see all your orders.</p>
                    <a href="{{ url('/') }}" class="op-btn op-btn--primary" style="margin:0 auto;">
                        <i class="fas fa-store"></i> Start Shopping
                    </a>
                </div>
            @endif

        </div>{{-- /.acct-main --}}
    </div>{{-- /.acct-layout --}}
</div>{{-- /.acct-wrap --}}

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Accordion toggle ────────────────────────────────────────────────────
    document.querySelectorAll('.op-card__head').forEach(head => {
        function toggle() {
            const card   = head.closest('.op-card');
            const isOpen = card.dataset.open === 'true';
            card.dataset.open = isOpen ? 'false' : 'true';
            head.setAttribute('aria-expanded', String(!isOpen));
        }
        head.addEventListener('click', toggle);
        head.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
        });
    });


});
</script>

@endsection
