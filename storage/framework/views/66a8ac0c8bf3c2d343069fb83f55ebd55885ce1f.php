<?php $__env->startSection('title', 'Albertina Nigeria – Premium Electronics & Home Appliances'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ═══════════════════════════════════════════════════════
   HERO SLIDER
═══════════════════════════════════════════════════════ */
.hero-section {
    padding: 0;
    max-width: 100%;
    margin: 0 auto;
}
.slider-outer {
    position: relative;
    border-radius: 0;
    overflow: hidden;
    touch-action: pan-y;
}
.slides-track {
    display: grid;
    will-change: opacity;
}
.slide {
    grid-column: 1;
    grid-row: 1;
    min-width: 100%;
    background: #f3f4f6;
    position: relative;
    opacity: 0;
    transition: opacity .7s ease-in-out;
    pointer-events: none;
}
.slide.is-active {
    opacity: 1;
    pointer-events: auto;
}
.slide img {
    display: block;
    width: 100%;
    height: auto;
}
.slider-dots-wrap {
    position: absolute;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 10;
}
.hero-dot {
    width: 8px; height: 8px;
    border-radius: 4px;
    background: rgba(90,171,31,.45);
    cursor: pointer;
    transition: all .3s cubic-bezier(.4,0,.2,1);
    border: 1.5px solid rgba(255,255,255,.5); padding: 0;
}
.hero-dot.active { width: 28px; background: #5aab1f; border-color: #5aab1f; }
.slider-btn {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    z-index: 10;
    background: rgba(0,0,0,.3);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1.5px solid rgba(255,255,255,.25);
    color: #fff;
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: background .2s, transform .2s;
}
.slider-btn:hover { background: rgba(0,0,0,.5); transform: translateY(-50%) scale(1.05); }
.slider-btn--prev { left: 20px; }
.slider-btn--next { right: 20px; }

@media (min-width: 1025px) {
    .hero-section { max-width: 1240px; margin: 16px auto 0; padding: 0 20px; }
    .slider-outer { border-radius: 0; }
}

/* ═══════════════════════════════════════════════════════
   FLASH
═══════════════════════════════════════════════════════ */
.flash-wrap { max-width: 1240px; margin: 14px auto 0; padding: 0 20px; }
.alert-flash {
    padding: 12px 16px; border-radius: 8px; margin-bottom: 8px;
    font-size: 13.5px; display: flex; align-items: center; gap: 10px;
}
.alert-flash--success { background: var(--g50); border-left: 3px solid var(--g500); color: var(--g700); }
.alert-flash--error   { background: #fef2f2; border-left: 3px solid #dc2626; color: #7f1d1d; }
.alert-flash--warning { background: #fffbeb; border-left: 3px solid #f59e0b; color: #78350f; }
.alert-flash--info    { background: #eff6ff; border-left: 3px solid #3b82f6; color: #1e3a5f; }

/* ═══════════════════════════════════════════════════════
   MAIN WRAP
═══════════════════════════════════════════════════════ */
.main-wrap {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 20px 64px;
}

/* ═══════════════════════════════════════════════════════
   TRUST / USP BAR
═══════════════════════════════════════════════════════ */
.usp-bar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 0;
    margin-top: 20px;
    overflow: hidden;
}
.usp-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 15px 16px;
    border-right: 1px solid var(--border);
    transition: background .15s;
}
.usp-item:last-child { border-right: none; }
.usp-item:hover { background: var(--g50); }
.usp-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    background: var(--g50);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--g600);
    font-size: 16px;
    transition: background .15s, color .15s;
}
.usp-item:hover .usp-icon { background: var(--g500); color: #fff; }
.usp-text { min-width: 0; }
.usp-label { font-size: 13px; font-weight: 700; color: var(--ink); white-space: nowrap; }
.usp-sub   { font-size: 11.5px; color: var(--ink4); white-space: nowrap; }

/* ═══════════════════════════════════════════════════════
   SECTION BLOCKS
═══════════════════════════════════════════════════════ */
.section-block { margin-top: 40px; }

.sec-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}
.sec-title {
    font-family: var(--font-head);
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.3px;
    position: relative;
    padding-left: 14px;
}
.sec-title::before {
    content: '';
    position: absolute;
    left: 0; top: 10%; bottom: 10%;
    width: 4px;
    background: var(--g500);
    border-radius: 2px;
}
.sec-right {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sec-view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    border: 1.5px solid var(--g500);
    border-radius: 6px;
    color: var(--g600);
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.sec-view-all:hover { background: var(--g500); color: #fff; }
.sec-view-all i { font-size: 9px; }
.sec-arrows { display: flex; gap: 6px; }
.sarrow {
    width: 36px; height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--border2);
    background: #fff;
    color: var(--ink3);
    font-size: 12px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all .15s;
    flex-shrink: 0;
}
.sarrow:hover {
    background: var(--ink);
    border-color: var(--ink);
    color: #fff;
}

/* ═══════════════════════════════════════════════════════
   SCROLL DOTS
═══════════════════════════════════════════════════════ */
.scroll-dots {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 12px;
}
.scroll-dot {
    width: 6px; height: 6px;
    border-radius: 3px;
    background: var(--border2);
    border: none;
    cursor: pointer;
    transition: all .25s;
    padding: 0;
}
.scroll-dot.active { width: 20px; background: var(--g500); }

/* ═══════════════════════════════════════════════════════
   BRANDS ROW
═══════════════════════════════════════════════════════ */
.brands-row {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 4px 0 10px;
    scrollbar-width: none;
    cursor: grab;
}
.brands-row::-webkit-scrollbar { display: none; }
.brands-row:active { cursor: grabbing; }
.brand-card {
    flex-shrink: 0;
    width: 120px; height: 68px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    padding: 12px;
    cursor: pointer;
    transition: border-color .2s;
    overflow: hidden;
}
.brand-card:hover {
    border-color: var(--g400);
}
.brand-card img {
    max-width: 100%; max-height: 100%;
    object-fit: contain;
    filter: grayscale(20%);
    transition: filter .2s;
}
.brand-card:hover img { filter: grayscale(0%); }
.brand-text-fallback {
    font-size: 12.5px;
    font-weight: 800;
    color: var(--ink);
    text-align: center;
    letter-spacing: -.2px;
}

/* ═══════════════════════════════════════════════════════
   PRODUCT ROW
═══════════════════════════════════════════════════════ */
.products-row {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    padding: 4px 2px 12px;
    scrollbar-width: none;
    flex-wrap: nowrap;
    cursor: grab;
}
.products-row::-webkit-scrollbar { display: none; }
.products-row:active { cursor: grabbing; }

/* ═══════════════════════════════════════════════════════
   PRODUCT CARD — Argos-level
═══════════════════════════════════════════════════════ */
.card-hry {
    flex-shrink: 0;
    width: 218px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
    transition: border-color .2s;
    cursor: pointer;
}
.card-hry:hover {
    border-color: var(--border2);
}

.image-15k {
    height: 210px;
    background: #f8f9fa;
    display: flex; align-items: center; justify-content: center;
    padding: 0;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
    border-bottom: 1px solid var(--border);
}
.image-15k img {
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform .3s cubic-bezier(.25,0,.25,1);
}
.card-hry:hover .image-15k img { transform: scale(1.04); }

/* Discount pill */
.disc-pill {
    position: absolute;
    top: 10px; left: 10px;
    background: #dc2626;
    color: #fff;
    font-size: 10.5px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 5px;
    letter-spacing: .2px;
    z-index: 2;
}


/* Info section */
.info-oa4 {
    padding: 13px 13px 56px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    flex-grow: 1;
}
.title-71g {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 36px;
    margin-bottom: 3px;
}
.title-71g a { color: inherit; text-decoration: none; }
.title-71g a:hover { color: var(--g600); }

/* Stars */
.rat-z9w {
    display: flex; align-items: center; gap: 1px;
    margin-top: 2px;
}
.rat-z9w i { color: #f5a623; font-size: 11px; }
.rating-dmm { font-size: 10.5px; color: var(--ink4); margin-left: 3px; }

/* Price row */
.price-row {
    display: flex;
    align-items: baseline;
    gap: 7px;
    flex-wrap: wrap;
    margin-top: 2px;
}
.product-gfx {
    font-family: var(--font-head);
    font-size: 18px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.4px;
    line-height: 1;
}
.product-original-price {
    font-size: 12px;
    color: var(--ink4);
    text-decoration: line-through;
    line-height: 1;
}

/* ATC button — always at card bottom */
.overlay-d8d {
    position: absolute;
    bottom: 0; left: 0;
    width: 100%;
    background: var(--g500);
    color: #fff;
    border: none;
    padding: 13px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-family: var(--font-body);
    font-weight: 700;
    font-size: 13px;
    letter-spacing: .1px;
    cursor: pointer;
    transition: background .15s;
    z-index: 5;
}
.overlay-d8d:hover { background: var(--g700); }
.overlay-d8d:disabled {
    background: #e5e7eb;
    color: var(--ink4);
    cursor: not-allowed;
}

/* ═══════════════════════════════════════════════════════
   DEAL / CONVENIENCE ROW
═══════════════════════════════════════════════════════ */
.deal-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}
.deal-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 26px 16px;
    text-align: center;
    transition: border-color .2s;
    cursor: default;
}
.deal-card:hover {
    border-color: var(--g400);
}
.deal-icon {
    width: 54px; height: 54px;
    background: var(--g50);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    color: var(--g600);
    font-size: 22px;
    transition: background .2s, color .2s;
}
.deal-card:hover .deal-icon { background: var(--g500); color: #fff; }
.deal-label { font-size: 13.5px; font-weight: 700; color: var(--ink); margin-bottom: 4px; }
.deal-sub   { font-size: 11.5px; color: var(--ink4); line-height: 1.4; }

/* ═══════════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════════ */
@media (max-width: 1024px) {
    .deal-row  { grid-template-columns: repeat(3, 1fr); }
    .usp-bar   { grid-template-columns: repeat(3, 1fr); }
    .usp-item:nth-child(3) { border-right: none; }
    .usp-item:nth-child(4) { border-top: 1px solid var(--border); }
    .usp-item:nth-child(5) { border-top: 1px solid var(--border); border-right: none; }
}
@media (max-width: 768px) {
    .usp-bar { display: none; }
}
@media (max-width: 640px) {
    .deal-row { grid-template-columns: repeat(2, 1fr); }
    .products-row {
        gap: 10px;
        padding: 4px 0 14px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }
    .card-hry {
        width: 172px;
        scroll-snap-align: start;
    }
    .image-15k { height: 168px; }
    .main-wrap { padding: 0 14px 40px; }
    .slider-btn { display: none; }
    .sec-title { font-size: 1.05rem; }
    .product-gfx { font-size: 15px; }
}
@media (max-width: 400px) {
    .deal-row { grid-template-columns: 1fr; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success') || session('error') || session('warning') || session('info')): ?>
    <div class="flash-wrap">
        <?php $__currentLoopData = ['success','error','warning','info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($type)): ?>
                <div class="alert-flash alert-flash--<?php echo e($type); ?>">
                    <i class="fas fa-<?php echo e($type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : ($type === 'warning' ? 'exclamation-triangle' : 'info-circle'))); ?>"></i>
                    <?php echo e(session($type)); ?>

                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>


<div class="hero-section">
    <div class="slider-outer" id="heroOuter">
        <div class="slides-track" id="heroTrack">
            <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="slide">
                    <?php if($banner->image): ?>
                        <?php if($banner->link): ?>
                            <a href="<?php echo e($banner->link); ?>" target="_blank" rel="noopener noreferrer" style="display:block;line-height:0;">
                                <img src="<?php echo e(Storage::url($banner->image)); ?>"
                                     alt="<?php echo e($banner->title ?: 'Banner ' . ($index + 1)); ?>"
                                     <?php if($index === 0): ?> fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
                            </a>
                        <?php else: ?>
                            <img src="<?php echo e(Storage::url($banner->image)); ?>"
                                 alt="<?php echo e($banner->title ?: 'Banner ' . ($index + 1)); ?>"
                                 <?php if($index === 0): ?> fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="slide"><img src="/images/banner1.jpg" alt="Banner 1" fetchpriority="high"></div>
                <div class="slide"><img src="/images/banner2.jpg" alt="Banner 2" loading="lazy"></div>
                <div class="slide"><img src="/images/banner3.jpg" alt="Banner 3" loading="lazy"></div>
            <?php endif; ?>
        </div>
        <button class="slider-btn slider-btn--prev" id="heroPrev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-btn slider-btn--next" id="heroNext" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
        <div class="slider-dots-wrap" id="heroDots">
            <?php $slideCount = $banners->isNotEmpty() ? $banners->count() : 3; ?>
            <?php for($i = 0; $i < $slideCount; $i++): ?>
                <button class="hero-dot <?php echo e($i === 0 ? 'active' : ''); ?>" data-i="<?php echo e($i); ?>" aria-label="Go to slide <?php echo e($i + 1); ?>"></button>
            <?php endfor; ?>
        </div>
    </div>
</div>


<div class="main-wrap">

    
    <div class="usp-bar">
        <div class="usp-item">
            <div class="usp-icon"><i class="fas fa-truck-fast"></i></div>
            <div class="usp-text">
                <div class="usp-label">Fast Delivery</div>
                <div class="usp-sub">Same-day options</div>
            </div>
        </div>
        <div class="usp-item">
            <div class="usp-icon"><i class="fas fa-store"></i></div>
            <div class="usp-text">
                <div class="usp-label">Click &amp; Collect</div>
                <div class="usp-sub">Ready in minutes</div>
            </div>
        </div>
        <div class="usp-item">
            <div class="usp-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="usp-text">
                <div class="usp-label">Warranty Assured</div>
                <div class="usp-sub">All brands covered</div>
            </div>
        </div>
        <div class="usp-item">
            <div class="usp-icon"><i class="fas fa-rotate-left"></i></div>
            <div class="usp-text">
                <div class="usp-label">Easy Returns</div>
                <div class="usp-sub">Hassle-free policy</div>
            </div>
        </div>
        <div class="usp-item">
            <div class="usp-icon"><i class="fas fa-headset"></i></div>
            <div class="usp-text">
                <div class="usp-label">24/7 Support</div>
                <div class="usp-sub">Always available</div>
            </div>
        </div>
    </div>

    
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Popular Brands</div>
            <div class="sec-right">
                <div class="sec-arrows">
                    <button class="sarrow" data-target="brandsRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="brandsRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="brands-row" id="brandsRow">
            <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="brand-card" onclick="window.location='/search?searchTerm=<?php echo e(urlencode($brand->name)); ?>'">
                    <?php if($brand->logo): ?>
                        <img src="<?php echo e(asset('storage/' . $brand->logo)); ?>"
                             alt="<?php echo e($brand->name); ?>"
                             onerror="this.outerHTML='<span class=\'brand-text-fallback\'><?php echo e(addslashes($brand->name)); ?></span>'">
                    <?php else: ?>
                        <span class="brand-text-fallback"><?php echo e($brand->name); ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p style="color:var(--ink4);font-size:13px;">No brands available.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($products->isNotEmpty()): ?>
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Best Selling</div>
            <div class="sec-right">
                <a href="/search" class="sec-view-all">View all <i class="fas fa-arrow-right"></i></a>
                <div class="sec-arrows">
                    <button class="sarrow" data-target="bestRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="bestRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="products-row" id="bestRow">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hry" data-product-id="<?php echo e($product->id); ?>" data-requires-truck="<?php echo e($product->requires_truck ? '1' : '0'); ?>">
                <div class="image-15k">
                    <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'); ?>"
                         alt="<?php echo e($product->name); ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'">
                </div>
                <?php if($product->old_price && $product->old_price > $product->sell_price): ?>
                    <?php $pctOff = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                    <?php if($pctOff >= 5): ?><div class="disc-pill">-<?php echo e($pctOff); ?>%</div><?php endif; ?>
                <?php endif; ?>

                <div class="info-oa4">
                    <div class="rat-z9w">
                        <?php if($product->review_rating_count > 0): ?>
                            <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <span class="rating-dmm">(<?php echo e($product->review_rating_count); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="title-71g"><a href="/product/<?php echo e($product->id); ?>"><?php echo e($product->name); ?></a></h4>
                    <div class="price-row">
                        <p class="product-gfx" data-base-price-ngn="<?php echo e($product->sell_price); ?>">&#8358;<?php echo e(number_format($product->sell_price, 0)); ?></p>
                        <?php if($product->old_price): ?><p class="product-original-price" data-old-price-ngn="<?php echo e($product->old_price); ?>">&#8358;<?php echo e(number_format($product->old_price, 0)); ?></p><?php endif; ?>
                    </div>
                </div>
                <button class="overlay-d8d"><i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart</button>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="scroll-dots" id="bestDots"></div>
    </div>
    <?php endif; ?>

    
    <?php if($acs->isNotEmpty()): ?>
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Air Conditioners</div>
            <div class="sec-right">
                <a href="/category/Split%20Ac" class="sec-view-all">View all <i class="fas fa-arrow-right"></i></a>
                <div class="sec-arrows">
                    <button class="sarrow" data-target="acRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="acRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="products-row" id="acRow">
            <?php $__currentLoopData = $acs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hry" data-product-id="<?php echo e($product->id); ?>" data-requires-truck="<?php echo e($product->requires_truck ? '1' : '0'); ?>">
                <div class="image-15k">
                    <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'); ?>"
                         alt="<?php echo e($product->name); ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'">
                </div>
                <?php if($product->old_price && $product->old_price > $product->sell_price): ?>
                    <?php $pctOff = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                    <?php if($pctOff >= 5): ?><div class="disc-pill">-<?php echo e($pctOff); ?>%</div><?php endif; ?>
                <?php endif; ?>

                <div class="info-oa4">
                    <div class="rat-z9w">
                        <?php if($product->review_rating_count > 0): ?>
                            <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <span class="rating-dmm">(<?php echo e($product->review_rating_count); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="title-71g"><a href="/product/<?php echo e($product->id); ?>"><?php echo e($product->name); ?></a></h4>
                    <div class="price-row">
                        <p class="product-gfx" data-base-price-ngn="<?php echo e($product->sell_price); ?>">&#8358;<?php echo e(number_format($product->sell_price, 0)); ?></p>
                        <?php if($product->old_price): ?><p class="product-original-price" data-old-price-ngn="<?php echo e($product->old_price); ?>">&#8358;<?php echo e(number_format($product->old_price, 0)); ?></p><?php endif; ?>
                    </div>
                </div>
                <button class="overlay-d8d"><i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart</button>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="scroll-dots" id="acDots"></div>
    </div>
    <?php endif; ?>

    
    <?php if($televisions->isNotEmpty()): ?>
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Televisions</div>
            <div class="sec-right">
                <a href="/category/Televisions" class="sec-view-all">View all <i class="fas fa-arrow-right"></i></a>
                <div class="sec-arrows">
                    <button class="sarrow" data-target="tvRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="tvRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="products-row" id="tvRow">
            <?php $__currentLoopData = $televisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hry" data-product-id="<?php echo e($product->id); ?>" data-requires-truck="<?php echo e($product->requires_truck ? '1' : '0'); ?>">
                <div class="image-15k">
                    <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'); ?>"
                         alt="<?php echo e($product->name); ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'">
                </div>
                <?php if($product->old_price && $product->old_price > $product->sell_price): ?>
                    <?php $pctOff = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                    <?php if($pctOff >= 5): ?><div class="disc-pill">-<?php echo e($pctOff); ?>%</div><?php endif; ?>
                <?php endif; ?>

                <div class="info-oa4">
                    <div class="rat-z9w">
                        <?php if($product->review_rating_count > 0): ?>
                            <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <span class="rating-dmm">(<?php echo e($product->review_rating_count); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="title-71g"><a href="/product/<?php echo e($product->id); ?>"><?php echo e($product->name); ?></a></h4>
                    <div class="price-row">
                        <p class="product-gfx" data-base-price-ngn="<?php echo e($product->sell_price); ?>">&#8358;<?php echo e(number_format($product->sell_price, 0)); ?></p>
                        <?php if($product->old_price): ?><p class="product-original-price" data-old-price-ngn="<?php echo e($product->old_price); ?>">&#8358;<?php echo e(number_format($product->old_price, 0)); ?></p><?php endif; ?>
                    </div>
                </div>
                <button class="overlay-d8d"><i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart</button>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="scroll-dots" id="tvDots"></div>
    </div>
    <?php endif; ?>

    
    <?php if($washingMachines->isNotEmpty()): ?>
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Washing Machines</div>
            <div class="sec-right">
                <a href="/category/Washing%20Machines" class="sec-view-all">View all <i class="fas fa-arrow-right"></i></a>
                <div class="sec-arrows">
                    <button class="sarrow" data-target="wmRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="wmRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="products-row" id="wmRow">
            <?php $__currentLoopData = $washingMachines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hry" data-product-id="<?php echo e($product->id); ?>" data-requires-truck="<?php echo e($product->requires_truck ? '1' : '0'); ?>">
                <div class="image-15k">
                    <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'); ?>"
                         alt="<?php echo e($product->name); ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'">
                </div>
                <?php if($product->old_price && $product->old_price > $product->sell_price): ?>
                    <?php $pctOff = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                    <?php if($pctOff >= 5): ?><div class="disc-pill">-<?php echo e($pctOff); ?>%</div><?php endif; ?>
                <?php endif; ?>

                <div class="info-oa4">
                    <div class="rat-z9w">
                        <?php if($product->review_rating_count > 0): ?>
                            <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <span class="rating-dmm">(<?php echo e($product->review_rating_count); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="title-71g"><a href="/product/<?php echo e($product->id); ?>"><?php echo e($product->name); ?></a></h4>
                    <div class="price-row">
                        <p class="product-gfx" data-base-price-ngn="<?php echo e($product->sell_price); ?>">&#8358;<?php echo e(number_format($product->sell_price, 0)); ?></p>
                        <?php if($product->old_price): ?><p class="product-original-price" data-old-price-ngn="<?php echo e($product->old_price); ?>">&#8358;<?php echo e(number_format($product->old_price, 0)); ?></p><?php endif; ?>
                    </div>
                </div>
                <button class="overlay-d8d"><i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart</button>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="scroll-dots" id="wmDots"></div>
    </div>
    <?php endif; ?>

    
    <?php if($featured->isNotEmpty()): ?>
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Featured Products</div>
            <div class="sec-right">
                <div class="sec-arrows">
                    <button class="sarrow" data-target="featRow" data-dir="-1"><i class="fas fa-chevron-left"></i></button>
                    <button class="sarrow" data-target="featRow" data-dir="1"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="products-row" id="featRow">
            <?php $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card-hry" data-product-id="<?php echo e($product->id); ?>" data-requires-truck="<?php echo e($product->requires_truck ? '1' : '0'); ?>">
                <div class="image-15k">
                    <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'); ?>"
                         alt="<?php echo e($product->name); ?>" loading="lazy"
                         onerror="this.onerror=null;this.src='https://placehold.co/210x210/f8f9fa/cccccc?text=No+Image'">
                </div>
                <?php if($product->old_price && $product->old_price > $product->sell_price): ?>
                    <?php $pctOff = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                    <?php if($pctOff >= 5): ?><div class="disc-pill">-<?php echo e($pctOff); ?>%</div><?php endif; ?>
                <?php endif; ?>

                <div class="info-oa4">
                    <div class="rat-z9w">
                        <?php if($product->review_rating_count > 0): ?>
                            <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <span class="rating-dmm">(<?php echo e($product->review_rating_count); ?>)</span>
                        <?php endif; ?>
                    </div>
                    <h4 class="title-71g"><a href="/product/<?php echo e($product->id); ?>"><?php echo e($product->name); ?></a></h4>
                    <div class="price-row">
                        <p class="product-gfx" data-base-price-ngn="<?php echo e($product->sell_price); ?>">&#8358;<?php echo e(number_format($product->sell_price, 0)); ?></p>
                        <?php if($product->old_price): ?><p class="product-original-price" data-old-price-ngn="<?php echo e($product->old_price); ?>">&#8358;<?php echo e(number_format($product->old_price, 0)); ?></p><?php endif; ?>
                    </div>
                </div>
                <button class="overlay-d8d"><i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart</button>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="scroll-dots" id="featDots"></div>
    </div>
    <?php endif; ?>

    
    <div class="section-block">
        <div class="sec-head">
            <div class="sec-title">Why Shop With Us</div>
        </div>
        <div class="deal-row">
            <div class="deal-card">
                <div class="deal-icon"><i class="fas fa-store"></i></div>
                <div class="deal-label">Click &amp; Collect</div>
                <div class="deal-sub">Order online, pick up at our showroom — ready in minutes</div>
            </div>
            <div class="deal-card">
                <div class="deal-icon"><i class="fas fa-truck-fast"></i></div>
                <div class="deal-label">Fast Delivery</div>
                <div class="deal-sub">Same-day and next-day delivery options available</div>
            </div>
            <div class="deal-card">
                <div class="deal-icon"><i class="fas fa-tag"></i></div>
                <div class="deal-label">Clearance Deals</div>
                <div class="deal-sub">Big savings on selected lines — limited stock only</div>
            </div>
            <div class="deal-card">
                <div class="deal-icon"><i class="fas fa-shield-halved"></i></div>
                <div class="deal-label">Warranty Assured</div>
                <div class="deal-sub">Full manufacturer warranty on every product we sell</div>
            </div>
            <div class="deal-card">
                <div class="deal-icon"><i class="fas fa-headset"></i></div>
                <div class="deal-label">24/7 Support</div>
                <div class="deal-sub">Expert help whenever you need it — always available</div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('newsletter'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ════════════════════════════════════════════════════════════
       HERO SLIDER — cross-fade dissolve, infinite loop, swipe
       ════════════════════════════════════════════════════════════ */
    (function initHeroSlider() {
        const track = document.getElementById('heroTrack');
        const outer = document.getElementById('heroOuter');
        if (!track || !outer) return;

        const dots   = Array.from(document.querySelectorAll('.hero-dot'));
        const slides = Array.from(track.children);
        const REAL   = slides.length;
        const AUTO_MS = 5500;

        if (REAL <= 1) {
            document.getElementById('heroPrev')?.remove();
            document.getElementById('heroNext')?.remove();
            document.getElementById('heroDots')?.remove();
            slides[0]?.classList.add('is-active');
            return;
        }

        let cur = 0, animating = false, timer = null;

        function show(next) {
            if (animating) return;
            next = ((next % REAL) + REAL) % REAL;
            if (next === cur) return;
            animating = true;
            slides[cur].classList.remove('is-active');
            slides[next].classList.add('is-active');
            dots.forEach((d, i) => d.classList.toggle('active', i === next));
            cur = next;
            setTimeout(() => { animating = false; }, 750);
        }

        function step(dir)   { show(cur + dir); }
        function jumpTo(i)   { if (i !== cur) show(i); }
        function stopAuto()  { if (timer) { clearInterval(timer); timer = null; } }
        function startAuto() { stopAuto(); timer = setInterval(() => step(1), AUTO_MS); }

        slides[0].classList.add('is-active');
        dots[0]?.classList.add('active');

        document.getElementById('heroNext')?.addEventListener('click', () => { step(1);  startAuto(); });
        document.getElementById('heroPrev')?.addEventListener('click', () => { step(-1); startAuto(); });
        dots.forEach(d => d.addEventListener('click', () => { jumpTo(+d.dataset.i); startAuto(); }));
        outer.addEventListener('mouseenter', stopAuto);
        outer.addEventListener('mouseleave', startAuto);
        document.addEventListener('visibilitychange', () => document.hidden ? stopAuto() : startAuto());

        let touchStartX = 0, touchDelta = 0;
        outer.addEventListener('touchstart', (e) => {
            touchDelta = 0; touchStartX = e.touches[0].clientX; stopAuto();
        }, { passive: true });
        outer.addEventListener('touchmove', (e) => {
            touchDelta = e.touches[0].clientX - touchStartX;
        }, { passive: true });
        outer.addEventListener('touchend', () => {
            if (Math.abs(touchDelta) > outer.offsetWidth * 0.18) step(touchDelta < 0 ? 1 : -1);
            touchDelta = 0; startAuto();
        });

        startAuto();
    })();

    /* ── Scroll dots ── */
    window.initScrollDots('bestRow',  'bestDots');
    window.initScrollDots('acRow',    'acDots');
    window.initScrollDots('tvRow',    'tvDots');
    window.initScrollDots('wmRow',    'wmDots');
    window.initScrollDots('featRow',  'featDots');

    /* ── Add-to-cart ── */
    document.querySelectorAll('.overlay-d8d').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            const card  = btn.closest('.card-hry');
            const id    = card.dataset.productId;
            const name  = card.querySelector('.title-71g')?.textContent?.trim() || 'Product';
            const price = parseFloat(card.querySelector('.product-gfx')?.dataset.basePriceNgn || 0);
            const img   = card.querySelector('.image-15k img')?.src || '';
            const requiresTruck = card.dataset.requiresTruck === '1';
            window.addToCart(id, name, price, img, requiresTruck);
            btn.innerHTML = '<i class="fas fa-check" style="font-size:12px"></i> Added!';
            btn.style.background = 'var(--g700)';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-shopping-bag" style="font-size:11px"></i> Add to Cart';
                btn.style.background = '';
            }, 1800);
        });
    });

    /* ── Card click → product page ── */
    document.querySelectorAll('.card-hry').forEach(card => {
        card.addEventListener('click', e => {
            if (!e.target.closest('.overlay-d8d') && !e.target.closest('.card-wish')) {
                window.location.href = `/product/${card.dataset.productId}`;
            }
        });
    });

    /* ── Update prices ── */
    window.updatePricesOnPage?.();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/user/dashboard.blade.php ENDPATH**/ ?>