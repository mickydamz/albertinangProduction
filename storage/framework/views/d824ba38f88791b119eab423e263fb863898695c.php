

<?php $__env->startSection('content'); ?>
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
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Coupons</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.coupons.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Coupon
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="row mb-2">
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card">
                        <div class="stat-label">Total coupons</div>
                        <div class="stat-value"><?php echo e($coupons->total()); ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--green">
                        <div class="stat-label">Active</div>
                        <div class="stat-value"><?php echo e($activeCount); ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--orange">
                        <div class="stat-label">Expired</div>
                        <div class="stat-value"><?php echo e($expiredCount); ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-1">
                    <div class="stat-card stat-card--blue">
                        <div class="stat-label">Total uses</div>
                        <div class="stat-value"><?php echo e($totalUses); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <h4 class="card-title mb-0">All Coupons</h4>
                            <form method="GET" action="<?php echo e(route('admin.coupons.index')); ?>"
                                  class="d-flex align-items-center gap-1">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                       class="form-control form-control-sm"
                                       placeholder="Search code…"
                                       style="max-width:200px;">
                                <select name="filter" class="form-select form-select-sm" style="max-width:130px;">
                                    <option value="">All</option>
                                    <option value="active"   <?php echo e(request('filter') === 'active'   ? 'selected' : ''); ?>>Active</option>
                                    <option value="inactive" <?php echo e(request('filter') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    <option value="expired"  <?php echo e(request('filter') === 'expired'  ? 'selected' : ''); ?>>Expired</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search" style="font-size:13px;"></i>
                                </button>
                                <?php if(request('search') || request('filter')): ?>
                                    <a href="<?php echo e(route('admin.coupons.index')); ?>" class="btn btn-secondary btn-sm">
                                        <i data-feather="x" style="width:13px;height:13px;"></i>
                                    </a>
                                <?php endif; ?>
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
                                    <?php $__empty_1 = true; $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                                            $isFull    = $coupon->max_uses && $coupon->used_count >= $coupon->max_uses;
                                        ?>
                                        <tr>
                                            
                                            <td data-label="Code">
                                                <span class="coupon-code-pill"><?php echo e($coupon->code); ?></span>
                                            </td>

                                            
                                            <td data-label="Discount">
                                                <?php if($coupon->discount_type === 'percent'): ?>
                                                    <span class="badge badge-light-primary">
                                                        <?php echo e(number_format($coupon->value, 0)); ?>% off
                                                    </span>
                                                    <?php if($coupon->max_discount_amount): ?>
                                                        <div class="text-muted mt-25" style="font-size:0.75rem;">
                                                            max ₦<?php echo e(number_format($coupon->max_discount_amount, 0)); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-light-success">
                                                        ₦<?php echo e(number_format($coupon->value, 0)); ?> off
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td data-label="Min order">
                                                <?php if($coupon->min_order_amount > 0): ?>
                                                    ₦<?php echo e(number_format($coupon->min_order_amount, 0)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">None</span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td data-label="Uses">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="<?php echo e($isFull ? 'text-danger fw-bold' : ''); ?>">
                                                        <?php echo e($coupon->used_count); ?>

                                                    </span>
                                                    <?php if($coupon->max_uses): ?>
                                                        <span class="text-muted">/ <?php echo e($coupon->max_uses); ?></span>
                                                        <div class="usage-bar">
                                                            <div class="usage-bar__fill <?php echo e($isFull ? 'usage-bar__fill--full' : ''); ?>"
                                                                 style="width:<?php echo e(min(100, ($coupon->used_count / $coupon->max_uses) * 100)); ?>%">
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size:0.75rem;">unlimited</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            
                                            <td data-label="Expires">
                                                <?php if($coupon->expires_at): ?>
                                                    <span class="<?php echo e($isExpired ? 'text-danger' : 'text-success'); ?>"
                                                          style="font-size:0.85rem;">
                                                        <?php echo e($coupon->expires_at->format('d M Y')); ?>

                                                    </span>
                                                    <?php if(!$isExpired): ?>
                                                        <div class="text-muted" style="font-size:0.72rem;">
                                                            in <?php echo e($coupon->expires_at->diffForHumans()); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td data-label="Status">
                                                <?php if($isExpired): ?>
                                                    <span class="badge bg-secondary">Expired</span>
                                                <?php elseif($isFull): ?>
                                                    <span class="badge bg-warning text-dark">Limit reached</span>
                                                <?php elseif($coupon->is_active): ?>
                                                    <span class="badge badge-light-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td data-label="Actions">
                                                <div class="d-flex flex-wrap gap-1">
                                                    
                                                    <form method="POST"
                                                          action="<?php echo e(route('admin.coupons.toggleActive', $coupon)); ?>"
                                                          class="d-inline-flex align-items-center">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                        <div class="form-check form-switch m-0">
                                                            <input type="checkbox" class="form-check-input" role="switch"
                                                                   <?php echo e($coupon->is_active ? 'checked' : ''); ?>

                                                                   onchange="this.form.submit()"
                                                                   title="<?php echo e($coupon->is_active ? 'Deactivate' : 'Activate'); ?>">
                                                        </div>
                                                    </form>

                                                    
                                                    <a href="<?php echo e(route('admin.coupons.edit', $coupon)); ?>"
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit">
                                                        <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                                                    </a>

                                                    
                                                    <form method="POST"
                                                          action="<?php echo e(route('admin.coupons.destroy', $coupon)); ?>"
                                                          onsubmit="return confirm('Delete coupon <?php echo e($coupon->code); ?>? This cannot be undone.')">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                title="Delete">
                                                            <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                No coupons found.
                                                <a href="<?php echo e(route('admin.coupons.create')); ?>">Create one</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($coupons->hasPages()): ?>
                            <div class="card-footer d-flex justify-content-end">
                                <?php echo e($coupons->withQueryString()->links()); ?>

                            </div>
                        <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/coupons/index.blade.php ENDPATH**/ ?>