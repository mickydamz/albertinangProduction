

<?php $__env->startSection('title', 'FAQ – Albertina Nigeria'); ?>

<?php $__env->startSection('content'); ?>
<?php
use App\Models\Faq;

$dbFaqs = Faq::active()->ordered()->get();

if ($dbFaqs->isEmpty()) {
    $faqData = collect([
        (object)['category'=>'orders',   'question'=>'How do I place an order?',                 'answer'=>"Placing an order is simple:\n1. Browse and select your desired products\n2. Add items to your cart by clicking \"Add to Cart\"\n3. Review your cart and proceed to checkout\n4. Provide your delivery and contact information\n5. Choose your preferred payment method and confirm\n\nYou'll receive an order confirmation once your order is processed."],
        (object)['category'=>'orders',   'question'=>'What payment methods do you accept?',       'answer'=>"We accept a variety of secure payment methods:\n\nBank Transfer: Direct bank transfers\nCard Payments: Visa and MasterCard via secure gateway\nCash on Delivery: Available for select locations\nPayment on Collection: Pay in-store when you collect\n\nAll transactions are processed through secure, encrypted channels."],
        (object)['category'=>'shipping', 'question'=>'How long does delivery take?',               'answer'=>"Delivery times depend on your location:\n\nEnugu (same day): Orders placed before 12 PM\nSouth-East Nigeria: 1–3 business days\nLagos & other states: 3–5 business days\nRemote locations: 5–7 business days\n\nYou'll receive tracking information once your order is dispatched."],
        (object)['category'=>'shipping', 'question'=>'What are your delivery/shipping costs?',     'answer'=>"Shipping costs are calculated at checkout based on your location and order size.\n\nFree delivery on orders over ₦350,000\nEnugu local delivery: from ₦1,500\nNationwide: from ₦3,500 depending on destination\n\nExact costs are shown at checkout before you confirm your order."],
        (object)['category'=>'shipping', 'question'=>'How can I track my order?',                  'answer'=>"You can track your order by:\n1. Logging into your account dashboard\n2. Viewing your order status under \"My Orders\"\n3. Contacting us directly with your order number\n\nOur team is also available on 08064066170 for live order updates."],
        (object)['category'=>'orders',   'question'=>'Can I change or cancel my order?',           'answer'=>"Before dispatch: Orders can be cancelled or modified — contact us immediately.\nAfter dispatch: Orders cannot be cancelled but may be returned upon delivery.\n\nCall us on 08064066170 or email Info@Albertinang.com with your order number for fastest resolution."],
        (object)['category'=>'returns',  'question'=>'What is your return policy?',               'answer'=>"We offer a straightforward return process:\n\nItems must be unused and in original packaging\nReturn requests must be initiated within 7 days of delivery\nDefective or damaged products are eligible for free replacement\nItems must be accompanied by proof of purchase\n\nContact us at Info@Albertinang.com or call 08064066170 to initiate a return."],
        (object)['category'=>'orders',   'question'=>'Do products come with a warranty?',          'answer'=>"Yes — all our products come with manufacturer warranties:\n\nAir Conditioners: 1–2 year warranty\nTelevisions: 1 year warranty\nWashing Machines & Fridges: 1–2 year warranty\nGenerators & Inverters: 6 months – 1 year\n\nWarranty terms are stated on each product page. For warranty claims, contact us with your order details."],
    ]);
} else {
    $faqData = $dbFaqs;
}

$grouped    = $faqData->groupBy('category');
$categories = $grouped->keys();

$catMeta = [
    'orders'   => ['icon' => 'fa-shopping-bag',  'label' => 'Orders & Payment',    'desc' => 'Ordering and payments'],
    'shipping' => ['icon' => 'fa-truck',          'label' => 'Shipping & Delivery', 'desc' => 'Shipping and tracking'],
    'returns'  => ['icon' => 'fa-undo',           'label' => 'Returns & Refunds',   'desc' => 'Return policies'],
    'products' => ['icon' => 'fa-box',            'label' => 'Products',            'desc' => 'Product questions'],
    'account'  => ['icon' => 'fa-user',           'label' => 'My Account',          'desc' => 'Account help'],
    'general'  => ['icon' => 'fa-circle-question','label' => 'General',             'desc' => 'General questions'],
    'payment'  => ['icon' => 'fa-credit-card',    'label' => 'Payments',            'desc' => 'Payment methods'],
    'warranty' => ['icon' => 'fa-shield-alt',     'label' => 'Warranty',            'desc' => 'Warranty info'],
];
?>

<style>
.faq-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 28px 20px 60px;
}

