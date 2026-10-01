@extends('layouts.simslayout')
@section('content')

<style>
    /* ── PAGE ── */
    .cart-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.75rem 1.25rem 3rem;
    }
    .cart-page__hd {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 10px;
    }
    .cart-page__title {
        font-family: var(--fh);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cart-page__count {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--ink3);
        background: var(--surf3);
        border: 1px solid var(--border);
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: 0;
    }
    .cart-continue-link {
        font-size: 13px;
        color: var(--ink3);
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color .15s;
    }
    .cart-continue-link:hover { color: var(--g600); }

    /* ── LAYOUT ── */
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
    }

    /* ── CARD BASE ── */
    .cart-card {
        background: #fff;
        border: 1.5px solid var(--border);
        border-radius: 0;
        overflow: hidden;
    }

    /* ── ITEMS TABLE HEADER ── */
    .cart-table-head {
        display: grid;
        grid-template-columns: 1fr 110px 140px 110px 52px;
        column-gap: 8px;
        padding: 10px 20px;
        background: var(--surf2);
        border-bottom: 1px solid var(--border);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--ink3);
    }
    .cart-table-head span:nth-child(n+2) { text-align: center; }

    /* ── ITEM ROW ── */
    .ci-row {
        display: grid;
        grid-template-columns: 1fr 110px 140px 110px 52px;
        align-items: center;
        column-gap: 8px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .ci-row:last-child { border-bottom: none; }
    .ci-row:hover { background: #fafafa; }

    /* Product cell */
    .ci-product {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        padding-right: 12px;
    }
    .ci-img {
        width: 88px;
        height: 88px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: #f8f9fa;
        flex-shrink: 0;
    }
    .ci-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color .15s;
    }
    .ci-name:hover { color: var(--g600); }

    /* Unit price cell */
    .ci-unit {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--ink2);
        text-align: center;
    }

    /* Qty cell */
    .ci-qty {
        display: flex;
        justify-content: center;
    }
    .qty-ctrl {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--border);
        border-radius: 6px;
        overflow: hidden;
    }
    .qty-btn {
        width: 34px;
        height: 34px;
        background: var(--surf2);
        border: none;
        font-size: 16px;
        font-weight: 700;
        color: var(--ink2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s, color .15s;
        flex-shrink: 0;
    }
    .qty-btn:hover { background: var(--g100); color: var(--g700); }
    .qty-inp {
        width: 42px;
        height: 34px;
        text-align: center;
        border: none;
        border-left: 1px solid var(--border);
        border-right: 1px solid var(--border);
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        background: #fff;
        outline: none;
        -moz-appearance: textfield;
    }
    .qty-inp::-webkit-outer-spin-button,
    .qty-inp::-webkit-inner-spin-button { -webkit-appearance: none; }

    /* Subtotal cell */
    .ci-sub {
        font-family: var(--fh);
        font-size: 15px;
        font-weight: 800;
        color: var(--ink);
        text-align: center;
        letter-spacing: -.2px;
    }

    /* Remove cell */
    .ci-remove {
        display: flex;
        justify-content: center;
        padding-left: 4px;
    }
    .ci-remove-btn {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        background: none;
        border: 1px solid var(--border);
        color: var(--ink3);
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s;
    }
    .ci-remove-btn:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

    /* ── CART FOOTER ── */
    .cart-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: var(--surf2);
        border-top: 1px solid var(--border);
        gap: 10px;
        flex-wrap: wrap;
    }
    .cart-clear-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #fff;
        color: var(--ink3);
        border: 1.5px solid var(--border);
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .cart-clear-btn:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

    /* ── ORDER SUMMARY ── */
    .cart-summary-sticky { position: sticky; top: 5rem; }

    .summary-card {
        background: #fff;
        border: 1.5px solid var(--border);
        border-radius: 0;
        overflow: hidden;
    }
    .summary-card__head {
        padding: 16px 20px;
        border-bottom: 1.5px solid var(--border);
        font-family: var(--fh);
        font-size: 15px;
        font-weight: 800;
        color: var(--ink);
        letter-spacing: -.2px;
    }
    .summary-card__body { padding: 16px 20px; }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13.5px;
        color: var(--ink3);
        padding: 7px 0;
        border-bottom: 1px solid var(--border);
    }
    .summary-row:last-of-type { border-bottom: none; }
    .summary-row span:last-child { font-weight: 600; color: var(--ink2); }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0 0;
        margin-top: 4px;
        border-top: 2px solid var(--border);
    }
    .summary-total__label {
        font-family: var(--fh);
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
    }
    .summary-total__amount {
        font-family: var(--fh);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--ink);
        letter-spacing: -.5px;
    }

    .checkout-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        width: 100%;
        margin-top: 1.1rem;
        padding: 16px;
        background: var(--g500);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: var(--fh);
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: background .2s, transform .15s;
        letter-spacing: -.1px;
    }
    .checkout-cta:hover { background: var(--g700); transform: translateY(-1px); }

    .summary-note {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        font-size: 11.5px;
        color: var(--ink4);
        justify-content: center;
    }
    .summary-note i { color: var(--g500); font-size: 11px; }

    /* ── EMPTY STATE ── */
    .cart-empty {
        text-align: center;
        padding: 4rem 2rem;
    }
    .cart-empty__icon {
        font-size: 2.5rem;
        color: var(--border2);
        margin-bottom: 1rem;
    }
    .cart-empty__title {
        font-family: var(--fh);
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .cart-empty__sub {
        font-size: 13px;
        color: var(--ink3);
        margin-bottom: 1.5rem;
    }
    .cart-empty__cta {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 24px;
        background: var(--g500);
        color: #fff;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        transition: background .2s;
    }
    .cart-empty__cta:hover { background: var(--g700); }

    /* ── LOADING ── */
    .cart-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 180px;
    }
    .cart-spinner.hidden { display: none; }
    .spin-ring {
        width: 36px; height: 36px;
        border: 3px solid var(--g100);
        border-top-color: var(--g500);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── BREADCRUMB ── */
    .cart-breadcrumb {
        list-style: none; padding: 9px 0; margin: 0 0 18px 0;
        display: flex; flex-wrap: wrap; gap: 4px; align-items: center;
        font-size: 12.5px; color: var(--ink3);
        border-bottom: 1px solid var(--border);
    }
    .cart-breadcrumb li { display: flex; align-items: center; }
    .cart-breadcrumb li:not(:last-child)::after {
        content: '›'; margin: 0 6px; color: var(--border2); font-size: 14px;
    }
    .cart-breadcrumb a { color: var(--ink3); transition: color .15s; }
    .cart-breadcrumb a:hover { color: var(--g600); }
    .cart-breadcrumb li:last-child span { color: var(--ink); font-weight: 600; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .cart-layout { grid-template-columns: 1fr; }
        .cart-summary-sticky { position: static; }
    }
    @media (max-width: 640px) {
        .cart-page { padding: 1rem .875rem 2.5rem; }
        .cart-table-head { display: none; }
        .ci-row {
            grid-template-columns: 1fr;
            column-gap: 0;
            gap: 12px;
            padding: 14px 16px;
        }
        .ci-product { padding-right: 0; }
        .ci-unit, .ci-qty, .ci-sub, .ci-remove {
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            padding-left: 0;
        }
        .ci-unit::before  { content: 'Unit price'; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink3); }
        .ci-qty::before   { content: 'Quantity';   font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink3); }
        .ci-sub::before   { content: 'Subtotal';   font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--ink3); }
        .ci-remove        { justify-content: flex-end; }
    }
