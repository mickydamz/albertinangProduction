<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        
        <div class="content-header row">
            <div class="col-12 mb-1">
                <h2 class="content-header-title float-start mb-0">User Profile</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Users</a></li>
                        <li class="breadcrumb-item active">#<?php echo e($user->id); ?></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php
                $statusMap = ['green' => 'success', 'yellow' => 'warning', 'banned' => 'danger'];
                $statusColor = $statusMap[$user->status] ?? 'secondary';
                $verified = $user->hasVerifiedEmail();
                $avatar = $user->avatar ? asset('storage/' . $user->avatar) : null;
                $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
            ?>

            
            <div class="card mb-2">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <?php if($avatar): ?>
                            <img src="<?php echo e($avatar); ?>" alt="<?php echo e($user->name); ?>" width="72" height="72"
                                 class="rounded-circle border" style="object-fit:cover;"
                                 onerror="this.onerror=null;this.style.display='none';">
                        <?php else: ?>
                            <div class="avatar avatar-xl bg-light-primary rounded-circle">
                                <div class="avatar-content" style="font-size:1.6rem;font-weight:700;"><?php echo e($initial); ?></div>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h3 class="mb-25"><?php echo e($user->name); ?></h3>
                            <p class="mb-25 text-muted"><i class="fas fa-envelope me-50"></i><?php echo e($user->email); ?></p>
                            <span class="badge bg-light-info text-uppercase me-50"><?php echo e($user->role); ?></span>
                            <span class="badge bg-light-<?php echo e($statusColor); ?> text-capitalize"><?php echo e($user->status ?? 'n/a'); ?></span>
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-50"></i>Edit
                        </a>
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-50"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="row g-2 mb-2">
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100">
                        <div class="card-body d-flex align-items-center gap-1">
                            <div class="avatar bg-light-success rounded"><div class="avatar-content"><i class="fas fa-wallet"></i></div></div>
                            <div>
                                <h4 class="mb-0">&#8358;<?php echo e(number_format((float) ($user->account_balance ?? 0), 0)); ?></h4>
                                <small class="text-muted">Balance</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100">
                        <div class="card-body d-flex align-items-center gap-1">
                            <div class="avatar bg-light-primary rounded"><div class="avatar-content"><i class="fas fa-user-shield"></i></div></div>
                            <div>
                                <h4 class="mb-0 text-capitalize"><?php echo e($user->role); ?></h4>
                                <small class="text-muted">Role</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100">
                        <div class="card-body d-flex align-items-center gap-1">
                            <div class="avatar bg-light-<?php echo e($verified ? 'success' : 'danger'); ?> rounded">
                                <div class="avatar-content"><i class="fas fa-<?php echo e($verified ? 'check-circle' : 'times-circle'); ?>"></i></div>
                            </div>
                            <div>
                                <h4 class="mb-0"><?php echo e($verified ? 'Yes' : 'No'); ?></h4>
                                <small class="text-muted">Email Verified</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100">
                        <div class="card-body d-flex align-items-center gap-1">
                            <div class="avatar bg-light-<?php echo e($statusColor); ?> rounded"><div class="avatar-content"><i class="fas fa-circle-check"></i></div></div>
                            <div>
                                <h4 class="mb-0 text-capitalize"><?php echo e($user->status ?? 'n/a'); ?></h4>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title"><i class="fas fa-address-card text-primary me-50"></i>Contact & Account</h4>
                </div>
                <div class="card-body pt-1">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted"><i class="fas fa-phone me-50"></i>Phone</span><strong><?php echo e($user->phone_no ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted"><i class="fas fa-globe me-50"></i>Country</span><strong><?php echo e($user->country ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50"><span class="text-muted"><i class="fas fa-city me-50"></i>City</span><strong><?php echo e($user->city ?? '—'); ?></strong></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted"><i class="fas fa-hashtag me-50"></i>Affiliate Code</span><strong class="font-monospace"><?php echo e($user->affiliate_code ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted"><i class="fas fa-mail-bulk me-50"></i>Postal Code</span><strong><?php echo e($user->postal_code ?? '—'); ?></strong></li>
                                <li class="d-flex justify-content-between py-50"><span class="text-muted"><i class="fas fa-calendar me-50"></i>Joined</span><strong><?php echo e($user->created_at?->format('d M Y') ?? '—'); ?></strong></li>
                            </ul>
                        </div>
                    </div>
                    <?php if($user->shipping_address): ?>
                        <hr>
                        <h6 class="fw-bolder"><i class="fas fa-map-marker-alt text-danger me-50"></i>Shipping Address</h6>
                        <p class="text-muted mb-0"><?php echo e($user->shipping_address); ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/users/show.blade.php ENDPATH**/ ?>