

<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Store Settings</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Store Settings</li>
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

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($err); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST"
                  action="<?php echo e(route('admin.settings.update')); ?>"
                  enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row">

                    
                    <div class="col-lg-8">

                        
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-store me-50 text-primary"></i> Store Identity
                                </h4>
                            </div>
                            <div class="card-body pt-2">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Store Name <span class="text-danger">*</span></label>
                                    <input type="text" name="store_name" class="form-control"
                                           value="<?php echo e(old('store_name', $settings['store_name'] ?? 'Albertina Nigeria')); ?>"
                                           required maxlength="100" placeholder="e.g. Albertina Nigeria">
                                    <small class="text-muted">Shown in the browser title and emails.</small>
                                </div>

                                
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Store Logo</label>
                                    <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                                        <?php
                                            $logoPath = $settings['store_logo'] ?? null;
                                            $logoSrc  = $logoPath
                                                ? asset('storage/' . $logoPath)
                                                : asset('image.png');
                                        ?>
                                        <img id="logoPreview"
                                             src="<?php echo e($logoSrc); ?>"
                                             alt="Current logo"
                                             style="height:60px;max-width:180px;object-fit:contain;border:1px solid #ddd;border-radius:6px;padding:4px;background:#f8f9fa;">
                                        <div>
                                            <input type="file" name="store_logo" id="store_logo"
                                                   class="form-control form-control-sm"
                                                   accept="image/*"
                                                   style="max-width:280px;"
                                                   onchange="previewLogo(this)">
                                            <small class="text-muted d-block mt-1">JPG, PNG, WebP or SVG — max 2 MB. Leave blank to keep the current logo.</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-address-card me-50 text-primary"></i> Contact &amp; Address
                                </h4>
                            </div>
                            <div class="card-body pt-2">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Store Address / Locations</label>
                                    <textarea name="store_address" class="form-control" rows="4"
                                              placeholder="e.g. 12 Oba Akran Ave, Ikeja, Lagos"><?php echo e(old('store_address', $settings['store_address'] ?? '')); ?></textarea>
                                    <small class="text-muted">Shown on the store locator and contact pages.</small>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label fw-semibold">Phone Number</label>
                                        <input type="text" name="store_phone" class="form-control"
                                               value="<?php echo e(old('store_phone', $settings['store_phone'] ?? '')); ?>"
                                               maxlength="50" placeholder="+234 800 000 0000">
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label fw-semibold">Email Address</label>
                                        <input type="email" name="store_email" class="form-control"
                                               value="<?php echo e(old('store_email', $settings['store_email'] ?? '')); ?>"
                                               maxlength="150" placeholder="info@yourstore.com">
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>

                    
                    <div class="col-lg-4">

                        
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-toggle-on me-50 text-primary"></i> Fulfillment Methods
                                </h4>
                            </div>
                            <div class="card-body pt-3">

                                <p class="text-muted mb-3" style="font-size:13px;">
                                    Disable a method to hide it from customers at checkout.
                                    If both are disabled, customers will see a "temporarily unavailable" notice.
                                </p>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox"
                                           name="delivery_enabled" id="delivery_enabled"
                                           value="1"
                                           <?php echo e(($settings['delivery_enabled'] ?? '1') === '1' ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="delivery_enabled">
                                        <i class="fas fa-truck me-50 text-secondary"></i> Delivery Enabled
                                    </label>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        Customers can choose home delivery at checkout.
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="pickup_enabled" id="pickup_enabled"
                                           value="1"
                                           <?php echo e(($settings['pickup_enabled'] ?? '1') === '1' ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="pickup_enabled">
                                        <i class="fas fa-store me-50 text-secondary"></i> Pickup Enabled
                                    </label>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        Customers can collect orders from pickup points.
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-shield-alt me-50 text-primary"></i> Two-Factor Authentication
                                </h4>
                            </div>
                            <div class="card-body pt-3">

                                <p class="text-muted mb-3" style="font-size:13px;">
                                    Force email-based 2FA for logins. Users receive a one-time code before accessing the site.
                                </p>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox"
                                           name="require_2fa_admin" id="require_2fa_admin"
                                           value="1"
                                           <?php echo e(($settings['require_2fa_admin'] ?? '0') === '1' ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="require_2fa_admin">
                                        <i class="fas fa-user-shield me-50 text-secondary"></i> Require 2FA for Admins
                                    </label>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        Admins must verify via email on every login.
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox"
                                           name="require_2fa_users" id="require_2fa_users"
                                           value="1"
                                           <?php echo e(($settings['require_2fa_users'] ?? '0') === '1' ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="require_2fa_users">
                                        <i class="fas fa-users me-50 text-secondary"></i> Require 2FA for All Users
                                    </label>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        All customers, suppliers, and affiliates must verify via email.
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           name="email_verification_enabled" id="email_verification_enabled"
                                           value="1"
                                           <?php echo e(($settings['email_verification_enabled'] ?? '0') === '1' ? 'checked' : ''); ?>>
                                    <label class="form-check-label fw-semibold" for="email_verification_enabled">
                                        <i class="fas fa-envelope-circle-check me-50 text-secondary"></i> Require Email Verification
                                    </label>
                                    <div class="text-muted mt-1" style="font-size:12px;">
                                        New users must confirm their email address before using their account.
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-50"></i> Save Settings
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>