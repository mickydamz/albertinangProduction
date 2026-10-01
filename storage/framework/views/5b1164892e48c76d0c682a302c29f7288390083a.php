

<?php $__env->startSection('title', $categoryName . ' – Albertina Nigeria'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .cat-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 16px 16px 60px;
    }

    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        font-size: 13px;
        color: var(--ink3);
        margin-bottom: 14px;
    }
    .breadcrumb a { color: var(--ink3); transition: color .2s; text-decoration: none; }
    .breadcrumb a:hover { color: var(--g600); }
    .breadcrumb span { color: var(--ink); font-weight: 600; }
    .breadcrumb i { font-size: 9px; color: var(--border2); }

    .cat-layout {
        display: flex;
        gap: 18px;
        align-items: flex-start;
    }

    /* ── Sidebar ─────────────────────────────────────────────────────── */
    .cat-sidebar {
        flex: 0 0 250px;
        width: 250px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 0;
        overflow: hidden;
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }

    .sidebar-head {
        padding: 14px 16px 12px;
        background: #fff;
        border-bottom: 2px solid var(--border);
        color: var(--ink);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--font-head);
        font-size: 15px;
        font-weight: 800;
        letter-spacing: -.2px;
        flex-shrink: 0;
    }
    .sidebar-close-btn {
        color: var(--ink3); font-size: 17px; padding: 2px;
        transition: transform .2s, color .2s; display: none;
        background: none; border: none; cursor: pointer;
    }
    .sidebar-close-btn:hover { transform: rotate(90deg); color: var(--ink); }

    .sidebar-body {
        padding: 0;
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .sidebar-body::-webkit-scrollbar { width: 4px; }
    .sidebar-body::-webkit-scrollbar-track { background: transparent; }
    .sidebar-body::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

    .sidebar-footer {
        flex-shrink: 0;
        padding: 12px 16px;
        background: #fff;
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* ── Filter groups ───────────────────────────────────────────────── */
    .filter-group {
        border-bottom: 1px solid var(--border);
    }
    .filter-group:last-of-type { border-bottom: none; }

    .filter-group-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--ink);
        cursor: pointer;
        padding: 14px 16px 14px;
        margin-bottom: 0;
        user-select: none;
        letter-spacing: -.1px;
        transition: color .15s;
    }
    .filter-group-head:hover { color: var(--g700); }
    .filter-group-head i { font-size: 11px; color: var(--ink4); transition: transform .2s, color .15s; }
    .filter-group-head:hover i { color: var(--g600); }
    .filter-group.collapsed .filter-group-head { padding-bottom: 14px; }
    .filter-group.collapsed .filter-group-head i { transform: rotate(-90deg); }
    .filter-group--allempty .filter-group-head { opacity: .45; pointer-events: none; }

    .filter-options {
        overflow: hidden;
        transition: max-height .28s cubic-bezier(.4,0,.2,1), opacity .2s;
        max-height: 600px;
        opacity: 1;
        padding: 0 16px 14px;
    }
    .filter-group.collapsed .filter-options { max-height: 0; opacity: 0; padding-bottom: 0; }

    .filter-opt {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        cursor: pointer;
    }
    .filter-opt:first-child { padding-top: 2px; }
    .filter-opt input[type="checkbox"] {
        width: 17px; height: 17px;
        accent-color: var(--g500);
        cursor: pointer; flex-shrink: 0;
    }
    .filter-opt label {
        font-size: 13.5px; color: var(--ink2); font-weight: 500;
        cursor: pointer; transition: color .15s; line-height: 1.35; flex: 1;
    }
    .filter-opt:hover label { color: var(--ink); }
    .filter-opt input:checked + label { color: var(--g700); font-weight: 600; }

    /* ── Faceting: counts + dead-end dimming ─────────────────────────── */
    .facet-count {
        color: var(--ink4);
        font-size: 12px;
        font-weight: 400;
        margin-left: 1px;
    }
    .filter-opt--empty { opacity: .35; }
    .filter-opt--empty label,
    .filter-opt--empty input { cursor: not-allowed; }

    /* ── Price inputs ────────────────────────────────────────────────── */
    .price-inputs { display: flex; gap: 8px; margin-bottom: 8px; }
    .price-inputs input {
        flex: 1; min-width: 0;
        padding: 8px 10px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 13px; font-family: var(--font-body);
        text-align: center; outline: none;
        transition: border-color .2s, box-shadow .2s;
        -moz-appearance: textfield;
        color: var(--ink);
        background: var(--surf2);
    }
    .price-inputs input:focus {
        border-color: var(--g400);
        box-shadow: 0 0 0 3px rgba(78,122,26,.10);
        background: #fff;
    }
    .price-inputs input::-webkit-outer-spin-button,
    .price-inputs input::-webkit-inner-spin-button { -webkit-appearance: none; }

    .price-range-hint {
        font-size: 12px;
        color: var(--ink3);
        text-align: center;
        margin-top: 4px;
        min-height: 16px;
        line-height: 1.4;
    }

    /* ── Sidebar buttons ─────────────────────────────────────────────── */
    .btn-apply {
        background: var(--g500); color: #fff;
        padding: 12px; border-radius: 8px;
        font-size: 14px; font-weight: 700;
        font-family: var(--font-head); cursor: pointer;
        transition: background .2s; border: none; width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        letter-spacing: -.1px;
    }
    .btn-apply:hover { background: var(--g700); }
    .btn-reset {
        background: none; color: var(--ink3);
        padding: 8px; border-radius: 8px;
        font-size: 13px; font-weight: 500;
        font-family: var(--font-body); cursor: pointer;
        transition: color .2s; border: none; width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 5px;
        text-decoration: underline; text-underline-offset: 2px;
    }
    .btn-reset:hover { color: var(--g700); }

    /* ── Show more / fewer ───────────────────────────────────────── */
    .filter-opt--overflow { display: none; }
    .filter-options.show-more-expanded .filter-opt--overflow { display: flex; }

    .filter-show-more {
        display: flex; align-items: center; gap: 5px;
        font-size: 13px; font-weight: 600;
        color: var(--g600); background: none; border: none;
        padding: 6px 0 2px; cursor: pointer;
        font-family: var(--font-body);
        transition: color .15s;
    }
    .filter-show-more:hover { color: var(--g700); }
    .filter-show-more i { font-size: 10px; }

    /* ── Main column ─────────────────────────────────────────────────── */
    .cat-main { flex: 1; min-width: 0; }

    .cat-header {
        background: transparent; border: none;
        border-bottom: 1px solid var(--border); border-radius: 0; padding: 0 0 14px;
        display: flex; flex-direction: column; gap: 0; margin-bottom: 16px;
    }
    .cat-header h1 {
        font-family: var(--font-head); font-size: 1.3rem;
        font-weight: 800; color: var(--ink); margin: 0;
    }
    .cat-header-controls {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 8px;
    }
    .cat-header-left { display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; }
    .cat-header-right { display: flex; align-items: center; gap: 8px; }
    .cat-header-chips { border-top: 1px solid var(--border); padding-top: 10px; margin-top: 10px; }

    .cat-result-count { font-size: 13px; color: var(--ink3); font-weight: 500; }

    .mobile-filter-btn {
        display: none; align-items: center; gap: 6px;
        background: var(--surface); color: var(--ink2);
        padding: 10px 14px; min-height: 44px;
        border-radius: var(--radius); font-size: 14px; font-weight: 600;
        font-family: var(--font-body); cursor: pointer;
        border: 1.5px solid var(--border);
        transition: background .2s, border-color .2s, color .2s;
    }
    .mobile-filter-btn:hover { background: var(--g50); border-color: var(--g400); color: var(--g600); }
    .mobile-filter-btn .filter-count {
        background: var(--g500); color: #fff;
        font-size: 11px; font-weight: 800;
        padding: 1px 7px; border-radius: 12px;
        font-family: var(--font-head); margin-left: 2px;
    }

    .sort-wrap { display: flex; align-items: center; gap: 7px; }
    .sort-wrap label { font-size: 13px; color: var(--ink3); font-weight: 500; }
    .sort-select {
        -webkit-appearance: none; appearance: none;
        padding: 9px 36px 9px 13px;
        border: 1.5px solid var(--border); border-radius: 10px;
        font-size: 13.5px; font-weight: 600;
        font-family: var(--font-body); background-color: var(--surface);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%236b7280' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round' d='M1 1l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 11px center;
        color: var(--ink); outline: none; cursor: pointer;
        transition: border-color .18s, box-shadow .18s; min-height: 42px;
    }
    .sort-select:hover { border-color: var(--border2); }
    .sort-select:focus { border-color: var(--g500); box-shadow: 0 0 0 3px rgba(90,171,31,.12); }

    /* ── Product grid ────────────────────────────────────────────────── */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .cat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: border-color .2s;
        display: flex;
        flex-direction: column;
        min-height: 380px;
    }
    .cat-card:hover {
        border-color: var(--border2);
    }

    .cat-card__link {
        display: flex; flex-direction: column; flex-grow: 1;
        text-decoration: none; color: inherit; min-height: 0;
    }
    .cat-card__link:hover { text-decoration: none; }

    .cat-card__img {
        height: 230px;
        background: #f8f9fa;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
        border-bottom: 1px solid var(--border);
    }
    .cat-card__img img {
        width: 100%; height: 100%;
        object-fit: cover; object-position: center;
        display: block;
        transition: transform .35s cubic-bezier(.25,0,.25,1);
    }
    .cat-card:hover .cat-card__img img { transform: scale(1.03); }

    .cat-card__badge {
        position: absolute; top: 10px; left: 10px;
        background: #dc2626; color: #fff;
        font-size: 11px; font-weight: 800;
        padding: 4px 10px; border-radius: 6px;
        z-index: 1; letter-spacing: .2px;
    }

    .cat-card__body {
        padding: 14px 14px 60px;
        flex-grow: 1;
        display: flex; flex-direction: column; gap: 5px;
    }
    .cat-card__stars { display: flex; align-items: center; gap: 2px; margin-bottom: 3px; }
    .cat-card__stars i { font-size: 12px; color: #f5a623; }
    .cat-card__stars span { font-size: 11px; color: var(--ink4); margin-left: 4px; }

    .cat-card__name {
        font-size: 14px; font-weight: 600;
        color: var(--ink); line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 40px;
    }
    .cat-card__price {
        font-size: 20px; font-weight: 800;
        color: var(--ink);
        font-family: var(--font-head);
        letter-spacing: -.4px;
        line-height: 1;
        margin-top: 3px;
    }
    .cat-card__old { font-size: 12px; color: var(--ink4); text-decoration: line-through; }

    .cat-card__atc {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: var(--g500); color: #fff;
        padding: 14px;
        font-size: 14px; font-weight: 700;
        display: flex; align-items: center;
        justify-content: center; gap: 7px;
        border: none; cursor: pointer;
        width: 100%; font-family: var(--font-body);
        letter-spacing: .1px;
        transition: background .15s;
    }
    .cat-card__atc:hover { background: var(--g700); }
    .cat-card__atc:disabled { background: #e5e7eb; color: var(--ink4); cursor: not-allowed; }

    /* ── Active filter chips ─────────────────────────────────────────── */
    .active-filters {
        display: flex; flex-wrap: wrap; gap: 6px;
    }
    .active-filters:empty { display: none; }
    .filter-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--g50); border: 1px solid var(--border2);
        color: var(--g700); font-size: 12px; font-weight: 500;
        padding: 4px 10px; border-radius: 16px; font-family: var(--font-body);
    }
    .filter-chip button {
        background: none; border: none; color: var(--g600);
        cursor: pointer; font-size: 13px; line-height: 1;
        padding: 0; display: flex; align-items: center;
    }
    .filter-chip button:hover { color: #c0392b; }
    .filter-chip--clear {
        background: transparent; border: none; color: var(--ink3);
        text-decoration: underline; cursor: pointer; font-size: 12px;
        padding: 4px 6px;
    }
    .filter-chip--clear:hover { color: #c0392b; }

    /* ── Empty state ─────────────────────────────────────────────────── */
    .no-products {
        grid-column: 1 / -1; text-align: center;
        padding: 60px 20px; color: var(--ink3);
    }
    .no-products i { font-size: 48px; color: var(--border2); margin-bottom: 14px; display: block; }
    .no-products p { font-size: 15px; margin-bottom: 8px; }
    .no-products small { font-size: 13px; }

    /* ── Pagination ──────────────────────────────────────────────────── */
    .argos-pagination {
        margin-top: 28px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .argos-pagination__info { font-size: 13px; color: var(--ink3); font-family: var(--font-body); }
    .argos-pagination__info strong { color: var(--g600); font-weight: 700; }
    .argos-pagination__nav {
        display: flex; align-items: center; gap: 4px;
        flex-wrap: wrap; justify-content: center;
    }
    .argos-btn {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 5px; height: 38px; padding: 0 16px;
        background: var(--surface); border: 1.5px solid var(--border);
        border-radius: 19px; font-size: 13px; font-weight: 600;
        color: var(--ink2); font-family: var(--font-body); cursor: pointer;
        text-decoration: none;
        transition: background .18s, border-color .18s, color .18s;
        white-space: nowrap; user-select: none;
    }
    .argos-btn:hover:not(.argos-btn--disabled) {
        background: var(--g50); border-color: var(--g400); color: var(--g600);
    }
    .argos-btn--disabled { opacity: .38; cursor: not-allowed; pointer-events: none; }
    .argos-btn svg { width: 14px; height: 14px; fill: currentColor; flex-shrink: 0; }
    .argos-pages { display: flex; align-items: center; gap: 2px; }
    .argos-page {
        display: inline-flex; align-items: center; justify-content: center;
        width: 36px; height: 36px; border-radius: 50%;
        font-size: 13px; font-weight: 600; color: var(--ink2);
        text-decoration: none; font-family: var(--font-body);
        transition: background .18s, color .18s, border-color .18s;
        border: 1.5px solid transparent; background: transparent;
        cursor: pointer; user-select: none;
    }
    .argos-page:hover { background: var(--g50); border-color: var(--border2); color: var(--g600); }
    .argos-page--active {
        background: var(--g500) !important; color: #fff !important;
        border-color: var(--g500) !important; cursor: default; pointer-events: none;
    }
    .argos-page--dots {
        width: 28px; border: none; background: transparent; cursor: default;
        color: var(--ink3); pointer-events: none; font-size: 15px;
        letter-spacing: 1px; padding-bottom: 4px;
    }

    /* ── Mobile overlay ──────────────────────────────────────────────── */
    .mobile-filter-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5);
        z-index: 1040; display: none; opacity: 0; transition: opacity .3s;
    }
    .mobile-filter-overlay.active { display: block; opacity: 1; }

    /* ── Responsive ──────────────────────────────────────────────────── */
    @media (max-width: 900px) {
        .cat-sidebar {
            position: fixed; top: 0; left: 0;
            height: 100vh; width: 88%; max-width: 300px;
            border-radius: 0; z-index: 1050;
            transform: translateX(-100%);
            transition: transform .3s ease-out;
            max-height: 100vh; flex: none;
        }
        .cat-sidebar.active { transform: translateX(0); }
        .sidebar-close-btn { display: block; }
        .mobile-filter-btn { display: flex; }
        .cat-layout { display: block; }
        .cat-sidebar .sidebar-head {
            background: var(--g700);
            border-bottom-color: var(--g600);
            color: #fff;
        }
        .cat-sidebar .sidebar-close-btn { color: #fff; }
    }

    @media (max-width: 1100px) {
        .products-grid { grid-template-columns: repeat(3, 1fr); gap: 14px; }
    }
    @media (max-width: 600px) {
        .cat-page { padding: 12px 12px 48px; }
        .breadcrumb { font-size: 12px; margin-bottom: 10px; }
        .cat-header { padding: 10px 12px; margin-bottom: 12px; }
        .cat-header h1 { font-size: 1.1rem; }
        .sort-wrap label { display: none; }
        .sort-select { font-size: 12.5px; padding: 8px 32px 8px 11px; min-height: 38px; }
        .products-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .cat-card { min-height: 300px; }
        .cat-card__img { height: 165px; }
        .cat-card__name { font-size: 13px; min-height: 36px; }
        .cat-card__price { font-size: 16px; }
        .cat-card__atc { padding: 12px; font-size: 13px; }
        .argos-pages { display: none; }
        .argos-btn { height: 40px; padding: 0 20px; font-size: 13.5px; }
    }
    @media (max-width: 400px) {
        .products-grid { gap: 8px; }
        .cat-card__img { height: 148px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="cat-page">

    
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo e(url('/')); ?>">Home</a>
        <i class="fas fa-chevron-right"></i>
        <?php if(isset($parentCategory)): ?>
            <a href="<?php echo e(route('category.show', \Str::slug(str_replace(['/', '&', '+'], ' ', $parentCategory->name)))); ?>">
                <?php echo e($parentCategory->name); ?>

            </a>
            <i class="fas fa-chevron-right"></i>
        <?php endif; ?>
        <span><?php echo e($categoryName); ?></span>
    </nav>

    <div class="mobile-filter-overlay" id="filterOverlay"></div>

    <div class="cat-layout">

        
        <aside class="cat-sidebar" id="filterSidebar" aria-label="Filters">

            <div class="sidebar-head">
                <span><i class="fas fa-sliders-h" style="margin-right:6px;"></i>Filters</span>
                <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close filters">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="sidebar-body">

                
                <div class="filter-group" id="fg-price">
                    <div class="filter-group-head" data-target="price-opts">
                        <span id="price-filter-label">Price Range (₦)</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="filter-options" id="price-opts">
                        <div class="price-inputs">
                            <input type="number"
                                   id="min-price"
                                   placeholder="Min ₦"
                                   data-ngn="<?php echo e(request('min_price', '')); ?>"
                                   min="0"
                                   autocomplete="off">
                            <input type="number"
                                   id="max-price"
                                   placeholder="Max ₦"
                                   data-ngn="<?php echo e(request('max_price', '')); ?>"
                                   min="0"
                                   autocomplete="off">
                        </div>
                        <div class="price-range-hint"
                             id="price-range-hint"
                             data-min-ngn="<?php echo e($priceMin ?? 0); ?>"
                             data-max-ngn="<?php echo e($priceMax ?? 0); ?>">
                        </div>
                    </div>
                </div>

                
                <?php if(!empty($brands)): ?>
                <div class="filter-group" id="fg-brands">
                    <div class="filter-group-head" data-target="brand-opts">
                        Brands <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="filter-options" id="brand-opts">
                        <?php $brandVisible = 0; ?>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $count    = $brandCounts[$brand] ?? 0;
                                $checked  = in_array($brand, (array) request('brands', []));
                                $empty    = $count === 0 && !$checked;
                                $overflow = !$checked && $brandVisible >= 5;
                                if (!$overflow) $brandVisible++;
                            ?>
                            <div class="filter-opt <?php echo e($empty ? 'filter-opt--empty' : ''); ?> <?php echo e($overflow ? 'filter-opt--overflow' : ''); ?>">
                                <input type="checkbox" name="brands[]" value="<?php echo e($brand); ?>"
                                       id="brand-<?php echo e($loop->index); ?>"
                                       data-count="<?php echo e($count); ?>"
                                       <?php echo e($empty ? 'disabled' : ''); ?>

                                       <?php if($checked): echo 'checked'; endif; ?>>
                                <label for="brand-<?php echo e($loop->index); ?>">
                                    <?php echo e($brand); ?> <span class="facet-count">(<?php echo e($count); ?>)</span>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if(count($brands) > $brandVisible): ?>
                            <button class="filter-show-more" type="button">
                                <i class="fas fa-plus"></i>
                                Show <?php echo e(count($brands) - $brandVisible); ?> more
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php
                    $inStockCount  = $availabilityCounts['in-stock']  ?? 0;
                    $inChecked  = in_array('in-stock',  (array) request('availability', []));
                    $inEmpty    = $inStockCount === 0 && !$inChecked;
                ?>
                <div class="filter-group" id="fg-avail">
                    <div class="filter-group-head" data-target="avail-opts">
                        Availability <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="filter-options" id="avail-opts">
                        <div class="filter-opt <?php echo e($inEmpty ? 'filter-opt--empty' : ''); ?>">
                            <input type="checkbox" name="availability[]" value="in-stock" id="avail-in"
                                   data-count="<?php echo e($inStockCount); ?>"
                                   <?php echo e($inEmpty ? 'disabled' : ''); ?>

                                   <?php if($inChecked): echo 'checked'; endif; ?>>
                            <label for="avail-in">In Stock <span class="facet-count">(<?php echo e($inStockCount); ?>)</span></label>
                        </div>
                    </div>
                </div>

                
                <?php if(isset($customOptionsForFilter)): ?>
                    <?php $__currentLoopData = $customOptionsForFilter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $options): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($options)): ?>
                            <?php
                                $groupHasLive = false;
                                $custVisible  = 0;
                                foreach ($options as $opt) {
                                    $c = $customCounts[$key][$opt] ?? 0;
                                    if ($c > 0 || in_array($opt, (array) request("options.$key", []))) {
                                        $groupHasLive = true;
                                        break;
                                    }
                                }
                            ?>
                            <div class="filter-group <?php echo e($groupHasLive ? '' : 'filter-group--allempty'); ?>"
                                 id="fg-<?php echo e(Str::slug($key)); ?>">
                                <div class="filter-group-head" data-target="cust-<?php echo e(Str::slug($key)); ?>-opts">
                                    <?php echo e($filterLabels[$key] ?? ucwords(str_replace('_', ' ', $key))); ?>

                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="filter-options" id="cust-<?php echo e(Str::slug($key)); ?>-opts">
                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $count    = $customCounts[$key][$opt] ?? 0;
                                            $checked  = in_array($opt, (array) request("options.$key", []));
                                            $empty    = $count === 0 && !$checked;
                                            $overflow = !$checked && $custVisible >= 5;
                                            if (!$overflow) $custVisible++;
                                        ?>
                                        <div class="filter-opt <?php echo e($empty ? 'filter-opt--empty' : ''); ?> <?php echo e($overflow ? 'filter-opt--overflow' : ''); ?>">
                                            <input type="checkbox"
                                                   name="options[<?php echo e($key); ?>][]"
                                                   value="<?php echo e($opt); ?>"
                                                   id="cust-<?php echo e(Str::slug($key)); ?>-<?php echo e($idx); ?>"
                                                   data-count="<?php echo e($count); ?>"
                                                   <?php echo e($empty ? 'disabled' : ''); ?>

                                                   <?php if($checked): echo 'checked'; endif; ?>>
                                            <label for="cust-<?php echo e(Str::slug($key)); ?>-<?php echo e($idx); ?>">
                                                <?php echo e($opt); ?> <span class="facet-count">(<?php echo e($count); ?>)</span>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(count($options) > $custVisible): ?>
                                        <button class="filter-show-more" type="button">
                                            <i class="fas fa-plus"></i>
                                            Show <?php echo e(count($options) - $custVisible); ?> more
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>

            <div class="sidebar-footer">
                <button class="btn-apply" id="applyFiltersBtn">
                    <i class="fas fa-check" style="margin-right:5px;"></i>Apply Filters
                </button>
                <button class="btn-reset" id="resetFiltersBtn">
                    <i class="fas fa-undo" style="margin-right:5px;"></i>Reset All
                </button>
            </div>

        </aside>

        
        <div class="cat-main">

            <?php
                $activeChips = [];
                foreach ((array) request('brands', []) as $b) {
                    if ($b !== '') $activeChips[] = ['type' => 'brands', 'value' => $b, 'label' => $b];
                }
                foreach ((array) request('availability', []) as $a) {
                    if ($a !== '') {
                        $activeChips[] = [
                            'type'  => 'availability',
                            'value' => $a,
                            'label' => $a === 'in-stock' ? 'In Stock' : 'Pre-Order',
                        ];
                    }
                }
                foreach ((array) request('options', []) as $k => $vals) {
                    foreach ((array) $vals as $v) {
                        if ($v !== '') {
                            $activeChips[] = [
                                'type'  => 'options',
                                'key'   => $k,
                                'value' => $v,
                                'label' => $v,
                            ];
                        }
                    }
                }
                if (request('min_price') !== null && request('min_price') !== '') {
                    $activeChips[] = ['type' => 'min_price', 'value' => request('min_price'), 'label' => 'Min ₦' . number_format((float) request('min_price'))];
                }
                if (request('max_price') !== null && request('max_price') !== '') {
                    $activeChips[] = ['type' => 'max_price', 'value' => request('max_price'), 'label' => 'Max ₦' . number_format((float) request('max_price'))];
                }
            ?>

            <div class="cat-header">
                <div class="cat-header-controls">
                    <div class="cat-header-left">
                        <h1><?php echo e($categoryName); ?></h1>
                        <span class="cat-result-count">
                            <?php echo e($categoryProducts->total()); ?> <?php echo e(Str::plural('product', $categoryProducts->total())); ?>

                        </span>
                    </div>
                    <div class="cat-header-right">
                        <button class="mobile-filter-btn" id="mobileFilterBtn">
                            <i class="fas fa-sliders-h"></i> Filters
                            <?php if(!empty($activeChips)): ?>
                                <span class="filter-count"><?php echo e(count($activeChips)); ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="sort-wrap">
                            <label for="sort-by">Sort:</label>
                            <select id="sort-by" class="sort-select">
                                <option value="popularity" <?php if(request('sort_by','popularity') === 'popularity'): echo 'selected'; endif; ?>>Popularity</option>
                                <option value="price-asc"  <?php if(request('sort_by') === 'price-asc'): echo 'selected'; endif; ?>>Price ↑</option>
                                <option value="price-desc" <?php if(request('sort_by') === 'price-desc'): echo 'selected'; endif; ?>>Price ↓</option>
                                <option value="newest"     <?php if(request('sort_by') === 'newest'): echo 'selected'; endif; ?>>Newest</option>
                                <option value="rating"     <?php if(request('sort_by') === 'rating'): echo 'selected'; endif; ?>>Top Rated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if(!empty($activeChips)): ?>
                <div class="cat-header-chips">
                    <div class="active-filters" id="activeFilters">
                        <?php $__currentLoopData = $activeChips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="filter-chip"
                                  data-chip-type="<?php echo e($chip['type']); ?>"
                                  data-chip-key="<?php echo e($chip['key'] ?? ''); ?>"
                                  data-chip-value="<?php echo e($chip['value']); ?>">
                                <?php echo e($chip['label']); ?>

                                <button type="button" aria-label="Remove filter">&times;</button>
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <button class="filter-chip--clear" id="clearAllChips">Clear all</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if($categoryProducts->isEmpty()): ?>
                <div class="products-grid">
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <p>No products found in this category.</p>
                        <small>Try adjusting your filters or
                            <a href="<?php echo e(url()->current()); ?>" style="color:var(--g600);">clear all filters</a>.
                        </small>
                    </div>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php $__currentLoopData = $categoryProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        /*
                         * sell_price  = base price → markup resolved (product → sub → cat)
                         *                          → discount resolved (product → sub → cat)
                         *
                         * old_price   = post-markup, pre-discount price (null when no discount)
                         *
                         * Both are in NGN. The JS updatePricesOnPage() will convert them to
                         * the display currency using data-price-ngn / data-old-price-ngn attrs.
                         */
                        $sellPrice   = $product->sell_price;
                        $oldPrice    = $product->old_price;
                        $hasDiscount = $oldPrice && $oldPrice > $sellPrice;
                        $discPct     = $hasDiscount
                            ? round((($oldPrice - $sellPrice) / $oldPrice) * 100)
                            : 0;
                        $firstImg = $product->images->first();
                        $imgSrc   = $firstImg
                            ? asset('storage/' . $firstImg->image_url)
                            : 'https://placehold.co/160x145/eef3e8/3d8012?text=No+Image';
                    ?>

                    <div class="cat-card" data-product-id="<?php echo e($product->id); ?>">

                        
                        <a class="cat-card__link"
                           href="/product/<?php echo e($product->id); ?>"
                           target="_blank"
                           rel="noopener"
                           aria-label="<?php echo e($product->name); ?>">

                            <div class="cat-card__img">
                                <?php if($hasDiscount && $discPct >= 5): ?>
                                    <span class="cat-card__badge">-<?php echo e($discPct); ?>%</span>
                                <?php endif; ?>
                                <img src="<?php echo e($imgSrc); ?>"
                                     alt="<?php echo e($product->name); ?>"
                                     loading="lazy"
                                     onerror="this.src='https://placehold.co/160x145/eef3e8/3d8012?text=No+Image'">
                            </div>

                            <div class="cat-card__body">
                                <div class="cat-card__stars">
                                    <?php if($product->review_rating_count > 0): ?>
                                        <?php echo $__env->make('partials.stars', ['rating' => $product->review_rating], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        <span>(<?php echo e($product->review_rating_count); ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="cat-card__name"><?php echo e($product->name); ?></div>
                                <div class="cat-card__price" data-price-ngn="<?php echo e($sellPrice); ?>">
                                    &#8358;<?php echo e(number_format($sellPrice, 0)); ?>

                                </div>
                                <?php if($hasDiscount): ?>
                                <div class="cat-card__old" data-old-price-ngn="<?php echo e($oldPrice); ?>">
                                    &#8358;<?php echo e(number_format($oldPrice, 0)); ?>

                                </div>
                                <?php endif; ?>
                            </div>
                        </a>

                        <button class="cat-card__atc"
                                <?php if(($product->stock ?? 1) === 0): ?> disabled <?php endif; ?>
                                onclick="event.stopPropagation();
    window.addToCart(
        '<?php echo e($product->id); ?>',
        '<?php echo e(addslashes($product->name)); ?>',
        <?php echo e($sellPrice); ?>,
        '<?php echo e($imgSrc); ?>',
        <?php echo e($product->requires_truck ? 'true' : 'false'); ?>

    );
                                    this.textContent='✓ Added!';
                                    this.style.background='var(--g700)';
                                    setTimeout(()=>{
                                        this.innerHTML='<i class=\'fas fa-shopping-bag\' style=\'font-size:11px\'></i> Add to Cart';
                                        this.style.background='';
                                    },1800);">
                            <i class="fas fa-shopping-bag" style="font-size:11px;"></i>
                            <?php echo e(($product->stock ?? 1) === 0 ? 'Out of Stock' : 'Add to Cart'); ?>

                        </button>

                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php
                    $currentPage = $categoryProducts->currentPage();
                    $lastPage    = $categoryProducts->lastPage();
                    $total       = $categoryProducts->total();
                    $from        = $categoryProducts->firstItem();
                    $to          = $categoryProducts->lastItem();
                    $baseQuery   = request()->except('page');
                    $window      = 2;
                    $pageList    = [];
                    $prev        = null;
                    for ($p = 1; $p <= $lastPage; $p++) {
                        $show = ($p === 1) || ($p === $lastPage)
                             || ($p >= $currentPage - $window && $p <= $currentPage + $window);
                        if ($show) {
                            if ($prev !== null && $p - $prev > 1) {
                                $pageList[] = ['type' => 'dots'];
                            }
                            $pageList[] = ['type' => 'page', 'num' => $p];
                            $prev = $p;
                        }
                    }
                ?>

                <?php if($lastPage > 1): ?>
                <nav class="argos-pagination" aria-label="Page navigation">
                    <p class="argos-pagination__info">
                        Showing <strong><?php echo e($from); ?></strong>–<strong><?php echo e($to); ?></strong>
                        of <strong><?php echo e($total); ?></strong> products
                    </p>
                    <div class="argos-pagination__nav" role="list">

                        <?php if($currentPage > 1): ?>
                            <a class="argos-btn"
                               href="<?php echo e($categoryProducts->appends($baseQuery)->previousPageUrl()); ?>"
                               aria-label="Go to previous page">
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M13.293 4.293a1 1 0 0 1 0 1.414L9.414 10l3.879 3.879a1 1 0 0 1-1.414 1.414l-4.586-4.586a1 1 0 0 1 0-1.414l4.586-4.586a1 1 0 0 1 1.414 0z"/></svg>
                                Prev
                            </a>
                        <?php else: ?>
                            <span class="argos-btn argos-btn--disabled" aria-disabled="true">
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M13.293 4.293a1 1 0 0 1 0 1.414L9.414 10l3.879 3.879a1 1 0 0 1-1.414 1.414l-4.586-4.586a1 1 0 0 1 0-1.414l4.586-4.586a1 1 0 0 1 1.414 0z"/></svg>
                                Prev
                            </span>
                        <?php endif; ?>

                        <div class="argos-pages" role="list">
                            <?php $__currentLoopData = $pageList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($item['type'] === 'dots'): ?>
                                    <span class="argos-page argos-page--dots" aria-hidden="true">···</span>
                                <?php elseif($item['num'] === $currentPage): ?>
                                    <span class="argos-page argos-page--active"
                                          aria-current="page"
                                          aria-label="Page <?php echo e($item['num']); ?>, current page">
                                        <?php echo e($item['num']); ?>

                                    </span>
                                <?php else: ?>
                                    <a class="argos-page"
                                       href="<?php echo e($categoryProducts->appends($baseQuery)->url($item['num'])); ?>"
                                       aria-label="Go to page <?php echo e($item['num']); ?>">
                                        <?php echo e($item['num']); ?>

                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <?php if($currentPage < $lastPage): ?>
                            <a class="argos-btn"
                               href="<?php echo e($categoryProducts->appends($baseQuery)->nextPageUrl()); ?>"
                               aria-label="Go to next page">
                                Next
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M6.707 4.293a1 1 0 0 0 0 1.414L10.586 10l-3.879 3.879a1 1 0 0 0 1.414 1.414l4.586-4.586a1 1 0 0 0 0-1.414L8.121 4.293a1 1 0 0 0-1.414 0z"/></svg>
                            </a>
                        <?php else: ?>
                            <span class="argos-btn argos-btn--disabled" aria-disabled="true">
                                Next
                                <svg viewBox="0 0 20 20" aria-hidden="true"><path d="M6.707 4.293a1 1 0 0 0 0 1.414L10.586 10l-3.879 3.879a1 1 0 0 0 1.414 1.414l4.586-4.586a1 1 0 0 0 0-1.414L8.121 4.293a1 1 0 0 0-1.414 0z"/></svg>
                            </span>
                        <?php endif; ?>

                    </div>
                </nav>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    /* ═══════════════════════════════════════════════════════════════════════
       PRICE FILTER — CURRENCY AWARE
       ───────────────────────────────────────────────────────────────────────
       CURRENCY CONVENTION (from simslayout):
         rate_to_ngn = how many NGN per 1 unit of foreign currency.
         e.g. USD rate = 1500  →  $1 = ₦1,500

       Therefore:
         NGN  → foreign :  ngn  / rate      e.g. ₦150,000 / 1500 = $100
         foreign → NGN  :  val  * rate      e.g. $100 * 1500 = ₦150,000

       The server ALWAYS receives and stores prices in NGN.
       URL params min_price / max_price are ALWAYS NGN.
    ═══════════════════════════════════════════════════════════════════════ */

    const INITIAL_MIN_NGN = '<?php echo e(request('min_price', '')); ?>';
    const INITIAL_MAX_NGN = '<?php echo e(request('max_price', '')); ?>';

    const minInput = document.getElementById('min-price');
    const maxInput = document.getElementById('max-price');
    const hintEl   = document.getElementById('price-range-hint');
    const labelEl  = document.getElementById('price-filter-label');

    let minDirty = false;
    let maxDirty = false;
    minInput?.addEventListener('input', () => { minDirty = true; });
    maxInput?.addEventListener('input', () => { maxDirty = true; });

    /* ── Currency helpers ─────────────────────────────────────────────── */

    function getRate() {
        const cur = window.CURRENCY?.current ?? 'NGN';
        return window.CURRENCY?.rates?.[cur] ?? 1;
    }
    function getSymbol() {
        const cur = window.CURRENCY?.current ?? 'NGN';
        return window.CURRENCY?.symbols?.[cur] ?? '₦';
    }
    function getCur() {
        return window.CURRENCY?.current ?? 'NGN';
    }
    function ratesReady() {
        return !!(window.CURRENCY && window.CURRENCY.rates &&
                  Object.keys(window.CURRENCY.rates).length > 0);
    }

    function ngnToDisplay(ngn) {
        const v = parseFloat(ngn);
        if (!ngn || isNaN(v) || v <= 0) return '';
        const rate = getRate();
        const converted = v / rate;
        return parseFloat(converted.toFixed(2)).toString();
    }

    function displayToNgn(displayVal) {
        const v = parseFloat(displayVal);
        if (!displayVal || isNaN(v) || v <= 0) return null;
        const rate = getRate();
        return String(Math.round(v * rate));
    }

    /* ── Sync inputs from the baked-in NGN URL values ─────────────────── */

    function syncPriceInputs() {
        if (!ratesReady()) return;

        const sym = getSymbol();
        const cur = getCur();
        const ngnLabel = cur === 'NGN';

        if (minInput) minInput.placeholder = ngnLabel ? 'Min ₦' : `Min ${sym}`;
        if (maxInput) maxInput.placeholder = ngnLabel ? 'Max ₦' : `Max ${sym}`;

        if (!minDirty && minInput) {
            minInput.value = INITIAL_MIN_NGN !== '' ? ngnToDisplay(INITIAL_MIN_NGN) : '';
        }
        if (!maxDirty && maxInput) {
            maxInput.value = INITIAL_MAX_NGN !== '' ? ngnToDisplay(INITIAL_MAX_NGN) : '';
        }

        updatePriceLabel();
        updatePriceHint();
    }

    function updatePriceLabel() {
        if (!labelEl) return;
        const sym = getSymbol();
        const cur = getCur();
        labelEl.textContent = cur === 'NGN'
            ? 'Price Range (₦)'
            : `Price Range (${sym} ${cur})`;
    }

    function updatePriceHint() {
        if (!hintEl) return;

        const minNgn = parseFloat(hintEl.dataset.minNgn) || 0;
        const maxNgn = parseFloat(hintEl.dataset.maxNgn) || 0;

        if (!minNgn && !maxNgn) { hintEl.textContent = ''; return; }

        const sym = getSymbol();
        const cur = getCur();
        const lo  = ngnToDisplay(minNgn);
        const hi  = ngnToDisplay(maxNgn);

        if (!lo || !hi) { hintEl.textContent = ''; return; }

        const fmt = (val) => {
            const n = parseFloat(val);
            return cur === 'NGN'
                ? n.toLocaleString('en-NG')
                : n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        };

        hintEl.textContent = `Range: ${sym}${fmt(lo)} – ${sym}${fmt(hi)}`;
    }

    window.addEventListener('currencyChanged', () => {
        minDirty = false;
        maxDirty = false;
        syncPriceInputs();
    });

    /* ── DOM Ready ────────────────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', () => {

        syncPriceInputs();

        /* ── Sidebar open / close ─────────────────────────────────────── */

        const sidebar  = document.getElementById('filterSidebar');
        const overlay  = document.getElementById('filterOverlay');
        const openBtn  = document.getElementById('mobileFilterBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function openSidebar() {
            sidebar?.classList.add('active');
            overlay?.classList.add('active');
            document.body.style.overflow = 'hidden';
            closeBtn?.focus();
        }
        function closeSidebar() {
            sidebar?.classList.remove('active');
            overlay?.classList.remove('active');
            document.body.style.overflow = '';
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeSidebar();
        });

        /* ── Accordion collapse/expand ────────────────────────────────── */

        document.querySelectorAll('.filter-group-head').forEach(head => {
            head.addEventListener('click', () => {
                head.closest('.filter-group').classList.toggle('collapsed');
            });
        });

        /* ── Show more / Show fewer ───────────────────────────────────── */

        document.querySelectorAll('.filter-show-more').forEach(btn => {
            btn.addEventListener('click', () => {
                const list     = btn.closest('.filter-options');
                const expanded = list.classList.toggle('show-more-expanded');
                const hidden   = list.querySelectorAll('.filter-opt--overflow').length;
                if (expanded) {
                    btn.innerHTML = '<i class="fas fa-minus"></i> Show fewer';
                } else {
                    btn.innerHTML = `<i class="fas fa-plus"></i> Show ${hidden} more`;
                }
            });
        });

        /* ── Build filter URL ─────────────────────────────────────────── */

        function buildFilterUrl(reset = false) {
            const url = new URL(window.location.href);
            url.search = '';

            if (!reset) {
                const minNgn = displayToNgn(minInput?.value ?? '');
                const maxNgn = displayToNgn(maxInput?.value ?? '');
                if (minNgn !== null) url.searchParams.set('min_price', minNgn);
                if (maxNgn !== null) url.searchParams.set('max_price', maxNgn);

                ['brands', 'availability'].forEach(name => {
                    document.querySelectorAll(`input[name="${name}[]"]:checked`)
                        .forEach(cb => url.searchParams.append(`${name}[]`, cb.value));
                });

                const optKeys = new Set();
                document.querySelectorAll('input[name^="options["]').forEach(cb => {
                    const m = cb.name.match(/options\[([^\]]+)\]/);
                    if (m) optKeys.add(m[1]);
                });
                optKeys.forEach(key => {
                    document.querySelectorAll(`input[name="options[${key}][]"]:checked`)
                        .forEach(cb => url.searchParams.append(`options[${key}][]`, cb.value));
                });
            }

            const sort = document.getElementById('sort-by')?.value;
            if (sort && sort !== 'popularity') {
                url.searchParams.set('sort_by', sort);
            }

            return url.toString();
        }

        /* ── Apply / Reset / Sort ──────────────────────────────────────── */

        document.getElementById('applyFiltersBtn')?.addEventListener('click', () => {
            closeSidebar();
            window.location.href = buildFilterUrl();
        });

        document.getElementById('resetFiltersBtn')?.addEventListener('click', () => {
            document.querySelectorAll('.cat-sidebar input[type="checkbox"]')
                .forEach(cb => { cb.checked = false; });
            if (minInput) { minInput.value = ''; minDirty = false; }
            if (maxInput) { maxInput.value = ''; maxDirty = false; }
            closeSidebar();
            window.location.href = buildFilterUrl(true);
        });

        document.getElementById('sort-by')?.addEventListener('change', () => {
            window.location.href = buildFilterUrl();
        });

        /* ── Active filter chips → remove on click ─────────────────────── */
        //
        // Each chip removes its own param from the URL and reloads, so the
        // server recomputes facets and results. "Clear all" wipes filters
        // but keeps the current sort.

        function removeChip(chip) {
            const type  = chip.dataset.chipType;
            const key   = chip.dataset.chipKey;
            const value = chip.dataset.chipValue;
            const url   = new URL(window.location.href);
            const params = url.searchParams;

            if (type === 'min_price' || type === 'max_price') {
                params.delete(type);
            } else if (type === 'options') {
                const paramName = `options[${key}][]`;
                const kept = params.getAll(paramName).filter(v => v !== value);
                params.delete(paramName);
                kept.forEach(v => params.append(paramName, v));
            } else {
                // brands / availability
                const paramName = `${type}[]`;
                const kept = params.getAll(paramName).filter(v => v !== value);
                params.delete(paramName);
                kept.forEach(v => params.append(paramName, v));
            }

            params.delete('page'); // back to page 1 after changing filters
            window.location.href = url.toString();
        }

        document.querySelectorAll('.filter-chip button').forEach(btn => {
            btn.addEventListener('click', () => removeChip(btn.closest('.filter-chip')));
        });

        document.getElementById('clearAllChips')?.addEventListener('click', () => {
            window.location.href = buildFilterUrl(true);
        });

        /* ── Trigger global price re-render for card prices ──────────── */
        window.updatePricesOnPage?.();

    }); // end DOMContentLoaded

})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/category.blade.php ENDPATH**/ ?>