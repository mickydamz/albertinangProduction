

<?php $__env->startSection('title', 'Forgot Password — Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>

    <h4>Forgot Password? 🔒</h4>
    <p class="subtitle">Enter your email and we'll send you a link to reset your password.</p>

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

    <form method="POST" action="<?php echo e(route('password.email')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="you@example.com"
                value="<?php echo e(old('email')); ?>"
                required
                autofocus
                autocomplete="email"
            >
        </div>

        <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <div class="auth-divider"><span>Remembered it?</span></div>

    <p class="auth-card__foot">
        <a href="<?php echo e(route('login')); ?>">← Back to sign in</a>
    </p>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.authlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>