

<?php $__env->startSection('title', 'Contact Us – Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\Setting;
$cHeroTitle    = Setting::get('contact_hero_title',    'Contact Albertina Nigeria');
$cHeroSub      = Setting::get('contact_hero_subtitle', "We're here to help with all your electronics and appliance needs. Reach out and our team will get back to you promptly.");
$cEmail        = Setting::get('contact_email',         'Info@Albertinang.com');
$cPhone1       = Setting::get('contact_phone_1',       '+234 806 406 6170');
$cPhone2       = Setting::get('contact_phone_2',       '+234 703 768 0738');
$cAddr1        = Setting::get('contact_address_1',     "17-18 Zik's Avenue, Uwani, Enugu");
$cAddr2        = Setting::get('contact_address_2',     '26 Lawanson Road, Surulere, Lagos');
$cAddr3        = Setting::get('contact_address_3',     'Enugu-Onitsha Expressway, Awka');
$cHoursWd      = Setting::get('contact_hours_weekday', 'Mon – Sat: 8:00 AM – 6:00 PM');
$cHoursWe      = Setting::get('contact_hours_weekend', 'Sunday: 10:00 AM – 4:00 PM');
?>

<style>
.contact-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 28px 20px 60px;
}

/* ===== PAGE HEADER ===== */
.contact-hero {
    background: linear-gradient(135deg, var(--g700) 0%, var(--g600) 60%, var(--g500) 100%);
    border-radius: 0;
    padding: 52px 48px;
    color: #fff;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.contact-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    border-radius: 50%;
    background: rgba(171,235,115,.1);
}
.contact-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(90,171,31,.12);
}
.contact-hero h1 {
    font-family: var(--font-head);
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}
.contact-hero p {
    font-size: 14.5px;
    color: rgba(255,255,255,.75);
    max-width: 520px;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

/* ===== GRID ===== */
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 20px;
    align-items: start;
}

/* ===== CARD ===== */
.contact-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 0;
    padding: 32px 28px;
}
.contact-card h2 {
    font-family: var(--font-head);
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.contact-card h2 i { color: var(--g500); font-size: 14px; }
.contact-card > p {
    font-size: 13px;
    color: var(--ink3);
    margin-bottom: 22px;
    line-height: 1.5;
}

/* ===== FORM ===== */
.c-form { display: flex; flex-direction: column; gap: 16px; }
.c-field { display: flex; flex-direction: column; gap: 5px; }
.c-field label {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--ink2);
}
.c-field input,
.c-field textarea {
    padding: 10px 13px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    font-size: 13.5px;
    color: var(--ink);
    background: var(--surface);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-family: var(--font-body);
}
.c-field input:focus,
.c-field textarea:focus {
    border-color: var(--g400);
    box-shadow: 0 0 0 3px rgba(90,171,31,.1);
}
.c-field input::placeholder,
.c-field textarea::placeholder { color: var(--ink3); }
.c-field textarea {
    min-height: 110px;
    resize: vertical;
}
.c-submit {
    background: var(--g500);
    color: #fff;
    padding: 12px 28px;
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    align-self: flex-start;
    font-family: var(--font-body);
}
.c-submit:hover {
    background: var(--g600);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(61,128,18,.25);
}

