

<?php $__env->startSection('title', 'Order Confirmed – Albertina Nigeria'); ?>

<?php $__env->startSection('header_icon'); ?>
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M20 33L28 41L44 24" stroke="#abeb73" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title', 'Order Confirmed! 🎉'); ?>

<?php $__env->startSection('header_sub'); ?>
    Thank you for your purchase. We've received your order<br>
    and it's being processed right away.
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <p class="greeting">
        Hi <strong><?php echo e($order->user?->name ?? 'Valued Customer'); ?></strong>,<br><br>
        Great news — your order has been confirmed and is now being prepared.
        You'll receive another update once your item(s) are on their way.
    </p>

    
    <div class="items-title">Your Items</div>

    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="item-row" style="border-bottom:1px solid #eaf2e0;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="<?php echo e($item->image_url); ?>"
                     alt="<?php echo e($item->name); ?>"
                     width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name"><?php echo e($item->name); ?></div>
                <?php if($item->sku): ?>
                    <div class="item-meta">SKU: <?php echo e($item->sku); ?></div>
                <?php endif; ?>
                <?php if($item->installation_option): ?>
                    <div class="item-installation">
                        + Installation included
                        <?php if($item->installation_extra_ngn > 0): ?>
                            (₦<?php echo e(number_format($item->installation_extra_ngn, 0)); ?>)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div class="item-price">₦<?php echo e(number_format($item->price * $item->quantity, 0)); ?></div>
                <div class="item-qty">Qty: <?php echo e($item->quantity); ?> × ₦<?php echo e(number_format($item->price, 0)); ?></div>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    
    <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30;">Subtotal</td>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30; text-align:right; font-weight:600;">
                    ₦<?php echo e(number_format($order->items->sum(fn($i) => $i->price * $i->quantity), 0)); ?>

                </td>
            </tr>
            <?php $installationTotal = $order->items->sum(fn($i) => $i->installation_extra_ngn ?? 0); ?>
            <?php if($installationTotal > 0): ?>
            <tr>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30;">Installation</td>
                <td style="padding:4px 0; font-size:13px; color:#4a6a30; text-align:right; font-weight:600;">
                    ₦<?php echo e(number_format($installationTotal, 0)); ?>

                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2" style="padding:8px 0 0;">
                    <hr style="border:none; border-top:1.5px solid #dcefd0; margin:0;">
                </td>
            </tr>
            <tr>
                <td style="padding:10px 0 0; font-size:16px; font-weight:800; color:#1a2410;">Total</td>
                <td style="padding:10px 0 0; font-size:16px; font-weight:800; color:#2d7010; text-align:right;">
                    ₦<?php echo e(number_format($order->total, 0)); ?>

                </td>
            </tr>
            <?php if($order->total_usd): ?>
            <tr>
                <td colspan="2" style="padding:2px 0 0; font-size:11px; color:#7a9a60; text-align:right;">
                    ≈ $<?php echo e(number_format($order->total_usd, 2)); ?> USD
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="divider"></div>

    
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
        <tr>
            <td width="48%" valign="top" style="padding-right:8px;">
                <div class="info-block">
                    <div class="info-block-title">
                        <?php if($order->pickup_location): ?> 📍 Pickup Location <?php else: ?> 🚚 Delivery <?php endif; ?>
                    </div>
                    <div class="info-block-content">
                        <?php if($order->pickup_location || $order->pickup_point_name): ?>
                            <?php if($order->pickup_point_name): ?>
                                <strong><?php echo e($order->pickup_point_name); ?></strong><br>
                                <?php if($order->pickup_point_address): ?><span style="font-size:12px; color:#7a9a60;"><?php echo e($order->pickup_point_address); ?></span><br><?php endif; ?>
                                <?php if($order->pickup_location): ?><span style="font-size:12px; color:#7a9a60;"><?php echo e($order->pickup_location); ?></span><br><?php endif; ?>
                            <?php else: ?>
                                <strong><?php echo e($order->pickup_location); ?></strong><br>
                            <?php endif; ?>
                            <span style="font-size:12px; color:#7a9a60;">Click &amp; Collect — ready within 1–2 hours of confirmation</span>
                        <?php elseif($order->shipping_address): ?>
                            <strong><?php echo e($order->shipping_address); ?></strong><br>
                            <?php $loc = collect([$order->delivery_location_name, $order->delivery_state_name])->filter()->implode(', '); ?>
                            <?php if($loc): ?><span style="font-size:12px; color:#7a9a60;"><?php echo e($loc); ?></span><br><?php endif; ?>
                            <span style="font-size:12px; color:#7a9a60;">You'll receive a delivery update soon.</span>
                        <?php else: ?>
                            Your items will be delivered to your registered address.<br>
                            <span style="font-size:12px; color:#7a9a60;">You'll receive a delivery update soon.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td width="4%"></td>
        </tr>
    </table>

    
    <div style="text-align: center; margin: 30px 0;">
        <a href="<?php echo e(URL::signedRoute('guest.orders.invoice.download', ['order' => $order->id])); ?>" 
           style="background-color: #2e600d; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; display: inline-block;">
           Download Invoice
        </a>
    </div>

    <div class="help-box">
        <p>
            <strong>Need help?</strong> Our team is available 24/7.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com">support@albertinang.com</a>.
            We're happy to assist with your order.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">Albertina Nigeria</strong>.<br>
        We look forward to serving you again.
    </p>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/emails/order-confirmation.blade.php ENDPATH**/ ?>