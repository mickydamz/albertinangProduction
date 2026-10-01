<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0"><?php echo e($title); ?></h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo e($backRoute); ?>"><?php echo e(ucfirst($type)); ?>s</a></li>
                            <li class="breadcrumb-item active"><?php echo e($title); ?></li>
                        </ol>
                    </div>
                    <a href="<?php echo e($backRoute); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body"><?php echo e($errors->first()); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body"><?php echo e(session('error')); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title mb-0">Log a <?php echo e($type); ?> for an order</h4>
                        </div>
                        <div class="card-body pt-2">
                            <form method="POST" action="<?php echo e($storeRoute); ?>" id="requestForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="order_id" id="orderId" value="<?php echo e(old('order_id')); ?>">

                                
                                <div class="mb-3 position-relative">
                                    <label class="form-label">Order</label>
                                    <input type="text" id="orderSearch" class="form-control" autocomplete="off"
                                           placeholder="Search by order number, customer name or email…">
                                    <ul id="orderSuggestions" class="list-group position-absolute w-100 shadow-sm"
                                        style="z-index:20; display:none; max-height:280px; overflow:auto;"></ul>

                                    <div id="chosenOrder" class="mt-2" style="display:none;">
                                        <span class="badge bg-light-primary text-primary p-2" style="font-size:13px;">
                                            <i class="fas fa-receipt me-1"></i>
                                            <span id="chosenOrderText"></span>
                                            <button type="button" class="btn-close ms-2" id="clearOrder"
                                                    style="font-size:9px; vertical-align:middle;"></button>
                                        </span>
                                    </div>
                                </div>

                                
                                <div class="mb-3">
                                    <label class="form-label">Reason</label>
                                    <textarea name="reason" class="form-control" rows="3" required minlength="10" maxlength="1000"
                                              placeholder="Why is this order being <?php echo e($type === 'return' ? 'returned' : 'cancelled'); ?>? (min 10 characters)"><?php echo e(old('reason')); ?></textarea>
                                </div>

                                <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                                    <i class="fas fa-plus me-1"></i> Log <?php echo e(ucfirst($type)); ?>

                                </button>
                                <span id="submitHint" class="text-muted ms-2" style="font-size:12px;">Select an order first.</span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const searchOrdersUrl = "<?php echo e(route('admin.orders.search')); ?>";
const searchEl   = document.getElementById('orderSearch');
const suggestEl  = document.getElementById('orderSuggestions');
const orderIdEl  = document.getElementById('orderId');
const chosenWrap = document.getElementById('chosenOrder');
const chosenText = document.getElementById('chosenOrderText');
const submitBtn  = document.getElementById('submitBtn');
const submitHint = document.getElementById('submitHint');
let timer;

function refreshSubmit() {
    const ok = !!orderIdEl.value;
    submitBtn.disabled = !ok;
    submitHint.style.display = ok ? 'none' : 'inline';
}

searchEl.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { suggestEl.style.display = 'none'; return; }
    timer = setTimeout(() => {
        fetch(searchOrdersUrl + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.length) { suggestEl.style.display = 'none'; return; }
                suggestEl.innerHTML = data.map(o => `
                    <li class="list-group-item list-group-item-action" role="button"
                        data-id="${o.id}"
                        data-label="${('#' + o.order_number + ' · ' + o.customer).replace(/"/g,'&quot;')}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold" style="font-size:13.5px;">#${o.order_number}</div>
                                <div class="text-muted" style="font-size:12px;">${o.customer} · ${o.status}</div>
                            </div>
                            <span class="text-muted" style="font-size:12px;">N ${Number(o.total).toLocaleString('en-NG')}</span>
                        </div>
                    </li>`).join('');
                suggestEl.style.display = 'block';
            });
    }, 280);
});

suggestEl.addEventListener('click', function (e) {
    const li = e.target.closest('li[data-id]');
    if (!li) return;
    orderIdEl.value = li.dataset.id;
    chosenText.textContent = li.dataset.label;
    chosenWrap.style.display = 'block';
    suggestEl.style.display = 'none';
    searchEl.value = '';
    refreshSubmit();
});

document.getElementById('clearOrder').addEventListener('click', function () {
    orderIdEl.value = '';
    chosenWrap.style.display = 'none';
    refreshSubmit();
});

document.addEventListener('click', function (e) {
    if (!suggestEl.contains(e.target) && e.target !== searchEl) suggestEl.style.display = 'none';
});

refreshSubmit();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/orders/request-create.blade.php ENDPATH**/ ?>