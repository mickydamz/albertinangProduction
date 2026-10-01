<?php $__env->startSection('title', 'Sign In — Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>

    <h4>Sign In 🚀</h4>
    <p class="subtitle">Welcome back! Please sign in to continue.</p>

    
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

    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('login')); ?>" method="POST">
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
                autocomplete="email"
            >
        </div>

        
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
                    autocomplete="current-password"
                >
                <button type="button" class="pwd-toggle" onclick="togglePassword()" aria-label="Toggle password">
                    <i class="fas fa-eye" id="pwdEyeIcon"></i>
                </button>
            </div>
        </div>

        
        <div class="form-row">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <a href="<?php echo e(route('password.request')); ?>" class="form-forgot">Forgot password?</a>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>
    </form>

    <div class="auth-divider"><span>Don't have an account?</span></div>

    <p class="auth-card__foot">
        <a href="<?php echo e(route('register')); ?>">Create a free account →</a>
    </p>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/authlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/auth/login.blade.php ENDPATH**/ ?>