/* ===== HERO ===== */
.faq-hero {
    background: linear-gradient(135deg, var(--g700) 0%, var(--g600) 60%, var(--g500) 100%);
    border-radius: 0;
    padding: 52px 48px;
    color: #fff;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    text-align: center;
}
.faq-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    border-radius: 50%;
    background: rgba(171,235,115,.1);
}
.faq-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 80px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(90,171,31,.12);
}
.faq-hero h1 {
    font-family: var(--font-head);
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}
.faq-hero p {
    font-size: 14.5px;
    color: rgba(255,255,255,.75);
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.6;
    position: relative;
    z-index: 1;
}

/* ===== SEARCH ===== */
.faq-search-wrap {
    max-width: 520px;
    margin: 0 auto 28px;
    position: relative;
}
.faq-search-wrap input {
    width: 100%;
    padding: 12px 44px 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    font-size: 14px;
    font-family: var(--font-body);
    color: var(--ink);
    background: var(--surface);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.faq-search-wrap input:focus {
    border-color: var(--g400);
    box-shadow: 0 0 0 3px rgba(90,171,31,.1);
}
.faq-search-wrap input::placeholder { color: var(--ink3); }
.faq-search-icon {
    position: absolute;
    right: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--g500);
    font-size: 14px;
    pointer-events: none;
}

