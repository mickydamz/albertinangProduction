
<?php $__env->startSection('content'); ?>

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

    
    <ul class="acct-breadcrumb" aria-label="Breadcrumb">
        <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
        <li><a href="<?php echo e(url('/account')); ?>">My Account</a></li>
        <li>My Orders</li>
    </ul>

    <div class="acct-layout">

        
        <?php echo $__env->make('sims.partials.account-sidebar', ['activeNav' => 'orders'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <div class="acct-main">

            
            <div class="acct-header">
                <div class="op-header__left">
                    <span class="acct-header__title">My Orders</span>
                    <!--<?php if(!empty($orders) && count($orders) > 0): ?>-->
                    <!--    <span class="op-header__count"><?php echo e(count($orders)); ?> order<?php echo e(count($orders) !== 1 ? 's' : ''); ?></span>-->
                    <!--<?php endif; ?>-->
                </div>
            </div>

            
            <div class="op-filters">
                <span class="op-filter-label">Filter:</span>
                <select class="op-filter-select" id="statusFilter" aria-label="Filter by status">
                    <option value="all">All Orders</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="completed">Completed</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                </select>
                <span class="op-filter-label">Sort:</span>
                <select class="op-filter-select" id="sortFilter" aria-label="Sort orders">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="price-high">Total: High → Low</option>
                    <option value="price-low">Total: Low → High</option>
                </select>
                <div class="op-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orderSearch" placeholder="Search orders…" aria-label="Search orders">
                </div>
            </div>

            
            <?php if(!empty($orders) && count($orders) > 0): ?>
                <div id="ordersList">
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $installationTotal  = $order->items->sum('installation_extra_ngn') ?? 0;
                            $itemsSubtotal      = $order->items->sum(fn($i) => $i->price * $i->quantity);
                            $couponDiscount     = (float) ($order->coupon_discount_ngn ?? 0);
                            $hasCoupon          = $order->hasCoupon();
                            // Pre-discount subtotal (items + installation, before coupon)
                            $subtotalBeforeDiscount = $itemsSubtotal + $installationTotal;
                            // Pickup display — prefer the specific point, fall back to the city/location
                            $pickupHeadline = $order->pickup_point_name ?: $order->pickup_location;
                            $statusLower = strtolower($order->status);
                        ?>

                        <div class="op-card"
                             data-open="true"
                             data-status="<?php echo e($statusLower); ?>"
                             data-total="<?php echo e($order->total); ?>"
                             data-date="<?php echo e($order->created_at->timestamp); ?>"
                             style="animation: fadeInUp .3s ease <?php echo e($index * 0.04); ?>s both;">

                            
                            <div class="op-card__head" role="button" aria-expanded="true" tabindex="0">
                                <div class="op-card__id-row">
                                    <div class="op-card__id">
                                        <i class="fas fa-receipt"></i>
                                        Order
                                    </div>
                                    
                                    <span class="op-card__order-num"><?php echo e($order->order_number); ?></span>
                                    <span class="op-status <?php echo e($statusLower); ?>">
                                        <?php echo e(ucfirst($order->status)); ?>

                                    </span>
                                </div>
                                <div class="op-card__head-right">
                                    <span class="op-card__date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo e($order->created_at->format('d M Y, h:i A')); ?>

                                    </span>
                                </div>
                                <div class="op-card__chevron" aria-hidden="true">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            
                            <div class="op-card__body">
                                <div class="op-card__body-inner">

                                    
                                    <div class="op-card__strip">

                                        
                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Order Total</div>
                                            <?php if($hasCoupon): ?>
                                                <span class="op-strip-val strike">₦<?php echo e(number_format($subtotalBeforeDiscount, 2)); ?></span>
                                            <?php endif; ?>
                                            <div class="op-strip-val green total">₦<?php echo e(number_format($order->total, 2)); ?></div>
                                        </div>

                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Payment</div>
                                            <div class="op-strip-val"><?php echo e(ucfirst($order->payment_method ?? '—')); ?></div>
                                        </div>

                                        <div class="op-card__strip-cell">
                                            <div class="op-strip-label">Items</div>
                                            <div class="op-strip-val"><?php echo e($order->items->sum('quantity')); ?></div>
                                        </div>

                                        <?php if($installationTotal > 0): ?>
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Installation</div>
                                                <div class="op-strip-val green">+₦<?php echo e(number_format($installationTotal, 2)); ?></div>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($hasCoupon): ?>
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Discount</div>
                                                <div class="op-strip-val red">−₦<?php echo e(number_format($couponDiscount, 2)); ?></div>
                                                <div class="op-coupon-badge">
                                                    <i class="fas fa-tag"></i>
                                                    <?php echo e($order->coupon_code_used); ?>

                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        
                                        <?php if($pickupHeadline): ?>
                                            <div class="op-card__strip-cell">
                                                <div class="op-strip-label">Pickup</div>
                                                <div class="op-strip-val" style="font-size:12.5px;font-family:var(--font-body);font-weight:600;">
                                                    <?php echo e($pickupHeadline); ?>

                                                </div>
                                                <?php if($order->pickup_point_address): ?>
                                                    <div class="op-strip-sub"><?php echo e($order->pickup_point_address); ?></div>
                                                <?php endif; ?>
                                                <?php if($order->pickup_point_name && $order->pickup_location): ?>
                                                    <div class="op-strip-sub"><?php echo e($order->pickup_location); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="op-card__items">
                                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="op-item">
                                                <img class="op-item__img"
                                                     src="<?php echo e($item->image_url); ?>"
                                                     alt="<?php echo e($item->name); ?>"
                                                     loading="lazy"
                                                     onerror="this.onerror=null;this.src='https://placehold.co/56x56/eef3e8/3d8012?text=No+Image';">
                                                <div class="op-item__info">
                                                    <div class="op-item__name"><?php echo e($item->name); ?></div>
                                                    <div class="op-item__meta">
                                                        <span class="op-item__price">₦<?php echo e(number_format($item->price, 2)); ?></span>
                                                        <span class="op-item__qty">× <?php echo e($item->quantity); ?></span>
                                                    </div>

                                                    
                                                    <?php if($item->installation_option): ?>
                                                        <div class="op-item__install">
                                                            <i class="fas fa-tools"></i>
                                                            <?php echo e($item->installation_option); ?>

                                                            <?php if($item->installation_extra_ngn > 0): ?>
                                                                <span class="op-item__install-extra">
                                                                    +₦<?php echo e(number_format($item->installation_extra_ngn, 0)); ?>

                                                                </span>
                                                            <?php else: ?>
                                                                <span style="color:var(--g500);margin-left:2px;font-weight:400;">(included)</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                    
                                    <?php if($hasCoupon || $installationTotal > 0): ?>
                                        <div class="op-card__totals">
                                            <table class="op-totals-table">
                                                <tr>
                                                    <td>Items subtotal</td>
                                                    <td>₦<?php echo e(number_format($itemsSubtotal, 2)); ?></td>
                                                </tr>
                                                <?php if($installationTotal > 0): ?>
                                                    <tr>
                                                        <td>Installation</td>
                                                        <td>₦<?php echo e(number_format($installationTotal, 2)); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if($hasCoupon): ?>
                                                    <tr class="discount">
                                                        <td>
                                                            <i class="fas fa-tag" style="font-size:10px;margin-right:4px;"></i>
                                                            Discount
                                                            <span style="font-size:11px;font-weight:400;color:var(--ink3);margin-left:4px;">(<?php echo e($order->coupon_code_used); ?>)</span>
                                                        </td>
                                                        <td>−₦<?php echo e(number_format($couponDiscount, 2)); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr class="grand-total">
                                                    <td>Total</td>
                                                    <td>₦<?php echo e(number_format($order->total, 2)); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <?php if($order->return): ?>
                                        <?php
                                            $rs = strtolower($order->return->status);
                                            $rnMap = [
                                                'pending'  => ['fa-clock',          'Return requested',  'We\'re reviewing your request.'],
                                                'approved' => ['fa-check-circle',   'Return approved',   'Please follow the return instructions sent to you.'],
                                                'rejected' => ['fa-times-circle',   'Return rejected',   $order->return->admin_notes ?: 'Contact support for details.'],
                                                'refunded' => ['fa-rotate-left',    'Return refunded',   'Your refund has been processed.'],
                                            ];
                                            $rn = $rnMap[$rs] ?? ['fa-info-circle', 'Return '.$rs, ''];
                                        ?>
                                        <div class="op-request-note <?php echo e($rs); ?>">
                                            <i class="fas <?php echo e($rn[0]); ?>"></i>
                                            <div>
                                                <span class="rn-title"><?php echo e($rn[1]); ?></span>
                                                <?php if($rn[2]): ?>
                                                    <span class="rn-sub"> — <?php echo e($rn[2]); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <?php if($order->cancellation): ?>
                                        <?php $cs = strtolower($order->cancellation->status); ?>
                                        <div class="op-request-note <?php echo e(in_array($cs,['approved','refunded']) ? $cs : 'pending'); ?>">
                                            <i class="fas fa-ban"></i>
                                            <div>
                                                <span class="rn-title">Cancellation <?php echo e($cs); ?></span>
                                                <span class="rn-sub"> — requested <?php echo e($order->cancellation->created_at->format('d M Y')); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    
                                    <div class="op-card__foot">
                                        <a href="<?php echo e(route('account.orders.invoice', $order->id)); ?>"
                                           class="op-btn op-btn--primary">
                                            <i class="fas fa-file-invoice"></i> Invoice
                                        </a>
                                        <!--<a href="<?php echo e(route('account.orders.invoice', $order->id)); ?>?print=1"-->
                                        <!--   class="op-btn op-btn--outline"-->
                                        <!--   target="_blank">-->
                                        <!--    <i class="fas fa-download"></i> Download-->
                                        <!--</a>-->
                                        
                                        
                                        <a href="<?php echo e(route('account.orders.invoice.download', $order->id)); ?>"
   class="op-btn op-btn--outline">
    <i class="fas fa-download"></i> Download 
</a>

                                        
                                        <?php if(in_array($statusLower, ['pending', 'paid', 'processing']) && !$order->cancellation): ?>
                                            <a href="<?php echo e(url('/account/orders/' . $order->id . '/cancel')); ?>"
                                               class="op-btn op-btn--danger">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        <?php endif; ?>

                                        
                                        <?php if(in_array($statusLower, ['shipped', 'delivered', 'completed']) && !$order->return): ?>
                                            <a href="<?php echo e(url('/account/orders/' . $order->id . '/return')); ?>"
                                               class="op-btn op-btn--outline">
                                                <i class="fas fa-undo"></i> Return
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

            <?php else: ?>
                <div class="op-empty">
                    <div class="op-empty__icon"><i class="fas fa-shopping-bag"></i></div>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Explore our range of products and find something you'll love.</p>
                    <a href="<?php echo e(url('/')); ?>" class="op-btn op-btn--primary" style="margin:0 auto;">
                        <i class="fas fa-store"></i> Start Shopping
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

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

    // ── Filter + sort ───────────────────────────────────────────────────────
    const cards        = document.querySelectorAll('.op-card');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter   = document.getElementById('sortFilter');
    const searchInput  = document.getElementById('orderSearch');
    const list         = document.getElementById('ordersList');

    function applyFilters() {
        const status = statusFilter?.value || 'all';
        const search = searchInput?.value.toLowerCase() || '';

        cards.forEach(card => {
            const match = (status === 'all' || card.dataset.status === status)
                       && (!search || card.textContent.toLowerCase().includes(search));
            card.style.display = match ? '' : 'none';
        });

        // Sort visible cards
        const sort    = sortFilter?.value || 'newest';
        const visible = Array.from(cards).filter(c => c.style.display !== 'none');
        visible.sort((a, b) => {
            if (sort === 'oldest')     return a.dataset.date  - b.dataset.date;
            if (sort === 'price-high') return b.dataset.total - a.dataset.total;
            if (sort === 'price-low')  return a.dataset.total - b.dataset.total;
            return b.dataset.date - a.dataset.date; // newest
        });
        if (list) visible.forEach(c => list.appendChild(c));
    }

    statusFilter?.addEventListener('change', applyFilters);
    sortFilter?.addEventListener('change',   applyFilters);
    searchInput?.addEventListener('input',   applyFilters);
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/orders.blade.php ENDPATH**/ ?>