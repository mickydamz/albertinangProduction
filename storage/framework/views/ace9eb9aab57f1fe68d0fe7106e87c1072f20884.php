<?php $__env->startSection('title', 'Invoice #' . $order->order_number); ?>

<?php $__env->startPush('styles'); ?>
<style>
    :root {
        --inv-accent:      <?php echo e($invAccent      ?? '#1a7a4a'); ?>;
        --inv-accent-bg:   <?php echo e($invAccentBg    ?? '#e8f5ee'); ?>;
        --inv-accent-fill: <?php echo e($invAccentBg    ?? '#eef7f1'); ?>;
    }
    .invoice-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 24px; }
    .inv-btn {
        background: var(--inv-accent); color: #fff; padding: 10px 22px; border: none;
        border-radius: 5px; cursor: pointer; font-weight: 700; font-size: 14px;
        display: inline-flex; align-items: center; gap: 8px; transition: background .2s;
    }
    .inv-btn:hover { background: #155e38; }
    @media print {
        .invoice-actions { display: none !important; }
        body, html { background: #fff !important; padding: 0 !important; }
        /* Cancel the mobile shrink transform for print/PDF output */
        .inv-page { box-shadow: none !important; margin: 0 !important; transform: none !important; width: auto !important; }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    }

    .inv-page {
        background: #ffffff;
        max-width: 960px;
        margin: 0 auto;
        padding: 28px 36px;
        color: #1a1a1a;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1.45;
    }

    /* Header — sizes matched to PDF output */
    .inv-header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .inv-header-tbl td { vertical-align: middle; }
    .inv-brand-logo { height: 60px; width: auto; max-width: 220px; object-fit: contain; display: block; }
    .inv-right-head { text-align: right; }
    .inv-title { font-size: 24px; font-weight: bold; color: var(--inv-accent); letter-spacing: 2px; display: block; }
    .inv-contact-small { font-size: 10px; color: #888; margin-top: 3px; line-height: 1.7; }

    /* Issue date */
    .inv-issue-date {
        text-align: right; font-weight: bold; font-size: 12px;
        letter-spacing: .4px; text-transform: uppercase; color: #1a1a1a; margin-bottom: 16px;
    }

    /* Green bar */
    .inv-green-bar { background: var(--inv-accent); height: 26px; width: 100%; margin-bottom: 0; }

    /* Info tables */
    .inv-info-tbl { width: 100%; border-collapse: collapse; border: 1px solid #d0d0d0; margin-bottom: 0; }
    .inv-info-tbl td { width: 50%; vertical-align: top; }
    .inv-info-header {
        background: var(--inv-accent-bg); padding: 10px 20px; font-weight: 700; font-size: 12.5px;
        color: var(--inv-accent); text-transform: uppercase; letter-spacing: .5px;
        border-bottom: 1px solid #d0d0d0;
    }
    .inv-info-header.right { text-align: right; }
    .inv-info-body { padding: 18px 20px; font-size: 13.5px; line-height: 1.8; }
    .inv-info-right { border-left: 1px solid #d0d0d0; }
    .inv-info-body-right { padding: 18px 20px; text-align: right; font-size: 13.5px; line-height: 1.9; }

    /* Fulfilment box */
    .inv-fulfilment-tbl {
        width: 100%; border-collapse: collapse;
        border: 1px solid #d0d0d0; border-top: 0; margin-bottom: 22px;
    }
    .inv-fulfilment-header {
        background: var(--inv-accent-bg); padding: 10px 20px; font-weight: 700; font-size: 12.5px;
        color: var(--inv-accent); text-transform: uppercase; letter-spacing: .5px;
        border-bottom: 1px solid #d0d0d0;
    }
    .inv-fulfilment-body { padding: 18px 20px; font-size: 13.5px; line-height: 1.8; }

    /* Items table */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    table.items thead th {
        background: #1f2937; color: #fff; font-size: 12px; font-weight: 700;
        text-align: left; padding: 11px 14px; letter-spacing: .5px;
        text-transform: uppercase; border: 1px solid #1f2937;
    }
    table.items thead th.num { text-align: right; }
    table.items tbody td { padding: 11px 14px; border: 1px solid #e0e0e0; font-size: 13.5px; color: #1a1a1a; }
    table.items tbody td.num { text-align: right; }
    table.items tbody tr:nth-child(even) td { background: #fafafa; }
    .item-install-row td { background: #f7fdf9 !important; border-top: none !important; padding-top: 4px !important; padding-bottom: 8px !important; }
    .item-sku { font-size: 11.5px; color: #888; margin-top: 2px; }
    .item-install-label { font-size: 12.5px; color: var(--inv-accent); padding-left: 24px; }

    /* Notes + Totals */
    table.inv-bottom { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    table.inv-bottom > tbody > tr > td { vertical-align: top; }
    .inv-notes-box { background: var(--inv-accent-fill); padding: 18px 20px; font-size: 13px; line-height: 1.8; }
    .inv-notes-box strong { display: block; margin-bottom: 8px; font-size: 12.5px; color: var(--inv-accent); text-transform: uppercase; letter-spacing: .4px; }
    .inv-totals-box { background: var(--inv-accent-fill); padding: 18px 20px; font-size: 13.5px; }
    .inv-totals-tbl { width: 100%; border-collapse: collapse; }
    .inv-totals-tbl td { padding: 4px 0; }
    .inv-totals-tbl td.lbl { color: #333; font-weight: 600; }
    .inv-totals-tbl td.val { text-align: right; color: #1a1a1a; }
    .inv-totals-tbl tr.total-row td { border-top: 2px solid #1a1a1a; font-weight: 700; font-size: 15px; padding-top: 8px; }

    /* Terms */
    .inv-terms { border: 1px solid #d0d0d0; padding: 16px 20px; font-size: 12px; line-height: 1.9; color: #444; margin-bottom: 28px; }
    .inv-terms strong { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #1a1a1a; }

    /* Footer */
    .inv-footer { text-align: center; color: #888; font-size: 12.5px; margin-top: 12px; }

    /* ── Mobile: shrink the whole invoice to fit (same layout, just smaller) ──
       The full desktop layout is rendered at a fixed design width, then scaled
       down proportionally by JS so everything fits without reflowing. */
    @media (max-width: 767px) {
        .invlayout-body { padding-left: 0 !important; padding-right: 0 !important; overflow-x: hidden; }
        .inv-page {
            width: 760px;          /* fixed design width; JS scales to fit viewport */
            max-width: none;
            margin: 0;
            transform-origin: top left;
        }
        .invoice-actions { margin-left: 14px; margin-right: 14px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('header_actions'); ?>
    <div class="invoice-actions" style="gap:10px;">
        <?php if(isset($downloadUrl)): ?>
            <a href="<?php echo e($downloadUrl); ?>" class="inv-btn" style="background:#155e38;text-decoration:none;">
                <i class="fas fa-download"></i> Download PDF
            </a>
        <?php endif; ?>
        <button onclick="window.print()" class="inv-btn">
            <i class="fas fa-print"></i> Print Invoice
        </button>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php
    $storeName    = $appStoreName ?? 'AlbertinaNG';
    $storeAddr    = $storeAddress ?? 'No. 22 Zik Avenue, Uwani, Enugu State, Nigeria';
    $storeContact = $storeEmail   ?? 'support@albertinang.com';
    $storePhone   = $storePhone   ?? null;

    $isPickup     = $order->fulfillment_method === 'pickup';

    // Customer info
    $customerName  = $order->user->name  ?? 'Guest';
    $customerEmail = $order->user->email ?? $order->customer_email ?? '';
    $customerPhone = $order->user?->phone_no ?? null;

    // item->price is stored as base + installation_extra_ngn per unit
    $installTotal  = $order->items->sum(fn($i) => $i->installation_extra_ngn * $i->quantity);
    $itemsBase     = $order->items->sum(fn($i) => $i->price * $i->quantity);
    $baseOnlyTotal = $itemsBase - $installTotal;  // pure product cost for the totals breakdown
    $discount      = (float) ($order->coupon_discount_ngn ?? 0);
    $deliveryFee   = (float) ($order->shipping_cost ?? 0);
    $grandTotal    = (float) $order->total;

    // ── Admin-configured layout (per fulfilment mode) ─────────────────────────
    // Only the body SECTION ORDER is configurable; the header is fixed.
    $defaultBody = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];
    $bodyOrder = ($invLayout['body'] ?? null) ?: $defaultBody;
?>

<div class="inv-page">

    
    <?php echo $__env->make('sims.partials.invoice-header', ['issueDate' => $order->created_at->format('d F Y')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php $__currentLoopData = $bodyOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <?php if($sec === 'bill_to'): ?>
        
        <table class="inv-info-tbl">
            <tr>
                <td><div class="inv-info-header">Bill To</div></td>
                <td class="inv-info-right"><div class="inv-info-header right">Invoice Details</div></td>
            </tr>
            <tr>
                <td>
                    <div class="inv-info-body">
                        <strong><?php echo e($customerName); ?></strong><br>
                        <?php if($order->user?->shipping_address): ?><?php echo e($order->user->shipping_address); ?><br><?php endif; ?>
                        <?php if($order->user?->city): ?><?php echo e($order->user->city); ?><br><?php endif; ?>
                        <?php if($order->user?->state): ?><?php echo e($order->user->state); ?><br><?php endif; ?>
                        <?php if($customerEmail): ?><span style="color:#555;"><?php echo e($customerEmail); ?></span><br><?php endif; ?>
                        <?php if($customerPhone): ?><span style="color:#555;"><?php echo e($customerPhone); ?></span><?php endif; ?>
                    </div>
                </td>
                <td class="inv-info-right">
                    <div class="inv-info-body-right">
                        <span style="color:#555;">Order No.:</span> <?php echo e($order->order_number); ?><br>
                        <span style="color:#555;">Currency:</span> NGN (₦)<br>
                        <span style="color:#555;">Payment:</span> <?php echo e(ucfirst(str_replace('_', ' ', $order->payment_method))); ?><br>
                        <?php if($order->reference): ?>
                            <span style="color:#555;">Reference:</span> <?php echo e($order->reference); ?><br>
                        <?php endif; ?>
                        <?php if($order->coupon_code_used): ?>
                            <span style="color:#555;">Coupon:</span> <?php echo e($order->coupon_code_used); ?>

                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php elseif($sec === 'fulfillment'): ?>
        
        <table class="inv-fulfilment-tbl">
            <tr>
                <td>
                    <div class="inv-fulfilment-header">
                        <?php echo e($isPickup ? 'Collection From' : 'Delivery To'); ?>

                    </div>
                    <div class="inv-fulfilment-body">
                        <?php if($isPickup): ?>
                            <strong><?php echo e($order->pickup_point_name ?: $storeName . ' Collection Point'); ?></strong><br>
                            <?php if($order->pickup_point_address): ?><?php echo e($order->pickup_point_address); ?><br><?php endif; ?>
                            <?php if($order->pickup_location): ?><?php echo e($order->pickup_location); ?><br><?php endif; ?>
                            <?php echo e($storeContact); ?>

                        <?php else: ?>
                            <strong><?php echo e($customerName); ?></strong><br>
                            <?php if($order->shipping_address): ?><?php echo e($order->shipping_address); ?><br><?php endif; ?>
                            <?php if($order->delivery_location_name): ?><?php echo e($order->delivery_location_name); ?>,&nbsp;<?php endif; ?>
                            <?php if($order->delivery_state_name): ?><?php echo e($order->delivery_state_name); ?><br><?php endif; ?>
                            Nigeria<br>
                            <?php if($customerEmail): ?><?php echo e($customerEmail); ?><?php endif; ?>
                            <?php if($customerPhone): ?><br><?php echo e($customerPhone); ?><?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php elseif($sec === 'items'): ?>
        
        <table class="items">
            <thead>
                <tr>
                    <th style="width:4%">#</th>
                    <th style="width:52%">Item Description</th>
                    <th class="num" style="width:8%">Qty</th>
                    <th class="num" style="width:18%">Unit Price</th>
                    <th class="num" style="width:18%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $rowNum = 0; ?>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $rowNum++; ?>
                    <?php
                        $basePrice  = $item->price - $item->installation_extra_ngn;
                        $baseAmount = $basePrice * $item->quantity;
                        $instAmount = $item->installation_extra_ngn * $item->quantity;
                    ?>
                    <tr>
                        <td><?php echo e($rowNum); ?></td>
                        <td>
                            <?php echo e($item->name); ?>

                            <?php if($item->sku): ?>
                                <div class="item-sku">SKU: <?php echo e($item->sku); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="num"><?php echo e($item->quantity); ?></td>
                        <td class="num">&#8358;<?php echo e(number_format($basePrice, 2)); ?></td>
                        <td class="num">&#8358;<?php echo e(number_format($baseAmount, 2)); ?></td>
                    </tr>
                    <?php if($item->installation_option && $item->installation_extra_ngn > 0): ?>
                        <tr class="item-install-row">
                            <td></td>
                            <td class="item-install-label">
                                &#8627; Installation: <?php echo e($item->installation_option); ?>

                            </td>
                            <td class="num" style="font-size:12.5px;color:#555;"><?php echo e($item->quantity); ?></td>
                            <td class="num" style="font-size:12.5px;color:#555;">&#8358;<?php echo e(number_format($item->installation_extra_ngn, 2)); ?></td>
                            <td class="num" style="font-size:12.5px;color:#555;">&#8358;<?php echo e(number_format($instAmount, 2)); ?></td>
                        </tr>
                    <?php elseif($item->installation_option): ?>
                        <tr class="item-install-row">
                            <td></td>
                            <td class="item-install-label" colspan="4">
                                &#8627; Installation: <?php echo e($item->installation_option); ?> (included)
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php elseif($sec === 'notes_totals'): ?>
        
        <table class="inv-bottom">
            <tr>
                <td style="width:55%;padding-right:16px;">
                    <div class="inv-notes-box">
                        <strong>Order Notes</strong>
                        Fulfilment: <?php echo e($isPickup ? 'Customer collection' : 'Home delivery'); ?><br>
                        <?php if(!$isPickup && $order->delivery_state_name): ?>
                            Delivery to: <?php echo e($order->delivery_location_name ? $order->delivery_location_name . ', ' : ''); ?><?php echo e($order->delivery_state_name); ?><br>
                        <?php endif; ?>
                        Order reference: <?php echo e($order->order_number); ?><br>
                        <?php if($order->coupon_code_used): ?>
                            Coupon applied: <?php echo e($order->coupon_code_used); ?><br>
                        <?php endif; ?>
                        Customer support: <?php echo e($storeContact); ?>

                    </div>
                </td>
                <td style="width:45%;">
                    <div class="inv-totals-box">
                        <table class="inv-totals-tbl">
                            <tr>
                                <td class="lbl">Items Subtotal</td>
                                <td class="val">&#8358;<?php echo e(number_format($baseOnlyTotal, 2)); ?></td>
                            </tr>
                            <?php if($installTotal > 0): ?>
                            <tr>
                                <td class="lbl">Installation</td>
                                <td class="val">+&#8358;<?php echo e(number_format($installTotal, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($deliveryFee > 0): ?>
                            <tr>
                                <td class="lbl">Delivery Fee</td>
                                <td class="val">+&#8358;<?php echo e(number_format($deliveryFee, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if($discount > 0): ?>
                            <tr>
                                <td class="lbl">Discount</td>
                                <td class="val">-&#8358;<?php echo e(number_format($discount, 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td class="lbl">Total Due</td>
                                <td class="val">&#8358;<?php echo e(number_format($grandTotal, 2)); ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <?php elseif($sec === 'bank'): ?>
        
        <?php if(!empty($invBankDetails)): ?>
        <div class="inv-terms" style="margin-bottom:16px;">
            <strong>Bank Details</strong>
            <?php echo nl2br(e($invBankDetails)); ?>

        </div>
        <?php endif; ?>

        <?php elseif($sec === 'terms'): ?>
        
        <div class="inv-terms">
            <strong>Terms &amp; Conditions</strong>
            <?php if(!empty($invTerms)): ?>
                <?php echo nl2br(e($invTerms)); ?>

            <?php else: ?>
                1. This invoice is evidence of your order and payment record. Please retain it for your records.<br>
                2. Goods may be returned or exchanged only in accordance with the <?php echo e($storeName); ?> Returns Policy; eligibility depends on product condition and category.<br>
                3. Report delivery issues or damaged goods within 48 hours of receipt via <?php echo e($storeContact); ?>, quoting your order number.<br>
                4. Prices are in Nigerian Naira (₦).<br>
                5. This invoice is generated electronically and requires no signature.
            <?php endif; ?>
        </div>

        <?php elseif($sec === 'footer'): ?>
        
        <div class="inv-footer">
            <?php if(!empty($invFooter)): ?>
                <?php echo nl2br(e($invFooter)); ?>

            <?php else: ?>
                Thank you for shopping with <?php echo e($storeName); ?>. | <?php echo e($storeContact); ?>

            <?php endif; ?>
        </div>
        <?php endif; ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const params = new URLSearchParams(window.location.search);
    if (params.get('print') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    }

    // ── Mobile: scale the whole invoice down to fit the viewport ──
    // The invoice renders at its fixed 760px design width; here we shrink it
    // proportionally so everything is visible at once (no horizontal scroll),
    // then pull the following flow up to remove the gap the transform leaves.
    (function () {
        var DESIGN_W = 760;
        var mq = window.matchMedia('(max-width: 767px)');

        function fitInvoice() {
            var page = document.querySelector('.inv-page');
            if (!page) return;

            // Reset first so measurements are taken at natural size.
            page.style.transform = 'none';
            page.style.marginBottom = '';

            if (!mq.matches) return;

            var avail = (page.parentElement || document.body).clientWidth;
            var scale = Math.min(1, avail / DESIGN_W);
            var h = page.offsetHeight;               // natural (unscaled) height

            page.style.transform = 'scale(' + scale + ')';
            page.style.marginBottom = (-(h * (1 - scale))) + 'px'; // collapse leftover space
        }

        window.addEventListener('load', fitInvoice);
        window.addEventListener('resize', fitInvoice);
        window.addEventListener('orientationchange', fitInvoice);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitInvoice);
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.invoicelayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/invoice.blade.php ENDPATH**/ ?>