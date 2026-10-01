

<?php $__env->startSection('content'); ?>

<?php
    $globalDiscountValue = $globalDiscountValue ?? null;
    $sliderDefault       = (int) ($globalDiscountValue ?? 0);
?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Discount Manager</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Discounts</li>
                        </ol>
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

            
            <div class="global-markup-bar mb-2">
                <div class="gmb-left">
                    <div class="gmb-label">Global discount</div>
                    <div class="gmb-value-row">
                        <?php if($globalDiscountValue): ?>
                            <span class="gmb-pct"><?php echo e($globalDiscountValue); ?>%</span>
                            <span class="gmb-active-badge">
                                <i data-feather="zap" style="width:12px;height:12px;"></i> active
                            </span>
                            <?php if(isset($globalDiscountExpiresAt) && $globalDiscountExpiresAt): ?>
                                <span class="live-countdown badge rounded-pill ms-1"
                                      data-expires="<?php echo e($globalDiscountExpiresAt->toIso8601String()); ?>"></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="gmb-pct">0%</span>
                            <span class="gmb-inactive-badge">not set</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="gmb-actions d-flex gap-1">
                    <button type="button"
        class="btn btn-danger btn-sm d-flex align-items-center gap-1"
        onclick="openGlobalDiscountModal()">
    <i data-feather="edit-2" style="width:14px;height:14px;"></i>
    Edit global discount
