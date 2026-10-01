<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        
        <div class="content-header row">
            <div class="col-12 mb-1">
                <h2 class="content-header-title float-start mb-0">Product</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.products.index')); ?>">Products</a></li>
                        <li class="breadcrumb-item active">#<?php echo e($product->id); ?></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php
                $images    = $product->images;
                $first     = $images->first();
                $mainUrl   = $first ? asset('storage/' . $first->image_url) : 'https://placehold.co/500x400/eef3e8/3d8012?text=No+Image';
                $inStock   = (int) $product->stock > 0;
                $hasOld    = $product->old_price && $product->old_price > $product->price;
                $discount  = $hasOld ? round((($product->old_price - $product->price) / $product->old_price) * 100) : 0;
                $rating    = $product->review_rating;   // live review average (0 when no real reviews)
            ?>

            
            <div class="card mb-2">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?php echo e($mainUrl); ?>" alt="<?php echo e($product->name); ?>" width="72" height="72"
                             class="rounded border" style="object-fit:cover;background:#f8f9fa;"
                             onerror="this.src='https://placehold.co/72x72/eef3e8/3d8012?text=+'">
                        <div>
                            <h3 class="mb-25"><?php echo e($product->name); ?></h3>
                            <span class="fw-bolder fs-4 text-success">&#8358;<?php echo e(number_format((float) $product->price, 0)); ?></span>
                            <?php if($hasOld): ?>
                                <s class="text-muted ms-50">&#8358;<?php echo e(number_format((float) $product->old_price, 0)); ?></s>
                                <?php if($discount >= 5): ?><span class="badge bg-danger ms-50">-<?php echo e($discount); ?>%</span><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="badge bg-<?php echo e($product->is_active ? 'success' : 'secondary'); ?> fs-6">
                            <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

                        </span>
                        <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-50"></i>Edit
                        </a>
                        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-50"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="row g-2 mb-2">
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-success rounded"><div class="avatar-content"><i class="fas fa-tag"></i></div></div>
                        <div><h4 class="mb-0">&#8358;<?php echo e(number_format((float) $product->price, 0)); ?></h4><small class="text-muted">Price</small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-<?php echo e($inStock ? 'primary' : 'danger'); ?> rounded"><div class="avatar-content"><i class="fas fa-boxes-stacked"></i></div></div>
                        <div><h4 class="mb-0"><?php echo e((int) $product->stock); ?></h4><small class="text-muted"><?php echo e($inStock ? 'In stock' : 'Out of stock'); ?></small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-warning rounded"><div class="avatar-content"><i class="fas fa-star"></i></div></div>
                        <div><h4 class="mb-0"><?php echo e(number_format($rating, 1)); ?></h4><small class="text-muted"><?php echo e($product->review_rating_count); ?> review(s)</small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-info rounded"><div class="avatar-content"><i class="fas fa-copyright"></i></div></div>
                        <div><h4 class="mb-0 text-truncate" style="max-width:120px;"><?php echo e($product->brand->name ?? $product->brand ?? '—'); ?></h4><small class="text-muted">Brand</small></div>
                    </div></div>
                </div>
            </div>

            <div class="row g-2">
                
                <div class="col-lg-5 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom"><h4 class="card-title"><i class="fas fa-images text-primary me-50"></i>Gallery</h4></div>
                        <div class="card-body text-center pt-1">
                            <img src="<?php echo e($mainUrl); ?>" alt="<?php echo e($product->name); ?>" class="img-fluid rounded mb-1"
                                 style="max-height:280px;object-fit:contain;"
                                 onerror="this.src='https://placehold.co/500x400/eef3e8/3d8012?text=No+Image'">
                            <div class="d-flex flex-wrap justify-content-center gap-50">
                                <?php $__empty_1 = true; $__currentLoopData = $images->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <img src="<?php echo e(asset('storage/' . $img->image_url)); ?>" width="56" height="56"
                                         class="rounded border" style="object-fit:cover;" alt="thumb"
                                         onerror="this.style.display='none';">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <small class="text-muted">No images uploaded.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-7 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom"><h4 class="card-title"><i class="fas fa-circle-info text-primary me-50"></i>Details</h4></div>
                        <div class="card-body pt-1">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Category</span><strong><?php echo e($product->category->name ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Subcategory</span><strong><?php echo e($product->Subcategory->name ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Brand</span><strong><?php echo e($product->brand->name ?? $product->brand ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Price</span><strong class="text-success">&#8358;<?php echo e(number_format((float) $product->price, 0)); ?></strong></li>
                                <?php if($hasOld): ?>
                                    <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Old Price</span><s class="text-muted">&#8358;<?php echo e(number_format((float) $product->old_price, 0)); ?></s></li>
                                <?php endif; ?>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Stock</span><strong class="text-<?php echo e($inStock ? 'success' : 'danger'); ?>"><?php echo e((int) $product->stock); ?></strong></li>
                                <li class="d-flex justify-content-between py-50"><span class="text-muted">Created</span><strong><?php echo e($product->created_at?->format('d M Y') ?? '—'); ?></strong></li>
                            </ul>

                            <?php if($product->description): ?>
                                <hr>
                                <h6 class="fw-bolder"><i class="fas fa-align-left text-muted me-50"></i>Description</h6>
                                <p class="text-muted mb-0"><?php echo e($product->description); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/products/show.blade.php ENDPATH**/ ?>