/* Flash messages */
.c-success {
    background: var(--g50);
    border: 1px solid var(--g200);
    color: var(--g700);
    padding: 10px 14px;
    border-radius: var(--radius);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.c-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #7f1d1d;
    padding: 10px 14px;
    border-radius: var(--radius);
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ===== INFO SIDEBAR ===== */
.info-items { display: flex; flex-direction: column; gap: 16px; }
.info-item {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 14px;
    background: var(--surf2);
    border: 1px solid var(--border);
    border-radius: 0;
    transition: border-color .2s;
}
.info-item:hover {
    border-color: var(--border2);
    box-shadow: var(--shadow-sm);
}
.info-item__icon {
    width: 38px; height: 38px;
    border-radius: var(--radius);
    background: var(--g50);
    border: 1px solid var(--border2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.info-item__icon i { font-size: 14px; color: var(--g600); }
.info-item__body h3 {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 4px;
}
.info-item__body p,
.info-item__body a {
    font-size: 13px;
    color: var(--ink3);
    line-height: 1.5;
    display: block;
}
.info-item__body a {
    color: var(--g600);
    font-weight: 500;
    transition: color .15s;
}
.info-item__body a:hover { color: var(--g700); text-decoration: underline; }

/* ===== MAP ===== */
.map-wrap {
    margin-top: 20px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--border);
    background: var(--surf2);
}
.map-placeholder {
    height: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: var(--ink3);
    font-size: 13px;
}
.map-placeholder i { font-size: 32px; color: var(--g400); }

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
    .contact-grid { grid-template-columns: 1fr; }
    .contact-hero { padding: 36px 28px; }
    .contact-hero h1 { font-size: 1.7rem; }
}
@media (max-width: 600px) {
    .contact-wrap { padding: 16px 14px 48px; }
    .contact-hero { padding: 28px 20px; }
    .contact-hero h1 { font-size: 1.4rem; }
    .contact-card { padding: 22px 18px; }
    .c-submit { align-self: stretch; justify-content: center; }
}
</style>

<div class="contact-wrap">

    
    <div class="contact-hero">
        <h1><?php echo e($cHeroTitle); ?></h1>
        <p><?php echo e($cHeroSub); ?></p>
    </div>

    <div class="contact-grid">

        
        <div class="contact-card">
            <h2><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
            <p>Have a question or need assistance? Fill out the form below.</p>

            <?php if(session('success')): ?>
                <div class="c-success" style="margin-bottom:16px;">
                    <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="c-error" style="margin-bottom:16px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <form class="c-form" action="<?php echo e(route('contact.store')); ?>" method="POST" id="contactForm">
                <?php echo csrf_field(); ?>

                
                <div style="position:absolute; left:-9999px;" aria-hidden="true">
                    <label for="website">Leave this field blank</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="c-field">
                    <label for="name">Your Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="name" name="name"
                           value="<?php echo e(old('name')); ?>"
                           placeholder="Enter your full name" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="c-field">
                    <label for="email">Email Address <span style="color:#dc2626;">*</span></label>
                    <input type="email" id="email" name="email"
                           value="<?php echo e(old('email')); ?>"
                           placeholder="Enter your email address" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="c-field">
                    <label for="phone">Phone Number <span style="color:var(--ink3);font-weight:400;">(optional)</span></label>
                    <input type="tel" id="phone" name="phone"
                           value="<?php echo e(old('phone')); ?>"
                           placeholder="e.g. 08012345678">
                </div>
                <div class="c-field">
                    <label for="userMessage">Your Message <span style="color:#dc2626;">*</span></label>
                    <textarea id="userMessage" name="userMessage"
                              placeholder="How can we help you?" required><?php echo e(old('userMessage')); ?></textarea>
                    <?php $__errorArgs = ['userMessage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="c-field">
                    <div class="cf-turnstile" data-sitekey="<?php echo e(config('services.turnstile.site_key')); ?>"></div>
                    <?php $__errorArgs = ['cf-turnstile-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span style="font-size:12px;color:#dc2626;"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" class="c-submit">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        
        <div style="display:flex;flex-direction:column;gap:20px;">

            <div class="contact-card">
                <h2><i class="fas fa-address-book"></i> Contact Information</h2>
                <p>Reach us directly or visit one of our showrooms.</p>

                <div class="info-items">
                    <?php if($cEmail): ?>
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fas fa-envelope"></i></div>
                        <div class="info-item__body">
                            <h3>Email</h3>
                            <a href="mailto:<?php echo e($cEmail); ?>"><?php echo e($cEmail); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($cPhone1 || $cPhone2): ?>
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-item__body">
                            <h3>Phone</h3>
                            <?php if($cPhone1): ?><a href="tel:<?php echo e(preg_replace('/\s+/', '', $cPhone1)); ?>"><?php echo e($cPhone1); ?></a><?php endif; ?>
                            <?php if($cPhone2): ?><a href="tel:<?php echo e(preg_replace('/\s+/', '', $cPhone2)); ?>"><?php echo e($cPhone2); ?></a><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($cAddr1 || $cAddr2 || $cAddr3): ?>
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-item__body">
                            <h3>Showrooms</h3>
                            <?php if($cAddr1): ?><p><?php echo e($cAddr1); ?></p><?php endif; ?>
                            <?php if($cAddr2): ?><p><?php echo e($cAddr2); ?></p><?php endif; ?>
                            <?php if($cAddr3): ?><p><?php echo e($cAddr3); ?></p><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($cHoursWd || $cHoursWe): ?>
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fas fa-clock"></i></div>
                        <div class="info-item__body">
                            <h3>Working Hours</h3>
                            <?php if($cHoursWd): ?><p><?php echo e($cHoursWd); ?></p><?php endif; ?>
                            <?php if($cHoursWe): ?><p><?php echo e($cHoursWe); ?></p><?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="contact-card" style="padding:0;overflow:hidden;">
                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Map coming soon</span>
                    <span style="font-size:12px;">17-18 Zik's Avenue, Uwani, Enugu</span>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
document.getElementById('contactForm')?.addEventListener('submit', function (e) {
    const name        = this.querySelector('#name').value.trim();
    const email       = this.querySelector('#email').value.trim();
    const userMessage = this.querySelector('#userMessage').value.trim();
    if (!name || !email || !userMessage) {
        e.preventDefault();
        window.showNotify && window.showNotify('Please fill out all required fields.', 'warning');
        return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        e.preventDefault();
        window.showNotify && window.showNotify('Please enter a valid email address.', 'warning');
        return;
    }
    const turnstileResponse = this.querySelector('[name="cf-turnstile-response"]');
    if (!turnstileResponse || !turnstileResponse.value) {
        e.preventDefault();
        window.showNotify && window.showNotify('Please complete the verification check.', 'warning');
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/contact.blade.php ENDPATH**/ ?>