</button>
                    <?php if($globalDiscountValue): ?>
                        <form method="POST" action="<?php echo e(route('admin.discount.clearAll')); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm"
                                    onclick="return confirm('Clear ALL discounts across every category, subcategory and product?')">
                                <i data-feather="x" style="width:14px;height:14px;"></i>
                                Clear all
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            
            <section id="discount-manager">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Category &amp; Subcategory Discounts</h4>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       placeholder="Search categories..."
                                       id="categorySearch"
                                       style="max-width:220px;">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:30%;">Name</th>
                                                <th style="width:30%;">Subcategories</th>
                                                <th style="width:20%;">Category discount</th>
                                                <th style="width:20%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $subcatsWithDiscount = $category->subcategories
                                                        ->filter(fn($s) => !is_null($s->discount_percent));
                                                    $hasSubcatDiscounts  = $subcatsWithDiscount->isNotEmpty();
                                                ?>
                                                <tr>
                                                    <td data-label="Name">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i data-feather="folder" class="text-primary"></i>
                                                            <strong><?php echo e($category->name); ?></strong>
                                                        </div>
                                                    </td>

                                                    <td data-label="Subcategories" class="subcategories-cell">
                                                        <?php if($category->subcategories->isNotEmpty()): ?>
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <div class="dropdown">
                                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                                            type="button"
                                                                            data-bs-toggle="dropdown">
                                                                        Subcategories (<?php echo e($category->subcategories->count()); ?>)
                                                                    </button>
                                                                    <ul class="dropdown-menu p-0">
                                                                        <?php $__currentLoopData = $category->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                $effectiveDiscount = $Subcategory->discount_percent ?? $category->discount_percent ?? 0;
                                                                                $isInherited       = is_null($Subcategory->discount_percent);
                                                                            ?>
                                                                            <li class="border-bottom">
                                                                                <div class="dropdown-item-text d-flex justify-content-between align-items-center px-3 py-2"
                                                                                     style="cursor:pointer;"
                                                                                     onclick="event.stopPropagation(); setSubcategoryDiscount(
                                                                                         <?php echo e($Subcategory->id); ?>,
                                                                                         '<?php echo e(addslashes($Subcategory->name)); ?>',
                                                                                         <?php echo e($effectiveDiscount); ?>,
                                                                                         '<?php echo e($Subcategory->discount_expires_at ? $Subcategory->discount_expires_at->format('Y-m-d\TH:i') : ''); ?>'
                                                                                     )">
                                                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                                        <i data-feather="folder" class="text-success" style="width:16px;height:16px;"></i>
                                                                                        <div class="d-flex flex-column" style="min-width:0;">
                                                                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                                                <span><?php echo e($Subcategory->name); ?></span>
                                                                                                <?php if(!$isInherited): ?>
                                                                                                    <?php $subExpired = $Subcategory->discount_expires_at && now()->gt($Subcategory->discount_expires_at); ?>
                                                                                                    <span class="badge rounded-pill <?php echo e($subExpired ? 'badge-light-secondary' : 'badge-light-danger'); ?> ms-1">
                                                                                                        <?php echo e($effectiveDiscount); ?>% off
                                                                                                    </span>
                                                                                                    <?php if($Subcategory->discount_expires_at && !$subExpired): ?>
                                                                                                        <span class="live-countdown badge rounded-pill ms-1"
                                                                                                              data-expires="<?php echo e($Subcategory->discount_expires_at->toIso8601String()); ?>"></span>
                                                                                                    <?php elseif($subExpired): ?>
                                                                                                        <span class="badge rounded-pill badge-light-secondary ms-1">expired</span>
                                                                                                    <?php endif; ?>
                                                                                                <?php else: ?>
                                                                                                    <span class="badge rounded-pill markup-null-badge ms-1"
                                                                                                          title="Inherited from category">
                                                                                                        <?php echo e($effectiveDiscount); ?>% off
                                                                                                    </span>
                                                                                                <?php endif; ?>
                                                                                            </div>
                                                                                            <?php if(!$isInherited && $Subcategory->discount_expires_at): ?>
                                                                                                <?php $subExpired = $subExpired ?? (now()->gt($Subcategory->discount_expires_at)); ?>
                                                                                                <div class="expiry-meta <?php echo e($subExpired ? 'expiry-meta--expired' : ''); ?>" style="margin-top:2px;">
                                                                                                    <span class="expiry-date"><?php echo e($Subcategory->discount_expires_at->format('d M Y, g:ia')); ?></span>
                                                                                                    <span class="expiry-rel"><?php echo e($subExpired ? $Subcategory->discount_expires_at->diffForHumans() : 'in '.$Subcategory->discount_expires_at->diffForHumans()); ?></span>
                                                                                                </div>
                                                                                            <?php endif; ?>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div onclick="event.stopPropagation();">
                                                                                        <?php if(!is_null($Subcategory->discount_percent)): ?>
                                                                                            <form method="POST"
                                                                                                  action="<?php echo e(route('admin.discount.Subcategory.clear', $Subcategory)); ?>"
                                                                                                  class="d-inline">
                                                                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                                                <button type="submit"
                                                                                                        class="btn btn-sm btn-outline-danger px-2"
                                                                                                        title="Clear discount">
                                                                                                    <i data-feather="x" style="width:14px;height:14px;"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </ul>
                                                                </div>

                                                                <?php if($hasSubcatDiscounts): ?>
                                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                                        <?php $__currentLoopData = $subcatsWithDiscount->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <span class="badge badge-light-danger"
                                                                                  style="font-size:0.7rem;"
                                                                                  title="<?php echo e($sub->name); ?>: <?php echo e($sub->discount_percent); ?>% off">
                                                                                <?php echo e(\Str::limit($sub->name, 12, '...')); ?> (<?php echo e($sub->discount_percent); ?>%)
                                                                            </span>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        <?php if($subcatsWithDiscount->count() > 3): ?>
                                                                            <span class="badge bg-secondary bg-opacity-25 text-secondary" style="font-size:0.7rem;">
                                                                                +<?php echo e($subcatsWithDiscount->count() - 3); ?> more
                                                                            </span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <form method="POST"
                                                                          action="<?php echo e(route('admin.discount.category.clearSubcategories', $category)); ?>"
                                                                          class="d-inline"
                                                                          onsubmit="return confirm('Clear all subcategory discounts for <?php echo e(addslashes($category->name)); ?>?')">
                                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                                                            <i data-feather="x-circle" style="width:13px;height:13px;"></i>
                                                                            Clear all
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted">No subcategories</span>
                                                        <?php endif; ?>
                                                    </td>

                                                    <td data-label="Category discount">
                                                        <?php if(!is_null($category->discount_percent)): ?>
                                                            <?php $catExpired = $category->discount_expires_at && now()->gt($category->discount_expires_at); ?>
                                                            <div class="d-flex flex-column gap-1">
                                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                                    <span class="badge rounded-pill <?php echo e($catExpired ? 'badge-light-secondary' : 'badge-light-danger'); ?>">
                                                                        <?php echo e($category->discount_percent); ?>% off
                                                                    </span>
                                                                    <?php if($category->discount_expires_at && !$catExpired): ?>
                                                                        <span class="live-countdown badge rounded-pill"
                                                                              data-expires="<?php echo e($category->discount_expires_at->toIso8601String()); ?>"></span>
                                                                    <?php elseif($catExpired): ?>
                                                                        <span class="badge rounded-pill badge-light-secondary">expired</span>
                                                                    <?php else: ?>
                                                                        <span class="badge rounded-pill markup-null-badge" style="font-size:0.65rem;">no expiry</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <?php if($category->discount_expires_at): ?>
                                                                    <div class="expiry-meta <?php echo e($catExpired ? 'expiry-meta--expired' : ''); ?>">
                                                                        <span class="expiry-date"><?php echo e($category->discount_expires_at->format('d M Y, g:ia')); ?></span>
                                                                        <?php if(!$catExpired): ?>
                                                                            <span class="expiry-rel">in <?php echo e($category->discount_expires_at->diffForHumans()); ?></span>
                                                                        <?php else: ?>
                                                                            <span class="expiry-rel"><?php echo e($category->discount_expires_at->diffForHumans()); ?></span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="badge rounded-pill markup-null-badge">none</span>
                                                        <?php endif; ?>
                                                    </td>

                                                    <td data-label="Actions" class="actions-cell">
                                                        <div class="d-flex gap-1 flex-wrap">
                                                            <button type="button"
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="setCategoryDiscount(
                                                                        <?php echo e($category->id); ?>,
                                                                        '<?php echo e(addslashes($category->name)); ?>',
                                                                        <?php echo e($category->discount_percent ?? 0); ?>,
                                                                        '<?php echo e($category->discount_expires_at ? $category->discount_expires_at->format('Y-m-d\TH:i') : ''); ?>'
                                                                    )">
                                                                <i class="fas fa-percent me-1"></i> Set discount
                                                            </button>
                                                            <?php if(!is_null($category->discount_percent)): ?>
                                                                <form method="POST"
                                                                      action="<?php echo e(route('admin.discount.category.clear', $category)); ?>">
                                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                                        <i class="fas fa-xmark me-1"></i> Clear
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>


