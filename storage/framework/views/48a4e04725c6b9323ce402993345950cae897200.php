<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Admin Dashboard</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.audit.index')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-clock-rotate-left me-1"></i> Audit Trail
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">
            <section id="dashboard-analytics">

                
                <div class="row mt-1">
                    <div class="col-lg-4 col-sm-6 col-12 mb-2">
                        <div class="card stat-card stat-card--primary">
                            <div class="card-body">
                                <div class="stat-card__icon">
                                    <i class="fas fa-cart-shopping"></i>
                                </div>
                                <div class="stat-card__info">
                                    <div class="stat-card__label">Total Orders</div>
                                    <div class="stat-card__num"><?php echo e(number_format($totalTransactions)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 mb-2">
                        <div class="card stat-card stat-card--success">
                            <div class="card-body">
                                <div class="stat-card__icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="stat-card__info">
                                    <div class="stat-card__label">Total Products</div>
                                    <div class="stat-card__num"><?php echo e(number_format($totalProducts)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12 mb-2">
                        <div class="card stat-card stat-card--danger">
                            <div class="card-body">
                                <div class="stat-card__icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-card__info">
                                    <div class="stat-card__label">Total Users</div>
                                    <div class="stat-card__num"><?php echo e(number_format($totalUsers)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-xl-8 col-12 mb-3">
                        <?php echo $__env->make('admin.partials._chart-card', [
                            'chartId'   => 'revenueChart',
                            'title'     => 'Revenue — Last 12 Months',
                            'icon'      => 'fa-arrow-trend-up',
                            'colorClass'=> 'success',
                            'badgeId'   => 'revenueBadge',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-xl-4 col-12 mb-3">
                        <?php echo $__env->make('admin.partials._chart-card', [
                            'chartId'   => 'userChart',
                            'title'     => 'New User Registrations',
                            'icon'      => 'fa-user-plus',
                            'colorClass'=> 'primary',
                            'badgeId'   => 'userBadge',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-xl-5 col-12 mb-3">
                        <?php echo $__env->make('admin.partials._chart-card', [
                            'chartId'   => 'productChart',
                            'title'     => 'Top 10 Products Sold',
                            'icon'      => 'fa-boxes-stacked',
                            'colorClass'=> 'success',
                            'badgeId'   => 'productBadge',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-xl-4 col-12 mb-3">
                        <?php echo $__env->make('admin.partials._chart-card', [
                            'chartId'   => 'transactionChart',
                            'title'     => 'Orders Per Month',
                            'icon'      => 'fa-bag-shopping',
                            'colorClass'=> 'danger',
                            'badgeId'   => 'transactionBadge',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-xl-3 col-12 mb-3">
                        <?php echo $__env->make('admin.partials._chart-card', [
                            'chartId'   => 'statusChart',
                            'title'     => 'Orders by Status',
                            'icon'      => 'fa-chart-pie',
                            'colorClass'=> 'warning',
                            'badgeId'   => 'statusBadge',
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-wave-square me-50 text-primary" style="font-size:15px;"></i>
                                    Recent Admin Activity
                                </h4>
                                <a href="<?php echo e(route('admin.audit.index')); ?>" class="btn btn-sm btn-outline-primary waves-effect">
                                    View Full Audit Trail
                                </a>
                            </div>

                            <?php if($recentActivity->isEmpty()): ?>
                                <div class="card-body text-center text-muted py-4">
                                    <i class="fas fa-clock" style="font-size:30px;opacity:.25;"></i>
                                    <p class="mt-1 mb-0" style="font-size:13px;">No admin activity recorded yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="dashActivityTable" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th style="width:160px;">When</th>
                                                <th style="width:160px;">Admin</th>
                                                <th style="width:90px;">Event</th>
                                                <th style="width:140px;">Record</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $eventColor = match($log->event) {
                                                    'created' => 'success',
                                                    'updated' => 'info',
                                                    'deleted' => 'danger',
                                                    default   => 'secondary',
                                                };
                                                $auditStr = fn($v) => match(true) {
                                                    is_null($v)  => '—',
                                                    is_bool($v)  => ($v ? 'true' : 'false'),
                                                    is_array($v) => \Illuminate\Support\Str::limit(json_encode($v), 50),
                                                    default      => \Illuminate\Support\Str::limit((string)$v, 50),
                                                };
                                            ?>
                                            <tr>
                                                <td data-label="When" class="text-muted" style="white-space:nowrap;">
                                                    <?php echo e($log->created_at->format('d M Y, H:i')); ?><br>
                                                    <small style="font-size:11px;"><?php echo e($log->created_at->diffForHumans()); ?></small>
                                                </td>
                                                <td data-label="Admin">
                                                    <span class="fw-bolder"><?php echo e($log->user_name ?? 'System'); ?></span>
                                                    <?php if($log->ip_address): ?>
                                                        <br><small class="text-muted" style="font-size:11px;"><?php echo e($log->ip_address); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td data-label="Event">
                                                    <span class="badge bg-light-<?php echo e($eventColor); ?> text-<?php echo e($eventColor); ?>">
                                                        <?php echo e(ucfirst($log->event)); ?>

                                                    </span>
                                                </td>
                                                <td data-label="Record">
                                                    <span class="fw-bolder"><?php echo e($log->model_label); ?></span>
                                                    <span class="text-muted">&nbsp;#<?php echo e($log->auditable_id); ?></span>
                                                </td>
                                                <td data-label="Detail" style="max-width:400px;">
                                                    <?php if($log->event === 'updated' && $log->new_values): ?>
                                                        <?php $__currentLoopData = $log->new_values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $newVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div style="white-space:normal;line-height:1.6;">
                                                                <span class="text-muted"><?php echo e($field); ?>:</span>
                                                                <span class="text-danger" style="text-decoration:line-through;"><?php echo e($auditStr($log->old_values[$field] ?? null)); ?></span>
                                                                <span class="text-success">→ <?php echo e($auditStr($newVal)); ?></span>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php elseif($log->event === 'created'): ?>
                                                        <span class="text-muted">New record created</span>
                                                    <?php elseif($log->event === 'deleted'): ?>
                                                        <span class="text-muted">Record deleted</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-center py-75">
                                    <a href="<?php echo e(route('admin.audit.index')); ?>" class="small text-primary">
                                        See all activity &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
</div>

<style>
/* ── Stat cards ─────────────────────────────────────────── */
.stat-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(34, 41, 47, .08);
    transition: transform .2s ease, box-shadow .2s ease;
    overflow: hidden;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(34, 41, 47, .14);
}
.stat-card .card-body {
    display: flex;
    align-items: center;
    gap: 1.15rem;
    padding: 1.75rem 1.75rem;
}
.stat-card__icon {
    flex-shrink: 0;
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: #fff;
}
.stat-card__info { min-width: 0; }
.stat-card__label {
    font-size: 13px;
    font-weight: 600;
    color: #8a8d93;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 4px;
}
.stat-card__num {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.1;
    color: #4b4b4b;
}

/* Colour themes — soft gradient icon badge + matching left accent */
.stat-card--primary .stat-card__icon { background: linear-gradient(135deg, #7367f0, #9e95f5); box-shadow: 0 6px 14px rgba(115,103,240,.4); }
.stat-card--success .stat-card__icon { background: linear-gradient(135deg, #28c76f, #55d98d); box-shadow: 0 6px 14px rgba(40,199,111,.4); }
.stat-card--danger  .stat-card__icon { background: linear-gradient(135deg, #ea5455, #f08182); box-shadow: 0 6px 14px rgba(234,84,85,.4); }

@media (max-width: 575px) {
    .stat-card .card-body { padding: 1.4rem 1.4rem; gap: 1rem; }
    .stat-card__icon { width: 54px; height: 54px; font-size: 22px; }
    .stat-card__num { font-size: 1.7rem; }
}

.dash-chart-wrap { position: relative; min-height: 220px; }
.chart-loader, .chart-empty {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
}
.chart-empty { flex-direction: column; text-align: center; }

/* Responsive table — stack the Recent Admin Activity rows into cards on mobile */
@media (max-width: 768px) {
    #dashActivityTable thead { display: none; }
    #dashActivityTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #dashActivityTable td {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.5rem 0.75rem;
        border: none;
        border-bottom: 1px solid #f3f2f7;
        max-width: none !important;
        white-space: normal !important;
    }
    #dashActivityTable td:last-child { border-bottom: none; }
    #dashActivityTable td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #b9b9c3;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        flex-shrink: 0;
        padding-right: 0.5rem;
    }
    #dashActivityTable td > * { text-align: right; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    Chart.defaults.font.family = "'Montserrat','Helvetica Neue',Arial,sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = '#6e6b7b';

    const tooltipDefaults = {
        enabled: true,
        backgroundColor: '#fff',
        titleColor: '#5e5873',
        bodyColor: '#5e5873',
        borderColor: '#ebe9f1',
        borderWidth: 1,
        padding: 10,
    };

    function dataSum(arr) { return arr.reduce((a, b) => a + Number(b), 0); }

    function setBadge(id, arr, prefix) {
        const el = document.getElementById(id);
        if (!el) return;
        const s = dataSum(arr);
        el.textContent = prefix + (s > 0 ? s.toLocaleString('en-NG') : '0');
    }

    async function loadChart({ canvasId, loaderId, emptyId, badgeId, badgePrefix = '', url, type, options }) {
        const canvas = document.getElementById(canvasId);
        const loader = document.getElementById(loaderId);
        const empty  = document.getElementById(emptyId);

        if (!canvas) return;

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('HTTP ' + res.status + ' from ' + url);

            const payload = await res.json();

            const hasData = Array.isArray(payload.datasets)
                && payload.datasets.length
                && dataSum(payload.datasets[0].data) > 0;

            if (loader) loader.style.display = 'none';

            if (!hasData) {
                if (empty) empty.style.display = 'flex';
                setBadge(badgeId, [0], badgePrefix);
                return;
            }

            canvas.style.display = 'block';
            setBadge(badgeId, payload.datasets[0].data, badgePrefix);

            new Chart(canvas.getContext('2d'), { type, data: payload, options });

        } catch (err) {
            if (loader) loader.style.display = 'none';
            if (empty)  empty.style.display  = 'flex';
            const badge = document.getElementById(badgeId);
            if (badge) { badge.textContent = 'Error'; badge.className = 'badge bg-light-danger text-danger'; }
            console.error('[Dashboard]', canvasId, err.message);
        }
    }

    const scaleOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false }, tooltip: tooltipDefaults },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
        },
    };

    // ── Revenue (line) ──────────────────────────────────────────────────────
    loadChart({
        canvasId: 'revenueChart', loaderId: 'revenueChartLoader',
        emptyId:  'revenueChartEmpty', badgeId: 'revenueBadge', badgePrefix: '₦',
        url:  '<?php echo e(url("/admin/data/revenue-analytics")); ?>',
        type: 'line',
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...tooltipDefaults,
                    callbacks: { label: ctx => '₦' + Number(ctx.parsed.y).toLocaleString('en-NG', { maximumFractionDigits: 0 }) },
                },
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true, grid: { color: '#f0f0f0' },
                    ticks: { callback: v => '₦' + Number(v).toLocaleString('en-NG', { maximumFractionDigits: 0 }) },
                },
            },
        },
    });

    // ── User registrations (line) ────────────────────────────────────────────
    loadChart({
        canvasId: 'userChart', loaderId: 'userChartLoader',
        emptyId:  'userChartEmpty', badgeId: 'userBadge',
        url:  '<?php echo e(url("/admin/data/user-analytics")); ?>',
        type: 'line',
        options: scaleOpts,
    });

    // ── Top products (horizontal bar) ────────────────────────────────────────
    loadChart({
        canvasId: 'productChart', loaderId: 'productChartLoader',
        emptyId:  'productChartEmpty', badgeId: 'productBadge',
        url:  '<?php echo e(url("/admin/data/product-analytics")); ?>',
        type: 'bar',
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false }, tooltip: tooltipDefaults },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } },
            },
        },
    });

    // ── Orders per month (bar) ───────────────────────────────────────────────
    loadChart({
        canvasId: 'transactionChart', loaderId: 'transactionChartLoader',
        emptyId:  'transactionChartEmpty', badgeId: 'transactionBadge',
        url:  '<?php echo e(url("/admin/data/transaction-analytics")); ?>',
        type: 'bar',
        options: scaleOpts,
    });

    // ── Orders by status (doughnut) ──────────────────────────────────────────
    loadChart({
        canvasId: 'statusChart', loaderId: 'statusChartLoader',
        emptyId:  'statusChartEmpty', badgeId: 'statusBadge',
        url:  '<?php echo e(url("/admin/data/order-status-analytics")); ?>',
        type: 'doughnut',
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: {
                legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } },
                tooltip: tooltipDefaults,
            },
            cutout: '62%',
        },
    });

});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>