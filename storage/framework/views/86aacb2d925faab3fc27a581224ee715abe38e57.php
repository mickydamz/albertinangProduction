
<?php $__env->startSection('content'); ?>

<style>
body { background: #f5f7f4; }

/* ── Alert messages ── */
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
.profile-alert--success {
    background: #e6f4ea;
    border: 1px solid #c8e6c9;
    color: #2e7d32;
}
.profile-alert--error {
    background: #ffebee;
    border: 1px solid #ffcdd2;
    color: #c62828;
}
.profile-alert ul { margin: 6px 0 0 0; padding-left: 18px; }
.profile-alert ul li { margin-bottom: 3px; font-size: 13px; }

/* ── Summary card (single) ── */
.profile-summary {
    margin-bottom: 16px;
}
.profile-summary-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    max-width: 280px;
    transition: all .22s;
}
.profile-summary-card:hover {
    border-color: var(--border2);
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}
.profile-summary-card__icon {
    width: 48px; height: 48px;
    background: var(--g50);
    border: 1px solid var(--border2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.profile-summary-card__icon i { font-size: 17px; color: var(--g600); }
.profile-summary-card__info {}
.profile-summary-card__num {
    font-family: var(--font-head);
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
    margin-bottom: 3px;
}
.profile-summary-card__label {
    font-size: 12px;
    color: var(--ink3);
    font-weight: 500;
}

/* ── Section block ── */
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

/* ── Form ── */
.profile-form { display: flex; flex-direction: column; gap: 16px; }
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .05px;
}
.form-group label span.req { color: var(--g600); margin-left: 2px; }
.form-control {
    padding: 11px 14px;
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
.form-control:hover {
    border-color: var(--border2);
}
.form-control:focus {
    border-color: var(--g500);
    box-shadow: 0 0 0 3px rgba(90,171,31,.12);
}
.form-control::placeholder { color: var(--ink3); }
textarea.form-control { resize: vertical; min-height: 90px; }
.form-control[readonly] {
    background: var(--surf2);
    color: var(--ink3);
    cursor: not-allowed;
}

/* ── Form hint ── */
.form-hint {
    font-size: 11.5px;
    color: var(--ink3);
    margin-top: 2px;
}

/* ── Form actions ── */
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
.profile-btn--primary {
    background: var(--g500); color: #fff;
}
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
    border-color: var(--g400);
    color: var(--g600);
    background: var(--g50);
}
.profile-btn--danger {
    background: var(--surface);
    color: #c62828;
    border: 1.5px solid #ffcdd2;
}
.profile-btn--danger:hover {
    background: #ffebee;
    border-color: #ef9a9a;
}

/* ── Settings row ── */
.settings-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ── Affiliate copy button (icon-only SVG on mobile) ── */
.copy-btn__svg { display: none; }

/* ── Member since badge ── */
.member-badge {
    margin-left: auto;
    font-size: 12px;
    color: var(--ink3);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}
.member-badge i { font-size: 11px; color: var(--g400); }

/* ── Responsive ── */
@media (max-width: 640px) {
    .form-row { grid-template-columns: 1fr; }
    .profile-actions { flex-direction: column; }
    .profile-btn { width: 100%; justify-content: center; }
    .settings-row { flex-direction: column; }
    .profile-summary-card { max-width: 100%; }
    .member-badge { display: none; }

    /* Copy button becomes a compact square icon so the code field keeps its width */
    .copy-btn {
        width: 44px !important;
        height: 44px;
        padding: 10px 12px !important;
        justify-content: center;
        flex-shrink: 0;
    }
    .copy-btn .copy-btn__label,
    .copy-btn i.fa-copy { display: none; }
    .copy-btn .copy-btn__svg { display: inline-flex; }
}
</style>

<div class="acct-wrap">

    
    <ul class="acct-breadcrumb" aria-label="Breadcrumb">
        <li><a href="<?php echo e(url('/')); ?>">Home</a></li>
        <li>My Account</li>
    </ul>

    <div class="acct-layout">

        
        <?php echo $__env->make('sims.partials.account-sidebar', ['activeNav' => 'profile'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        
        <div class="acct-main">

            
            <div class="acct-header">
                <div class="acct-header__title">
                    <i class="fas fa-user"></i>
                    My Profile
                </div>
                <div class="member-badge">
                    <i class="fas fa-calendar-alt"></i>
                    Member since <?php echo e(auth()->user()->created_at->format('M Y')); ?>

                </div>
            </div>

            
            <?php if(session('success')): ?>
                <div class="profile-alert profile-alert--success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

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
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="profile-summary">
                <div class="profile-summary-card">
                    <div class="profile-summary-card__icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="profile-summary-card__info">
                        <div class="profile-summary-card__num"><?php echo e($orderCount); ?></div>
                        <div class="profile-summary-card__label">Total Orders</div>
                    </div>
                </div>
            </div>

            
            <div class="profile-section">
                <div class="profile-section__head">
                    <i class="fas fa-id-card"></i>
                    Profile Information
                </div>
                <div class="profile-section__body">
                    <form method="POST"
                          action="<?php echo e(route('account.update')); ?>"
                          class="profile-form"
                          id="profileForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>

                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">
                                    Full Name <span class="req">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('name', auth()->user()->name)); ?>"
                                       placeholder="Your full name"
                                       required
                                       autocomplete="name">
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    Email Address <span class="req">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('email', auth()->user()->email)); ?>"
                                       placeholder="you@example.com"
                                       required
                                       autocomplete="email">
                            </div>
                        </div>

                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone_no">Phone Number</label>
                                <input type="tel"
                                       id="phone_no"
                                       name="phone_no"
                                       class="form-control <?php $__errorArgs = ['phone_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('phone_no', auth()->user()->phone_no)); ?>"
                                       placeholder="+2348064066170"
                                       autocomplete="tel">
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date"
                                       id="date_of_birth"
                                       name="date_of_birth"
                                       class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('date_of_birth', auth()->user()->date_of_birth ? \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('Y-m-d') : '')); ?>"
                                       autocomplete="bday">
                            </div>
                        </div>

                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       class="form-control <?php $__errorArgs = ['city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('city', auth()->user()->city)); ?>"
                                       placeholder="Your city"
                                       autocomplete="address-level2">
                            </div>
                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input type="text"
                                       id="postal_code"
                                       name="postal_code"
                                       class="form-control <?php $__errorArgs = ['postal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('postal_code', auth()->user()->postal_code)); ?>"
                                       placeholder="e.g. 100001"
                                       autocomplete="postal-code">
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label for="shipping_address">Billing Address</label>
                            <textarea id="shipping_address"
                                      name="shipping_address"
                                      class="form-control <?php $__errorArgs = ['shipping_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      placeholder="Enter your billing address"
                                      autocomplete="street-address"><?php echo e(old('shipping_address', auth()->user()->shipping_address)); ?></textarea>
                            <span class="form-hint">This billing address appears on your invoices.</span>
                        </div>

                        
                        <div class="form-group">
                            <label for="state">state <span class="form-hint" style="font-weight:400;">(state / region)</span></label>
                            <input type="text"
                                   id="state"
                                   name="state"
                                   class="form-control <?php $__errorArgs = ['state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('state', auth()->user()->state)); ?>"
                                   placeholder="e.g. Lagos, Nigeria">
                        </div>

                        
                        <div class="profile-actions">
                            <button type="submit" class="profile-btn profile-btn--primary">
                                <i class="fas fa-save" style="font-size:11px;"></i>
                                Update Profile
                            </button>
                            <a href="<?php echo e(route('account.index')); ?>"
                               class="profile-btn profile-btn--secondary">
                                <i class="fas fa-times" style="font-size:11px;"></i>
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            
            <div class="profile-section">
                <div class="profile-section__head">
                    <i class="fas fa-cog"></i>
                    Account Settings
                </div>
                <div class="profile-section__body">
                    <div class="settings-row">
                        <a href="<?php echo e(url('/account/change-password')); ?>"
                           class="profile-btn profile-btn--secondary">
                            <i class="fas fa-lock" style="font-size:11px;"></i>
                            Change Password
                        </a>
                       
                        <a href="<?php echo e(url('/logout')); ?>"
                           class="profile-btn profile-btn--danger"
                           onclick="event.preventDefault(); document.getElementById('logout-form-main').submit();">
                            <i class="fas fa-sign-out-alt" style="font-size:11px;"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="profile-section">
                <div class="profile-section__head">
                    <i class="fas fa-info-circle"></i>
                    Account Details
                </div>
                <div class="profile-section__body">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Account Status</label>
                            <input type="text"
                                   class="form-control"
                                   value="active"
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label>Member Since</label>
                            <input type="text"
                                   class="form-control"
                                   value="<?php echo e(auth()->user()->created_at->format('d M Y')); ?>"
                                   readonly>
                        </div>
                    </div>
                    <?php if(auth()->user()->affiliate_code): ?>
                    <div class="form-group" style="margin-top:14px;">
                        <label>Your Referral Code</label>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <input type="text"
                                   id="affiliateCode"
                                   class="form-control"
                                   value="<?php echo e(auth()->user()->affiliate_code); ?>"
                                   readonly>
                            <button type="button"
                                    class="profile-btn profile-btn--secondary copy-btn"
                                    style="white-space:nowrap; flex-shrink:0;"
                                    aria-label="Copy referral code"
                                    onclick="copyAffiliateCode()">
                                <i class="fas fa-copy" style="font-size:11px;"></i>
                                <span class="copy-btn__label">Copy</span>
                                <svg class="copy-btn__svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                </svg>
                            </button>
                        </div>
                        <span class="form-hint">Share this code to earn referral rewards.</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="logout-form-main" action="<?php echo e(url('/logout')); ?>" method="POST" style="display:none;">
    <?php echo csrf_field(); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Sidebar keyboard nav ── */
    const sidebarLinks = document.querySelectorAll('.profile-sidebar a');
    sidebarLinks.forEach((link, i) => {
        link.addEventListener('keydown', e => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                (sidebarLinks[i + 1] || sidebarLinks[0]).focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                (sidebarLinks[i - 1] || sidebarLinks[sidebarLinks.length - 1]).focus();
            }
        });
    });

    /* ── Phone formatting (Nigerian numbers) ── */
    const phoneInput = document.getElementById('phone_no');
    if (phoneInput) {
        phoneInput.addEventListener('input', e => {
            let value = e.target.value.replace(/[^\d+]/g, '');
            if (!value.startsWith('+')) {
                if (value.startsWith('234')) {
                    value = '+' + value;
                } else if (value.startsWith('0') && value.length > 1) {
                    value = '+234' + value.substring(1);
                }
            }
            e.target.value = value;
        });
    }

    /* ── Client-side form validation ── */
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', e => {
            const name  = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!name) {
                e.preventDefault();
                document.getElementById('name').focus();
                showInlineError('name', 'Full name is required.');
                return;
            }
            if (!email || !emailRegex.test(email)) {
                e.preventDefault();
                document.getElementById('email').focus();
                showInlineError('email', 'Please enter a valid email address.');
                return;
            }
        });
    }

    function showInlineError(fieldId, msg) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        field.style.borderColor = '#c62828';
        let hint = field.parentElement.querySelector('.inline-err');
        if (!hint) {
            hint = document.createElement('span');
            hint.className = 'form-hint inline-err';
            hint.style.color = '#c62828';
            field.parentElement.appendChild(hint);
        }
        hint.textContent = msg;
        setTimeout(() => {
            field.style.borderColor = '';
            hint.remove();
        }, 4000);
    }

    /* ── Auto-dismiss success alert ── */
    const successAlert = document.querySelector('.profile-alert--success');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity .4s';
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 400);
        }, 5000);
    }

});

/* ── Copy affiliate code ── */
function copyAffiliateCode() {
    const input = document.getElementById('affiliateCode');
    if (!input) return;
    navigator.clipboard.writeText(input.value).then(() => {
        const btn = input.parentElement.querySelector('button');
        const original = btn.innerHTML;
        const checkSvg = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        const isMobile = window.matchMedia('(max-width: 640px)').matches;
        btn.innerHTML = isMobile
            ? checkSvg
            : '<i class="fas fa-check" style="font-size:11px;"></i> Copied!';
        btn.style.color = 'var(--g600)';
        setTimeout(() => {
            btn.innerHTML = original;
            btn.style.color = '';
        }, 2000);
    });
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/account.blade.php ENDPATH**/ ?>