<div class="modal fade" id="globalDiscountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Global discount</h5>
                    <small class="text-muted">Applies after markup to every product</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('admin.discount.bulk')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="global-current-display mb-3">
                        <div class="text-muted small mb-1">Current global discount</div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="global-current-pct"><?php echo e($globalDiscountValue ?? 0); ?>%</span>
                            <?php if($globalDiscountValue): ?>
                                <span class="badge badge-light-danger">active</span>
                                <?php if(isset($globalDiscountExpiresAt) && $globalDiscountExpiresAt): ?>
                                    <span class="live-countdown badge rounded-pill"
                                          data-expires="<?php echo e($globalDiscountExpiresAt->toIso8601String()); ?>"></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge markup-null-badge">not set</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="alert-triangle" style="width:14px;height:14px;"></i>
                        Overrides all category &amp; subcategory discounts.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold mb-2">Set new global discount</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1"
                                   name="discount_percent" min="0" max="100" step="1"
                                   value="<?php echo e($sliderDefault); ?>"
                                   id="globalDiscountSlider"
                                   oninput="updateGlobalDiscountSlider()">
                            <span class="badge bg-danger text-white px-3 py-2"
                                  id="globalDiscountValueBadge"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">
                                <?php echo e($sliderDefault); ?>%
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">
                            Expiry <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <input type="datetime-local"
                               class="form-control form-control-sm"
                               name="discount_expires_at"
                               id="globalDiscountExpiry"
                               min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                               oninput="updateExpiryPreview('globalDiscountExpiry','globalExpiryPreview')">
                        <div id="globalExpiryPreview" class="expiry-preview mt-2" style="display:none;"></div>
                        <div class="form-text" id="globalExpiryHint">Leave blank and the discount never expires.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                        <i data-feather="zap" style="width:14px;height:14px;"></i>
                        Apply to all
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="categoryDiscountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set category discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryDiscountForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Category</label>
                        <p class="fw-bold mb-0" id="categoryDiscountName"></p>
                    </div>
                    <div class="markup-current-display mb-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">Current:</span>
                            <span class="badge badge-light-danger" id="categoryDiscountCurrentVal">0%</span>
                            
                            <span class="live-countdown badge rounded-pill d-none"
                                  id="categoryCurrentCountdown"></span>
                            <i data-feather="arrow-right" style="width:14px;height:14px;color:#6e84a3;"></i>
                            <span class="text-muted small">New:</span>
                            <span class="badge badge-light-warning" id="categoryDiscountNewVal">0%</span>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Discount applies after markup. e.g. 20% markup then 10% discount = 8% net increase.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Discount percentage</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1"
                                   name="discount_percent" min="0" max="100" step="1" value="0"
                                   id="categoryDiscountSlider"
                                   oninput="updateCategoryDiscountPreview()">
                            <span class="badge bg-danger px-3 py-2"
                                  id="categoryDiscountValue"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">0%</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">
                            Expiry <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <input type="datetime-local"
                               class="form-control form-control-sm"
                               name="discount_expires_at"
                               id="categoryDiscountExpiry"
                               min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                               oninput="updateExpiryPreview('categoryDiscountExpiry','categoryExpiryPreview')">
                        <div id="categoryExpiryPreview" class="expiry-preview mt-2" style="display:none;"></div>
                        <div class="form-text" id="categoryExpiryHint">Leave blank and the discount never expires.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Apply discount</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="SubcategoryDiscountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set subcategory discount</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="SubcategoryDiscountForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Subcategory</label>
                        <p class="fw-bold mb-0" id="SubcategoryDiscountName"></p>
                    </div>
                    <div class="markup-current-display mb-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small">Current:</span>
                            <span class="badge badge-light-danger" id="SubcategoryDiscountCurrentVal">0%</span>
                            
                            <span class="live-countdown badge rounded-pill d-none"
                                  id="subcategoryCurrentCountdown"></span>
                            <i data-feather="arrow-right" style="width:14px;height:14px;color:#6e84a3;"></i>
                            <span class="text-muted small">New:</span>
                            <span class="badge badge-light-warning" id="SubcategoryDiscountNewVal">0%</span>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Overrides the category discount for all products in this subcategory.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Discount percentage</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1"
                                   name="discount_percent" min="0" max="100" step="1" value="0"
                                   id="SubcategoryDiscountSlider"
                                   oninput="updateSubcategoryDiscountPreview()">
                            <span class="badge bg-danger px-3 py-2"
                                  id="SubcategoryDiscountValue"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">0%</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">
                            Expiry <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <input type="datetime-local"
                               class="form-control form-control-sm"
                               name="discount_expires_at"
                               id="SubcategoryDiscountExpiry"
                               min="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                               oninput="updateExpiryPreview('SubcategoryDiscountExpiry','subcategoryExpiryPreview')">
                        <div id="subcategoryExpiryPreview" class="expiry-preview mt-2" style="display:none;"></div>
                        <div class="form-text" id="subcategoryExpiryHint">Leave blank and the discount never expires.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.markup-null-badge   { background:rgba(108,117,125,0.12);color:#6c757d;border:1px solid rgba(108,117,125,0.25);font-weight:600; }
.global-markup-bar   { display:flex;align-items:center;gap:1.25rem;padding:1rem 1.25rem;background:#fff;border:1px solid #e2e8f0;border-left:4px solid #ea5455;border-radius:8px;flex-wrap:wrap; }
.gmb-left            { display:flex;flex-direction:column;gap:4px;min-width:120px; }
.gmb-label           { font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;color:#6e84a3;font-weight:600; }
.gmb-value-row       { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.gmb-pct             { font-size:2rem;font-weight:700;color:#2c3e50;line-height:1; }
.gmb-active-badge    { display:inline-flex;align-items:center;gap:4px;background:rgba(234,84,85,0.15);color:#c0392b;font-size:0.72rem;font-weight:600;padding:3px 8px;border-radius:20px;border:1px solid rgba(234,84,85,0.35); }
.gmb-inactive-badge  { display:inline-flex;align-items:center;gap:4px;background:rgba(108,117,125,0.1);color:#6c757d;font-size:0.72rem;font-weight:600;padding:3px 8px;border-radius:20px;border:1px solid rgba(108,117,125,0.2); }
.gmb-actions         { margin-left:auto; }
.global-current-display { background:#f8f9fa;border:1px solid #e2e8f0;border-radius:8px;padding:0.85rem 1rem; }
.global-current-pct  { font-size:1.75rem;font-weight:700;color:#2c3e50;line-height:1; }
.markup-current-display { background:#f8f9fa;border:1px solid #e2e8f0;border-radius:6px;padding:0.6rem 0.85rem; }
.dropdown-menu       { min-width:360px;max-height:300px;overflow-y:auto;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12); }
.dropdown-item-text  { font-size:0.9rem;color:#0f1111;display:block;width:100%;transition:background-color 0.15s; }
.dropdown-item-text:hover { background-color:#f0f2f2; }

/* ── Expiry meta (table cell date + relative line) ── */
.expiry-meta {
    display: flex; align-items: center; gap: 0.35rem;
    flex-wrap: wrap; margin-top: 3px;
}
.expiry-date {
    font-size: 0.72rem; font-weight: 600; color: #2c3e50;
}
.expiry-rel {
    font-size: 0.7rem; color: #28c76f; font-weight: 500;
}
.expiry-meta--expired .expiry-date { color: #6c757d; }
.expiry-meta--expired .expiry-rel  { color: #ea5455; }

/* ── Live countdown badge states ── */
.live-countdown             { font-size:0.7rem; font-weight:600; }
.live-countdown.cd-ok       { background:rgba(40,199,111,0.12); color:#1a7a45; border:1px solid rgba(40,199,111,0.3); }
.live-countdown.cd-soon     { background:rgba(255,159,67,0.15);  color:#995500; border:1px solid rgba(255,159,67,0.4); }
.live-countdown.cd-expired  { background:rgba(108,117,125,0.12); color:#6c757d; border:1px solid rgba(108,117,125,0.25); }

/* ── Expiry preview block (inside modals) ── */
.expiry-preview            { display:flex;align-items:flex-start;gap:0.6rem;padding:0.6rem 0.8rem;border-radius:6px;font-size:0.8rem;line-height:1.5;border:1px solid; }
.expiry-preview.state-future { background:#f0fdf4;border-color:#bbf7d0;color:#166534; }
.expiry-preview.state-soon   { background:#fffbeb;border-color:#fde68a;color:#92400e; }
.expiry-preview.state-past   { background:#fff1f2;border-color:#fecdd3;color:#9f1239; }
.expiry-preview .ep-icon   { flex-shrink:0;margin-top:1px; }
.expiry-preview .ep-main   { font-weight:600; }
.expiry-preview .ep-sub    { font-weight:400;opacity:0.75;font-size:0.73rem; }

@media(max-width:768px){
    .global-markup-bar { flex-direction:column;align-items:flex-start; }
    .gmb-actions { margin-left:0;width:100%; }
    .table thead { display:none; }
    .table tr  { display:flex;flex-direction:column;margin-bottom:1.5rem;border:1px solid #e0e0e0;border-radius:8px;padding:1rem;background:#fff; }
    .table td  { display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0;border:none; }
    .table td::before { content:attr(data-label);font-weight:600;color:#37475a;flex:1; }
}
</style>

<script>
/* ═══════════════════════════════════════════════
   LIVE COUNTDOWN ENGINE  (ticks every second)
   Any element with class="live-countdown" and
   data-expires="<ISO 8601 string>" is updated.
═══════════════════════════════════════════════ */
(function () {
    function formatCountdown(ms) {
        if (ms <= 0) return { text: 'Expired', cls: 'cd-expired' };
        const s   = Math.floor(ms / 1000);
        const m   = Math.floor(s  / 60);
        const h   = Math.floor(m  / 60);
        const d   = Math.floor(h  / 24);
        const cls = ms < 86400000 ? 'cd-soon' : 'cd-ok';
        let text;
        if      (d > 0) text = d + 'd ' + (h % 24) + 'h left';
        else if (h > 0) text = h + 'h ' + (m % 60) + 'm left';
        else if (m > 0) text = m + 'm ' + (s % 60) + 's left';
        else            text = s + 's left';
        return { text, cls };
    }

    function tick() {
        var now = Date.now();
        document.querySelectorAll('.live-countdown[data-expires]').forEach(function (el) {
            var exp = new Date(el.dataset.expires).getTime();
            if (!exp) return;
            var r = formatCountdown(exp - now);
            el.textContent = r.text;
            el.classList.remove('cd-ok', 'cd-soon', 'cd-expired');
            el.classList.add(r.cls);
            el.style.display = '';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        tick();
        setInterval(tick, 1000);
    });
})();

/* ═══════════════════════════════════════════════
   EXPIRY PREVIEW  (modal datetime-local input)
═══════════════════════════════════════════════ */
function updateExpiryPreview(inputId, previewId) {
    var input   = document.getElementById(inputId);
    var preview = document.getElementById(previewId);
    var hint    = preview.nextElementSibling;   /* the .form-text div */

    var raw = input.value;
    if (!raw) {
        preview.style.display = 'none';
        preview.className = 'expiry-preview mt-2';
        if (hint) hint.style.display = '';
        return;
    }

    var expiry = new Date(raw);
    var now    = new Date();
    var diffMs = expiry - now;

    if (hint) hint.style.display = 'none';
    preview.style.display = 'flex';

    var absDate = expiry.toLocaleDateString('en-GB', { weekday:'short', day:'numeric', month:'short', year:'numeric' });
    var absTime = expiry.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' });

    var rel = '';
    if (diffMs <= 0) {
        rel = 'already in the past';
    } else {
        var days = Math.floor(diffMs / 86400000);
        var hrs  = Math.floor((diffMs % 86400000) / 3600000);
        var mins = Math.floor((diffMs % 3600000)  / 60000);
        if      (days > 0) rel = 'in ' + days + 'd ' + hrs + 'h';
        else if (hrs  > 0) rel = 'in ' + hrs  + 'h ' + mins + 'm';
        else if (mins > 0) rel = 'in ' + mins + ' minute' + (mins !== 1 ? 's' : '');
        else               rel = 'in less than a minute';
    }

    var stateClass = 'state-future', icon = '\u2713';
    if      (diffMs <= 0)       { stateClass = 'state-past';  icon = '\u2715'; }
    else if (diffMs < 86400000) { stateClass = 'state-soon';  icon = '\u26a0'; }

    preview.className = 'expiry-preview mt-2 ' + stateClass;
    preview.innerHTML =
        '<span class="ep-icon">' + icon + '</span>' +
        '<div>' +
            '<div class="ep-main">' + absDate + ' at ' + absTime + '</div>' +
            '<div class="ep-sub">'  + rel + '</div>' +
        '</div>';
}

/* ═══════════════════════════════════════════════
   BOOTSTRAP + FEATHER INIT
═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dropdown-toggle').forEach(function (el) {
        new bootstrap.Dropdown(el);
    });
    if (typeof feather !== 'undefined') feather.replace();
    document.querySelectorAll('.modal').forEach(function (el) {
        el.addEventListener('shown.bs.modal', function () {
            if (typeof feather !== 'undefined') feather.replace();
        });
    });
    document.getElementById('categorySearch')?.addEventListener('input', function (e) {
        var term = e.target.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(function (row) {
            var cell = row.querySelector('td[data-label="Name"]');
            if (cell) row.style.display = cell.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });
});

/* ── Global slider ── */
function updateGlobalDiscountSlider() {
    var val = parseInt(document.getElementById('globalDiscountSlider').value);
    document.getElementById('globalDiscountValueBadge').textContent = val + '%';
}

/* ── Global modal (pre-fills existing expiry) ── */
function openGlobalDiscountModal() {
    var existingExpiry = <?php echo json_encode(isset($globalDiscountExpiresAt) && $globalDiscountExpiresAt
        ? $globalDiscountExpiresAt->format('Y-m-d\TH:i')
        : '', 15, 512) ?>;

    var expiryInput = document.getElementById('globalDiscountExpiry');
    expiryInput.value = existingExpiry || '';

    /* fire the preview immediately so it shows on open */
    updateExpiryPreview('globalDiscountExpiry', 'globalExpiryPreview');

    /* wire the countdown badge inside the modal's "Current" display */
    var cdBadge = document.querySelector('#globalDiscountModal .live-countdown');
    if (cdBadge && existingExpiry) {
        cdBadge.dataset.expires = new Date(existingExpiry).toISOString();
        cdBadge.classList.remove('d-none');
    }

    new bootstrap.Modal(document.getElementById('globalDiscountModal')).show();
}
/* ═══════════════════════════════════════════════
   CATEGORY MODAL
═══════════════════════════════════════════════ */
function setCategoryDiscount(categoryId, categoryName, currentDiscount, currentExpiry) {
    var discount = currentDiscount || 0;
    document.getElementById('categoryDiscountName').textContent       = categoryName;
    document.getElementById('categoryDiscountSlider').value           = discount;
    document.getElementById('categoryDiscountValue').textContent      = discount + '%';
    document.getElementById('categoryDiscountCurrentVal').textContent = discount + '%';
    document.getElementById('categoryDiscountNewVal').textContent     = discount + '%';
    document.getElementById('categoryDiscountForm').action            = '/admin/discount/category/' + categoryId;

    /* expiry input */
    document.getElementById('categoryDiscountExpiry').value = currentExpiry || '';

    /* ticking countdown badge next to "Current:" */
    var cdBadge = document.getElementById('categoryCurrentCountdown');
    if (currentExpiry) {
        cdBadge.dataset.expires = new Date(currentExpiry).toISOString();
        cdBadge.classList.remove('d-none');
    } else {
        cdBadge.dataset.expires = '';
        cdBadge.textContent = '';
        cdBadge.classList.add('d-none');
    }

    /* fire preview immediately for pre-filled expiry */
    updateExpiryPreview('categoryDiscountExpiry', 'categoryExpiryPreview');

    new bootstrap.Modal(document.getElementById('categoryDiscountModal')).show();
}

function updateCategoryDiscountPreview() {
    var val     = parseInt(document.getElementById('categoryDiscountSlider').value);
    var current = parseInt(document.getElementById('categoryDiscountCurrentVal').textContent) || 0;
    var newEl   = document.getElementById('categoryDiscountNewVal');
    document.getElementById('categoryDiscountValue').textContent = val + '%';
    newEl.textContent = val + '%';
    newEl.className   = 'badge ' + (val > current ? 'badge-light-danger' : val < current ? 'badge-light-success' : 'badge-light-warning');
}

/* ═══════════════════════════════════════════════
   SUBCATEGORY MODAL
═══════════════════════════════════════════════ */
function setSubcategoryDiscount(SubcategoryId, SubcategoryName, currentDiscount, currentExpiry) {
    var discount = currentDiscount || 0;
    document.getElementById('SubcategoryDiscountName').textContent       = SubcategoryName;
    document.getElementById('SubcategoryDiscountSlider').value           = discount;
    document.getElementById('SubcategoryDiscountValue').textContent      = discount + '%';
    document.getElementById('SubcategoryDiscountCurrentVal').textContent = discount + '%';
    document.getElementById('SubcategoryDiscountNewVal').textContent     = discount + '%';
    document.getElementById('SubcategoryDiscountForm').action            = '/admin/discount/Subcategory/' + SubcategoryId;

    /* expiry input */
    document.getElementById('SubcategoryDiscountExpiry').value = currentExpiry || '';

    /* ticking countdown badge next to "Current:" */
    var cdBadge = document.getElementById('subcategoryCurrentCountdown');
    if (currentExpiry) {
        cdBadge.dataset.expires = new Date(currentExpiry).toISOString();
        cdBadge.classList.remove('d-none');
    } else {
        cdBadge.dataset.expires = '';
        cdBadge.textContent = '';
        cdBadge.classList.add('d-none');
    }

    /* fire preview immediately for pre-filled expiry */
    updateExpiryPreview('SubcategoryDiscountExpiry', 'subcategoryExpiryPreview');

    setTimeout(function () {
        new bootstrap.Modal(document.getElementById('SubcategoryDiscountModal')).show();
    }, 100);
}

function updateSubcategoryDiscountPreview() {
    var val     = parseInt(document.getElementById('SubcategoryDiscountSlider').value);
    var current = parseInt(document.getElementById('SubcategoryDiscountCurrentVal').textContent) || 0;
    var newEl   = document.getElementById('SubcategoryDiscountNewVal');
    document.getElementById('SubcategoryDiscountValue').textContent = val + '%';
    newEl.textContent = val + '%';
    newEl.className   = 'badge ' + (val > current ? 'badge-light-danger' : val < current ? 'badge-light-success' : 'badge-light-warning');
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/discount/index.blade.php ENDPATH**/ ?>