</style>

<div class="cart-page">

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb">
        <ul class="cart-breadcrumb">
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><span>Shopping Cart</span></li>
        </ul>
    </nav>

    {{-- Page header --}}
    <div class="cart-page__hd">
        <h1 class="cart-page__title">
            Shopping Cart
            <span class="cart-page__count" id="cartItemCount"></span>
        </h1>
        <a href="/" class="cart-continue-link">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Continue Shopping
        </a>
    </div>

    <div class="cart-layout" id="cartLayout">

        {{-- Left: items --}}
        <div>
            <div class="cart-card">
                {{-- Column headers --}}
                <div class="cart-table-head" id="cartTableHead">
                    <span>Product</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span>Subtotal</span>
                    <span></span>
                </div>

                {{-- Items injected by JS --}}
                <div id="cartItemsDisplay">
                    <div class="cart-spinner" id="loadingSpinner">
                        <div class="spin-ring"></div>
                    </div>
                </div>
            </div>

            {{-- Footer actions (shown when cart has items) --}}
            <div class="cart-card-footer" id="cartFooterActions" style="display:none;">
                <button class="cart-clear-btn" id="clearCartBtn">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </button>
            </div>
        </div>

        {{-- Right: order summary --}}
        <div class="cart-summary-sticky">
            <div class="summary-card" id="cartSummaryAndActions" style="display:none;">
                <div class="summary-card__head">Order Summary</div>
                <div class="summary-card__body">
                    <div class="summary-row">
                        <span>Items (<span id="cartItemCountSummary">0</span>)</span>
                        <span id="cartSubtotalDisplay">₦0</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <div class="summary-total">
                        <span class="summary-total__label">Total</span>
                        <span class="summary-total__amount" id="cartTotalDisplay">₦0</span>
                    </div>
                    <button class="checkout-cta" onclick="window.location.href='/checkout'">
                        Proceed to Checkout <i class="fas fa-arrow-right"></i>
                    </button>
                    <div class="summary-note">
                        <i class="fas fa-lock"></i> Secure checkout
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const cartItemsDisplay      = document.getElementById('cartItemsDisplay');
    const cartSummaryAndActions = document.getElementById('cartSummaryAndActions');
    const cartTotalDisplay      = document.getElementById('cartTotalDisplay');
    const cartSubtotalDisplay   = document.getElementById('cartSubtotalDisplay');
    const cartItemCountEl       = document.getElementById('cartItemCount');
    const cartItemCountSummary  = document.getElementById('cartItemCountSummary');
    const cartTableHead         = document.getElementById('cartTableHead');
    const cartFooterActions     = document.getElementById('cartFooterActions');
    const loadingSpinner        = document.getElementById('loadingSpinner');
    const clearCartBtn          = document.getElementById('clearCartBtn');

    // ── Cart state ────────────────────────────────────────────────────────────
    let cart = [];

    function fmt(ngnAmount) {
        if (window.CURRENCY && typeof window.CURRENCY.format === 'function') {
            return window.CURRENCY.format(ngnAmount);
        }
        return '₦' + parseFloat(ngnAmount).toLocaleString('en-NG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        });
    }

    // ── localStorage helpers ──────────────────────────────────────────────────
    function loadCart() {
        try {
            const raw    = localStorage.getItem('shoppingCart');
            const parsed = raw ? JSON.parse(raw) : [];
            cart = Array.isArray(parsed)
                ? parsed.map(item => ({
                    ...item,
                    basePriceNgn: parseFloat(item.basePriceNgn) || 0,
                    quantity:     parseInt(item.quantity)        || 1,
                }))
                : [];
        } catch (e) {
            cart = [];
        }
    }

    function saveCart() {
        try { localStorage.setItem('shoppingCart', JSON.stringify(cart)); } catch (e) {}
        updateHeaderBadge();
    }

    function updateHeaderBadge() {
        const badge = document.getElementById('cartCount');
        if (!badge) return;
        const total = cart.reduce((s, i) => s + (i.quantity || 1), 0);
        badge.textContent   = total;
        badge.style.display = total > 0 ? 'flex' : 'none';
    }

    // ── Totals ────────────────────────────────────────────────────────────────
    function recalcTotals() {
        const totalNgn = cart.reduce((sum, item) =>
            sum + (item.basePriceNgn || 0) * (item.quantity || 1), 0);
        const totalQty = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

        if (cartTotalDisplay)     cartTotalDisplay.textContent     = fmt(totalNgn);
        if (cartSubtotalDisplay)  cartSubtotalDisplay.textContent  = fmt(totalNgn);
        if (cartItemCountSummary) cartItemCountSummary.textContent = totalQty;
        if (cartItemCountEl)      cartItemCountEl.textContent      = totalQty + ' item' + (totalQty !== 1 ? 's' : '');
    }

    // ── Render ────────────────────────────────────────────────────────────────
    function render() {
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (!cartItemsDisplay) return;

        // Clear previous items (keep spinner)
        Array.from(cartItemsDisplay.children).forEach(child => {
            if (child.id !== 'loadingSpinner') child.remove();
        });

        if (cart.length === 0) {
            if (cartSummaryAndActions)  cartSummaryAndActions.style.display  = 'none';
            if (cartTableHead)          cartTableHead.style.display          = 'none';
            if (cartFooterActions)      cartFooterActions.style.display      = 'none';
            if (cartItemCountEl)        cartItemCountEl.textContent          = '0 items';

            const empty = document.createElement('div');
            empty.className = 'cart-empty';
            empty.innerHTML = `
                <div class="cart-empty__icon"><i class="fas fa-shopping-bag"></i></div>
                <p class="cart-empty__title">Your cart is empty</p>
                <p class="cart-empty__sub">You haven't added any items yet.</p>
                <a href="/" class="cart-empty__cta"><i class="fas fa-arrow-left"></i> Start Shopping</a>`;
            cartItemsDisplay.appendChild(empty);
            recalcTotals();
            return;
        }

        if (cartSummaryAndActions) cartSummaryAndActions.style.display = 'block';
        if (cartTableHead)         cartTableHead.style.display         = '';
        if (cartFooterActions)     cartFooterActions.style.display     = '';

        cart.forEach((item, index) => {
            const price    = item.basePriceNgn || 0;
            const qty      = item.quantity || 1;
            const subtotal = price * qty;
            const img      = item.image || item.imageSrc
                           || 'https://placehold.co/72x72/f5f5f5/cccccc?text=Product';
            const href     = item.id ? '/product/' + item.id : '#';

            const row = document.createElement('div');
            row.className         = 'ci-row';
            row.dataset.productId = item.id;
            row.innerHTML = `
                <div class="ci-product">
                    <img src="${img}" class="ci-img" alt="${item.name}"
                         onerror="this.src='https://placehold.co/72x72/f5f5f5/cccccc?text=Product'">
                    <a href="${href}" class="ci-name">${item.name}</a>
                </div>
                <div class="ci-unit" data-ngn="${price}">${fmt(price)}</div>
                <div class="ci-qty">
                    <div class="qty-ctrl">
                        <button class="qty-btn decrease-qty" data-index="${index}" aria-label="Decrease">−</button>
                        <input type="number" class="qty-inp quantity-input" data-index="${index}"
                               value="${qty}" min="1" aria-label="Quantity for ${item.name}">
                        <button class="qty-btn increase-qty" data-index="${index}" aria-label="Increase">+</button>
                    </div>
                </div>
                <div class="ci-sub item-subtotal" data-ngn="${subtotal}">${fmt(subtotal)}</div>
                <div class="ci-remove">
                    <button class="ci-remove-btn remove-item-btn" data-index="${index}" aria-label="Remove ${item.name}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`;
            cartItemsDisplay.appendChild(row);
        });

        recalcTotals();
        attachListeners();
    }

    // ── Event listeners ───────────────────────────────────────────────────────
    function attachListeners() {
        cartItemsDisplay.querySelectorAll('.decrease-qty, .increase-qty').forEach(btn => {
            btn.addEventListener('click', handleQtyBtn);
        });
        cartItemsDisplay.querySelectorAll('.quantity-input').forEach(inp => {
            inp.addEventListener('change', handleQtyInput);
        });
        cartItemsDisplay.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', handleRemove);
        });
    }

    clearCartBtn && clearCartBtn.addEventListener('click', () => {
        if (cart.length === 0) return;
        const doIt = () => { cart = []; saveCart(); render(); };
        if (window.Swal) {
            Swal.fire({
                title: 'Clear cart?',
                text: 'All items will be removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clear it',
            }).then(r => { if (r.isConfirmed) doIt(); });
        } else {
            if (confirm('Clear all items from your cart?')) doIt();
        }
    });

    function handleQtyBtn(e) {
        const idx = parseInt(e.currentTarget.dataset.index);
        if (isNaN(idx) || !cart[idx]) return;
        const delta = e.currentTarget.classList.contains('increase-qty') ? 1 : -1;
        cart[idx].quantity = Math.max(1, (cart[idx].quantity || 1) + delta);
        saveCart();
        updateRow(idx);
        recalcTotals();
    }

    function handleQtyInput(e) {
        const idx = parseInt(e.currentTarget.dataset.index);
        if (isNaN(idx) || !cart[idx]) return;
        const val = parseInt(e.currentTarget.value) || 1;
        cart[idx].quantity = Math.max(1, val);
        e.currentTarget.value = cart[idx].quantity;
        saveCart();
        updateRow(idx);
        recalcTotals();
    }

    function handleRemove(e) {
        const idx = parseInt(e.currentTarget.dataset.index);
        if (isNaN(idx) || !cart[idx]) return;
        const doRemove = () => { cart.splice(idx, 1); saveCart(); render(); };
        if (window.Swal) {
            Swal.fire({
                title: 'Remove item?',
                text: `"${cart[idx].name}" will be removed from your cart.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it',
            }).then(r => { if (r.isConfirmed) doRemove(); });
        } else {
            if (confirm(`Remove "${cart[idx].name}" from your cart?`)) doRemove();
        }
    }

    // ── Live row update ───────────────────────────────────────────────────────
    function updateRow(idx) {
        const item = cart[idx];
        const row  = cartItemsDisplay.querySelector(`.ci-row[data-product-id="${item.id}"]`);
        if (!row) return;
        const price    = item.basePriceNgn || 0;
        const subtotal = price * item.quantity;
        const qtyInp   = row.querySelector('.quantity-input');
        const unitEl   = row.querySelector('.ci-unit');
        const subEl    = row.querySelector('.ci-sub');
        if (qtyInp)  qtyInp.value        = item.quantity;
        if (unitEl)  { unitEl.textContent = fmt(price);    unitEl.dataset.ngn = price; }
        if (subEl)   { subEl.textContent  = fmt(subtotal); subEl.dataset.ngn  = subtotal; }
    }

    // ── Currency change ───────────────────────────────────────────────────────
    window.addEventListener('currencyChanged', () => {
        cartItemsDisplay.querySelectorAll('[data-ngn]').forEach(el => {
            el.textContent = fmt(parseFloat(el.dataset.ngn) || 0);
        });
        recalcTotals();
    });

    window.updateCartAmounts = () => render();

    // ── Init ──────────────────────────────────────────────────────────────────
    loadCart();
    render();
    updateHeaderBadge();
});
</script>

@endsection
