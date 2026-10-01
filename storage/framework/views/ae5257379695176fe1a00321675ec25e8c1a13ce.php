

<?php $__env->startSection('content'); ?>
<?php
    $sortLink = function ($column) use ($sortBy, $sortDir) {
        $newDir = ($sortBy === $column && $sortDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $newDir, 'page' => null]);
    };
    $sortIcon = function ($column) use ($sortBy, $sortDir) {
        $asc  = $sortBy === $column && $sortDir === 'asc';
        $desc = $sortBy === $column && $sortDir === 'desc';
        return '<span style="display:inline-flex; flex-direction:column; line-height:1; font-size:9px; color:#b9b9c3; margin-left:4px;">'
             . '<span style="' . ($asc  ? 'color:#5aab1f;' : '') . '">▲</span>'
             . '<span style="' . ($desc ? 'color:#5aab1f;' : '') . '">▼</span>'
             . '</span>';
    };

    $pageQuery = array_filter([
        'brand'  => $activeBrand ?? '',
        'search' => $search ?? '',
        'status' => $filterStatus ?? '',
        'stock'  => $filterStock ?? '',
        'sort'   => $sortBy ?? '',
        'dir'    => $sortDir ?? '',
    ], fn ($v) => $v !== '' && $v !== null);
?>



        <!-- Header -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Manager Dashboard</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                <div class="mb-1 breadcrumb-right">
                    <span class="text-muted">Welcome back, <strong><?php echo e(auth()->user()->name); ?></strong></span>
                </div>
            </div>
        </div>

        <div class="content-body">

            <!-- Stats Row -->
            <div class="row mt-2">
                <div class="col-lg-3 col-sm-6 col-12 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0"><?php echo e($totalProducts); ?></h2>
                                <p class="card-text">Assigned Products</p>
                            </div>
                            <div class="avatar bg-light-primary p-50 m-0">
                                <div class="avatar-content"><i data-feather="box" class="font-medium-5"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0"><?php echo e($totalBrands); ?></h2>
                                <p class="card-text">Brands Managed</p>
                            </div>
                            <div class="avatar bg-light-success p-50 m-0">
                                <div class="avatar-content"><i data-feather="tag" class="font-medium-5"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0"><?php echo e($inStock); ?></h2>
                                <p class="card-text">In Stock</p>
                            </div>
                            <div class="avatar bg-light-info p-50 m-0">
                                <div class="avatar-content"><i data-feather="check-circle" class="font-medium-5"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 col-12 mb-2">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h2 class="fw-bolder mb-0"><?php echo e($outOfStock); ?></h2>
                                <p class="card-text">Out of Stock</p>
                            </div>
                            <div class="avatar bg-light-danger p-50 m-0">
                                <div class="avatar-content"><i data-feather="alert-circle" class="font-medium-5"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Stats Row -->

            <!-- Products Card -->
            <div class="card">

                <!-- Title + line-style brand tabs -->
                <div class="card-header d-block pb-0">
                    <h4 class="card-title mb-1"><i class="fas fa-box me-1"></i> My Products</h4>

                    <ul class="nav nav-tabs manager-brand-tabs mb-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e($activeBrand === null ? 'active' : ''); ?>"
                               href="<?php echo e(request()->fullUrlWithQuery(['brand' => null, 'page' => null])); ?>">
                                All
                                <span class="badge rounded-pill bg-light-secondary ms-50"><?php echo e($totalProducts); ?></span>
                            </a>
                        </li>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e($activeBrand === $brand->brand ? 'active' : ''); ?>"
                                   href="<?php echo e(request()->fullUrlWithQuery(['brand' => $brand->brand, 'page' => null])); ?>">
                                    <?php echo e($brand->brand); ?>

                                    <span class="badge rounded-pill bg-light-secondary ms-50"><?php echo e($brand->total); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                <!-- Toolbar (auto-submit) -->
                <div class="card-body border-bottom py-1">
                    <form method="GET" action="<?php echo e(route('manager.products.index')); ?>" id="managerFilterForm"
                          class="d-flex flex-wrap align-items-center gap-50 w-100">

                        <?php if($activeBrand): ?>
                            <input type="hidden" name="brand" value="<?php echo e($activeBrand); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="sort" value="<?php echo e($sortBy); ?>">
                        <input type="hidden" name="dir"  value="<?php echo e($sortDir); ?>">

                        <div style="flex: 1 1 auto; min-width: 200px;">
                            <input type="search" name="search" value="<?php echo e($search); ?>"
                                   class="form-control" placeholder="Search name, description, brand…">
                        </div>

                        <div class="d-block d-sm-none w-100" style="height:0;"></div>

                        <select name="status" class="form-select" style="width:140px; flex-shrink:0;"
                                onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="1" <?php echo e($filterStatus === '1' ? 'selected' : ''); ?>>Active</option>
                            <option value="0" <?php echo e($filterStatus === '0' ? 'selected' : ''); ?>>Inactive</option>
                        </select>

                        <select name="stock" class="form-select" style="width:150px; flex-shrink:0;"
                                onchange="this.form.submit()">
                            <option value="">All Stock</option>
                            <option value="in"  <?php echo e($filterStock === 'in'  ? 'selected' : ''); ?>>In stock</option>
                            <option value="out" <?php echo e($filterStock === 'out' ? 'selected' : ''); ?>>Out of stock</option>
                        </select>

                        <a href="<?php echo e(route('manager.products.index', $activeBrand ? ['brand' => $activeBrand] : [])); ?>"
                           class="btn btn-icon btn-outline-secondary waves-effect"
                           data-bs-toggle="tooltip" title="Reset filters" style="flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 .49-3.96"></path></svg>
                        </a>

                    </form>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table table-hover align-middle mb-0" id="managerProductsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="cursor:pointer; white-space:nowrap; user-select:none;">
                                    <a href="<?php echo e($sortLink('name')); ?>" class="d-flex align-items-center text-body text-decoration-none">Product <?php echo $sortIcon('name'); ?></a>
                                </th>

                                <th>Brand</th>
                                <th style="cursor:pointer; white-space:nowrap; user-select:none;">
                                    <a href="<?php echo e($sortLink('price')); ?>" class="d-flex align-items-center text-body text-decoration-none">Price <?php echo $sortIcon('price'); ?></a>
                                </th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="cursor:pointer; white-space:nowrap; user-select:none;">
                                    <a href="<?php echo e($sortLink('stock')); ?>" class="d-flex align-items-center justify-content-center text-body text-decoration-none">Stock <?php echo $sortIcon('stock'); ?></a>
                                </th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td data-label="Product" class="fw-bold"><?php echo e($product->name); ?></td>

                                    <td data-label="Brand"><span class="badge bg-light-primary"><?php echo e($product->brand); ?></span></td>
                                    <td data-label="Price">₦<?php echo e(number_format($product->price, 2)); ?></td>
                                    <td data-label="Status">
                                        <div class="form-check form-switch d-flex align-items-center gap-50 ps-0 m-0">
                                            <input class="form-check-input product-toggle m-0" type="checkbox" role="switch"
                                                   id="toggle-<?php echo e($product->id); ?>"
                                                   data-id="<?php echo e($product->id); ?>"
                                                   data-url="<?php echo e(route('manager.products.toggleActive', $product->id)); ?>"
                                                   <?php echo e($product->is_active ? 'checked' : ''); ?>

                                                   style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                                            <label for="toggle-<?php echo e($product->id); ?>" class="form-check-label toggle-label-<?php echo e($product->id); ?> m-0"
                                                   style="font-size:12px; font-weight:500; cursor:pointer;
                                                          color:<?php echo e($product->is_active ? '#28c76f' : '#b0b0b0'); ?>;">
                                                <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

                                            </label>
                                        </div>
                                    </td>
                                    <td data-label="Stock" class="text-center">
                                        <?php if($product->stock > 0): ?>
                                            <span class="badge bg-light-success"><?php echo e($product->stock); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-light-danger">Out of stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Action" class="text-center">
                                        <a href="<?php echo e(route('manager.products.edit', $product->id)); ?>"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        <?php if($search || $filterStatus || $filterStock): ?>
                                            No products match your search/filters.
                                        <?php elseif($activeBrand): ?>
                                            No products for "<?php echo e($activeBrand); ?>".
                                        <?php else: ?>
                                            No products assigned yet.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="row mx-2 my-1 align-items-center">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info">
                            Showing <?php echo e($products->firstItem() ?? 0); ?> to <?php echo e($products->lastItem() ?? 0); ?> of <?php echo e($products->total()); ?> entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
                        <?php if($products->lastPage() > 1): ?>
                            <?php
                                $cur   = $products->currentPage();
                                $last  = $products->lastPage();
                                $start = max(1, $cur - 2);
                                $end   = min($last, $cur + 2);
                                $paged = $products->appends($pageQuery);
                            ?>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?php echo e($products->onFirstPage() ? 'disabled' : ''); ?>">
                                    <a class="page-link" href="<?php echo e($products->onFirstPage() ? '#' : $paged->previousPageUrl()); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                    </a>
                                </li>
                                <?php if($start > 1): ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo e($paged->url(1)); ?>">1</a></li>
                                    <?php if($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">…</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php for($p = $start; $p <= $end; $p++): ?>
                                    <li class="page-item <?php echo e($p === $cur ? 'active' : ''); ?>">
                                        <a class="page-link" href="<?php echo e($paged->url($p)); ?>"><?php echo e($p); ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if($end < $last): ?>
                                    <?php if($end < $last - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">…</span></li>
                                    <?php endif; ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo e($paged->url($last)); ?>"><?php echo e($last); ?></a></li>
                                <?php endif; ?>
                                <li class="page-item <?php echo e(!$products->hasMorePages() ? 'disabled' : ''); ?>">
                                    <a class="page-link" href="<?php echo e($products->hasMorePages() ? $paged->nextPageUrl() : '#'); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                    </a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
    .gap-50 { gap: 0.5rem !important; }

    /* ── Line-style brand tabs (Vuexy underline look) ── */
    .manager-brand-tabs {
        border-bottom: 1px solid #ebe9f1;
        flex-wrap: wrap;
        gap: 0;
    }
    .manager-brand-tabs .nav-item { margin-bottom: -1px; }
    .manager-brand-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        color: #6e6b7b;
        font-weight: 500;
        padding: 0.6rem 1rem;
        background: transparent;
        transition: color .15s ease, border-color .15s ease;
    }
    .manager-brand-tabs .nav-link:hover { color: #7367f0; }
    .manager-brand-tabs .nav-link.active {
        color: #7367f0;
        background: transparent;
        border-bottom-color: #7367f0;
    }
    .manager-brand-tabs .nav-link .badge { font-weight: 600; }
    .manager-brand-tabs .nav-link.active .badge {
        background-color: rgba(115,103,240,.12) !important;
        color: #7367f0 !important;
    }

    /* Tighten gap between tabs and table */
    #managerProductsTable thead th { border-top: none; }

    /* Pagination — admin green theme */
    .pagination .page-item.active .page-link { background-color:#5aab1f !important; border-color:#5aab1f !important; color:#fff !important; }
    .pagination .page-link { color:#5aab1f; }
    .pagination .page-item.disabled .page-link { pointer-events: none; }

    /* Sortable header hover */
    #managerProductsTable thead a:hover { color:#5aab1f !important; }

    /* Responsive toolbar */
    @media (max-width: 576px) {
        #managerFilterForm { flex-wrap: wrap; }
        #managerFilterForm > div:first-child { flex: 1 1 100%; }
        #managerFilterForm select { flex: 1 1 0; min-width: 0; width: auto !important; }
        #managerFilterForm .btn-icon { flex-shrink: 0; }
        .manager-brand-tabs { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .manager-brand-tabs .nav-link { white-space: nowrap; }
    }

    /* Responsive table — stacked cards on mobile */
    @media (max-width: 768px) {
        #managerProductsTable thead { display: none; }
        #managerProductsTable tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #ebe9f1;
            border-radius: 6px;
        }
        #managerProductsTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border: none;
            border-bottom: 1px solid #f3f2f7;
            font-size: 0.875rem;
        }
        #managerProductsTable td:last-child { border-bottom: none; }
        #managerProductsTable td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #b9b9c3;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
            padding-right: 0.5rem;
        }
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .forEach(el => new bootstrap.Tooltip(el));

    // ── Status toggle (AJAX) ──────────────────────────────────────────
    // Uses POST + _method=PATCH spoofing so it works even on hosts
    // that block PATCH requests at the server level.
    document.querySelectorAll('.product-toggle').forEach(toggle => {
      toggle.addEventListener('change', async function () {
        const label = document.querySelector('.toggle-label-' + this.dataset.id);
        this.disabled = true;

        try {
          const res = await fetch(this.dataset.url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json',
            },
            body: new URLSearchParams({ _method: 'PATCH' }),
          });

          if (!res.ok) throw new Error('HTTP ' + res.status);
          const data = await res.json();

          // Sync the switch with what the server actually saved
          this.checked = data.is_active;

          if (label) {
            label.textContent = data.is_active ? 'Active' : 'Inactive';
            label.style.color = data.is_active ? '#28c76f' : '#b0b0b0';
          }
        } catch (e) {
          // Revert the switch if the request failed
          this.checked = !this.checked;
          alert('Toggle failed: ' + e.message);
        } finally {
          this.disabled = false;
        }
      });
    });

    // ── Search auto-submit (debounced) ────────────────────────────────
    const form  = document.getElementById('managerFilterForm');
    const input = form ? form.querySelector('input[name="search"]') : null;
    if (input) {
      let timer = null;
      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => form.submit(), 500);
      });
      input.focus();
      const v = input.value;
      input.value = '';
      input.value = v;
    }
  });
