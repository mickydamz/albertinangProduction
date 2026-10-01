

<?php $__env->startSection('title', 'Order Delivered – Albertina Nigeria'); ?>

<?php $__env->startSection('header_icon'); ?>
    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="31" stroke="#abeb73" stroke-width="2" fill="rgba(171,235,115,0.12)"/>
        <path d="M20 33L28 41L44 24" stroke="#abeb73" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title', 'Order Delivered! 📦'); ?>

<?php $__env->startSection('header_sub'); ?>
    Your order has arrived. We hope you love it!<br>
    Thank you for shopping with Albertina Nigeria.
<?php $__env->stopSection(); ?>

<?php
    $statusLabel  = 'Delivered';
    $statusBg     = '#eef5e6';
    $statusColor  = '#3d7018';
    $statusBorder = '#c0e0a0';
?>

<?php $__env->startSection('body'); ?>

    <p class="greeting">
        Hi <strong><?php echo e($order->user?->name ?? 'Valued Customer'); ?></strong>,<br><br>
        Your order has been successfully delivered. We hope everything arrived
        in perfect condition and meets your expectations!
    </p>

    <div class="divider"></div>

    
    <div class="items-title">Items Delivered</div>
    <table class="items-table" width="100%" cellpadding="0" cellspacing="0">
        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr style="border-bottom:1px solid #eaf2e0;">
            <td style="width:64px; padding:12px 12px 12px 0; vertical-align:middle;">
                <img src="<?php echo e($item->image_url); ?>" alt="<?php echo e($item->name); ?>" width="52" height="52"
                     style="width:52px;height:52px;border-radius:8px;object-fit:cover;border:1px solid #dcefd0;background:#f0f4eb;"
                     onerror="this.style.display='none'">
            </td>
            <td style="padding:12px 8px; vertical-align:middle;">
                <div class="item-name"><?php echo e($item->name); ?></div>
                <?php if($item->sku): ?><div class="item-meta">SKU: <?php echo e($item->sku); ?></div><?php endif; ?>
            </td>
            <td style="padding:12px 0 12px 8px; vertical-align:middle; text-align:right; white-space:nowrap;">
                <div class="item-price">₦<?php echo e(number_format($item->price * $item->quantity, 0)); ?></div>
                <div class="item-qty">Qty: <?php echo e($item->quantity); ?></div>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>

    <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:20px;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-size:16px; font-weight:800; color:#1a2410;">Total Paid</td>
                <td style="font-size:16px; font-weight:800; color:#2d7010; text-align:right;">₦<?php echo e(number_format($order->total, 0)); ?></td>
            </tr>
        </table>
    </div>

    
    <?php if($order->pickup_location || $order->pickup_point_name): ?>
        <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:16px;">
            <div style="font-size:13px; font-weight:700; color:#1a2410; margin-bottom:6px;">📍 Collected From (Pickup)</div>
            <div style="font-size:13px; color:#4a6a30; line-height:1.6;">
                <?php if($order->pickup_point_name): ?>
                    <strong><?php echo e($order->pickup_point_name); ?></strong><br>
                    <?php if($order->pickup_point_address): ?><?php echo e($order->pickup_point_address); ?><br><?php endif; ?>
                    <?php if($order->pickup_location): ?><span style="color:#7a9a60;"><?php echo e($order->pickup_location); ?></span><?php endif; ?>
                <?php else: ?>
                    <?php echo e($order->pickup_location); ?>

                <?php endif; ?>
            </div>
        </div>
    <?php elseif($order->shipping_address): ?>
        <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:18px 20px; margin-top:16px;">
            <div style="font-size:13px; font-weight:700; color:#1a2410; margin-bottom:6px;">🚚 Delivered To</div>
            <div style="font-size:13px; color:#4a6a30; line-height:1.6;">
                <?php echo e($order->shipping_address); ?>

                <?php $loc = collect([$order->delivery_location_name, $order->delivery_state_name])->filter()->implode(', '); ?>
                <?php if($loc): ?><br><?php echo e($loc); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="divider"></div>

    
    <!-- <div style="background:#f7faf3; border:1px solid #dcefd0; border-radius:10px; padding:20px; text-align:center; margin-bottom:28px;">
        <div style="font-size:22px; margin-bottom:8px;">⭐⭐⭐⭐⭐</div>
        <div style="font-size:14px; font-weight:700; color:#1a2410; margin-bottom:6px;">Enjoying your purchase?</div>
        <p style="font-size:13px; color:#4a6a30; line-height:1.6;">
            We'd love to hear your feedback. Share your experience and help other customers make informed decisions.
        </p>
    </div> -->

    <div class="help-box">
        <p>
            <strong>Something not right?</strong> We'll make it good.<br>
            Call or WhatsApp us at <a href="tel:+2348064066170">+2348064066170</a>
            or email <a href="mailto:support@albertinang.com">support@albertinang.com</a>.
        </p>
    </div>

    <p style="font-size:13px; color:#7a9a60; line-height:1.7; text-align:center;">
        Thank you for choosing <strong style="color:#2d5610;">Albertina Nigeria</strong>.<br>
        We look forward to serving you again.
    </p>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/emails/order-delivered.blade.php ENDPATH**/ ?>