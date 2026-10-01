<aside class="acct-sidebar">
    <nav>
        <ul>
            <li>
                <a href="<?php echo e(route('account.index')); ?>"
                   <?php if(($activeNav ?? '') === 'profile'): ?> class="active" aria-current="page" <?php endif; ?>>
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a href="<?php echo e(url('/account/orders')); ?>"
                   <?php if(($activeNav ?? '') === 'orders'): ?> class="active" aria-current="page" <?php endif; ?>>
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('account.change-password')); ?>"
                   <?php if(($activeNav ?? '') === 'password'): ?> class="active" aria-current="page" <?php endif; ?>>
                    <i class="fas fa-lock"></i> Change Password
                </a>
            </li>
            <li>
                <a href="<?php echo e(url('/logout')); ?>"
                   onclick="event.preventDefault(); document.getElementById('acct-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
<form id="acct-logout-form" action="<?php echo e(url('/logout')); ?>" method="POST" style="display:none;"><?php echo csrf_field(); ?></form>
<?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/partials/account-sidebar.blade.php ENDPATH**/ ?>