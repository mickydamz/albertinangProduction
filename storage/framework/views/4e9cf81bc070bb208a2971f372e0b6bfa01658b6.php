

<?php $__env->startSection('title', $product->name . ' - Albertina Nigeria'); ?>

<?php $__env->startPush('styles'); ?>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.pd-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 10px 20px 24px;
    font-family: var(--font-body, 'DM Sans', sans-serif);
}

.pd-breadcrumb {
    display: flex; flex-wrap: wrap; align-items: center; gap: 5px;
    font-size: 12px; color: var(--ink3); margin-bottom: 10px;
    padding: 6px 0; border-bottom: 1px solid var(--border);
}
.pd-breadcrumb a { color: var(--ink3); text-decoration: none; transition: color .15s; }
.pd-breadcrumb a:hover { color: var(--g600); }
.pd-breadcrumb .bc-current { color: var(--ink); font-weight: 600; }
.pd-breadcrumb .bc-sep { font-size: 9px; color: var(--border2); opacity: .6; }

.pd-main {
    display: grid;
    grid-template-columns: 460px 1fr;
    gap: 24px;
    align-items: start;
}

.pd-gallery { display: flex; flex-direction: row; gap: 8px; align-items: flex-start; }
.pd-thumbs {
    display: flex; flex-direction: column; gap: 6px; order: -1;
    overflow-y: auto; overflow-x: hidden; scrollbar-width: none;
    max-height: 480px; flex-shrink: 0; width: 66px;
}
.pd-thumbs::-webkit-scrollbar { display: none; }
.pd-thumbs img {
    width: 62px; height: 62px; object-fit: contain;
    border: 2px solid var(--border); border-radius: 8px; cursor: pointer;
    transition: border-color .2s; background: var(--surf2); padding: 3px;
    flex-shrink: 0; display: block;
}
.pd-thumbs img.active, .pd-thumbs img:hover { border-color: var(--g400); }

