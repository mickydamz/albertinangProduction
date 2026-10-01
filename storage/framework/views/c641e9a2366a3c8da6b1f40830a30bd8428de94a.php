

<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Shipping Settings</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Shipping</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-0">Delivery Cost per Location</h4>
                        <small class="text-muted">Click a state to expand its locations. Pickup orders are always free.</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <input type="search" id="shippingFilter" class="form-control form-control-sm"
                               placeholder="Filter state or location…" style="max-width:230px;">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="expandAllBtn">
                            <i class="fas fa-expand-alt me-50"></i> Expand All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                            <i class="fas fa-compress-alt me-50"></i> Collapse All
                        </button>
                        <a href="<?php echo e(route('admin.states.index')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-globe-africa me-50"></i> Manage States
                        </a>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        <strong>Standard fee</strong> applies to regular products.
                        <strong class="text-warning">Truck fee</strong> applies when the cart contains refrigerators or cooling products.
                    </p>

                    <form method="POST" action="<?php echo e(route('admin.shipping.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="accordion" id="shippingAccordion">
                        <?php $__empty_1 = true; $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stateName => $locations): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $stateSlug = Str::slug($stateName); ?>

                            <div class="accordion-item border mb-2 shipping-state" style="border-radius:8px;overflow:hidden;"
                                 data-filter="<?php echo e(strtolower($stateName . ' ' . $locations->pluck('name')->implode(' '))); ?>">

                                
                                <h2 class="accordion-header" id="heading-<?php echo e($stateSlug); ?>">
                                    <button class="accordion-button collapsed fw-bold"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-<?php echo e($stateSlug); ?>"
                                            aria-expanded="false"
                                            aria-controls="collapse-<?php echo e($stateSlug); ?>"
                                            style="background:#f8f9fa;">
                                        <i class="fas fa-globe-africa me-1 text-primary"></i>
                                        <?php echo e($stateName); ?>

                                        <span class="badge bg-secondary ms-2" style="font-size:11px;">
                                            <?php echo e($locations->count()); ?> location<?php echo e($locations->count() !== 1 ? 's' : ''); ?>

                                        </span>
                                        <?php if($locations->where('is_active', false)->count()): ?>
                                            <span class="badge bg-warning ms-1" style="font-size:11px;">
                                                <?php echo e($locations->where('is_active', false)->count()); ?> inactive
                                            </span>
                                        <?php endif; ?>
                                    </button>
                                </h2>

                                
                                <div id="collapse-<?php echo e($stateSlug); ?>"
                                     class="accordion-collapse collapse"
                                     aria-labelledby="heading-<?php echo e($stateSlug); ?>"
                                     data-bs-parent="">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" style="min-width:520px;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width:36%;padding-left:1rem;">Location</th>
                                                        <th style="width:28%;">
                                                            <i class="fas fa-truck me-1 text-secondary"></i>Standard (₦)
                                                        </th>
                                                        <th style="width:28%;">
                                                            <i class="fas fa-truck-moving me-1 text-warning"></i>Truck (₦)
                                                            <small class="d-block text-muted fw-normal" style="font-size:10px;">Fridges / cooling</small>
                                                        </th>
                                                        <th style="width:8%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td class="align-middle" style="padding-left:1rem;">
                                                                <span class="fw-semibold"><?php echo e($location->name); ?></span>
                                                                <?php if(!$location->is_active): ?>
                                                                    <span class="badge bg-light-secondary ms-1" style="font-size:10px;">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">₦</span>
                                                                    <input type="number"
                                                                           name="shipping[<?php echo e($location->id); ?>]"
                                                                           class="form-control"
                                                                           value="<?php echo e(old("shipping.{$location->id}", rtrim(rtrim(number_format($location->shipping_cost ?? 0, 2, '.', ''), '0'), '.') ?: '0')); ?>"
                                                                           min="0" step="0.01" placeholder="0.00">
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text" style="background:#fff8e1;">₦</span>
                                                                    <input type="number"
                                                                           name="truck_shipping[<?php echo e($location->id); ?>]"
                                                                           class="form-control"
                                                                           style="background:#fff8e1;"
                                                                           value="<?php echo e(old("truck_shipping.{$location->id}", rtrim(rtrim(number_format($location->truck_shipping_cost ?? 0, 2, '.', ''), '0'), '.') ?: '0')); ?>"
                                                                           min="0" step="0.01" placeholder="0.00">
                                                                </div>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <a href="<?php echo e(route('admin.locations.edit', $location->id)); ?>"
                                                                   class="btn btn-xs btn-outline-warning"
                                                                   style="font-size:11px;padding:2px 7px;"
                                                                   title="Edit location">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center text-muted py-4">
                                No locations found.
                                <a href="<?php echo e(route('admin.states.create')); ?>">Add a state</a> then
                                <a href="<?php echo e(route('admin.locations.create')); ?>">add locations</a> to it.
                            </div>
                        <?php endif; ?>
                        </div>

                        <?php if($grouped->isNotEmpty()): ?>
                            <div class="mt-3 d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-50"></i> Save All Shipping Costs
                                </button>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Saves every location in one click — collapsed ones are included.
                                </small>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('expandAllBtn')?.addEventListener('click', function () {
        document.querySelectorAll('#shippingAccordion .accordion-collapse').forEach(function (el) {
            new bootstrap.Collapse(el, { show: true });
        });
    });
    document.getElementById('collapseAllBtn')?.addEventListener('click', function () {
        document.querySelectorAll('#shippingAccordion .accordion-collapse').forEach(function (el) {
            new bootstrap.Collapse(el, { hide: true });
        });
    });

    // Client-side filter — hides state groups that don't match the query by
    // state name or any location name. Doesn't touch the form, so "Save" still
    // submits every location (matched or not).
    (function () {
        var filter = document.getElementById('shippingFilter');
        if (!filter) return;
        filter.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            document.querySelectorAll('#shippingAccordion .shipping-state').forEach(function (item) {
                var hay = item.getAttribute('data-filter') || '';
                item.style.display = (q === '' || hay.indexOf(q) !== -1) ? '' : 'none';
            });
        });
    })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/shipping/index.blade.php ENDPATH**/ ?>