
<?php $__env->startSection('content'); ?>

<style>
body { background: #f5f7f4; }

.profile-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 13px 16px;
    border-radius: var(--radius);
    margin-bottom: 16px;
    font-size: 13.5px;
    font-weight: 500;
}
.profile-alert i { font-size: 14px; margin-top: 1px; flex-shrink: 0; }
.profile-alert--success { background: #e6f4ea; border: 1px solid #c8e6c9; color: #2e7d32; }
.profile-alert--error   { background: #ffebee; border: 1px solid #ffcdd2; color: #c62828; }

.profile-section {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    margin-bottom: 16px;
}
.profile-section__head {
    padding: 16px 22px;
    background: var(--surf2);
    border-bottom: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    gap: 9px;
    font-family: var(--font-head);
    font-size: 15px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.2px;
}
.profile-section__head i { color: var(--g500); font-size: 14px; }
.profile-section__body { padding: 22px; }

.profile-form { display: flex; flex-direction: column; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label { font-size: 13px; font-weight: 700; color: var(--ink); letter-spacing: .05px; }
.form-group label span.req { color: var(--g600); margin-left: 2px; }

.input-wrap { position: relative; }
.form-control {
    padding: 11px 44px 11px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    font-family: var(--font-body);
    color: var(--ink);
    background: var(--surface);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
    box-sizing: border-box;
    min-height: 44px;
}
.form-control:hover { border-color: var(--border2); }
.form-control:focus {
    border-color: var(--g500);
    box-shadow: 0 0 0 3px rgba(90,171,31,.12);
}
.form-control::placeholder { color: var(--ink3); }
.form-control.is-invalid { border-color: #c62828 !important; }

.toggle-pw {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--ink3);
    font-size: 13px;
    padding: 4px;
    transition: color .15s;
}
.toggle-pw:hover { color: var(--g600); }

.form-hint { font-size: 11.5px; color: var(--ink3); margin-top: 2px; }
.form-hint.error { color: #c62828; }

/* Password strength bar */
.strength-bar-wrap {
    height: 4px;
    background: var(--border);
    border-radius: 99px;
    margin-top: 6px;
    overflow: hidden;
}
.strength-bar {
    height: 100%;
    border-radius: 99px;
    width: 0%;
    transition: width .3s, background .3s;
}

.profile-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    margin-top: 4px;
}
.profile-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: var(--font-body);
    cursor: pointer;
    border: none;
    transition: all .18s;
    text-decoration: none;
    white-space: nowrap;
    min-height: 44px;
}
.profile-btn--primary { background: var(--g500); color: #fff; }
.profile-btn--primary:hover {
    background: var(--g700);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(90,171,31,.32);
}
.profile-btn--secondary {
    background: var(--surface);
    color: var(--ink2);
    border: 1.5px solid var(--border);
}
.profile-btn--secondary:hover {
    border-color: var(--g400); color: var(--g600); background: var(--g50);
}

/* Tips box */
.pw-tips {
    background: var(--g50);
    border: 1px solid var(--border2);
    border-radius: var(--radius);
    padding: 14px 16px;
    font-size: 12.5px;
    color: var(--ink2);
    line-height: 1.7;
}
.pw-tips strong { color: var(--ink); display: block; margin-bottom: 4px; font-size: 13px; }
.pw-tips ul { margin: 0; padding-left: 18px; }
.pw-tips ul li { margin-bottom: 2px; }

@media (max-width: 640px) {
    .profile-actions { flex-direction: column; }
    .profile-btn { width: 100%; justify-content: center; }
}
</style>

<div class="acct-wrap">

    <ul class="acct-breadcrumb" aria-label="Breadcrumb">
        <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
        <li><a href="<?php echo e(route('account.index')); ?>">My Account</a></li>
        <li>Change Password</li>
    </ul>

    <div class="acct-layout">

        
        <?php echo $__env->make('sims.partials.account-sidebar', ['activeNav' => 'password'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <div class="acct-main">

            <div class="acct-header">
                <div class="acct-header__title">
                    <i class="fas fa-lock"></i>
                    Change Password
                </div>
            </div>

            <?php if(session('error')): ?>
                <div class="profile-alert profile-alert--error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="profile-alert profile-alert--error" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Please fix the following:</strong>
                        <ul style="margin:6px 0 0; padding-left:18px;">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li style="font-size:13px;"><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="profile-section">
                <div class="profile-section__head">
                    <i class="fas fa-key"></i>
                    Update Your Password
                </div>
                <div class="profile-section__body">
                    <form method="POST"
                          action="<?php echo e(route('account.change-password.update')); ?>"
                          class="profile-form"
                          id="pwForm">
                        <?php echo csrf_field(); ?>

                        
                        <div class="form-group">
                            <label for="current_password">
                                Current Password <span class="req">*</span>
                            </label>
                            <div class="input-wrap">
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="Enter your current password"
                                       required
                                       autocomplete="current-password">
                                <button type="button" class="toggle-pw" data-target="current_password" aria-label="Show password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="form-hint error"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-group">
                            <label for="password">
                                New Password <span class="req">*</span>
                            </label>
                            <div class="input-wrap">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       placeholder="At least 8 characters"
                                       required
                                       autocomplete="new-password">
                                <button type="button" class="toggle-pw" data-target="password" aria-label="Show password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="strength-bar-wrap">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <span class="form-hint" id="strengthLabel">Enter a new password</span>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="form-hint error"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="form-group">
                            <label for="password_confirmation">
                                Confirm New Password <span class="req">*</span>
                            </label>
                            <div class="input-wrap">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Re-enter your new password"
                                       required
                                       autocomplete="new-password">
                                <button type="button" class="toggle-pw" data-target="password_confirmation" aria-label="Show password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="form-hint" id="matchHint"></span>
                        </div>

                        
                        <div class="pw-tips">
                            <strong><i class="fas fa-shield-alt" style="color:var(--g500);margin-right:5px;"></i>Password Tips</strong>
                            <ul>
                                <li>At least 8 characters long</li>
                                <li>Mix uppercase and lowercase letters</li>
                                <li>Include at least one number</li>
                                <li>Add a special character (e.g. !, @, #, $)</li>
                            </ul>
                        </div>

                        <div class="profile-actions">
                            <button type="submit" class="profile-btn profile-btn--primary" id="submitBtn">
                                <i class="fas fa-save" style="font-size:11px;"></i>
                                Change Password
                            </button>
                            <a href="<?php echo e(route('account.index')); ?>" class="profile-btn profile-btn--secondary">
                                <i class="fas fa-arrow-left" style="font-size:11px;"></i>
                                Back to Profile
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Toggle password visibility ── */
    document.querySelectorAll('.toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    /* ── Password strength meter ── */
    const pwInput     = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthLbl = document.getElementById('strengthLabel');

    function getStrength(pw) {
        let score = 0;
        if (pw.length >= 8)  score++;
        if (pw.length >= 12) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;
        return score;
    }

    const levels = [
        { pct: '0%',   color: 'transparent', label: 'Enter a new password' },
        { pct: '25%',  color: '#e53935',     label: 'Weak' },
        { pct: '50%',  color: '#fb8c00',     label: 'Fair' },
        { pct: '75%',  color: '#fdd835',     label: 'Good' },
        { pct: '90%',  color: '#43a047',     label: 'Strong' },
        { pct: '100%', color: '#1b5e20',     label: 'Very Strong' },
    ];

    pwInput.addEventListener('input', () => {
        const score = pwInput.value.length === 0 ? 0 : Math.min(getStrength(pwInput.value), 5);
        strengthBar.style.width      = levels[score].pct;
        strengthBar.style.background = levels[score].color;
        strengthLbl.textContent      = levels[score].label;
        checkMatch();
    });

    /* ── Password match indicator ── */
    const confirmInput = document.getElementById('password_confirmation');
    const matchHint    = document.getElementById('matchHint');

    function checkMatch() {
        if (!confirmInput.value) { matchHint.textContent = ''; return; }
        if (pwInput.value === confirmInput.value) {
            matchHint.textContent = '✓ Passwords match';
            matchHint.style.color = 'var(--g600)';
            confirmInput.style.borderColor = 'var(--g400)';
        } else {
            matchHint.textContent = '✗ Passwords do not match';
            matchHint.style.color = '#c62828';
            confirmInput.style.borderColor = '#c62828';
        }
    }

    confirmInput.addEventListener('input', checkMatch);

    /* ── Client-side submit guard ── */
    document.getElementById('pwForm').addEventListener('submit', e => {
        const current = document.getElementById('current_password').value.trim();
        const pw      = pwInput.value;
        const confirm = confirmInput.value;

        if (!current) {
            e.preventDefault();
            document.getElementById('current_password').focus();
            return;
        }
        if (pw.length < 8) {
            e.preventDefault();
            pwInput.focus();
            return;
        }
        if (pw !== confirm) {
            e.preventDefault();
            confirmInput.focus();
            return;
        }
    });

});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/change-password.blade.php ENDPATH**/ ?>