/* ===== CATEGORIES ===== */
.faq-cats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
.faq-cat {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 0;
    padding: 18px 14px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
}
.faq-cat:hover {
    border-color: var(--g400);
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}
.faq-cat.active {
    border-color: var(--g500);
    background: var(--g50);
}
.faq-cat__icon {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: var(--g50);
    border: 1px solid var(--border2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
    transition: all .2s;
}
.faq-cat__icon i { font-size: 16px; color: var(--g600); }
.faq-cat.active .faq-cat__icon,
.faq-cat:hover .faq-cat__icon {
    background: var(--g500);
    border-color: var(--g500);
}
.faq-cat.active .faq-cat__icon i,
.faq-cat:hover .faq-cat__icon i { color: #fff; }
.faq-cat h3 {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 3px;
}
.faq-cat p { font-size: 11.5px; color: var(--ink3); }

/* ===== ACCORDION ===== */
.faq-list {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 0;
    overflow: hidden;
}
.faq-item { border-bottom: 1px solid var(--border); }
.faq-item:last-child { border-bottom: none; }
.faq-item.active { background: var(--g50); }

.faq-question {
    width: 100%;
    padding: 18px 22px;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    font-size: 14.5px;
    font-weight: 700;
    color: var(--ink);
    font-family: var(--font-body);
    transition: color .2s;
    outline: none;
}
.faq-question:hover { color: var(--g600); }
.faq-item.active .faq-question { color: var(--g600); }
.faq-question:focus-visible {
    box-shadow: inset 0 0 0 2px var(--g400);
}

.faq-q-icon {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--g50);
    border: 1.5px solid var(--border2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: all .25s;
}
.faq-q-icon i { font-size: 10px; color: var(--g600); transition: transform .25s; }
.faq-item.active .faq-q-icon {
    background: var(--g500);
    border-color: var(--g500);
}
.faq-item.active .faq-q-icon i {
    color: #fff;
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height .35s ease;
}
.faq-item.active .faq-answer { max-height: 600px; }

.faq-answer-body {
    padding: 0 22px 20px 22px;
    font-size: 13.5px;
    color: var(--ink3);
    line-height: 1.7;
}
.faq-answer-body p { margin-bottom: 10px; }
.faq-answer-body p:last-child { margin-bottom: 0; }
.faq-answer-body ul,
.faq-answer-body ol {
    padding-left: 18px;
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.faq-answer-body li { font-size: 13.5px; color: var(--ink3); }
.faq-answer-body strong { color: var(--ink2); }
.faq-answer-body a { color: var(--g600); font-weight: 500; }
.faq-answer-body a:hover { text-decoration: underline; }

/* No results */
.faq-no-results {
    padding: 32px;
    text-align: center;
    color: var(--ink3);
    font-size: 13.5px;
    display: none;
}
.faq-no-results i { font-size: 28px; color: var(--border2); margin-bottom: 10px; display: block; }

/* ===== CTA ===== */
.faq-cta {
    margin-top: 24px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-top: 3px solid var(--g500);
    border-radius: var(--radius-lg);
    padding: 36px 32px;
    text-align: center;
}
.faq-cta h3 {
    font-family: var(--font-head);
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 8px;
}
.faq-cta p {
    font-size: 13.5px;
    color: var(--ink3);
    margin-bottom: 20px;
}
.faq-cta-btns {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
.faq-btn-primary {
    background: var(--g500);
    color: #fff;
    padding: 11px 24px;
    border-radius: var(--radius);
    font-size: 13.5px;
    font-weight: 700;
    font-family: var(--font-body);
    border: none;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
}
.faq-btn-primary:hover {
    background: var(--g600);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(61,128,18,.25);
}
.faq-btn-secondary {
    background: var(--g50);
    color: var(--g700);
    padding: 11px 24px;
    border-radius: var(--radius);
    font-size: 13.5px;
    font-weight: 700;
    font-family: var(--font-body);
    border: 2px solid var(--g400);
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
}
.faq-btn-secondary:hover {
    background: var(--g100);
    transform: translateY(-2px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
    .faq-cats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .faq-wrap { padding: 16px 14px 48px; }
    .faq-hero { padding: 32px 20px; }
    .faq-hero h1 { font-size: 1.5rem; }
    .faq-cats { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .faq-question { padding: 16px; font-size: 13px; }
    .faq-answer-body { padding: 0 16px 16px; }
    .faq-cta { padding: 24px 18px; }
    .faq-cta-btns { flex-direction: column; align-items: stretch; }
    .faq-btn-primary, .faq-btn-secondary { justify-content: center; }
}
</style>

<div class="faq-wrap">

    
    <div class="faq-hero">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about shopping with Albertina Nigeria. We're here to make your experience as smooth as possible.</p>
    </div>

    
    <div class="faq-search-wrap">
        <input type="text" id="faqSearch" placeholder="Search for answers…" autocomplete="off">
        <i class="fas fa-search faq-search-icon"></i>
    </div>

    
    <div class="faq-cats">
        <div class="faq-cat active" data-cat="all">
            <div class="faq-cat__icon"><i class="fas fa-th-large"></i></div>
            <h3>All Questions</h3>
            <p>Browse all FAQs</p>
        </div>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $m = $catMeta[$cat] ?? ['icon' => 'fa-circle-question', 'label' => ucwords(str_replace(['-','_'], ' ', $cat)), 'desc' => '']; ?>
        <div class="faq-cat" data-cat="<?php echo e($cat); ?>">
            <div class="faq-cat__icon"><i class="fas <?php echo e($m['icon']); ?>"></i></div>
            <h3><?php echo e($m['label']); ?></h3>
            <?php if($m['desc']): ?><p><?php echo e($m['desc']); ?></p><?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="faq-list" id="faqList">

        <?php $__currentLoopData = $faqData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="faq-item" data-cat="<?php echo e($faq->category); ?>">
            <button class="faq-question" aria-expanded="false">
                <span><?php echo e($faq->question); ?></span>
                <div class="faq-q-icon"><i class="fas fa-plus"></i></div>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-body">
                    <?php echo nl2br(e($faq->answer)); ?>

                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="faq-no-results" id="faqNoResults">
            <i class="fas fa-search"></i>
            No results found. Try a different search term or browse by category.
        </div>

    </div>

    
    <div class="faq-cta">
        <h3>Still Need Help?</h3>
        <p>Can't find the answer you're looking for? Our team is always happy to help.</p>
        <div class="faq-cta-btns">
            <a href="<?php echo e(route('contact')); ?>" class="faq-btn-primary">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
            <a href="tel:08064066170" class="faq-btn-secondary">
                <i class="fas fa-phone"></i> Call Us Now
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const items    = document.querySelectorAll('.faq-item');
    const cats     = document.querySelectorAll('.faq-cat');
    const search   = document.getElementById('faqSearch');
    const noResult = document.getElementById('faqNoResults');

    /* ── Accordion ── */
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item   = btn.parentElement;
            const isOpen = item.classList.contains('active');
            items.forEach(i => { i.classList.remove('active'); i.querySelector('.faq-question').setAttribute('aria-expanded','false'); });
            if (!isOpen) { item.classList.add('active'); btn.setAttribute('aria-expanded','true'); }
        });
    });

    /* ── Category filter ── */
    cats.forEach(cat => {
        cat.addEventListener('click', () => {
            cats.forEach(c => c.classList.remove('active'));
            cat.classList.add('active');
            search.value = '';
            filterByCat(cat.dataset.cat);
        });
    });

    function filterByCat(cat) {
        let visible = 0;
        items.forEach(item => {
            const show = cat === 'all' || item.dataset.cat === cat;
            item.style.display = show ? '' : 'none';
            if (!show) { item.classList.remove('active'); item.querySelector('.faq-question').setAttribute('aria-expanded','false'); }
            if (show) visible++;
        });
        noResult.style.display = visible === 0 ? 'block' : 'none';
    }

    /* ── Search ── */
    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const q = search.value.toLowerCase().trim();
            cats.forEach(c => c.classList.remove('active'));
            document.querySelector('.faq-cat[data-cat="all"]').classList.add('active');
            let visible = 0;
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const show = !q || text.includes(q);
                item.style.display = show ? '' : 'none';
                if (!show) { item.classList.remove('active'); item.querySelector('.faq-question').setAttribute('aria-expanded','false'); }
                if (show) visible++;
            });
            noResult.style.display = visible === 0 ? 'block' : 'none';
        }, 280);
    });

});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/faq.blade.php ENDPATH**/ ?>