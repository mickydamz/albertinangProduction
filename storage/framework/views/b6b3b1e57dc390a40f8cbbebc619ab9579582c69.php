

<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Coupon</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.coupons.index')); ?>">Coupons</a></li>
                                <li class="breadcrumb-item active"><?php echo e($coupon->code); ?></li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-6">

                    
                    <div class="card mb-1">
                        <div class="card-body py-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="coupon-code-pill"><?php echo e($coupon->code); ?></span>
                                    <?php if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at->isFuture())): ?>
                                        <span class="badge badge-light-success">Active</span>
                                    <?php elseif($coupon->expires_at && $coupon->expires_at->isPast()): ?>
                                        <span class="badge bg-secondary">Expired</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-danger">Inactive</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 text-muted" style="font-size:0.82rem;">
                                    <span>
                                        <i data-feather="users" style="width:13px;height:13px;"></i>
                                        <?php echo e($coupon->usages_count); ?> use<?php echo e($coupon->usages_count !== 1 ? 's' : ''); ?>

                                    </span>
                                    <span>Created <?php echo e($coupon->created_at->diffForHumans()); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">Edit Coupon</h4>
                        </div>
                        <div class="card-body pt-2">

                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo e(route('admin.coupons.update', $coupon)); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">
                                        Coupon code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="code"
                                           value="<?php echo e(old('code', $coupon->code)); ?>"
                                           class="form-control text-uppercase <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           style="letter-spacing:0.08em;font-family:monospace;font-weight:700;"
                                           required>
                                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Discount type <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        <label class="discount-type-card <?php echo e(old('discount_type', $coupon->discount_type) === 'percent' ? 'selected' : ''); ?>"
                                               for="type_percent">
                                            <input type="radio" id="type_percent" name="discount_type"
                                                   value="percent"
                                                   <?php echo e(old('discount_type', $coupon->discount_type) === 'percent' ? 'checked' : ''); ?>

                                                   onchange="updateDiscountType()">
                                            <i data-feather="percent" style="width:20px;height:20px;"></i>
                                            <span>Percentage</span>
                                        </label>
                                        <label class="discount-type-card <?php echo e(old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : ''); ?>"
                                               for="type_fixed">
                                            <input type="radio" id="type_fixed" name="discount_type"
                                                   value="fixed"
                                                   <?php echo e(old('discount_type', $coupon->discount_type) === 'fixed' ? 'checked' : ''); ?>

                                                   onchange="updateDiscountType()">
                                            <i data-feather="tag" style="width:20px;height:20px;"></i>
                                            <span>Fixed (₦)</span>
                                        </label>
                                    </div>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold" id="valueLabel">Discount value <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="valuePrefix">
                                            <?php echo e(old('discount_type', $coupon->discount_type) === 'percent' ? '%' : '₦'); ?>

                                        </span>
                                        <input type="number"
                                               name="value"
                                               value="<?php echo e(old('value', $coupon->value)); ?>"
                                               class="form-control <?php $__errorArgs = ['value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               step="0.01" min="0.01" required>
                                    </div>
                                    <?php $__errorArgs = ['value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-1" id="maxDiscountRow"
                                     style="<?php echo e(old('discount_type', $coupon->discount_type) === 'fixed' ? 'display:none;' : ''); ?>">
                                    <label class="form-label fw-bold">Max discount amount (₦)</label>
                                    <input type="number"
                                           name="max_discount_amount"
                                           value="<?php echo e(old('max_discount_amount', $coupon->max_discount_amount)); ?>"
                                           class="form-control <?php $__errorArgs = ['max_discount_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           step="1" min="0"
                                           placeholder="Leave blank for no cap">
                                    <?php $__errorArgs = ['max_discount_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Minimum order amount (₦)</label>
                                    <input type="number"
                                           name="min_order_amount"
                                           value="<?php echo e(old('min_order_amount', $coupon->min_order_amount)); ?>"
                                           class="form-control <?php $__errorArgs = ['min_order_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           step="1" min="0">
                                    <?php $__errorArgs = ['min_order_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Max uses</label>
                                    <?php if($coupon->used_count > 0): ?>
                                        <small class="text-muted ms-1">
                                            (<?php echo e($coupon->used_count); ?> used so far — cannot set below this)
                                        </small>
                                    <?php endif; ?>
                                    <input type="number"
                                           name="max_uses"
                                           value="<?php echo e(old('max_uses', $coupon->max_uses)); ?>"
                                           class="form-control <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           step="1" min="<?php echo e($coupon->used_count ?: 1); ?>"
                                           placeholder="Leave blank for unlimited">
                                    <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="text-muted">Total redemptions across all customers.</small>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Usage per customer</label>
                                    <div class="d-flex gap-2">
                                        <label class="discount-type-card <?php echo e(old('multi_use', $coupon->multi_use) ? '' : 'selected'); ?>"
                                               for="use_single">
                                            <input type="radio" id="use_single" name="multi_use"
                                                   value="0" <?php echo e(old('multi_use', $coupon->multi_use) ? '' : 'checked'); ?>

                                                   onchange="updateUsageType()">
                                            <i data-feather="user-check" style="width:20px;height:20px;"></i>
                                            <span>Single use</span>
                                        </label>
                                        <label class="discount-type-card <?php echo e(old('multi_use', $coupon->multi_use) ? 'selected' : ''); ?>"
                                               for="use_multi">
                                            <input type="radio" id="use_multi" name="multi_use"
                                                   value="1" <?php echo e(old('multi_use', $coupon->multi_use) ? 'checked' : ''); ?>

                                                   onchange="updateUsageType()">
                                            <i data-feather="repeat" style="width:20px;height:20px;"></i>
                                            <span>Multiple uses</span>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Single use: each customer can redeem this coupon only once.
                                        Multiple uses: a customer may redeem it repeatedly (subject to Max uses).
                                    </small>
                                </div>

                                
                                <div class="mb-1">
                                    <label class="form-label fw-bold">Expiry date</label>
                                    <input type="datetime-local"
                                           name="expires_at"
                                           value="<?php echo e(old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i'))); ?>"
                                           class="form-control <?php $__errorArgs = ['expires_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php $__errorArgs = ['expires_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="is_active" id="is_active" value="1"
                                               <?php echo e(old('is_active', $coupon->is_active) ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>

                                
                                <div class="coupon-preview mb-2" id="couponPreview">
                                    <div class="coupon-preview__label">Preview</div>
                                    <div class="coupon-preview__body">
                                        <span class="coupon-code-pill" id="previewCode"><?php echo e($coupon->code); ?></span>
                                        <span class="coupon-preview__desc" id="previewDesc"><?php echo e($coupon->describeDiscount()); ?></span>
                                    </div>
                                </div>

                                <div class="d-flex gap-1">
                                    <button type="submit" class="btn btn-warning">
                                        <i data-feather="save" style="width:14px;height:14px;"></i>
                                        Save changes
                                    </button>
                                    <a href="<?php echo e(route('admin.coupons.index')); ?>" class="btn btn-secondary">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('admin.coupons._form_styles_scripts', ['mode' => 'edit'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/coupons/edit.blade.php ENDPATH**/ ?>