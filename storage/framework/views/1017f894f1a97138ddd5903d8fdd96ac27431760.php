

<?php $__env->startSection('content'); ?>
   <!-- BEGIN: Content-->
   <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Account</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Account Settings </a>
                                    </li>
                                    <li class="breadcrumb-item active"> Account
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-pills mb-2">
                            <!-- account -->
                            <li class="nav-item">
                                <a class="nav-link" href="page-account-settings-account">
                                    <i data-feather="user" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">Account</span>
                                </a>
                            </li>
                            <!-- security -->
                            <li class="nav-item">
                                <a class="nav-link" href="page-account-settings-security">
                                    <i data-feather="lock" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">Security</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link active" href="twofactor">
                                    <i data-feather="lock" class="font-medium-3 me-50"></i>
                                    <span class="fw-bold">2FA</span>
                                </a>
                            </li>
                            <!-- billing and plans -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="page-account-settings-billing">-->
                            <!--        <i data-feather="bookmark" class="font-medium-3 me-50"></i>-->
                            <!--        <span class="fw-bold">Billings &amp; Plans</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!-- notification -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="page-account-settings-notifications">-->
                            <!--        <i data-feather="bell" class="font-medium-3 me-50"></i>-->
                            <!--        <span class="fw-bold">Notifications</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                            <!-- connection -->
                            <!--<li class="nav-item">-->
                            <!--    <a class="nav-link" href="page-account-settings-connections">-->
                            <!--        <i data-feather="link" class="font-medium-3 me-50"></i>-->
                            <!--        <span class="fw-bold">Connections</span>-->
                            <!--    </a>-->
                            <!--</li>-->
                        </ul>

                        <!-- profile -->
<div class="container">
    <h2>Two-Factor Authentication Settings</h2>

    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <!-- Check if two-factor authentication is enabled or not -->
    <?php if(auth()->user()->two_factor_code): ?>
        <div class="mb-4">
            <p>Two-factor authentication is currently <strong>enabled</strong>.</p>
            <form action="<?php echo e(route('two-factor.disable')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger">Disable Two-Factor Authentication</button>
            </form>
        </div>
    <?php else: ?>
        <div class="mb-4">
            <p>Two-factor authentication is currently <strong>disabled</strong>.</p>
            <form action="<?php echo e(route('two-factor.enable')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-primary">Enable Two-Factor Authentication</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/account/two-factor-settings.blade.php ENDPATH**/ ?>