.pd-main-img-wrap {
    position: relative; flex: 1; min-width: 0; min-height: 0;
    aspect-ratio: 1 / 1; border-radius: 12px; border: 1px solid var(--border);
    overflow: hidden; cursor: crosshair; background: var(--surf2);
}
.pd-main-img-wrap img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transform-origin: center center; transition: transform .15s ease; will-change: transform;
}
.pd-gal-nav {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,.92); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 12px; color: var(--ink2);
    transition: background .18s, border-color .18s, color .18s;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
}
.pd-gal-nav:hover { background: #fff; color: var(--g600); border-color: var(--g400); }
.pd-gal-nav:disabled { opacity: .25; cursor: default; pointer-events: none; }
.pd-gal-nav--prev { left: 10px; }
.pd-gal-nav--next { right: 10px; }

.pd-info {
    display: flex; flex-direction: column; gap: 10px;
    position: sticky; top: 82px;
}
.pd-name {
    font-family: var(--fh);
    font-size: 1.35rem; font-weight: 800; color: var(--ink); line-height: 1.2;
}
.pd-meta-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px; }
.pd-brand { font-size: 12px; color: var(--ink3); display: flex; align-items: center; gap: 5px; }
.pd-brand strong { color: var(--ink2); }
.pd-stars { display: flex; align-items: center; gap: 3px; cursor: pointer; }
.pd-stars i { font-size: 14px; color: #f5c518; }
.pd-stars i.far { color: #d1d5db; }
.pd-stars span { font-size: 12px; color: var(--ink3); margin-left: 4px; font-weight: 500; }
.pd-stars:hover span { color: var(--g600); text-decoration: underline; }

.pd-price-row {
    display: flex; align-items: baseline; gap: 8px;
    padding: 8px 0;
}
.pd-price { font-family: 'DM Sans', sans-serif; font-size: 1.65rem; font-weight: 700; color: var(--g600); letter-spacing: -0.5px; }
.pd-price-old { font-size: .9rem; color: var(--ink3); text-decoration: line-through; }
.pd-badge { background: #e05a1a; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-left: auto; }

.pd-stock { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; width: fit-content; }
.pd-stock.in  { background: var(--g50); color: var(--g700); border: 1px solid var(--g200); }
.pd-stock.out { background: #fef2f2; color: #7f1d1d; border: 1px solid #fecaca; }

.pd-desc { font-size: 13px; color: var(--ink3); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.pd-trust { display: flex; gap: 8px; flex-wrap: wrap; }
.pd-trust-item { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--ink3); }
.pd-trust-item i { color: var(--g500); font-size: 12px; }

.pd-qty-block { display: flex; flex-direction: column; gap: 10px; margin-top: 8px; }
.pd-qty-label { font-size: 12px; font-weight: 600; color: var(--ink2); }
.pd-qty-row { display: flex; align-items: center; gap: 0; border: 1.5px solid var(--border); border-radius: 10px; overflow: hidden; width: fit-content; }
.pd-qty-btn {
    width: 42px; height: 42px; border: none; background: var(--surf2); color: var(--ink);
    font-size: 20px; font-weight: 600; cursor: pointer; transition: background .15s, color .15s;
    display: flex; align-items: center; justify-content: center; line-height: 1; flex-shrink: 0;
    font-family: 'DM Sans', sans-serif;
}
.pd-qty-btn:hover:not(:disabled) { background: var(--g100); color: var(--g700); }
.pd-qty-btn:disabled { opacity: .35; cursor: not-allowed; }
.pd-qty-divider { width: 1px; height: 22px; background: var(--border); flex-shrink: 0; }
.pd-qty-val { min-width: 44px; text-align: center; font-size: 14px; font-weight: 700; color: var(--ink); font-family: 'DM Sans', sans-serif; padding: 0 4px; user-select: none; }

.pd-actions-row { display: flex; align-items: stretch; gap: 10px; }
.pd-btn-atc {
    flex: 1; padding: 15px 18px; background: var(--g500); color: #fff; border: none;
    border-radius: 10px; font-size: 15px; font-weight: 700; font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 8px;
}
.pd-btn-atc:hover { background: var(--g600); transform: translateY(-1px); }
.pd-btn-atc:disabled { background: var(--ink3); cursor: not-allowed; transform: none; }
.pd-btn-buy {
    flex: 1; padding: 15px 18px; background: var(--g50); color: var(--g700);
    border: 2px solid var(--g400); border-radius: 10px; font-size: 15px; font-weight: 700;
    font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.pd-btn-buy:hover { background: var(--g100); transform: translateY(-1px); }
.pd-btn-buy:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.pd-tabs-wrap { border-top: 1px solid var(--border); overflow: hidden; margin-top: 18px; }
.pd-tab-nav { display: flex; border-bottom: 1px solid var(--border); overflow-x: auto; scrollbar-width: none; }
.pd-tab-nav::-webkit-scrollbar { display: none; }
.pd-tab-btn {
    padding: 13px 18px; font-size: 13px; font-weight: 600; color: var(--ink3); white-space: nowrap;
    border: none; background: none; border-bottom: 3px solid transparent; cursor: pointer;
    transition: all .2s; font-family: 'DM Sans', sans-serif;
}
.pd-tab-btn:hover { color: var(--g600); }
.pd-tab-btn.active { color: var(--g600); border-bottom-color: var(--g500); }
.pd-tab-panel { padding: 22px; display: none; }
.pd-tab-panel.active { display: block; }
.pd-tab-panel h3 { font-family: var(--fh); font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 14px; }

/* Rating summary */
.rating-summary { display: flex; gap: 24px; padding: 16px 0; border-bottom: 1px solid var(--border); margin-bottom: 20px; flex-wrap: wrap; }
.rating-summary__overall { text-align: center; min-width: 80px; flex-shrink: 0; }
.rating-summary__score { font-family: var(--fh); font-size: 2.4rem; font-weight: 800; color: var(--ink); line-height: 1; }
.rating-summary__stars { margin: 6px 0 4px; font-size: 14px; color: #f5c518; }
.rating-summary__stars i.far { color: #d1d5db; }
.rating-summary__count { font-size: 11px; color: var(--ink3); }
.rating-distribution { flex: 1; min-width: 180px; display: flex; flex-direction: column; gap: 5px; justify-content: center; }
.rating-bar-row { display: flex; align-items: center; gap: 8px; }
.rating-bar-label { font-size: 11px; color: var(--ink3); min-width: 28px; text-align: right; white-space: nowrap; display: flex; align-items: center; gap: 2px; }
.rating-bar-label i { color: #f5c518; font-size: 9px; }
.rating-bar-track { flex: 1; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
.rating-bar-fill { height: 100%; background: var(--g500); border-radius: 3px; transition: width 0.5s ease; min-width: 2px; }
.rating-bar-count { font-size: 11px; color: var(--ink3); min-width: 18px; text-align: left; font-weight: 500; }

/* Review cards */
.review-card { background: var(--surf2); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; margin-bottom: 12px; }
.review-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.reviewer-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--g100); border: 2px solid var(--g200); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--g700); flex-shrink: 0; }
.reviewer-name { font-size: 13px; font-weight: 600; color: var(--ink); }
.review-stars { display: flex; align-items: center; gap: 2px; margin-top: 2px; }
.review-stars i { font-size: 10px; color: #f5c518; }
.review-stars i.far { color: #d1d5db; }
.review-date { font-size: 11px; color: var(--ink3); margin-left: 8px; }
.review-text { font-size: 13px; color: var(--ink3); line-height: 1.6; margin-top: 2px; }

/* Review list collapse / show more */
.reviews-collapsed .review-card:nth-of-type(n+4) { display: none; }
.reviews-show-more-wrap { display: flex; justify-content: center; margin-top: 4px; }
.reviews-show-more-btn {
    display: inline-flex; align-items: center; gap: 6px;
    margin: 4px auto 8px; padding: 9px 20px;
    background: var(--g50); color: var(--g700);
    border: 1.5px solid var(--g200); border-radius: 8px;
    font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: all .2s;
}
.reviews-show-more-btn:hover { background: var(--g100); border-color: var(--g400); }
.reviews-show-more-btn i { font-size: 11px; transition: transform .2s; }

/* Review form */
.review-form-wrap { padding: 16px 0 20px; margin-bottom: 20px; border-bottom: 1px solid var(--border); }
.review-form-wrap h3 { color: var(--g700); margin-bottom: 14px; font-size: 1rem; }
.form-group-star { margin-bottom: 16px; }
.star-label, .textarea-label { display: block; font-size: 13px; font-weight: 600; color: var(--ink2); margin-bottom: 8px; }
.required-star { color: #dc2626; }
.star-rating { display: flex; gap: 4px; direction: rtl; justify-content: flex-start; margin-bottom: 4px; }
.star-rating input { display: none; }
.star-rating label { font-size: 1.6rem; color: var(--border2); cursor: pointer; transition: color .15s, transform .15s; }
.star-rating label i { transition: color .15s, transform .15s; }
.star-rating label:hover i, .star-rating label:hover ~ label i { color: #f5c518; transform: scale(1.15); }
.star-rating input:checked ~ label i { color: #f5c518; }
.star-rating.invalid label i { animation: shake 0.5s ease; }
@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-4px); } 75% { transform: translateX(4px); } }
.rating-text { display: inline-block; font-size: 12px; color: var(--ink3); margin-left: 10px; transition: color .2s; min-height: 18px; }
.rating-text.active { color: var(--g600); font-weight: 600; }
.field-error { display: block; font-size: 12px; color: #dc2626; margin-top: 5px; font-weight: 500; min-height: 0; line-height: 1.4; }
.field-error:empty { display: none; }
.form-group-textarea { margin-bottom: 4px; }
.review-textarea { width: 100%; min-height: 80px; padding: 10px 13px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--ink); resize: vertical; outline: none; transition: border-color .2s; margin-bottom: 2px; }
.review-textarea:focus { border-color: var(--g400); }
.review-textarea.invalid { border-color: #dc2626 !important; background: #fff5f5 !important; }
.textarea-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 2px; margin-bottom: 12px; }
.char-count { font-size: 11px; color: var(--ink3); margin-left: auto; }
.char-count.warning { color: #f59e0b; }
.char-count.danger  { color: #dc2626; }
.review-submit-btn { position: relative; display: inline-flex; align-items: center; gap: 8px; min-width: 150px; justify-content: center; background: var(--g500); color: #fff; padding: 10px 22px; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background .2s; }
.review-submit-btn:hover { background: var(--g600); }
.review-submit-btn:disabled { background: var(--ink3); cursor: not-allowed; }
.review-submit-btn .btn-spinner { display: inline-flex; align-items: center; gap: 6px; }
.form-message { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-top: 12px; display: flex; align-items: flex-start; gap: 8px; line-height: 1.6; }
.form-message i { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
.form-message.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-left: 4px solid #22c55e; }
.form-message.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; border-left: 4px solid #dc2626; }
.form-message.warning { background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; border-left: 4px solid #f59e0b; }
.form-message.info    { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-left: 4px solid #3b82f6; }
.form-message a { color: inherit; font-weight: 700; text-decoration: underline; }
.empty-state { text-align: center; color: var(--ink3); padding: 24px 0; font-size: 13px; }

/* Related products */
.related-row { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 6px; scrollbar-width: none; cursor: grab; }
.related-row::-webkit-scrollbar { display: none; }
.related-row:active { cursor: grabbing; }
.rel-card { min-width: 155px; max-width: 155px; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; flex-shrink: 0; cursor: pointer; transition: all .2s; position: relative; }
.rel-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.rel-card__img { height: 120px; background: #f8f9fa; overflow: hidden; }
.rel-card__img img { width: 100%; height: 100%; object-fit: cover; transition: transform .2s; }
.rel-card:hover .rel-card__img img { transform: scale(1.05); }
.rel-card__body { padding: 9px; }
.rel-card__name { font-size: 11.5px; font-weight: 500; color: var(--ink); line-height: 1.3; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.rel-card__price { font-size: 13px; font-weight: 700; color: var(--g600); font-family: 'DM Sans', sans-serif; }
.rel-card__atc { position: absolute; bottom: 0; left: 0; right: 0; background: var(--g500); color: #fff; padding: 7px; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 4px; transform: translateY(100%); transition: transform .2s; border: none; cursor: pointer; width: 100%; }
.rel-card:hover .rel-card__atc { transform: translateY(0); }

@media (max-width: 959px) {
    .pd-main { grid-template-columns: 1fr; max-height: none; }
    .pd-gallery { flex-direction: column; gap: 8px; }
    .pd-main-img-wrap { aspect-ratio: 4 / 3; width: 100%; cursor: default; }
    .pd-thumbs { flex-direction: row; order: 1; max-height: none; overflow-x: auto; overflow-y: hidden; width: auto; padding-bottom: 2px; }
    .pd-thumbs img { width: 60px; height: 60px; flex-shrink: 0; }
    .pd-info { position: static; }
    .pd-actions-row { flex-wrap: wrap; }
    .pd-btn-atc, .pd-btn-buy { flex: 1 1 140px; }
    .rating-summary { flex-direction: column; gap: 14px; }
    .rating-summary__overall { display: flex; align-items: center; gap: 12px; min-width: auto; }
}
@media (max-width: 599px) {
    .pd-wrap { padding: 8px 12px 20px; }
    .pd-main-img-wrap { aspect-ratio: 1 / 1; }
    .pd-thumbs img { width: 52px; height: 52px; }
    .pd-name { font-size: 1.1rem; }
    .pd-price { font-size: 1.35rem; }
    .pd-tab-panel { padding: 14px; }
    .pd-tab-btn { padding: 11px 13px; font-size: 12px; }
    .pd-actions-row { flex-direction: row; }
    .pd-btn-atc, .pd-btn-buy { flex: 1 1 0; padding: 13px 8px; font-size: 14px; }
    .rating-summary__score { font-size: 1.8rem; }
    .review-form-wrap { padding: 12px 14px; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="pd-wrap">


<nav class="pd-breadcrumb" aria-label="Breadcrumb">
    <?php
        $crumbs = [['name' => 'Home', 'url' => url('/')]];
        if (isset($product)) {
            $sub = $product->Subcategory ?? null;
            $cat = $sub?->category ?? $product->category ?? null;
            if ($cat) $crumbs[] = ['name' => $cat->name, 'url' => route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $cat->name)))];
            if ($sub) $crumbs[] = ['name' => $sub->name, 'url' => route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $sub->name)))];
            $crumbs[] = ['name' => $product->name, 'url' => null];
        }
    ?>
    <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($i > 0): ?><i class="fas fa-chevron-right bc-sep"></i><?php endif; ?>
        <?php if($crumb['url']): ?>
            <a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['name']); ?></a>
        <?php else: ?>
            <span class="bc-current"><?php echo e(Str::limit($crumb['name'], 40)); ?></span>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>


<div class="pd-main">

    
    <div class="pd-gallery">
        <div class="pd-main-img-wrap" id="pdZoomContainer">
            <button class="pd-gal-nav pd-gal-nav--prev" id="pdNavPrev" aria-label="Previous image" type="button">
                <i class="fas fa-chevron-left"></i>
            </button>
            <img src="<?php echo e($product->images->first() ? asset('storage/' . $product->images->first()->image_url) : 'https://placehold.co/500x500/eef3e8/3d8012?text=No+Image'); ?>"
                 alt="<?php echo e($product->name); ?>" id="pdMainImg"
                 onerror="this.src='https://placehold.co/500x500/eef3e8/3d8012?text=No+Image'">
            <button class="pd-gal-nav pd-gal-nav--next" id="pdNavNext" aria-label="Next image" type="button">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="pd-thumbs">
            <?php $__empty_1 = true; $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <img src="<?php echo e(asset('storage/' . $image->image_url)); ?>"
                     alt="View <?php echo e($i + 1); ?>"
                     class="pd-thumb <?php echo e($i === 0 ? 'active' : ''); ?>"
                     data-full="<?php echo e(asset('storage/' . $image->image_url)); ?>"
                     onerror="this.src='https://placehold.co/62x62/eef3e8/3d8012?text=+'">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <img src="https://placehold.co/62x62/eef3e8/3d8012?text=+"
                     class="pd-thumb active"
                     data-full="https://placehold.co/500x500/eef3e8/3d8012?text=No+Image">
            <?php endif; ?>
        </div>
    </div>

    
    <div class="pd-info">
        <h1 class="pd-name"><?php echo e($product->name); ?></h1>

        <div class="pd-meta-row">
            <div class="pd-brand">
                <i class="fas fa-tag" style="color:var(--g500);font-size:10px;"></i>
                Brand: <strong><?php echo e($product->brand ?? 'N/A'); ?></strong>
            </div>
            <?php
                $displayCount    = $ratingCount   ?? $product->rating_count ?? 0;
                // No real reviews → no stars (ignore any leftover placeholder rating).
                $displayRating   = $displayCount > 0 ? ($averageRating ?? $product->rating ?? 0) : 0;
                $fullStars       = floor($displayRating);
                $halfStar        = ($displayRating - $fullStars) >= 0.25 && ($displayRating - $fullStars) < 0.75;
                $fullStarRoundUp = ($displayRating - $fullStars) >= 0.75;
                $emptyStars      = 5 - $fullStars - ($halfStar || $fullStarRoundUp ? 1 : 0);
                if ($fullStarRoundUp) $fullStars++;
                // Verified buyer who hasn't reviewed yet — eligible to leave the first rating.
                $canReview       = auth()->check() && ($hasPurchased ?? false) && !($hasReviewed ?? false);
            ?>
            <div class="pd-stars" id="pdHeadStars" role="button" tabindex="0"
                 title="<?php echo e($displayCount > 0 ? number_format($displayRating, 1) . ' out of 5 stars — see reviews' : ($canReview ? 'Rate this product — write the first review' : '')); ?>">
                <?php if($displayCount > 0): ?>
                    <?php for($i = 0; $i < $fullStars; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    <?php if($halfStar && !$fullStarRoundUp): ?><i class="fas fa-star-half-alt"></i><?php endif; ?>
                    <?php for($i = 0; $i < $emptyStars; $i++): ?><i class="far fa-star"></i><?php endfor; ?>
                    <span>(<?php echo e($displayCount); ?>)</span>
                <?php elseif($canReview): ?>
                    <?php for($i = 0; $i < 5; $i++): ?><i class="far fa-star"></i><?php endfor; ?>
                    <span>Rate this product</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="pd-price-row">
            <span class="pd-price" data-price-ngn="<?php echo e($product->sell_price); ?>">
                ₦<?php echo e(number_format($product->sell_price, 0)); ?>

            </span>
            <?php if($product->old_price): ?>
                <span class="pd-price-old" data-old-price-ngn="<?php echo e($product->old_price); ?>">
                    ₦<?php echo e(number_format($product->old_price, 0)); ?>

                </span>
                <?php $disc = round((($product->old_price - $product->sell_price) / $product->old_price) * 100); ?>
                <?php if($disc >= 5): ?><span class="pd-badge">-<?php echo e($disc); ?>%</span><?php endif; ?>
            <?php endif; ?>
        </div>

        <p class="pd-desc"><?php echo e($product->description); ?></p>

        <div class="pd-trust">
            <span class="pd-trust-item"><i class="fas fa-shield-alt"></i> Secure Checkout</span>
            <span class="pd-trust-item"><i class="fas fa-undo"></i> Easy Returns</span>
            <span class="pd-trust-item"><i class="fas fa-truck"></i> Fast Delivery</span>
        </div>

        <div class="pd-qty-block">
            <span class="pd-qty-label">Quantity</span>
            <div class="pd-qty-row">
                <button class="pd-qty-btn" id="pd-qtyMinus" <?php if($product->stock === 0): ?> disabled <?php endif; ?> aria-label="Decrease quantity">&#8722;</button>
                <span class="pd-qty-divider"></span>
                <span class="pd-qty-val" id="pd-qtyVal">1</span>
                <span class="pd-qty-divider"></span>
                <button class="pd-qty-btn" id="pd-qtyPlus" <?php if($product->stock === 0): ?> disabled <?php endif; ?> aria-label="Increase quantity">&#43;</button>
            </div>
            <div class="pd-actions-row">
                <button class="pd-btn-atc" id="pd-atcBtn" <?php if($product->stock === 0): ?> disabled <?php endif; ?>>
                    <i class="fas fa-shopping-bag"></i> Add to Cart
                </button>
                <button class="pd-btn-buy" id="pd-buyBtn" <?php if($product->stock === 0): ?> disabled <?php endif; ?>>
                    <i class="fas fa-bolt"></i> Buy Now
                </button>
            </div>
        </div>
    </div>
</div>


<div class="pd-tabs-wrap">
    <div class="pd-tab-nav">
        <button class="pd-tab-btn active" data-tab="pd-specifications">Specifications</button>
        <button class="pd-tab-btn" data-tab="pd-description">Description</button>
        <button class="pd-tab-btn" data-tab="pd-reviews">Reviews (<span id="reviewTabCount"><?php echo e($displayCount); ?></span>)</button>
        <button class="pd-tab-btn" data-tab="pd-related">Related Products</button>
    </div>

    
    <div id="pd-specifications" class="pd-tab-panel active">
        <h3>Product Specifications</h3>

        <?php $attrGroups = $product->getAttributeGroups(); ?>

        <?php if(!empty($attrGroups)): ?>

            
            <table id="rawSpecTable" style="display:none;">
                <tbody>
                    <?php $__currentLoopData = $attrGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $group['attrs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-group="<?php echo e($group['group']); ?>">
                                <th><?php echo e($attr['key']); ?></th>
                                <td><?php echo e($attr['value']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div id="specLayoutContainer"></div>

        <?php elseif(!empty($product->getAllCustomAttributes())): ?>

            
            <table class="spec-table" id="rawSpecTable" style="display:none;">
                <tbody>
                    <?php $__currentLoopData = $product->getAllCustomAttributes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th><?php echo e(ucwords(str_replace('_', ' ', $key))); ?></th>
                            <td>
                                <?php if(is_bool($value)): ?>        <?php echo e($value ? 'Yes' : 'No'); ?>

                                <?php elseif(is_array($value)): ?>   <?php echo e(implode(', ', $value)); ?>

                                <?php else: ?>                       <?php echo e($value ?? 'N/A'); ?>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div id="specLayoutContainer"></div>

        <?php else: ?>
            <p class="empty-state">No specifications available.</p>
        <?php endif; ?>
    </div>

    
    <div id="pd-description" class="pd-tab-panel">
        <?php if($product->description): ?>
            <p style="font-size:13.5px;color:var(--ink3);line-height:1.75;">
                <?php echo e($product->description); ?>

            </p>
        <?php endif; ?>
        <?php if(!empty($product->description_blocks)): ?>
            <?php echo $__env->make('partials.block-renderer', ['blocks' => $product->description_blocks], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
        <?php if(!$product->description && empty($product->description_blocks)): ?>
            <p class="empty-state">No description available for this product.</p>
        <?php endif; ?>
    </div>

    
    <div id="pd-reviews" class="pd-tab-panel">

     <?php if(auth()->guard()->check()): ?>
    <?php if($hasReviewed ?? false): ?>
        
    <?php elseif($hasPurchased ?? false): ?>
        <div class="review-form-wrap" id="reviewFormWrap">
            <h3><i class="fas fa-pen" style="color:var(--g500);margin-right:5px;"></i>Write a Review</h3>
            <form id="pd-reviewForm" action="<?php echo e(route('reviews.store', ['productId' => $product->id])); ?>" method="POST" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                <div class="form-group-star">
                    <label class="star-label">Your Rating <span class="required-star">*</span></label>
                    <div class="star-rating" id="starRating">
                        <?php $__currentLoopData = [5,4,3,2,1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <input type="radio" id="star<?php echo e($s); ?>" name="rating" value="<?php echo e($s); ?>">
                            <label for="star<?php echo e($s); ?>" title="<?php echo e($s); ?> star<?php echo e($s > 1 ? 's' : ''); ?>" data-rating="<?php echo e($s); ?>">
                                <i class="fas fa-star"></i>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <span class="rating-text" id="ratingText">Click to rate</span>
                    <span class="field-error" id="ratingError"></span>
                </div>
                <div class="form-group-textarea">
                    <label for="reviewComment" class="textarea-label">Your Review <span class="required-star">*</span></label>
                    <textarea name="comment" id="reviewComment" class="review-textarea"
                              placeholder="Share your experience with this product…"
                              maxlength="1000" rows="4"></textarea>
                    <div class="textarea-footer">
                        <span class="field-error" id="commentError"></span>
                        <span class="char-count" id="charCount">0/1000</span>
                    </div>
                </div>
                <button type="submit" class="review-submit-btn" id="reviewSubmitBtn">
                    <span class="btn-text">Submit Review</span>
                    <span class="btn-spinner" style="display:none;"><i class="fas fa-spinner fa-pulse"></i> Submitting…</span>
                </button>
                <div id="pd-formMessage" class="form-message" style="display:none;"></div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>

        <?php
            $distSeed = [];
            foreach ([5,4,3,2,1] as $star) { $distSeed[$star] = $ratingDistribution[$star] ?? 0; }
        ?>
        <div class="rating-summary" id="ratingSummary"
             data-count="<?php echo e($displayCount); ?>"
             data-dist="<?php echo e(json_encode($distSeed)); ?>"
             <?php if($displayCount <= 0): ?> style="display:none;" <?php endif; ?>>
            <div class="rating-summary__overall">
                <div class="rating-summary__score" id="ratingSummaryScore"><?php echo e(number_format($displayRating, 1)); ?></div>
                <div class="rating-summary__stars" id="ratingSummaryStars">
                    <?php for($i = 0; $i < $fullStars; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    <?php if($halfStar && !$fullStarRoundUp): ?><i class="fas fa-star-half-alt"></i><?php endif; ?>
                    <?php for($i = 0; $i < $emptyStars; $i++): ?><i class="far fa-star"></i><?php endfor; ?>
                </div>
                <div class="rating-summary__count" id="ratingSummaryCount"><?php echo e($displayCount); ?> review(s)</div>
            </div>
            <div class="rating-distribution" id="ratingDistribution">
                <?php $__currentLoopData = [5,4,3,2,1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $star): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $count      = $distSeed[$star];
                        $percentage = $displayCount > 0 ? round(($count / $displayCount) * 100) : 0;
                    ?>
                    <div class="rating-bar-row" data-star="<?php echo e($star); ?>">
                        <span class="rating-bar-label"><?php echo e($star); ?> <i class="fas fa-star"></i></span>
                        <div class="rating-bar-track"><div class="rating-bar-fill" style="width:<?php echo e($percentage); ?>%;"></div></div>
                        <span class="rating-bar-count"><?php echo e($count); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <h3 style="margin-bottom:12px;">Customer Reviews (<span id="reviewCount"><?php echo e($displayCount); ?></span>)</h3>
        <div id="reviewsList" class="reviews-collapsed">
            <?php $__empty_1 = true; $__currentLoopData = $product->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-avatar"><?php echo e(strtoupper(substr($review->user_name ?? 'U', 0, 1))); ?></div>
                        <div>
                            <div class="reviewer-name"><?php echo e($review->user_name ?? 'Anonymous'); ?></div>
                            <div class="review-stars">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <i class="<?php echo e(($review->rating ?? 0) > $i ? 'fas' : 'far'); ?> fa-star"></i>
                                <?php endfor; ?>
                                <span class="review-date"><?php echo e($review->created_at ? $review->created_at->diffForHumans() : ''); ?></span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text"><?php echo e($review->content); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="empty-state" id="noReviewsMsg">No reviews yet. Be the first to review this product!</p>
            <?php endif; ?>
        </div>

        <div class="reviews-show-more-wrap">
            <button type="button" class="reviews-show-more-btn" id="reviewsShowMoreBtn" style="display:none;">
                <i class="fas fa-chevron-down"></i> <span class="btn-label">Show all reviews</span>
            </button>
        </div>
    </div>

    
    <div id="pd-related" class="pd-tab-panel">
        <h3>Related Products</h3>
        <?php if(isset($relatedProducts) && $relatedProducts->isNotEmpty()): ?>
            <div class="related-row" id="pd-relatedRow">
                <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="rel-card" onclick="window.location='/product/<?php echo e($rel->id); ?>'">
                        <div class="rel-card__img">
                            <img src="<?php echo e($rel->images->first() ? asset('storage/' . $rel->images->first()->image_url) : 'https://placehold.co/155x120/eef3e8/3d8012?text=No+Image'); ?>"
                                 alt="<?php echo e($rel->name); ?>"
                                 onerror="this.src='https://placehold.co/155x120/eef3e8/3d8012?text=No+Image'">
                        </div>
                        <div class="rel-card__body">
                            <div class="rel-card__name"><?php echo e($rel->name); ?></div>
                            <div class="rel-card__price" data-price-ngn="<?php echo e($rel->sell_price); ?>">
                                ₦<?php echo e(number_format($rel->sell_price, 0)); ?>

                            </div>
                        </div>
                        <button class="rel-card__atc"
                        
                                onclick="event.stopPropagation(); window.addToCart('<?php echo e($rel->id); ?>', '<?php echo e(addslashes($rel->name)); ?>', <?php echo e($rel->sell_price); ?>, '<?php echo e($rel->images->first() ? asset('storage/' . $rel->images->first()->image_url) : ''); ?>', <?php echo e($rel->requires_truck ? 'true' : 'false'); ?>)"
                            <i class="fas fa-shopping-bag" style="font-size:10px;"></i> Add to Cart
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="empty-state">No related products found.</p>
        <?php endif; ?>
    </div>

</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ═══════════════════════════════════════════════════
       UNIFIED SPEC ORGANIZER
       Handles both grouped (admin data-group) and smart (keyword) modes.
       Both produce identical spec-v2-* layout.
    ═══════════════════════════════════════════════════ */

    (function organizeSpecifications() {
        const rawTable  = document.getElementById('rawSpecTable');
        const container = document.getElementById('specLayoutContainer');
        if (!rawTable || !container) return;

        const tbody = rawTable.querySelector('tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length === 0) return;

        const hasExplicitGroups = rows.some(r => r.dataset.group);

        let orderedGroupNames = [];
        let grouped = new Map();

        if (hasExplicitGroups) {

            /* ── GROUPED MODE: admin-assigned group names via data-group ── */
            rows.forEach(row => {
                const name = row.dataset.group || 'General';
                if (!grouped.has(name)) {
                    orderedGroupNames.push(name);
                    grouped.set(name, []);
                }
                grouped.get(name).push(row);
            });

        } else {

            /* ── SMART MODE: keyword scoring ── */
            const PRODUCT_CATEGORY    = '<?php echo e($product->category->name ?? ""); ?>';
            const PRODUCT_SUBCATEGORY = '<?php echo e($product->Subcategory->name ?? ""); ?>';

            const groupsByCategory = {
                'Gas Cookers': [
                    { name:'Basic Data',                keywords:['lid','control panel','net weight','gross weight'] },
                    { name:'Styling And Color',         keywords:['side panel','front panel','door panel','panel of drawer','cooktop surface','oven door type','door type','oven tray','oven rack','dishwarmer','chrome coated','enameled','inox','electrostatic','colour','color','finish','shade','painted','coating','texture','gloss','matte','matt','metallic','brushed','polished'] },
                    { name:'Packaging & Prices',        keywords:['net dimension','gross dimension','packaging','packing','carton','shipping weight','package weight','box dimension','carton size'] },
                    { name:'Oven Safety',               keywords:['oven safety','timer (oven safety)','timer (oven','safety device','thermocouple','overheat','auto shutoff','cool touch','residual heat'] },
                    { name:'Spec',                      keywords:['width','depth','height','size','dimension','length','thickness','diameter','weight','kg','mm','cm','capacity','volume'] },
                    { name:'Energy',                    keywords:['gas supply','electric supply','energy','lpg','voltage','power supply','watt','wattage','power consumption','rated power','energy rating','kwh','ampere','amp','current','hz','frequency','plug type','british','natural gas','butane','propane'] },
                    { name:'Cooktop Controls',          keywords:['knobs','mechanical','cooktop control','manuel','manual control','zebra knob','control type','touch control','rotary','dial','push button','ignition type','spark','auto ignition','piezo','thermostat'] },
                    { name:'Gas CookTop Configuration', keywords:['no.of burner','burner cooking zone','ignition for top','flame failure','front (left)','front (right)','rear (left)','rear (right)','medium (center)','pan support','cap material','cap color','sabaf','pool type','top burner type','cooking zone','hob','burner layout','burner type','burner power','burner output','triple ring','wok burner','rapid burner','semi-rapid','grate material','grate color','cast iron','number of burners','no of burners','cooktop material','hob surface'] },
                    { name:'Gas Oven',                  keywords:['no.of oven','oven wire rack','oven grill burner','oven baking burner','number of trays','oven cavity','chicken rotisserie','lamp (gas','oven control (gas','ladder wire','rotisserie','oven function','oven mode','grill element','top element','bottom element','fan','convection','oven light','oven lamp','interior light','oven lining','cavity lining','oven coating','oven shelf','shelf position','wire shelf','drip tray','roasting tray','baking tray','grill tray','oven volume','oven capacity','usable capacity','oven temperature','max temperature','temperature range','preheat','self clean','pyrolytic','catalytic','oven door glass','door glass','inner glass','oven window'] },
                    { name:'Convenience Feature',       keywords:['adjustable feet','clean innerdoo','detachable cavity','activenamel','activ enamel','adjustable','easy clean','detachable','removable','foldable','soft close','timer','minute minder','countdown','alarm','display','led display','digital display','clock','programmable','delay start','quick start','rapid heat','turbo','eco mode','defrost','cord storage','handle','anti-slip','levelling','leveling'] },
                    { name:'Electrical Oven',           keywords:['elec. oven','electrical oven','oven control (elec','temperature control (elec','oven door (elec','lamp (elec','number of trays and grids','electric oven','electric grill','heating element','element type','element power','fan assisted','fan forced','multifunction','multi-function','number of functions'] },
                    { name:'More',                      keywords:['model number','model no','spec','top burner type','ignition for oven','electric plug','certification','certified','compliance','standard','remark','special feature','color available'] },
                ],
                'Gas Hobs': [
                    { name:'Spec',                  keywords:['width','depth','height','size','dimension','weight','kg','mm','cm','cutout','cut-out'] },
                    { name:'Styling And Color',     keywords:['color','colour','finish','surface','material','glass','stainless','coating','texture','body','trim','frame'] },
                    { name:'Burner Configuration',  keywords:['burner','cooking zone','hob','ignition','flame failure','front','rear','left','right','center','wok','triple ring','sabaf','rapid','semi-rapid','auxiliary','pan support','cap','grate','cast iron','enamel'] },
                    { name:'Controls',              keywords:['knob','control','dial','rotary','manual','mechanical','auto ignition','piezo','spark','thermocouple'] },
                    { name:'Energy',                keywords:['gas supply','lpg','natural gas','butane','propane','electric supply','voltage','power','watt','plug','british'] },
                    { name:'More',                  keywords:['model','certification','compliance','standard','note','remark'] },
                ],
                'Split Ac': [
                    { name:'Cooling & Heating',     keywords:['cooling capacity','heating capacity','btu','btu/h','rated cooling','rated heating','cooling power','heating power','cop','eer','seer','energy efficiency','inverter','compressor','refrigerant','r410','r32','r22','r600','gas type'] },
                    { name:'Spec',                  keywords:['width','depth','height','size','dimension','weight','kg','mm','cm','indoor','outdoor','pipe length','drain'] },
                    { name:'Energy',                keywords:['rated power','power consumption','watt','wattage','voltage','ampere','amp','current','hz','frequency','energy rating','energy class','annual energy','kwh','power factor','starting current','standby power'] },
                    { name:'Air & Filtration',      keywords:['airflow','air flow','air volume','cfm','air throw','dehumidification','dehumidify','moisture','filter','air filter','dust filter','pm2.5','hepa','self cleaning','self-clean','auto clean','nano','plasma','ionizer','purif'] },
                    { name:'Features & Controls',   keywords:['wifi','wi-fi','smart','app','remote','auto restart','sleep mode','turbo cool','turbo heat','swing','louver','auto swing','horizontal swing','vertical swing','timer','weekly timer','follow me','quiet mode','eco mode','dry mode','fan mode','heat mode','cool mode','auto mode','display','led','temperature display'] },
                    { name:'Build & Materials',     keywords:['copper','condenser','evaporator','gold fin','hydrophilic','anti-corrosion','fin coating','coil','fan blade','cabinet','panel','drain pan'] },
                    { name:'Noise',                 keywords:['noise','db','dba','db(a)','sound level','sound pressure','indoor noise','outdoor noise','quiet','silent'] },
                    { name:'More',                  keywords:['model','certification','compliance','standard','installation','operating temperature','ambient temperature','working temperature','pre-charged','refrigerant charge'] },
                ],
                'Floor Standing AC': [
                    { name:'Cooling & Heating',     keywords:['cooling capacity','heating capacity','btu','btu/h','rated cooling','rated heating','cop','eer','seer','energy efficiency','inverter','compressor','refrigerant','r410','r32','r22','gas type'] },
                    { name:'Spec',                  keywords:['width','depth','height','size','dimension','weight','kg','mm','cm','pipe length','drain'] },
                    { name:'Energy',                keywords:['rated power','power consumption','watt','voltage','ampere','amp','current','hz','frequency','energy rating','kwh','power factor'] },
                    { name:'Air & Filtration',      keywords:['airflow','air flow','cfm','air throw','dehumidification','filter','air filter','pm2.5','hepa','self cleaning','auto clean','ionizer'] },
                    { name:'Features & Controls',   keywords:['wifi','smart','app','remote','auto restart','sleep mode','turbo','swing','louver','timer','eco mode','display','led'] },
                    { name:'Noise',                 keywords:['noise','db','dba','sound level','indoor noise','outdoor noise'] },
                    { name:'More',                  keywords:['model','certification','operating temperature','pre-charged'] },
                ],
                'Televisions': [
                    { name:'Display',               keywords:['screen size','display size','resolution','pixel','4k','8k','fhd','uhd','hd','panel','lcd','led','oled','qled','amoled','ips','va','brightness','nits','contrast','refresh rate','response time','viewing angle','aspect ratio','bezel','curved','hdr','hdr10','dolby vision','color depth','colour depth','backlight','local dimming','color gamut','colour gamut','dci','rec.709','rec.2020'] },
                    { name:'Smart & Connectivity',  keywords:['smart tv','android tv','google tv','tizen','webos','operating system','os','wifi','wi-fi','bluetooth','ethernet','lan','hdmi','usb','optical','headphone','av input','rf input','composite','component','miracast','airplay','cast','screen mirror','dlna','hbb tv','freeview','freesat','ci+','common interface'] },
                    { name:'Audio',                 keywords:['speaker','watt','audio output','sound','dolby','dts','atmos','surround','stereo','mono','subwoofer','tweeter','woofer','channel','audio system','sound mode','equalizer','bass','treble','bluetooth audio','optical out'] },
                    { name:'Performance',           keywords:['processor','cpu','gpu','chip','ram','memory','storage','rom','operating system','android version','response','motion','input lag'] },
                    { name:'Spec & Design',         keywords:['width','depth','height','size','dimension','weight','kg','mm','cm','with stand','without stand','vesa','wall mount','stand type','color','colour','finish','bezel','frame'] },
                    { name:'Energy',                keywords:['power consumption','watt','voltage','energy rating','standby power','annual energy','kwh'] },
                    { name:'More',                  keywords:['model','certification','compliance','ean','upc'] },
                ],
                'Sound Bar': [
                    { name:'Audio',         keywords:['channel','speaker','watt','rms','peak','frequency','response','impedance','sensitivity','driver','tweeter','woofer','subwoofer','surround','dolby','dts','atmos','spatial','stereo','mono','audio mode','sound mode','equalizer','bass','treble'] },
                    { name:'Connectivity',  keywords:['hdmi','arc','earc','optical','coaxial','aux','bluetooth','wifi','wi-fi','usb','rca','input','output','wireless','pairing'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Features',      keywords:['remote','app','smart','wall mount','pass through','4k pass','auto power','auto volume','night mode','dialogue','voice enhance','display','led'] },
                    { name:'Energy',        keywords:['power','watt','voltage','standby','energy'] },
                    { name:'More',          keywords:['model','certification','color','colour','finish'] },
                ],
                'Home Theaters': [
                    { name:'Audio',         keywords:['channel','speaker','watt','rms','frequency','response','impedance','driver','tweeter','woofer','subwoofer','surround','dolby','dts','atmos','stereo','sound mode','equalizer','bass','treble','satellite','centre speaker','center speaker'] },
                    { name:'Connectivity',  keywords:['hdmi','optical','coaxial','aux','bluetooth','wifi','usb','rca','input','output','wireless'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Features',      keywords:['remote','app','smart','display','karaoke','mic','microphone','fm radio','radio','usb playback','format','disc','dvd','blu-ray'] },
                    { name:'Energy',        keywords:['power','watt','voltage','standby','energy'] },
                    { name:'More',          keywords:['model','certification','color','colour'] },
                ],
                'Chest Freezers': [
                    { name:'Capacity & Cooling',        keywords:['capacity','volume','litre','liter','gross capacity','net capacity','usable capacity','freezing capacity','cooling','temperature','min temperature','max temperature','climate class','star rating','freezer rating','no frost','frost free','manual defrost','auto defrost'] },
                    { name:'Refrigeration System',      keywords:['compressor','inverter','refrigerant','r134a','r600','r290','gas','cooling system','refrigeration','evaporator','condenser','thermostat'] },
                    { name:'Spec',                      keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','external','internal','inner','outer'] },
                    { name:'Features',                  keywords:['lock','key','lock key','door','lid','hinge','basket','divider','drain','drain plug','drain valve','interior light','light','alarm','temperature alarm','door alarm','fast freeze','quick freeze','super freeze','holiday mode'] },
                    { name:'Energy',                    keywords:['power','watt','voltage','energy rating','energy class','annual energy','kwh','current','ampere','frequency','hz'] },
                    { name:'Noise',                     keywords:['noise','db','dba','sound level'] },
                    { name:'More',                      keywords:['model','certification','color','colour','finish','compliance'] },
                ],
                'Water Dispensers': [
                    { name:'Dispensing',    keywords:['hot water','cold water','normal water','room temperature','cooling capacity','heating capacity','temperature','hot temp','cold temp','tank','reservoir','bottle','bottle size','bottle neck','drip tray'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','freestanding','countertop','floor standing'] },
                    { name:'Features',      keywords:['child lock','safety lock','hot safety','tap type','tap color','faucet','spigot','led','display','night light','filter','filtration','uv','self clean','self-clean'] },
                    { name:'Energy',        keywords:['power','watt','voltage','energy','cooling power','heating power','ampere','current','frequency','hz'] },
                    { name:'More',          keywords:['model','certification','color','colour','finish','material'] },
                ],
                'Washing Machines': [
                    { name:'Capacity & Performance',    keywords:['capacity','load','kg','drum','drum volume','drum size','spin speed','rpm','wash programme','wash program','wash cycle','spin cycle','rinse','prewash','quick wash','eco wash','cotton','synthetic','delicate','wool','hand wash','number of programmes','number of programs'] },
                    { name:'Water & Efficiency',        keywords:['water consumption','water usage','water level','energy rating','energy class','annual energy','kwh','efficiency class'] },
                    { name:'Spec',                      keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','freestanding','built-in','integrated','front load','top load'] },
                    { name:'Features',                  keywords:['child lock','delay timer','time remaining','end of cycle','display','led display','digital display','door type','door opening','porthole','door seal','drum light','self clean drum','tub clean','steam','add garment','pause','quick wash','fuzzy logic','inverter motor','direct drive','smart','wifi','app','remote'] },
                    { name:'Noise',                     keywords:['noise','db','dba','noise washing','noise spinning','vibration'] },
                    { name:'Energy',                    keywords:['power','watt','voltage','ampere','amp','current','hz','frequency','energy rating','kwh','standby'] },
                    { name:'More',                      keywords:['model','certification','color','colour','finish','compliance'] },
                ],
                'Generators': [
                    { name:'Power Output',          keywords:['rated power','max power','peak power','running watts','starting watts','watt','kva','kw','power output','output power','ac output','dc output','overload'] },
                    { name:'Engine',                keywords:['engine','motor','displacement','cc','cylinder','ohv','ohc','rpm','speed','governor','air cooled','water cooled','4 stroke','4-stroke','two stroke','spark plug','valve','bore','stroke','compression'] },
                    { name:'Fuel',                  keywords:['fuel','petrol','gasoline','diesel','gas','lpg','dual fuel','fuel tank','tank capacity','fuel consumption','running time','runtime','run time','litre','liter','gallon','economy'] },
                    { name:'Outlets & Connectivity',keywords:['outlet','socket','plug','ac outlet','dc outlet','usb','12v','240v','120v','earthing','circuit breaker','avr','automatic voltage','voltage regulator','parallel'] },
                    { name:'Spec',                  keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','dry weight','net weight','gross weight'] },
                    { name:'Features',              keywords:['electric start','recoil start','remote start','auto start','ats','transfer switch','low oil','low oil shutdown','overload protection','hour meter','noise','db','dba','muffler','silencer','eco mode','inverter generator'] },
                    { name:'More',                  keywords:['model','certification','color','colour','compliance','warranty'] },
                ],
                'Inverters': [
                    { name:'Power',     keywords:['rated power','output power','peak power','surge power','continuous power','watt','kva','kw','input voltage','output voltage','dc input','ac output','efficiency','power factor','overload'] },
                    { name:'Battery',   keywords:['battery','battery type','battery voltage','battery capacity','ah','charge current','charging','battery charger','low battery','battery protection','deep cycle','lead acid','lithium','agm','gel'] },
                    { name:'Spec',      keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Features',  keywords:['pure sine wave','modified sine wave','display','lcd','led','protection','short circuit','overload protection','thermal protection','temperature','cooling fan','remote','solar','mppt','charger controller','transfer time','transfer switch','ups','backup'] },
                    { name:'Energy',    keywords:['frequency','hz','voltage','current','ampere','power consumption','standby','idle consumption'] },
                    { name:'More',      keywords:['model','certification','color','colour','compliance','warranty'] },
                ],
                'Batteries': [
                    { name:'Electrical',    keywords:['voltage','capacity','ah','ampere hour','watt hour','wh','kwh','charge rate','discharge rate','c-rate','internal resistance','self discharge','cycle life','cycles','depth of discharge','dod','state of charge','soc'] },
                    { name:'Build',         keywords:['type','chemistry','lithium','lead acid','agm','gel','vrla','lifepo4','lion','nickel','terminal','terminal type','positive','negative','polarity','electrolyte','sealed','flooded','maintenance free'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Performance',   keywords:['cold cranking','cca','cranking','reserve capacity','rc','standby','float','operating temperature','charge temperature','discharge temperature','shelf life'] },
                    { name:'More',          keywords:['model','certification','brand','warranty','compliance'] },
                ],
                'Juicers': [
                    { name:'Performance',   keywords:['power','watt','rpm','speed','motor','extraction','yield','juicing','centrifugal','masticating','slow juicer','cold press','twin gear'] },
                    { name:'Capacity',      keywords:['capacity','volume','litre','liter','jar','jug','cup','bowl','feed tube','chute','pulp container','pulp bin'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Features',      keywords:['filter','mesh','strainer','reverse','anti-drip','safety lock','overload','dishwasher safe','bpa','bpa free','material','stainless','plastic','display','speed setting','pulse'] },
                    { name:'Energy',        keywords:['voltage','frequency','hz','current','power consumption'] },
                    { name:'More',          keywords:['model','color','colour','certification','compliance'] },
                ],
                'Blenders': [
                    { name:'Performance',   keywords:['power','watt','rpm','speed','motor','blade','blending','crushing','grinding','pulse','turbo'] },
                    { name:'Capacity',      keywords:['capacity','volume','litre','liter','jar','jug','cup','ml'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size'] },
                    { name:'Features',      keywords:['speed setting','program','preset','safety lock','overload','dishwasher safe','bpa','bpa free','material','stainless','glass','plastic','display','led','anti-slip','suction','jar type','lid','seal'] },
                    { name:'Energy',        keywords:['voltage','frequency','hz','current','power consumption'] },
                    { name:'More',          keywords:['model','color','colour','certification'] },
                ],
                'Standing Fans': [
                    { name:'Performance',   keywords:['speed','rpm','airflow','cfm','air volume','wind speed','blade','blade span','blade diameter','number of blades','oscillation','rotation','angle','tilt'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','pole','stand','base'] },
                    { name:'Features',      keywords:['speed setting','timer','remote','touch','control','display','led','sleep mode','natural wind','breeze mode','ionizer','purifier','filter','child safety','grill','guard','material'] },
                    { name:'Energy',        keywords:['power','watt','voltage','frequency','hz','current','energy','standby'] },
                    { name:'More',          keywords:['model','color','colour','certification','noise','db'] },
                ],
                'Ceiling Fan': [
                    { name:'Performance',   keywords:['speed','rpm','airflow','cfm','air volume','blade','blade span','blade length','blade angle','number of blades','sweep','motor'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','mounting','downrod','canopy'] },
                    { name:'Lighting',      keywords:['light','led','bulb','watt light','lumen','color temperature','kelvin','dimmable','light kit','lamp','fixture'] },
                    { name:'Features',      keywords:['speed setting','timer','remote','wall control','smart','wifi','app','sleep mode','reverse','winter mode','summer mode','material','finish','blade material','blade finish'] },
                    { name:'Energy',        keywords:['power','watt','voltage','frequency','hz','current','energy','standby'] },
                    { name:'More',          keywords:['model','color','colour','certification','noise','db'] },
                ],
                'Tower Fan': [
                    { name:'Performance',   keywords:['speed','rpm','airflow','cfm','air volume','wind speed','oscillation','rotation','angle','bladeless','blade'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','base'] },
                    { name:'Features',      keywords:['speed setting','timer','remote','touch','control','display','led','sleep mode','natural wind','breeze','ionizer','purifier','filter','hepa','uv','child safety','material'] },
                    { name:'Energy',        keywords:['power','watt','voltage','frequency','hz','current','energy','standby'] },
                    { name:'More',          keywords:['model','color','colour','certification','noise','db'] },
                ],
                'Wall Fan': [
                    { name:'Performance',   keywords:['speed','rpm','airflow','cfm','air volume','blade','blade span','blade diameter','number of blades','oscillation','tilt','angle'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','mounting','bracket','wall bracket'] },
                    { name:'Features',      keywords:['speed setting','timer','remote','control','display','sleep mode','material','grill','guard','pull cord','chain'] },
                    { name:'Energy',        keywords:['power','watt','voltage','frequency','hz','current','energy'] },
                    { name:'More',          keywords:['model','color','colour','certification','noise','db'] },
                ],
                'Electric Iron': [
                    { name:'Performance',   keywords:['power','watt','steam','steam rate','steam output','shot of steam','vertical steam','dry iron','steam iron','temperature','heat','soleplate','thermostat'] },
                    { name:'Soleplate',     keywords:['soleplate','plate','ceramic','stainless','titanium','non-stick','coating','steam hole','steam vent','glide'] },
                    { name:'Water & Steam', keywords:['water tank','water capacity','tank capacity','ml','anti-calc','anti-scale','calc clean','self clean','drip stop','anti-drip'] },
                    { name:'Spec',          keywords:['width','depth','height','dimension','weight','kg','mm','cm','size','cord','cord length','cable','swivel cord'] },
                    { name:'Features',      keywords:['auto off','auto shut-off','safety cut','indicator','light','led','spray','mist','boost','fabric guide','setting'] },
                    { name:'Energy',        keywords:['voltage','frequency','hz','current','power consumption'] },
                    { name:'More',          keywords:['model','color','colour','certification'] },
                ],
                '__default__': [
                    { name:'Dimensions & Weight',   keywords:['width','depth','height','size','dimension','length','thickness','diameter','weight','kg','mm','cm','capacity','volume','litre','liter'] },
                    { name:'Power & Energy',        keywords:['power','watt','wattage','voltage','volt','ampere','amp','current','hz','frequency','energy','kwh','rated power','power consumption','standby','efficiency','energy rating','plug','socket','cable'] },
                    { name:'Performance',           keywords:['speed','rpm','motor','compressor','inverter','output','capacity','cooling','heating','airflow','cfm','btu','cop','eer'] },
                    { name:'Connectivity',          keywords:['wifi','wi-fi','bluetooth','usb','hdmi','ethernet','lan','wireless','nfc','optical','aux','input','output','port','socket','connectivity'] },
                    { name:'Display & Audio',       keywords:['screen','display','resolution','panel','brightness','contrast','speaker','watt audio','dolby','dts','sound','audio','db','frequency response'] },
                    { name:'Features',              keywords:['smart','app','remote','timer','lock','child lock','display','led','auto','mode','setting','program','control','filter','clean','self clean','safety','protection'] },
                    { name:'Build & Design',        keywords:['color','colour','finish','material','coating','texture','stainless','plastic','glass','metal','painted','chrome','brushed','polished','matte','gloss'] },
                    { name:'More',                  keywords:['model','certification','compliance','warranty','standard','note','remark','ean','upc','sku'] },
                ],
            };

            function getGroups() {
                if (groupsByCategory[PRODUCT_SUBCATEGORY]) return groupsByCategory[PRODUCT_SUBCATEGORY];
                if (groupsByCategory[PRODUCT_CATEGORY])    return groupsByCategory[PRODUCT_CATEGORY];
                return groupsByCategory['__default__'];
            }
            const groups = getGroups();

            function getKey(row) { const th = row.querySelector('th'); return th ? th.textContent.toLowerCase().trim() : ''; }
            function scoreMatch(key, keywords) {
                let score = 0;
                keywords.forEach(kw => {
                    if (key.includes(kw)) {
                        score += kw.length;
                        if (new RegExp('\\b' + kw.replace(/[()]/g, '\\$&') + '\\b', 'i').test(key)) score += 10;
                    }
                });
                return score;
            }

            groups.forEach(g => { orderedGroupNames.push(g.name); grouped.set(g.name, []); });

            rows.forEach(row => {
                const key = getKey(row);
                let best = null, bestS = 0;
                groups.forEach(g => { const s = scoreMatch(key, g.keywords); if (s > bestS) { bestS = s; best = g; } });
                const name = best ? best.name : 'General';
                if (!grouped.has(name)) { orderedGroupNames.push(name); grouped.set(name, []); }
                grouped.get(name).push(row);
            });
        }

        /* ── SHARED RENDERER (identical output for both modes) ── */
        const style = document.createElement('style');
        style.textContent = `
            .spec-v2-wrap { font-family:'DM Sans',sans-serif; font-size:13.5px; border:1px solid var(--border,#e5e7eb); border-radius:8px; overflow:hidden; }
            .spec-v2-group { display:grid; grid-template-columns:200px 1fr; border-bottom:1px solid var(--border,#e5e7eb); }
            .spec-v2-group:last-child { border-bottom:none; }
            .spec-v2-heading { padding:16px; display:flex; align-items:center; background:var(--surf2,#f9fafb); border-right:1px solid var(--border,#e5e7eb); cursor:pointer; user-select:none; transition:background .15s; }
            .spec-v2-heading:hover { background:var(--g50,#f0fdf4); }
            .spec-v2-heading-name { font-family:var(--fh); font-size:0.88rem; font-weight:800; color:var(--ink,#111); line-height:1.3; }
            .spec-v2-group.open .spec-v2-heading-name { color:var(--g700,#15803d); }
            .spec-v2-right { position:relative; }
            .spec-v2-toggle { position:absolute; top:50%; right:14px; transform:translateY(-50%); width:28px; height:28px; border-radius:50%; border:1.5px solid var(--border,#e5e7eb); background:var(--surface,#fff); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background .2s,border-color .2s; color:var(--ink3,#9ca3af); z-index:1; }
            .spec-v2-toggle svg { width:11px; height:11px; display:block; transition:transform .35s cubic-bezier(0.4,0,0.2,1); }
            .spec-v2-right:hover .spec-v2-toggle { border-color:var(--g300,#86efac); background:var(--g50,#f0fdf4); color:var(--g600,#16a34a); }
            .spec-v2-group.open .spec-v2-toggle { background:var(--g500,#22c55e); border-color:var(--g500,#22c55e); color:#fff; }
            .spec-v2-group.open .spec-v2-toggle svg { transform:rotate(180deg); }
            .spec-v2-rows-wrap { overflow:hidden; max-height:0; transition:max-height .38s cubic-bezier(0.4,0,0.2,1); }
            .spec-v2-group.open .spec-v2-rows-wrap { max-height:9999px; }
            .spec-v2-collapsed-bar { height:48px; display:flex; align-items:center; padding:0 52px 0 14px; font-size:12px; color:var(--ink3,#9ca3af); font-style:italic; }
            .spec-v2-group.open .spec-v2-collapsed-bar { display:none; }
            .spec-v2-row { display:grid; grid-template-columns:38% 62%; border-bottom:1px solid var(--border,#e5e7eb); }
            .spec-v2-row:last-child { border-bottom:none; }
            .spec-v2-row:nth-child(odd)  { background:var(--surf2,#f9fafb); }
            .spec-v2-row:nth-child(even) { background:var(--surface,#fff); }
            .spec-v2-key { padding:10px 14px; color:var(--ink3,#6b7280); font-weight:400; font-size:13px; border-right:1px solid var(--border,#e5e7eb); line-height:1.5; }
            .spec-v2-val { padding:10px 14px; color:var(--ink,#111); font-weight:500; font-size:13px; line-height:1.5; }
            @media(max-width:699px){.spec-v2-group{grid-template-columns:130px 1fr}.spec-v2-heading{padding:12px}.spec-v2-heading-name{font-size:0.78rem}.spec-v2-key,.spec-v2-val{padding:9px 10px;font-size:12px}.spec-v2-toggle{width:24px;height:24px;right:10px}}
            @media(max-width:479px){.spec-v2-group{grid-template-columns:1fr}.spec-v2-heading{border-right:none;border-bottom:1px solid var(--border,#e5e7eb);padding:11px 50px 11px 14px;position:relative}.spec-v2-toggle{position:absolute;top:50%;transform:translateY(-50%)}.spec-v2-rows-wrap{border-left:none}}
        `;
        document.head.appendChild(style);

        const chevronSVG = `<svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 4L6 8L10 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
        const wrap = document.createElement('div');
        wrap.className = 'spec-v2-wrap';

        let isFirst = true;
        orderedGroupNames.forEach(name => {
            const groupRows = grouped.get(name) || [];
            if (groupRows.length === 0) return;

            const groupEl = document.createElement('div');
            groupEl.className = 'spec-v2-group' + (isFirst ? ' open' : '');

            const heading = document.createElement('div');
            heading.className = 'spec-v2-heading';
            heading.innerHTML = `<span class="spec-v2-heading-name">${name}</span>`;

            const right = document.createElement('div');
            right.className = 'spec-v2-right';

            const toggle = document.createElement('div');
            toggle.className = 'spec-v2-toggle';
            toggle.setAttribute('role', 'button');
            toggle.setAttribute('aria-label', 'Toggle section');
            toggle.innerHTML = chevronSVG;

            const collapsedBar = document.createElement('div');
            collapsedBar.className = 'spec-v2-collapsed-bar';
            collapsedBar.textContent = `${groupRows.length} item${groupRows.length !== 1 ? 's' : ''}`;

            const rowsWrap = document.createElement('div');
            rowsWrap.className = 'spec-v2-rows-wrap';
            if (isFirst) rowsWrap.style.maxHeight = '9999px';

            groupRows.forEach(r => {
                const th = r.querySelector('th'), td = r.querySelector('td');
                if (!th || !td) return;
                const row = document.createElement('div');
                row.className = 'spec-v2-row';
                row.innerHTML = `<div class="spec-v2-key">${th.textContent.trim()}</div><div class="spec-v2-val">${td.textContent.trim()}</div>`;
                rowsWrap.appendChild(row);
            });

            right.appendChild(toggle);
            right.appendChild(collapsedBar);
            right.appendChild(rowsWrap);

            function doToggle() {
                const isOpen = groupEl.classList.contains('open');
                if (isOpen) {
                    rowsWrap.style.maxHeight = rowsWrap.scrollHeight + 'px';
                    requestAnimationFrame(() => { rowsWrap.style.maxHeight = '0'; });
                    groupEl.classList.remove('open');
                } else {
                    groupEl.classList.add('open');
                    rowsWrap.style.maxHeight = rowsWrap.scrollHeight + 'px';
                    rowsWrap.addEventListener('transitionend', () => {
                        if (groupEl.classList.contains('open')) rowsWrap.style.maxHeight = '9999px';
                    }, { once: true });
                }
            }

            heading.addEventListener('click', doToggle);
            toggle.addEventListener('click', doToggle);
            groupEl.appendChild(heading);
            groupEl.appendChild(right);
            wrap.appendChild(groupEl);
            isFirst = false;
        });

        container.appendChild(wrap);
    })();

    /* ── Tabs ── */
    function activateTab(tabId) {
        const btn = document.querySelector('.pd-tab-btn[data-tab="' + tabId + '"]');
        if (!btn) return false;
        document.querySelectorAll('.pd-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.pd-tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(tabId)?.classList.add('active');
        return true;
    }
    document.querySelectorAll('.pd-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => activateTab(btn.dataset.tab));
    });

    /* ── Top star badge → jump to Reviews tab ── */
    const headStars = document.getElementById('pdHeadStars');
    function goToReviews() {
        if (activateTab('pd-reviews')) {
            document.querySelector('.pd-tabs-wrap')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    if (headStars) {
        headStars.addEventListener('click', goToReviews);
        headStars.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); goToReviews(); }
        });
    }

     (function openTabFromHash() {
        const hash = window.location.hash.replace('#', '').toLowerCase();
        if (!hash) return;
        const target = document.querySelector('.pd-tab-btn[data-tab="pd-' + hash + '"]');
        if (target) {
            target.click();
            setTimeout(() => {
                document.querySelector('.pd-tabs-wrap')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    })();

    /* ── Thumbnail gallery + arrow nav ── */
    const mainImg  = document.getElementById('pdMainImg');
    const thumbs   = Array.from(document.querySelectorAll('.pd-thumb'));
    const prevBtn  = document.getElementById('pdNavPrev');
    const nextBtn  = document.getElementById('pdNavNext');

    function activeIdx() { return thumbs.findIndex(t => t.classList.contains('active')); }

    function goTo(idx) {
        if (idx < 0 || idx >= thumbs.length) return;
        thumbs.forEach(t => t.classList.remove('active'));
        thumbs[idx].classList.add('active');
        if (mainImg) mainImg.src = thumbs[idx].dataset.full;
        thumbs[idx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
        updateNav();
    }

    function updateNav() {
        const i = activeIdx();
        if (prevBtn) prevBtn.disabled = i <= 0;
        if (nextBtn) nextBtn.disabled = i >= thumbs.length - 1;
        const showArrows = thumbs.length > 1;
        if (prevBtn) prevBtn.style.display = showArrows ? '' : 'none';
        if (nextBtn) nextBtn.style.display = showArrows ? '' : 'none';
    }

    thumbs.forEach((thumb, idx) => {
        thumb.addEventListener('click', () => goTo(idx));
    });
    prevBtn?.addEventListener('click', () => goTo(activeIdx() - 1));
    nextBtn?.addEventListener('click', () => goTo(activeIdx() + 1));
    updateNav();

    /* ── Image zoom ── */
    const ZOOM_RATIO = 1.5;
    const zoomContainer = document.getElementById('pdZoomContainer');
    if (zoomContainer && mainImg) {
        zoomContainer.addEventListener('mouseenter', () => { mainImg.style.transition = 'transform .2s ease'; mainImg.style.transform = `scale(${ZOOM_RATIO})`; });
        zoomContainer.addEventListener('mouseleave', () => { mainImg.style.transition = 'transform .2s ease'; mainImg.style.transform = 'scale(1)'; mainImg.style.transformOrigin = 'center center'; });
        zoomContainer.addEventListener('mousemove', e => {
            const rect = zoomContainer.getBoundingClientRect();
            mainImg.style.transformOrigin = `${((e.clientX - rect.left) / rect.width) * 100}% ${((e.clientY - rect.top) / rect.height) * 100}%`;
        });
    }

    /* ── Quantity stepper ── */
    const maxStock = <?php echo e($product->stock); ?>;
    let qty = 1;
    const qtyVal   = document.getElementById('pd-qtyVal');
    const minusBtn = document.getElementById('pd-qtyMinus');
    const plusBtn  = document.getElementById('pd-qtyPlus');
    function updateQty(n) {
        qty = Math.min(Math.max(1, n), maxStock || 1);
        qtyVal.textContent = qty;
        minusBtn.disabled = qty <= 1;
        plusBtn.disabled  = qty >= maxStock;
    }
    minusBtn?.addEventListener('click', () => updateQty(qty - 1));
    plusBtn?.addEventListener('click',  () => updateQty(qty + 1));
    updateQty(1);

    /* ── Add to Cart / Buy Now ──
       FIX: this product's requires_truck flag (from the Product model's
       requires_truck accessor — subcategory → category fallback) is now
       captured once and passed as the 5th arg on every addToCart call, so
       checkout's truck-fee calculation actually sees it. */
    const productId            = '<?php echo e($product->id); ?>';
    const productName          = '<?php echo e(addslashes($product->name)); ?>';
    const productImg           = '<?php echo e($product->images->first() ? asset("storage/" . $product->images->first()->image_url) : ""); ?>';
    const basePrice            = <?php echo e($product->sell_price); ?>;
    const productRequiresTruck = <?php echo e($product->requires_truck ? 'true' : 'false'); ?>;

    function doAddToCart() {
        if (!window.addToCart) return;
        for (let i = 0; i < qty; i++) {
            window.addToCart(productId, productName, basePrice, productImg, productRequiresTruck);
        }
    }
    const atcBtn = document.getElementById('pd-atcBtn');
    atcBtn?.addEventListener('click', () => {
        doAddToCart();
        atcBtn.innerHTML = '<i class="fas fa-check"></i> Added!';
        atcBtn.style.background = 'var(--g700)';
        setTimeout(() => { atcBtn.innerHTML = '<i class="fas fa-shopping-bag"></i> Add to Cart'; atcBtn.style.background = ''; }, 1800);
    });
    const buyBtn = document.getElementById('pd-buyBtn');
    buyBtn?.addEventListener('click', () => {
        doAddToCart();
        buyBtn.innerHTML = '<i class="fas fa-check"></i> Redirecting…';
        setTimeout(() => { window.location.href = '/cart'; }, 1200);
    });

    /* ── Related drag scroll ── */
    const relRow = document.getElementById('pd-relatedRow');
    if (relRow) {
        let isDragging = false, startX, scrollLeft;
        relRow.addEventListener('mousedown', e => { isDragging = true; startX = e.pageX - relRow.offsetLeft; scrollLeft = relRow.scrollLeft; });
        relRow.addEventListener('mouseleave', () => { isDragging = false; });
        relRow.addEventListener('mouseup',    () => { isDragging = false; });
        relRow.addEventListener('mousemove',  e => { if (!isDragging) return; e.preventDefault(); relRow.scrollLeft = scrollLeft - (e.pageX - relRow.offsetLeft - startX); });
    }

    /* ── Reviews show more / less ── */
    (function reviewsCollapse() {
        const list = document.getElementById('reviewsList');
        const btn  = document.getElementById('reviewsShowMoreBtn');
        if (!list || !btn) return;
        const VISIBLE = 3;

        function refresh() {
            const count = list.querySelectorAll('.review-card').length;
            if (count > VISIBLE) {
                btn.style.display = 'inline-flex';
                if (!list.classList.contains('reviews-expanded')) {
                    btn.querySelector('.btn-label').textContent = `Show all reviews (${count})`;
                }
            } else {
                btn.style.display = 'none';
                list.classList.remove('reviews-collapsed');
            }
        }

        btn.addEventListener('click', () => {
            const expanded = list.classList.toggle('reviews-expanded');
            list.classList.toggle('reviews-collapsed', !expanded);
            const icon  = btn.querySelector('i');
            const label = btn.querySelector('.btn-label');
            if (expanded) {
                icon.className = 'fas fa-chevron-up';
                label.textContent = 'Show fewer reviews';
            } else {
                icon.className = 'fas fa-chevron-down';
                const count = list.querySelectorAll('.review-card').length;
                label.textContent = `Show all reviews (${count})`;
                document.getElementById('pd-reviews')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        refresh();
        // expose so the AJAX submit can re-check after prepending a new card
        window.refreshReviewsCollapse = refresh;
    })();

    /* ── Live rating summary (recompute bars + score after a new review) ── */
    (function ratingSummaryUpdater() {
        const summary = document.getElementById('ratingSummary');
        if (!summary) return;

        let dist;
        try { dist = JSON.parse(summary.dataset.dist || '{}'); } catch { dist = {}; }
        [5,4,3,2,1].forEach(s => { dist[s] = parseInt(dist[s]) || 0; });
        let total = parseInt(summary.dataset.count) || 0;

        function starsHtml(avg) {
            const full = Math.floor(avg);
            const frac = avg - full;
            const half = frac >= 0.25 && frac < 0.75;
            const roundUp = frac >= 0.75;
            const fullCount = full + (roundUp ? 1 : 0);
            const empty = 5 - fullCount - (half ? 1 : 0);
            let html = '';
            for (let i = 0; i < fullCount; i++) html += '<i class="fas fa-star"></i>';
            if (half) html += '<i class="fas fa-star-half-alt"></i>';
            for (let i = 0; i < empty; i++) html += '<i class="far fa-star"></i>';
            return html;
        }

        window.addReviewToSummary = function(rating) {
            rating = parseInt(rating);
            if (rating >= 1 && rating <= 5) { dist[rating]++; total++; }

            let sum = 0;
            [5,4,3,2,1].forEach(s => sum += s * dist[s]);
            const avg = total > 0 ? sum / total : 0;

            const scoreEl = document.getElementById('ratingSummaryScore');
            const starsEl = document.getElementById('ratingSummaryStars');
            const countEl = document.getElementById('ratingSummaryCount');
            if (scoreEl) scoreEl.textContent = avg.toFixed(1);
            if (starsEl) starsEl.innerHTML = starsHtml(avg);
            if (countEl) countEl.textContent = total + ' review(s)';

            [5,4,3,2,1].forEach(s => {
                const row = summary.querySelector(`.rating-bar-row[data-star="${s}"]`);
                if (!row) return;
                const pct = total > 0 ? Math.round((dist[s] / total) * 100) : 0;
                const fill = row.querySelector('.rating-bar-fill');
                const cnt  = row.querySelector('.rating-bar-count');
                if (fill) fill.style.width = pct + '%';
                if (cnt)  cnt.textContent = dist[s];
            });

            // Reveal if it was hidden (product had zero reviews on load)
            if (summary.style.display === 'none') summary.style.display = '';

            // Keep the top-of-page star badge in sync
            const headStars = document.querySelector('.pd-stars');
            if (headStars) {
                const span = headStars.querySelector('span');
                headStars.innerHTML = starsHtml(avg) + (span ? `<span>(${total})</span>` : '');
            }
        };
    })();

    /* ── Review form ── */
    const reviewForm = document.getElementById('pd-reviewForm');
    if (reviewForm) {
        const formMsg      = document.getElementById('pd-formMessage');
        const ratingError  = document.getElementById('ratingError');
        const commentError = document.getElementById('commentError');
        const ratingText   = document.getElementById('ratingText');
        const charCount    = document.getElementById('charCount');
        const reviewComment= document.getElementById('reviewComment');
        const submitBtn    = document.getElementById('reviewSubmitBtn');
        const starRating   = document.getElementById('starRating');
        const ratingLabels = { 1:'Poor', 2:'Fair', 3:'Good', 4:'Very Good', 5:'Excellent' };

        function showFieldError(el, input, msg) { if(el) el.textContent = msg; if(input) input.classList.add('invalid'); }
        function clearFieldError(el, input) { if(el) el.textContent = ''; if(input) input.classList.remove('invalid'); }
        function showFormMessage(type, html) {
            if(!formMsg) return;
            const icons = { success:'fa-check-circle', error:'fa-exclamation-circle', warning:'fa-exclamation-triangle', info:'fa-info-circle' };
            formMsg.style.display = 'flex';
            formMsg.className = 'form-message ' + type;
            formMsg.innerHTML = `<i class="fas ${icons[type]||icons.error}"></i><span>${html}</span>`;
            formMsg.scrollIntoView({ behavior:'smooth', block:'nearest' });
        }
        function hideFormMessage() { if(formMsg){ formMsg.style.display='none'; formMsg.className='form-message'; } }
        function setLoading(on) {
            if(!submitBtn) return;
            submitBtn.disabled = on;
            const bt = submitBtn.querySelector('.btn-text');
            const bs = submitBtn.querySelector('.btn-spinner');
            if(bt) bt.style.display = on ? 'none' : '';
            if(bs) bs.style.display = on ? 'inline-flex' : 'none';
        }
        function escHtml(t) { const d = document.createElement('div'); d.textContent = t||''; return d.innerHTML; }

        // Collapse + remove the write-a-review form. Safe to call once.
        let formHidden = false;
        function hideReviewForm(delay) {
            if (formHidden) return;
            formHidden = true;
            const formWrap = document.getElementById('reviewFormWrap') || reviewForm.closest('.review-form-wrap');
            if (!formWrap) return;
            formWrap.style.overflow = 'hidden';
            formWrap.style.transition = 'opacity .3s ease, max-height .45s ease, margin .45s ease, padding .45s ease, border-width .45s ease';
            formWrap.style.maxHeight = formWrap.scrollHeight + 'px';
            setTimeout(() => {
                formWrap.style.maxHeight   = '0';
                formWrap.style.opacity     = '0';
                formWrap.style.margin      = '0';
                formWrap.style.padding     = '0';
                formWrap.style.borderWidth = '0';
                setTimeout(() => formWrap.remove(), 450);
            }, delay ?? 1400);
        }

        starRating?.querySelectorAll('input[name="rating"]').forEach(r => {
            r.addEventListener('change', function(){
                const v = parseInt(this.value);
                if(ratingText){ ratingText.textContent = ratingLabels[v]||''; ratingText.classList.add('active'); ratingText.style.color=''; }
                clearFieldError(ratingError, null); starRating.classList.remove('invalid'); hideFormMessage();
            });
        });
        reviewComment?.addEventListener('input', function(){
            const len = this.value.length, max = 1000;
            if(charCount){ charCount.textContent = len+'/'+max; charCount.classList.toggle('warning',len>max*0.9); charCount.classList.toggle('danger',len>=max); }
            clearFieldError(commentError, this); hideFormMessage();
        });
        reviewComment?.addEventListener('focus', function(){ clearFieldError(commentError, this); });

        reviewForm.addEventListener('submit', async function(e){
            e.preventDefault();
            hideFormMessage();
            clearFieldError(ratingError, null);
            clearFieldError(commentError, reviewComment);
            starRating?.classList.remove('invalid');

            const fd      = new FormData(reviewForm);
            const rating  = fd.get('rating');
            const comment = (fd.get('comment')||'').trim();
            let hasError  = false;

            if(!rating){ showFieldError(ratingError,null,'Please select a star rating.'); starRating?.classList.add('invalid'); if(ratingText){ratingText.textContent='Rating required';ratingText.classList.remove('active');ratingText.style.color='#dc2626';} hasError=true; }
            if(!comment){ showFieldError(commentError,reviewComment,'Please write your review.'); hasError=true; }
            else if(comment.length<10){ showFieldError(commentError,reviewComment,`Review too short — min 10 chars (you have ${comment.length}).`); hasError=true; }
            if(hasError){ showFormMessage('error','Please fix the highlighted fields.'); document.querySelector('.field-error:not(:empty)')?.scrollIntoView({behavior:'smooth',block:'center'}); return; }

            setLoading(true);
            try {
                const res = await fetch(reviewForm.action, {
                    method:'POST', body:fd,
                    headers:{ 'X-Requested-With':'XMLHttpRequest','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'' }
                });
                let result;
                try { result = await res.json(); } catch { throw new Error('Unexpected server response.'); }

                // Debug: inspect what the server actually returned
                console.log('[review submit] status', res.status, 'body', result);

                // Treat as success if the server says so in any common shape
                const isSuccess = res.ok && (
                    result.success === true ||
                    result.status === 'success' ||
                    !!result.review ||
                    (!!result.rating && !result.errors)
                );

                if(isSuccess){
                    showFormMessage('success', result.message||'Review submitted!');

                    // Prepend the new review card
                    const rl = document.getElementById('reviewsList');
                    const nm = document.getElementById('noReviewsMsg');
                    const newRating  = result.rating  ?? (result.review && result.review.rating)  ?? rating;
                    const newComment = result.comment ?? result.content ?? (result.review && (result.review.comment || result.review.content)) ?? comment;
                    const newName    = result.user_name ?? (result.review && result.review.user_name) ?? 'You';
                    if(rl){
                        if(nm) nm.remove();
                        const card = document.createElement('div'); card.className='review-card';
                        card.style.cssText='opacity:0;transform:translateY(10px);transition:all .35s ease;';
                        const stars = [1,2,3,4,5].map(s=>`<i class="${s<=(newRating||0)?'fas':'far'} fa-star"></i>`).join('');
                        card.innerHTML = `<div class="review-header"><div class="reviewer-avatar">${escHtml((newName||'U').charAt(0).toUpperCase())}</div><div><div class="reviewer-name">${escHtml(newName)}</div><div class="review-stars">${stars}<span class="review-date">Just now</span></div></div></div><p class="review-text">${escHtml(newComment||'')}</p>`;
                        rl.insertBefore(card, rl.firstChild);
                        requestAnimationFrame(()=>{ card.style.opacity='1'; card.style.transform='translateY(0)'; });
                    }

                    // Bump counters
                    const ce = document.getElementById('reviewCount'), tc = document.getElementById('reviewTabCount');
                    if(ce){ const nc = Math.max(0,(parseInt(ce.textContent)||0)+1); ce.textContent=nc; if(tc) tc.textContent=nc; }

                    // Recompute the rating-summary bars/score (and reveal it if it was hidden)
                    window.addReviewToSummary?.(newRating);

                    // Re-check the show more/less control with the new card present
                    window.refreshReviewsCollapse?.();

                    // Collapse the form away; the rating bars + reviews list slide up to take its place
                    hideReviewForm(1400);

                } else if(res.status===401){ showFormMessage('error','Please <a href="/login">log in</a> to review.'); }
                else if(res.status===403){ showFormMessage('warning',result.message||'You can only review purchased products.'); }
                else if(res.status===422 && result.message?.toLowerCase().includes('already')){
                    // Already reviewed — no second review allowed, so remove the form too
                    showFormMessage('info', result.message);
                    hideReviewForm(2000);
                }
                else if(res.status===422 && result.errors){
                    const msgs=[];
                    if(result.errors.rating){ showFieldError(ratingError,null,result.errors.rating[0]); starRating?.classList.add('invalid'); msgs.push('Rating: '+result.errors.rating[0]); }
                    if(result.errors.comment){ showFieldError(commentError,reviewComment,result.errors.comment[0]); msgs.push('Review: '+result.errors.comment[0]); }
                    showFormMessage('error','Please fix:<br>'+msgs.map(m=>'• '+escHtml(m)).join('<br>'));
                } else { showFormMessage('error',escHtml(result.message||'Something went wrong.')); }
            } catch(err){ showFormMessage('error',escHtml(err.message)||'Network error.'); }
            finally { setLoading(false); }
        });
    }

    window.updatePricesOnPage?.();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/products/show.blade.php ENDPATH**/ ?>