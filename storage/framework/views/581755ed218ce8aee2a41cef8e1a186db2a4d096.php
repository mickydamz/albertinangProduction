<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Pickup Points</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Pickup Points</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.pickup-points.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Pickup Point
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-body"><?php echo e(session('success')); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body"><?php echo e(session('error')); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Pickup Points List</h4>
                    <?php echo $__env->make('admin.partials.search-box', ['action' => route('admin.pickup-points.index'), 'value' => $search, 'placeholder' => 'Search name, address or location…'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Hours</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $pickupPoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td data-label="Location">
                                            <span class="badge badge-light-primary"><?php echo e($point->location->name ?? '—'); ?></span>
                                        </td>
                                        <td data-label="Name"><span class="fw-bolder"><?php echo e($point->name); ?></span></td>
                                        <td data-label="Address"><?php echo e($point->address); ?></td>
                                        <td data-label="Hours"><?php echo e($point->hours ?? '—'); ?></td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <a href="<?php echo e(route('admin.pickup-points.edit', $point->id)); ?>"
                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="<?php echo e(route('admin.pickup-points.destroy', $point->id)); ?>"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this pickup point?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit"
                                                            class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                                            data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-store mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No pickup points found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if($pickupPoints->hasPages()): ?>
                        <div class="card-footer d-flex justify-content-center">
                            <?php echo e($pickupPoints->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-50 { gap: 0.5rem !important; }

@media (max-width: 768px) {
    .table thead { display: none; }
    .table tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    .table td {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.5rem 0.75rem; border: none; border-bottom: 1px solid #f3f2f7;
    }
    .table td:last-child { border-bottom: none; }
    .table td::before {
        content: attr(data-label); font-weight: 600; color: #b9b9c3;
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;
        flex-shrink: 0; padding-right: 0.5rem;
    }
    .table td[data-label="Actions"] { justify-content: flex-end; }
    .table td[data-label="Actions"]::before { display: none; }
}
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .forEach(el => new bootstrap.Tooltip(el));
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/pickup_points/index.blade.php ENDPATH**/ ?>