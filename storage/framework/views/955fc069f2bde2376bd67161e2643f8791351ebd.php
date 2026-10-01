

<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

    
    <div class="content-header row">
        <div class="col-12 mb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h2 class="content-header-title mb-0">Banners</h2>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Banners</li>
                    </ol>
                </div>
                <a href="<?php echo e(route('admin.banners.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Create New Banner
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="d-flex mb-3">
        <?php echo $__env->make('admin.partials.search-box', ['action' => route('admin.banners.index'), 'value' => $search, 'placeholder' => 'Search banners by title or type…'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card mb-4 banner-admin-card <?php echo e($banner->status ? '' : 'banner-inactive'); ?>">

                
                <span class="order-badge" title="Display order">#<?php echo e($banner->sort_order); ?></span>

                <img src="<?php echo e(asset('storage/' . $banner->image)); ?>"
                     class="card-img-top"
                     alt="<?php echo e($banner->title ?? 'Banner'); ?>"
                     style="height: 200px; object-fit: cover;">

                <div class="card-body">
                    <h5 class="card-title mb-1"><?php echo e($banner->title ?: 'Untitled banner'); ?></h5>

                    <div class="d-flex flex-wrap gap-50 mb-1">
                        <span class="badge bg-light-primary text-primary"><?php echo e($banner->type); ?></span>
                        <?php if($banner->status): ?>
                            <span class="badge bg-light-success text-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-light-secondary text-secondary">Inactive</span>
                        <?php endif; ?>
                    </div>

                    <?php if($banner->link): ?>
                        <p class="card-text mb-1 text-truncate" title="<?php echo e($banner->link); ?>">
                            <i class="fas fa-link me-50 text-muted"></i><?php echo e($banner->link); ?>

                        </p>
                    <?php endif; ?>

                    <div class="d-flex gap-50 mt-1">
                        <a href="<?php echo e(route('admin.banners.edit', $banner->id)); ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-pen me-25"></i> Edit
                        </a>
                        <form action="<?php echo e(route('admin.banners.destroy', $banner->id)); ?>" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this banner? This cannot be undone.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-25"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4 text-muted">
                    No banners yet. Click <strong>Create New Banner</strong> to add your first slide.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($banners->hasPages()): ?>
        <div class="d-flex justify-content-center mb-3">
            <?php echo e($banners->links()); ?>

        </div>
    <?php endif; ?>

    </div>
    </div>
</div>

<style>
    .banner-admin-card { position: relative; }
    .order-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 2;
        background: rgba(17, 24, 39, .85);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        letter-spacing: .3px;
    }
    .banner-inactive .card-img-top { filter: grayscale(70%); opacity: .65; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/banners/index.blade.php ENDPATH**/ ?>