

<?php $__env->startSection('title', 'Confirm Password — Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>

    <h4>Confirm Password 🔒</h4>
    <p class="subtitle">Please confirm your password before continuing.</p>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <strong>Oops! Something went wrong</strong>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('password.confirm')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autofocus
                    autocomplete="current-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword()" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Confirm Password</button>
    </form>

    <?php if(Route::has('password.request')): ?>
        <div class="auth-divider"><span>Trouble signing in?</span></div>
        <p class="auth-card__foot">
            <a href="<?php echo e(route('password.request')); ?>">Forgot your password? →</a>
        </p>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.authlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/auth/passwords/confirm.blade.php ENDPATH**/ ?>