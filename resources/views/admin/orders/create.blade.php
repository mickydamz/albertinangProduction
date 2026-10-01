@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Create Order</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
                @csrf
                <div id="itemsContainer"></div>{{-- hidden inputs live here, inside the form --}}

                <div class="row g-2">

                    {{-- ══════════ LEFT COLUMN ══════════ --}}
                    <div class="col-lg-8">

                        {{-- Customer --}}
                        <div class="card mb-2">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0"><i class="fas fa-user text-primary me-50"></i>Customer</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label">Search existing user</label>
                                        <div class="position-relative">
                                            <input type="text" id="userSearch" class="form-control"
                                                   placeholder="Name, email or phone…" autocomplete="off">
                                            <ul id="userSuggestions" class="suggest-list"></ul>
                                        </div>
                                        <div id="selectedUser" class="selected-badge" style="display:none;"></div>
                                        <input type="hidden" name="user_id" id="userId">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Or guest email</label>
                                        <input type="email" name="customer_email" id="customerEmail"
                                               class="form-control" placeholder="customer@email.com">
                                        <small class="text-muted">Required only if no user is selected</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Order Items --}}
                        <div class="card mb-2">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0"><i class="fas fa-boxes text-primary me-50"></i>Order Items</h4>
                            </div>
                            <div class="card-body">
                                {{-- Product search --}}
                                <div class="position-relative mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="productSearch" class="form-control"
                                               placeholder="Search products by name or SKU…" autocomplete="off">
                                    </div>
                                    <ul id="productSuggestions" class="suggest-list suggest-list--wide"></ul>
                                </div>

                                {{-- Items table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th style="width:52px"></th>
                                                <th>Product</th>
                                                <th style="width:80px">Qty</th>
                                                <th style="width:140px">Unit Price (N)</th>
                                                <th style="width:110px" class="text-end">Line Total</th>
                                                <th style="width:36px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                            <tr id="emptyRow">
                                                <td colspan="6" class="text-center text-muted py-3" style="font-size:13px;">
                                                    <i class="fas fa-search me-1 opacity-50"></i>Search above to add products
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot id="itemsFoot" style="display:none;">
                                            <tr>
                                                <td colspan="4" class="text-end fw-semibold text-muted" style="font-size:13px;">Items Subtotal</td>
                                                <td class="text-end fw-bold" id="subtotalDisplay">N 0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ══════════ RIGHT COLUMN ══════════ --}}
                    <div class="col-lg-4">

                        {{-- Fulfilment --}}
                        <div class="card mb-2">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0"><i class="fas fa-truck text-primary me-50"></i>Fulfilment</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Method</label>
                                    <div class="d-flex gap-2">
                                        <label class="fulfil-radio flex-fill text-center py-2 rounded border" id="radioPickup" style="cursor:pointer;">
                                            <input type="radio" name="fulfillment_method" value="pickup" checked class="d-none" id="fulfillMethodPickup">
                                            <i class="fas fa-store d-block mb-25"></i> Pickup
                                        </label>
                                        <label class="fulfil-radio flex-fill text-center py-2 rounded border" id="radioDelivery" style="cursor:pointer;">
                                            <input type="radio" name="fulfillment_method" value="delivery" class="d-none" id="fulfillMethodDelivery">
                                            <i class="fas fa-truck d-block mb-25"></i> Delivery
                                        </label>
                                    </div>
                                </div>

                                <div id="pickupSection">
                                    <label class="form-label">Pickup Point</label>
                                    <select name="pickup_point_id" class="form-select">
                                        <option value="">— Select pickup point —</option>
                                        @foreach ($pickupPoints as $pp)
                                            <option value="{{ $pp->id }}">{{ $pp->name }}@if($pp->location) — {{ $pp->location->name }}@endif</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="deliverySection" style="display:none">
                                    <div class="mb-2">
                                        <label class="form-label">State</label>
                                        <select name="delivery_state_id" id="deliveryStateSelect" class="form-select">
                                            <option value="">— Select state —</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Area</label>
                                        <select name="delivery_location_id" id="deliveryLocationSelect" class="form-select">
                                            <option value="">— Select area —</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Shipping Address</label>
                                        <textarea name="shipping_address" id="shippingAddress" class="form-control" rows="2"
                                                  placeholder="Street address, landmark, city…" maxlength="500"></textarea>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <label class="form-label">Shipping Cost (N)</label>
                                    <input type="number" name="shipping_cost" class="form-control"
                                           min="0" step="0.01" value="0" id="shippingCost">
                                </div>
                            </div>
                        </div>

                        {{-- Payment --}}
                        <div class="card mb-2">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0"><i class="fas fa-credit-card text-primary me-50"></i>Payment</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="form-label">Method</label>
                                    <select name="payment_method" id="paymentMethod" class="form-select" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="pos">POS</option>
                                        <option value="paystack">Paystack</option>
                                        <option value="stripe">Stripe</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-2" id="referenceField" style="display:none">
                                    <label class="form-label">Payment Reference</label>
                                    <input type="text" name="payment_reference" class="form-control"
                                           placeholder="Leave blank to auto-generate">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Coupon Discount (N)</label>
                                    <input type="number" name="coupon_discount_ngn" class="form-control"
                                           min="0" step="0.01" value="0" id="couponDiscount">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Internal Notes <span class="text-muted fw-normal" style="font-size:12px;">(optional)</span></label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="Internal note about this order…"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Order Summary --}}
                        <div class="card mb-2">
                            <div class="card-header border-bottom">
                                <h4 class="card-title mb-0"><i class="fas fa-receipt text-primary me-50"></i>Summary</h4>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-unstyled mb-0 summary-list">
                                    <li><span class="text-muted">Items Subtotal</span><span id="summarySubtotal">N 0.00</span></li>
                                    <li><span class="text-muted">Shipping</span><span id="summaryShipping">N 0.00</span></li>
                                    <li><span class="text-muted">Discount</span><span id="summaryDiscount" class="text-success">— N 0.00</span></li>
                                    <li class="summary-total"><span>Total</span><span id="summaryTotal">N 0.00</span></li>
                                </ul>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-success w-100 py-1" disabled>
                            <i class="fas fa-plus-circle me-50"></i> Create Order
                        </button>
                        <p id="submitHint" class="text-muted text-center mt-1 mb-0" style="font-size:12px;">
                            Add at least one product and select a customer to continue
                        </p>

                    </div>

                </div>{{-- /.row --}}
            </form>
        </div>
    </div>