</script>

<style>/* ── Mobile: smooth scrolling tabs, hidden scrollbar, edge fade ── */
    @media (max-width: 576px) {
        /* Toolbar stacks cleanly */
        #managerFilterForm { flex-wrap: wrap; }
        #managerFilterForm > div:first-child { flex: 1 1 100%; }
        #managerFilterForm select { flex: 1 1 0; min-width: 0; width: auto !important; }
        #managerFilterForm .btn-icon { flex-shrink: 0; }

        /* Wrap tabs so we can fade the edges */
        .manager-tabs-wrap { position: relative; }
        .manager-tabs-wrap::after {
            content: "";
            position: absolute;
            top: 0; right: 0;
            width: 28px; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0), #fff);
            pointer-events: none;
        }

        .manager-brand-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;          /* Firefox */
            -ms-overflow-style: none;       /* IE/Edge */
            padding-bottom: 0;
        }
        .manager-brand-tabs::-webkit-scrollbar { display: none; }  /* Chrome/Safari */
        .manager-brand-tabs .nav-link {
            white-space: nowrap;
            padding: 0.6rem 0.85rem;
        }
    }

    /* Responsive table — stacked cards on mobile */
    @media (max-width: 768px) {
        #managerProductsTable thead { display: none; }
        #managerProductsTable tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #ebe9f1;
            border-radius: 6px;
        }
        #managerProductsTable td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            border: none;
            border-bottom: 1px solid #f3f2f7;
            font-size: 0.875rem;
        }
        #managerProductsTable td:last-child { border-bottom: none; }
        #managerProductsTable td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #b9b9c3;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
            padding-right: 0.5rem;
        }
    }</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.managerlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/manager/dashboard.blade.php ENDPATH**/ ?>