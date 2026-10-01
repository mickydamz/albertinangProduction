
<?php $loaderId = $chartId . 'Loader'; $emptyId = $chartId . 'Empty'; ?>
<div class="card h-100">
    <div class="card-header border-bottom d-flex align-items-center justify-content-between py-1">
        <h4 class="card-title mb-0" style="font-size:.95rem;">
            <i class="fas <?php echo e($icon); ?> me-50 text-<?php echo e($colorClass); ?>" style="font-size:15px;"></i>
            <?php echo e($title); ?>

        </h4>
        <span class="badge bg-light-<?php echo e($colorClass); ?> text-<?php echo e($colorClass); ?>" id="<?php echo e($badgeId); ?>">…</span>
    </div>
    <div class="card-body p-1" style="position:relative;min-height:220px;">
        <div id="<?php echo e($loaderId); ?>" class="chart-loader">
            <div class="spinner-border text-<?php echo e($colorClass); ?>" role="status" style="width:1.4rem;height:1.4rem;border-width:2px;"></div>
            <span class="ms-2 text-muted" style="font-size:12px;">Loading…</span>
        </div>
        <canvas id="<?php echo e($chartId); ?>" style="display:none;max-height:240px;"></canvas>
        <div id="<?php echo e($emptyId); ?>" class="chart-empty" style="display:none;">
            <i class="fas <?php echo e($icon); ?>" style="font-size:30px;opacity:.25;"></i>
            <p class="text-muted mt-1 mb-0" style="font-size:12px;">No data yet.</p>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/partials/_chart-card.blade.php ENDPATH**/ ?>