</div>

<style>
    .suggest-list {
        position: absolute; left: 0; right: 0; top: calc(100% + 2px);
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,.1);
        max-height: 280px; overflow-y: auto;
        list-style: none; margin: 0; padding: 4px; z-index: 9999;
        display: none;
    }
    .suggest-list--wide { min-width: 400px; }
    .suggest-list li {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 10px; border-radius: 7px;
        cursor: pointer; font-size: 13.5px;
        transition: background .1s;
    }
    .suggest-list li:hover { background: #f3f4f6; }
    .suggest-list .sug-img {
        width: 40px; height: 40px; border-radius: 6px;
        object-fit: contain; background: #f9fafb;
        border: 1px solid #e5e7eb; flex-shrink: 0;
    }
    .suggest-list .sug-placeholder {
        width: 40px; height: 40px; border-radius: 6px;
        background: #f3f4f6; border: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: center;
        color: #9ca3af; font-size: 16px; flex-shrink: 0;
    }
    .suggest-list .sug-name { font-weight: 600; color: #111827; }
    .suggest-list .sug-meta { font-size: 12px; color: #6b7280; }

    .selected-badge {
        margin-top: 8px; padding: 6px 10px;
        background: #f0fce8; border: 1px solid #bbf7d0;
        border-radius: 8px; font-size: 13px; color: #166534;
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px;
    }
    .selected-badge button {
        background: none; border: none; cursor: pointer;
        color: #6b7280; font-size: 16px; line-height: 1; padding: 0;
        flex-shrink: 0;
    }
    .selected-badge button:hover { color: #dc2626; }

    #itemsTable tbody td { padding: 8px 12px !important; }
    #itemsTable .item-img {
        width: 40px; height: 40px; border-radius: 6px;
        object-fit: contain; background: #f9fafb;
        border: 1px solid #e5e7eb;
    }
    #itemsTable .item-placeholder {
        width: 40px; height: 40px; border-radius: 6px;
        background: #f3f4f6; border: 1px solid #e5e7eb;
        display: flex; align-items: center; justify-content: center;
        color: #d1d5db; font-size: 18px;
    }

    .fulfil-radio { font-size: 13px; font-weight: 500; color: #374151; transition: all .15s; }
    .fulfil-radio.active {
        background: #f0fce8 !important; border-color: #3d8012 !important;
        color: #3d8012 !important;
    }

    .summary-list { font-size: 13.5px; }
    .summary-list li {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .summary-list li:last-child { border-bottom: none; }
    .summary-total {
        font-weight: 700; font-size: 15px !important;
        background: #f9fafb; border-radius: 0 0 10px 10px;
    }

    .mb-25 { margin-bottom: 0.25rem !important; }
    .me-50 { margin-right: 0.375rem !important; }
    .gap-2 { gap: 0.5rem !important; }
</style>

@push('scripts')
<script>
const searchProductsUrl = "{{ route('admin.orders.search-products') }}";
const searchUsersUrl    = "{{ route('admin.orders.search-users') }}";
const locationsUrl      = "/api/locations";

let items = [];
let productData = {};   // id -> full product (carries installation_options)
let selectedUserId = null;

function fmt(n) {
    return 'N ' + Number(n).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Render items table ───────────────────────────────────────────────────────
function renderItems() {
    const tbody     = document.getElementById('itemsBody');
    const tfoot     = document.getElementById('itemsFoot');
    const container = document.getElementById('itemsContainer');

    container.innerHTML = '';

    let subtotal = 0;

    if (items.length === 0) {
        tbody.innerHTML = '<tr id="emptyRow"><td colspan="6" class="text-center text-muted py-3" style="font-size:13px;"><i class="fas fa-search me-1 opacity-50"></i>Search above to add products</td></tr>';
        tfoot.style.display = 'none';
    } else {
        tbody.innerHTML = items.map((item, i) => {
            const extra = Number(item.installation_extra) || 0;
            const line  = (item.price + extra) * item.quantity;
            subtotal += line;
            const imgCell = item.image
                ? `<img src="${item.image}" class="item-img" alt="">`
                : `<div class="item-placeholder"><i class="fas fa-image"></i></div>`;
            const opts = item.installation_options || [];
            const installCell = opts.length ? `
                <select class="form-select form-select-sm install-select mt-1" data-i="${i}" style="max-width:250px; font-size:12px;">
                    <option value="">No installation</option>
                    ${opts.map(o => `<option value="${String(o.label).replace(/"/g,'&quot;')}" data-extra="${o.price}" ${item.installation_option === o.label ? 'selected' : ''}>${o.label} (+${fmt(o.price)})</option>`).join('')}
                </select>` : '';
            return `<tr>
                <td>${imgCell}</td>
                <td>
                    <div class="fw-semibold" style="font-size:13.5px;">${item.name}</div>
                    ${item.sku ? `<div class="text-muted" style="font-size:11px;">${item.sku}</div>` : ''}
                    ${installCell}
                </td>
                <td><input type="number" class="form-control form-control-sm qty-input" data-i="${i}" value="${item.quantity}" min="1" style="width:64px;"></td>
                <td><input type="number" class="form-control form-control-sm price-input" data-i="${i}" value="${item.price}" min="0" step="0.01" style="width:120px;"></td>
                <td class="text-end fw-semibold">${fmt(line)}</td>
                <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-item" data-i="${i}" style="width:28px;height:28px;padding:0;line-height:1;">×</button></td>
            </tr>`;
        }).join('');
        tfoot.style.display = '';

        items.forEach((item, i) => {
            const extra = Number(item.installation_extra) || 0;
            container.innerHTML += `
                <input type="hidden" name="items[${i}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${i}][quantity]"   value="${item.quantity}">
                <input type="hidden" name="items[${i}][price]"       value="${item.price + extra}">
                <input type="hidden" name="items[${i}][installation_option]"    value="${(item.installation_option || '').replace(/"/g,'&quot;')}">
                <input type="hidden" name="items[${i}][installation_extra_ngn]" value="${extra}">
            `;
        });
    }

    document.getElementById('subtotalDisplay').textContent = fmt(subtotal);
    updateSummary(subtotal);
    checkSubmit();
}

function updateSummary(subtotal) {
    const shipping = parseFloat(document.getElementById('shippingCost').value)  || 0;
    const discount = parseFloat(document.getElementById('couponDiscount').value) || 0;
    document.getElementById('summarySubtotal').textContent = fmt(subtotal);
    document.getElementById('summaryShipping').textContent = fmt(shipping);
    document.getElementById('summaryDiscount').textContent = '— ' + fmt(discount);
    document.getElementById('summaryTotal').textContent    = fmt(subtotal + shipping - discount);
}

function checkSubmit() {
    const btn  = document.getElementById('submitBtn');
    const hint = document.getElementById('submitHint');
    const hasItems = items.length > 0;
    const hasCustomer = !!selectedUserId || document.getElementById('customerEmail').value.trim();
    const ok = hasItems && hasCustomer;
    btn.disabled = !ok;
    hint.style.display = ok ? 'none' : 'block';
}

// ── Product search ────────────────────────────────────────────────────────────
const productSearchEl  = document.getElementById('productSearch');
const productSuggestEl = document.getElementById('productSuggestions');
let productTimer;

productSearchEl.addEventListener('input', function () {
    clearTimeout(productTimer);
    const q = this.value.trim();
    if (q.length < 2) { productSuggestEl.style.display = 'none'; return; }
    productTimer = setTimeout(() => {
        fetch(searchProductsUrl + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.length) { productSuggestEl.style.display = 'none'; return; }
                data.forEach(p => { productData[p.id] = p; });
                productSuggestEl.innerHTML = data.map(p => `
                    <li data-id="${p.id}"
                        data-name="${p.name.replace(/"/g,'&quot;')}"
                        data-price="${p.price}"
                        data-sku="${(p.sku || '').replace(/"/g,'&quot;')}"
                        data-image="${(p.image || '').replace(/"/g,'&quot;')}">
                        ${p.image
                            ? `<img src="${p.image}" class="sug-img" alt="">`
                            : `<div class="sug-placeholder"><i class="fas fa-image"></i></div>`}
                        <div>
                            <div class="sug-name">${p.name}</div>
                            <div class="sug-meta">${p.sku ?? ''} &nbsp;·&nbsp; N ${Number(p.price).toLocaleString('en-NG')}</div>
                        </div>
                    </li>
                `).join('');
                productSuggestEl.style.display = 'block';
            });
    }, 280);
});

productSuggestEl.addEventListener('click', function (e) {
    const li = e.target.closest('li[data-id]');
    if (!li) return;
    const product_id = li.dataset.id;
    const name       = li.dataset.name;
    const price      = parseFloat(li.dataset.price) || 0;
    const sku        = li.dataset.sku || null;
    const image      = li.dataset.image || null;

    const p = productData[product_id] || {};

    const existing = items.findIndex(i => i.product_id == product_id);
    if (existing >= 0) {
        items[existing].quantity++;
    } else {
        items.push({
            product_id, name, price, quantity: 1, sku, image,
            installation_options: p.installation_options || [],
            installation_option: '',
            installation_extra: 0,
        });
    }
    renderItems();
    productSearchEl.value = '';
    productSuggestEl.style.display = 'none';
    productSearchEl.focus();
});

// ── Item table events ─────────────────────────────────────────────────────────
document.getElementById('itemsTable').addEventListener('input', function (e) {
    const i = parseInt(e.target.dataset.i);
    if (isNaN(i)) return;
    if (e.target.classList.contains('qty-input'))   items[i].quantity = parseInt(e.target.value)   || 1;
    if (e.target.classList.contains('price-input')) items[i].price    = parseFloat(e.target.value) || 0;
    if (e.target.classList.contains('install-select')) {
        const opt = e.target.selectedOptions[0];
        items[i].installation_option = e.target.value || '';
        items[i].installation_extra  = parseFloat(opt && opt.dataset.extra) || 0;
    }
    renderItems();
});

document.getElementById('itemsTable').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    items.splice(parseInt(btn.dataset.i), 1);
    renderItems();
});

// ── Shipping / discount → update summary ─────────────────────────────────────
document.getElementById('shippingCost').addEventListener('input',   () => renderItems());
document.getElementById('couponDiscount').addEventListener('input', () => renderItems());
document.getElementById('customerEmail').addEventListener('input',  () => checkSubmit());

// ── Fulfilment radio toggle ───────────────────────────────────────────────────
function setFulfilment(val) {
    document.getElementById('pickupSection').style.display   = val === 'pickup'   ? 'block' : 'none';
    document.getElementById('deliverySection').style.display = val === 'delivery' ? 'block' : 'none';
    document.getElementById('radioPickup').classList.toggle('active',   val === 'pickup');
    document.getElementById('radioDelivery').classList.toggle('active', val === 'delivery');
}
setFulfilment('pickup');
document.getElementById('fulfillMethodPickup').addEventListener('change', () => setFulfilment('pickup'));
document.getElementById('fulfillMethodDelivery').addEventListener('change', () => setFulfilment('delivery'));
document.getElementById('radioPickup').addEventListener('click', () => {
    document.getElementById('fulfillMethodPickup').checked = true;
    setFulfilment('pickup');
});
document.getElementById('radioDelivery').addEventListener('click', () => {
    document.getElementById('fulfillMethodDelivery').checked = true;
    setFulfilment('delivery');
});

// ── Delivery state → load areas ───────────────────────────────────────────────
document.getElementById('deliveryStateSelect').addEventListener('change', function () {
    const stateId = this.value;
    const locSel  = document.getElementById('deliveryLocationSelect');
    if (!stateId) { locSel.innerHTML = '<option value="">— Select area —</option>'; return; }
    locSel.innerHTML = '<option value="">Loading…</option>';
    fetch(locationsUrl + '?state_id=' + stateId)
        .then(r => r.json())
        .then(data => {
            locSel.innerHTML = '<option value="">— Select area —</option>' +
                data.map(l => `<option value="${l.id}">${l.name}</option>`).join('');
        });
});

// ── Payment method → reference field ─────────────────────────────────────────
document.getElementById('paymentMethod').addEventListener('change', function () {
    document.getElementById('referenceField').style.display =
        ['paystack', 'stripe'].includes(this.value) ? 'block' : 'none';
});

// ── User search ───────────────────────────────────────────────────────────────
const userSearchEl  = document.getElementById('userSearch');
const userSuggestEl = document.getElementById('userSuggestions');
let userTimer;

userSearchEl.addEventListener('input', function () {
    clearTimeout(userTimer);
    const q = this.value.trim();
    if (q.length < 2) { userSuggestEl.style.display = 'none'; return; }
    userTimer = setTimeout(() => {
        fetch(searchUsersUrl + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.length) { userSuggestEl.style.display = 'none'; return; }
                userSuggestEl.innerHTML = data.map(u => `
                    <li data-id="${u.id}"
                        data-name="${u.name.replace(/"/g,'&quot;')}"
                        data-email="${u.email}">
                        <div class="sug-placeholder" style="width:32px;height:32px;font-size:13px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="sug-name">${u.name}</div>
                            <div class="sug-meta">${u.email}${u.phone_no ? ' · ' + u.phone_no : ''}</div>
                        </div>
                    </li>
                `).join('');
                userSuggestEl.style.display = 'block';
            });
    }, 280);
});

userSuggestEl.addEventListener('click', function (e) {
    const li = e.target.closest('li[data-id]');
    if (!li) return;
    selectedUserId = li.dataset.id;
    document.getElementById('userId').value        = li.dataset.id;
    document.getElementById('customerEmail').value = li.dataset.email;
    const badge = document.getElementById('selectedUser');
    badge.innerHTML = `<span><i class="fas fa-user me-1"></i>${li.dataset.name} <span style="opacity:.6">(${li.dataset.email})</span></span>
                       <button type="button" id="clearUser" title="Clear">×</button>`;
    badge.style.display = 'flex';
    userSearchEl.style.display = 'none';
    userSuggestEl.style.display = 'none';
    document.getElementById('clearUser').onclick = () => {
        selectedUserId = null;
        document.getElementById('userId').value   = '';
        badge.style.display   = 'none';
        userSearchEl.style.display = 'block';
        userSearchEl.value    = '';
        userSearchEl.focus();
        checkSubmit();
    };
    checkSubmit();
});

// ── Close dropdowns on outside click ─────────────────────────────────────────
document.addEventListener('click', e => {
    if (!productSearchEl.contains(e.target) && !productSuggestEl.contains(e.target))
        productSuggestEl.style.display = 'none';
    if (!userSearchEl.contains(e.target) && !userSuggestEl.contains(e.target))
        userSuggestEl.style.display = 'none';
});

// ── Form submit guard ─────────────────────────────────────────────────────────
document.getElementById('createOrderForm').addEventListener('submit', function (e) {
    if (items.length === 0) {
        e.preventDefault();
        alert('Please add at least one product.');
        return;
    }
    if (!selectedUserId && !document.getElementById('customerEmail').value.trim()) {
        e.preventDefault();
        alert('Please select a customer or enter a guest email.');
    }
});
</script>
@endpush
@endsection
