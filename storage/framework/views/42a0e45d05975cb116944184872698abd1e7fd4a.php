<?php $__env->startSection('title', 'Reset Password — Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>

    <h4>Reset Password 🔒</h4>
    <p class="subtitle">Your new password must be different from previously used passwords.</p>

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

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('password.update')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="token" value="<?php echo e($token); ?>">

        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="you@example.com"
                value="<?php echo e($email ?? old('email')); ?>"
                required
                autofocus
                autocomplete="email"
            >
        </div>

        
        <div class="form-group">
            <label for="password">New Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter new password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword('password','pwdEyeIcon')" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <div class="pwd-wrap">
                <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repeat new password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword('password_confirmation','pwdEyeIcon2')" aria-label="Toggle confirm password">
                    <i class="fas fa-eye" id="pwdEyeIcon2"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Set New Password</button>
    </form>

    <div class="auth-divider"><span>Back to sign in?</span></div>

    <p class="auth-card__foot">
        <a href="<?php echo e(route('login')); ?>">← Return to login</a>
    </p>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.authlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/auth/passwords/reset.blade.php ENDPATH**/ ?>