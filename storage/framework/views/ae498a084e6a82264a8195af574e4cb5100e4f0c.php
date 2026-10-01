<?php $__env->startSection('title', 'Currencies'); ?>

<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Currencies</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Currencies</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.currencies.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Currency
                    </a>
                </div>
            </div>
        </div>

        
        <div class="content-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-1" role="alert">
                    <div class="alert-body">
                        <i class="fas fa-circle-check me-50 font-small-4"></i>
                        <?php echo e(session('success')); ?>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-1" role="alert">
                    <div class="alert-body">
                        <i class="fas fa-circle-exclamation me-50 font-small-4"></i>
                        <?php echo e(session('error')); ?>

                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <section id="currencies-table">
                <div class="row pt-1">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">
                                    All Currencies <span class="badge bg-primary ms-1"><?php echo e($currencies->total()); ?></span>
                                </h4>
                                <?php echo $__env->make('admin.partials.search-box', ['action' => route('admin.currencies.index'), 'value' => $search, 'placeholder' => 'Search code, name or symbol…'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>

                            <div class="card-datatable table-responsive">

                                
                                <table class="table table-hover align-middle d-none d-md-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>Code</th>
                                            <th>Symbol</th>
                                            <th>Name</th>
                                            <th>Rate to NGN</th>
                                            <th>Sort</th>
                                            <th>Status</th>
                                            <th>Base</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                
                                                <td class="text-muted" style="font-size:13px;"><?php echo e($i + 1); ?></td>

                                                
                                                <td>
                                                    <span class="badge bg-secondary fw-bold"
                                                          style="font-size:13px;letter-spacing:.5px;">
                                                        <?php echo e($currency->code); ?>

                                                    </span>
                                                </td>

                                                
                                                <td>
                                                    <span style="font-size:1.1rem;font-weight:700;color:#5a5e60;">
                                                        <?php echo e($currency->symbol); ?>

                                                    </span>
                                                </td>

                                                
                                                <td style="font-size:13.5px;"><?php echo e($currency->name); ?></td>

                                                
                                                <td style="font-size:13.5px;">
                                                    <?php if($currency->is_base): ?>
                                                        <span class="text-muted">—</span>
                                                    <?php else: ?>
                                                        &#8358;<?php echo e(number_format($currency->rate_to_ngn, 2)); ?>

                                                    <?php endif; ?>
                                                </td>

                                                
                                                <td class="text-muted" style="font-size:13px;">
                                                    <?php echo e($currency->sort_order); ?>

                                                </td>

                                                
                                                <td>
                                                    <?php if($currency->is_active): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>

                                                
                                                <td>
                                                    <?php if($currency->is_base): ?>
                                                        <span class="badge bg-warning text-dark">
                                                            Base
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>

                                                
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-50">
                                                        <a href="<?php echo e(route('admin.currencies.edit', $currency)); ?>"
                                                           class="btn btn-sm btn-icon btn-outline-primary"
                                                           title="Edit">
                                                            <i class="fas fa-pen font-small-4"></i>
                                                        </a>

                                                        <?php if(!$currency->is_base): ?>
                                                            <form action="<?php echo e(route('admin.currencies.destroy', $currency)); ?>"
                                                                  method="POST"
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Delete <?php echo e($currency->name); ?> (<?php echo e($currency->code); ?>)? This cannot be undone.');">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-icon btn-outline-danger"
                                                                        title="Delete">
                                                                    <i class="fas fa-trash font-small-4"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-icon btn-outline-secondary"
                                                                    disabled title="Cannot delete base currency">
                                                                <i class="fas fa-lock font-small-4"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="9" class="text-center py-3 text-muted">
                                                    <i class="fas fa-dollar-sign mb-50"
                                                       style="width:40px;height:40px;opacity:.3;display:block;margin:0 auto 8px;"></i>
                                                    No currencies found.
                                                    <a href="<?php echo e(route('admin.currencies.create')); ?>">Add one now.</a>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                
                                <div class="d-block d-md-none px-1 pb-1">
                                    <?php $__empty_1 = true; $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="currency-mobile-card">

                                            <div class="cmc-header">
                                                <div class="d-flex align-items-center gap-50 flex-wrap">
                                                    <span class="cmc-symbol"><?php echo e($currency->symbol); ?></span>
                                                    <span class="badge bg-secondary fw-bold"
                                                          style="font-size:13px;letter-spacing:.5px;">
                                                        <?php echo e($currency->code); ?>

                                                    </span>
                                                    <?php if($currency->is_base): ?>
                                                        <span class="badge bg-warning text-dark">Base</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <?php if($currency->is_active): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="cmc-body">
                                                <div class="cmc-row">
                                                    <span class="cmc-label">Name</span>
                                                    <span class="cmc-value"><?php echo e($currency->name); ?></span>
                                                </div>
                                                <div class="cmc-row">
                                                    <span class="cmc-label">Rate to NGN</span>
                                                    <span class="cmc-value">
                                                        <?php if($currency->is_base): ?>
                                                            <span class="text-muted">— (base)</span>
                                                        <?php else: ?>
                                                            &#8358;<?php echo e(number_format($currency->rate_to_ngn, 2)); ?>

                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <div class="cmc-row">
                                                    <span class="cmc-label">Sort Order</span>
                                                    <span class="cmc-value"><?php echo e($currency->sort_order); ?></span>
                                                </div>
                                            </div>

                                            <div class="cmc-footer">
                                                <a href="<?php echo e(route('admin.currencies.edit', $currency)); ?>"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-pen font-small-4 me-25"></i> Edit
                                                </a>

                                                <?php if(!$currency->is_base): ?>
                                                    <form action="<?php echo e(route('admin.currencies.destroy', $currency)); ?>"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Delete <?php echo e($currency->name); ?>? This cannot be undone.');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash font-small-4 me-25"></i> Delete
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        <i class="fas fa-lock font-small-4 me-25"></i> Locked
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="text-center py-3 text-muted">
                                            No currencies found.
                                            <a href="<?php echo e(route('admin.currencies.create')); ?>">Add one now.</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if($currencies->hasPages()): ?>
                                    <div class="d-flex justify-content-center py-2">
                                        <?php echo e($currencies->links()); ?>

                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
/* ── Search bar ─────────────────────────────────────── */
.currencies-search-group {
    max-width: 500px;
    margin: 0 auto;
}
.currencies-search-group .form-control { height: 40px; }

@media (max-width: 767px) {
    .currencies-search-group { max-width: 100%; width: 100%; }
}

/* ── Mobile currency cards ──────────────────────────── */
.currency-mobile-card {
    border: 1px solid #e8ede4;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: .85rem;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}

.cmc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .75rem 1rem;
    background: #fafcf8;
    border-bottom: 1px solid #f0f0f0;
    gap: .5rem;
}

.cmc-symbol {
    font-size: 1.3rem;
    font-weight: 800;
    color: #3a5a20;
    line-height: 1;
}

.cmc-body { padding: .25rem 1rem; }

.cmc-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .45rem 0;
    border-bottom: 1px solid #f5f5f5;
    font-size: 13.5px;
}
.cmc-row:last-child { border-bottom: none; }

.cmc-label {
    color: #8a9a80;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.cmc-value {
    font-weight: 500;
    color: #3a3f37;
}

.cmc-footer {
    display: flex;
    gap: .5rem;
    padding: .65rem 1rem;
    background: #fafcf8;
    border-top: 1px solid #f0f0f0;
}

.cmc-footer .btn { flex: 1; justify-content: center; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/currencies/index.blade.php ENDPATH**/ ?>