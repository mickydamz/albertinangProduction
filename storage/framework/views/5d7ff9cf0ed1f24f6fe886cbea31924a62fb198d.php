

<?php $__env->startSection('content'); ?>
<style>
    .brand-logo-thumb {
        width: 44px; height: 44px; object-fit: contain;
        border-radius: 0.357rem; border: 1px solid #ebe9f1;
        background: #f8f8f8; padding: 3px;
    }
    .brand-logo-placeholder {
        width: 44px; height: 44px;
        border-radius: 0.357rem; border: 1px dashed #d8d6de;
        background: #f8f8f8; display: inline-flex;
        align-items: center; justify-content: center;
        color: #b9b9c3; font-size: 1.1rem;
    }
    .badge-product-count {
        background: #f0effe; color: #7367f0;
        border: 1px solid #ddd9fb; border-radius: 999px;
        font-size: 0.75rem; font-weight: 700;
        padding: 0.18rem 0.65rem;
    }

    /* Status toggle green */
    .brand-toggle:checked { background-color:#28c76f !important; border-color:#28c76f !important; }
    .brand-toggle:focus   { box-shadow: 0 0 0 3px rgba(40,199,111,.25) !important; }

    @media (max-width: 768px) {
        .table thead { display: none; }
        .table tr { display: block; margin-bottom: 1rem; border: 1px solid #e0e0e0; border-radius: 0.5rem; overflow: hidden; }
        .table td { display: block; text-align: right; position: relative; padding: 0.75rem 1rem; border: none; }
        .table td::before { content: attr(data-label); position: absolute; left: 1rem; text-align: left; font-weight: bold; color: #6c757d; }
        .d-inline-flex { justify-content: flex-end; width: 100%; }
    }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Brands Management</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Brands</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.brands.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Brand
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="brand-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Brands List</h4>
                                <?php echo $__env->make('admin.partials.search-box', ['action' => route('admin.brands.index'), 'value' => $search, 'placeholder' => 'Search brands…'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">

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

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Logo</th>
                                                    <th>Name</th>
                                                    <th>Slug</th>
                                                    <th>Products</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td data-label="#"><?php echo e($loop->iteration + ($brands->currentPage() - 1) * $brands->perPage()); ?></td>
                                                        <td data-label="Logo">
                                                            <?php if($brand->logo): ?>
                                                              <img src="<?php echo e(asset('storage/' . $brand->logo)); ?>" alt="<?php echo e($brand->name); ?>" class="brand-logo-thumb" style="width:44px;height:44px;object-fit:contain;border-radius:0.357rem;border:1px solid #ebe9f1;background:#f8f8f8;padding:3px;">
                                                            <?php else: ?>
                                                                <span class="brand-logo-placeholder">
                                                                    <i class="fas fa-tag"></i>
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td data-label="Name"><strong><?php echo e($brand->name); ?></strong></td>
                                                        <td data-label="Slug"><code style="font-size:.8rem; color:#6e6b7b;"><?php echo e($brand->slug); ?></code></td>
                                                        <td data-label="Products">
                                                            <span class="badge-product-count"><?php echo e($brand->products_count); ?></span>
                                                        </td>
                                                        <td data-label="Status">
                                                            <div class="form-check form-switch d-flex align-items-center gap-2 ps-0 m-0 justify-content-end justify-content-md-start">
                                                                <input class="form-check-input brand-toggle m-0"
                                                                       type="checkbox" role="switch"
                                                                       id="brand-toggle-<?php echo e($brand->id); ?>"
                                                                       data-id="<?php echo e($brand->id); ?>"
                                                                       data-url="<?php echo e(route('admin.brands.toggleActive', $brand->id)); ?>"
                                                                       <?php echo e($brand->is_active ? 'checked' : ''); ?>

                                                                       style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                                                                <label for="brand-toggle-<?php echo e($brand->id); ?>"
                                                                       class="form-check-label brand-toggle-label-<?php echo e($brand->id); ?> m-0"
                                                                       style="font-size:12px; font-weight:500; cursor:pointer;
                                                                              color:<?php echo e($brand->is_active ? '#28c76f' : '#b0b0b0'); ?>;">
                                                                    <?php echo e($brand->is_active ? 'Active' : 'Inactive'); ?>

                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td data-label="Created"><?php echo e($brand->created_at->format('d M Y')); ?></td>
                                                        <td data-label="Actions" class="text-center">
                                                            <div class="d-flex justify-content-center gap-50">
                                                                <a href="<?php echo e(route('admin.brands.edit', $brand->id)); ?>"
                                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                                   data-bs-toggle="tooltip" title="Edit">
                                                                    <i class="fas fa-pen"></i>
                                                                </a>
                                                                <form action="<?php echo e(route('admin.brands.destroy', $brand->id)); ?>"
                                                                      method="POST" class="d-inline-block"
                                                                      onsubmit="return confirm('Delete brand \'<?php echo e(addslashes($brand->name)); ?>\'? This cannot be undone.');">
                                                                    <?php echo csrf_field(); ?>
                                                                    <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect" data-bs-toggle="tooltip" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-3">No brands found.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-center mt-3">
                                        <?php echo e($brands->links()); ?>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') feather.replace();

        // AJAX status toggle
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.querySelectorAll('.brand-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                const id = this.dataset.id, url = this.dataset.url, checked = this.checked;
                const label = document.querySelector('.brand-toggle-label-' + id);
                label.textContent = checked ? 'Active' : 'Inactive';
                label.style.color = checked ? '#28c76f' : '#b0b0b0';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-HTTP-Method-Override': 'PATCH'
                    },
                    body: JSON.stringify({ _method: 'PATCH' }),
                }).catch(function () {
                    toggle.checked = !checked;
                    label.textContent = !checked ? 'Active' : 'Inactive';
                    label.style.color = !checked ? '#28c76f' : '#b0b0b0';
                    alert('Failed to update status. Please try again.');
                });
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/brands/index.blade.php ENDPATH**/ ?>