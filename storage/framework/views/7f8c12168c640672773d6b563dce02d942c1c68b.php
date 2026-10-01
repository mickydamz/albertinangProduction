

<?php $__env->startSection('content'); ?>
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">

 
<div class="content-header row">
  <div class="col-12 mb-2">
    <div class="d-flex align-items-center justify-content-between">

      <div style="min-width:0; flex:1 1 auto;">
        <div class="d-flex align-items-center gap-1">
          <h2 class="content-header-title mb-0">Products</h2>
          <div style="width:1px; height:20px; background:#ebe9f1;"></div>
          <nav>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
              <li class="breadcrumb-item active">Products</li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="d-flex align-items-center gap-50" style="flex-shrink:0;">
        <a href="<?php echo e(route('admin.products.create')); ?>"
           class="btn btn-primary waves-effect waves-float waves-light">
            <i class="fas fa-plus me-1"></i> Add New Product
        </a>
      </div>

    </div>
  </div>
</div>

    <div class="content-body">

      <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <div class="alert-body"><?php echo e(session('success')); ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <div class="alert-body"><?php echo e(session('error')); ?></div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="card">

        
        <div class="card-header border-bottom">
          <form method="GET" action="<?php echo e(route('admin.products.index')); ?>" id="filterForm"
                class="d-flex flex-wrap align-items-center gap-50 w-100">

            
            <input type="hidden" name="sort" value="<?php echo e($sortBy ?? ''); ?>">
            <input type="hidden" name="dir"  value="<?php echo e($sortDir ?? ''); ?>">

            
            <div style="flex: 1 1 auto;">
              <input type="search" name="search" value="<?php echo e(old('search', $search)); ?>"
                     class="form-control" placeholder="Search products…" style="width:100%;">
            </div>

            
            <div class="d-block d-sm-none w-100" style="height:0;"></div>

            
            <select name="category" class="form-select" style="width:140px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Categories</option>
              <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>" <?php echo e(($filterCategory ?? '') == $cat->id ? 'selected' : ''); ?>>
                  <?php echo e($cat->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <select name="status" class="form-select" style="width:130px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="1" <?php echo e(($filterStatus ?? '') === '1' ? 'selected' : ''); ?>>Active</option>
              <option value="0" <?php echo e(($filterStatus ?? '') === '0' ? 'selected' : ''); ?>>Inactive</option>
            </select>

            
            <select name="brand" class="form-select" style="width:130px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Brands</option>
              <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($brand->id); ?>" <?php echo e(($filterBrand ?? '') == $brand->id ? 'selected' : ''); ?>>
                  <?php echo e($brand->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            
            <a href="<?php echo e(route('admin.products.index')); ?>"
               class="btn btn-icon btn-outline-secondary waves-effect"
               data-bs-toggle="tooltip" title="Reset" style="flex-shrink:0;">
              <i class="fas fa-rotate"></i>
            </a>

          </form>
        </div>

        
        <?php
          $sortableHeaders = [
            ['col' => 'name',       'label' => 'Name'],
            ['col' => '',           'label' => 'Description'],
            ['col' => 'price',      'label' => 'Price'],
            ['col' => 'stock',      'label' => 'Stock'],
            ['col' => '',           'label' => 'Category'],
            ['col' => '',           'label' => 'Image'],
            ['col' => 'is_active',  'label' => 'Status'],
            ['col' => 'rating',     'label' => 'Rating'],
            ['col' => '',           'label' => 'Actions'],
          ];

          $pageQuery = array_filter([
            'search'   => $search ?? '',
            'category' => $filterCategory ?? '',
            'status'   => $filterStatus ?? '',
            'brand'    => $filterBrand ?? '',
            'sort'     => $sortBy ?? '',
            'dir'      => $sortDir ?? '',
          ]);
        ?>

        <div class="card-datatable table-responsive">
          <table class="table table-hover align-middle" id="productsTable">
            <thead>
              <tr>
                <?php $__currentLoopData = $sortableHeaders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <th
                    <?php if($header['col']): ?> style="cursor:pointer; white-space:nowrap; user-select:none;" <?php endif; ?>
                    <?php if($header['label'] === 'Actions'): ?> class="text-center" <?php endif; ?>
                  >
                    <?php if($header['col']): ?>
                      <?php
                        $isActive = ($sortBy ?? '') === $header['col'];
                        $nextDir  = ($isActive && ($sortDir ?? '') === 'asc') ? 'desc' : 'asc';
                        $url      = request()->fullUrlWithQuery(array_merge($pageQuery, [
                          'sort' => $header['col'],
                          'dir'  => $nextDir,
                          'page' => 1,
                        ]));
                      ?>
                      <a href="<?php echo e($url); ?>"
                         class="d-flex align-items-center gap-25 text-body text-decoration-none">
                        <?php echo e($header['label']); ?>

                        <span style="display:inline-flex; flex-direction:column; line-height:1; font-size:9px; color:#b9b9c3; margin-left:3px;">
                          <span style="<?php echo e($isActive && ($sortDir ?? '') === 'asc'  ? 'color:#5aab1f;' : ''); ?>">▲</span>
                          <span style="<?php echo e($isActive && ($sortDir ?? '') === 'desc' ? 'color:#5aab1f;' : ''); ?>">▼</span>
                        </span>
                      </a>
                    <?php else: ?>
                      <?php echo e($header['label']); ?>

                    <?php endif; ?>
                  </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td data-label="Name">
                    <a href="<?php echo e(route('product.show', $product->id)); ?>" class="fw-bolder">
                      <?php echo e(\Illuminate\Support\Str::limit($product->name, 20)); ?>

                    </a>
                  </td>
                  <td data-label="Description">
                    <small class="text-muted"><?php echo e(\Illuminate\Support\Str::limit($product->description, 30)); ?></small>
                  </td>
                  <td data-label="Price">₦<?php echo e(number_format($product->price, 2)); ?></td>
                  <td data-label="Stock"><?php echo e($product->stock); ?></td>
                  <td data-label="Category">
                    <?php echo e($product->category?->name ?? 'N/A'); ?>

                    <?php if($product->Subcategory): ?>
                      <br><small class="text-muted"><?php echo e($product->Subcategory->name); ?></small>
                    <?php endif; ?>
                  </td>
                  <td data-label="Image">
                    <?php if($product->images->isNotEmpty()): ?>
                      <img src="<?php echo e(asset('storage/' . $product->images->first()->image_url)); ?>"
                           alt="<?php echo e($product->name); ?>" class="product-thumb">
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td data-label="Status">
                    <div class="form-check form-switch d-flex align-items-center gap-50 ps-0 m-0">
                      <input class="form-check-input product-toggle m-0"
                             type="checkbox" role="switch"
                             id="toggle-<?php echo e($product->id); ?>"
                             data-id="<?php echo e($product->id); ?>"
                             data-url="<?php echo e(route('admin.products.toggleActive', $product->id)); ?>"
                             <?php echo e($product->is_active ? 'checked' : ''); ?>

                             style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                      <label for="toggle-<?php echo e($product->id); ?>"
                             class="form-check-label toggle-label-<?php echo e($product->id); ?> m-0"
                             style="font-size:12px; font-weight:500; cursor:pointer;
                                    color:<?php echo e($product->is_active ? '#28c76f' : '#b0b0b0'); ?>;">
                        <?php echo e($product->is_active ? 'Active' : 'Inactive'); ?>

                      </label>
                    </div>
                  </td>
                  <td data-label="Rating">
                    <div class="d-flex gap-25">
                      <?php for($i = 1; $i <= 5; $i++): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="<?php echo e($i <= $product->review_rating ? '#ffc107' : '#e0e0e0'); ?>" stroke="none">
                          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                      <?php endfor; ?>
                    </div>
                  </td>
                  <td data-label="Actions" class="text-center">
                    <div class="d-flex justify-content-center gap-50">
                      <a href="<?php echo e(route('admin.products.show', $product->id)); ?>"
                         class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                         data-bs-toggle="tooltip" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="<?php echo e(route('admin.products.edit', $product->id)); ?>"
                         class="btn btn-sm btn-icon btn-relief-outline-primary waves-effect"
                         data-bs-toggle="tooltip" title="Edit">
                        <i class="fas fa-pen"></i>
                      </a>
                      <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" method="POST"
                            onsubmit="return confirm('Delete this product? This cannot be undone.');" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                data-bs-toggle="tooltip" title="Delete">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="10">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                      <p class="mb-0">No products found.</p>
                    </div>
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
                  <li class="page-item">
                    <a class="page-link" href="<?php echo e($paged->url(1)); ?>">1</a>
                  </li>
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
                  <li class="page-item">
                    <a class="page-link" href="<?php echo e($paged->url($last)); ?>"><?php echo e($last); ?></a>
                  </li>
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


<div class="modal fade" id="markupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ff9f43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-50"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
          Change Markup on All Products
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-1">Enter the percentage to increase <strong>all markup percentages</strong> by.</p>
        <div class="input-group mt-1" style="max-width:100%; margin:0;">
          <input type="number" id="markupInput" class="form-control" placeholder="e.g. 10"
                 min="0.1" max="1000" step="0.1">
          <span class="input-group-text">%</span>
        </div>
        <div id="markupError" class="text-danger mt-50 d-none small">Please enter a valid percentage greater than 0.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning waves-effect" onclick="proceedToConfirm()">Continue</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="markupConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-warning">⚠️ Are you sure?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-2">
        <p class="mb-25">You are about to increase</p>
        <p class="fs-5 fw-bold mb-25">ALL product markup by <span id="confirmPercent" class="text-warning">0</span>%</p>
        <p class="text-muted small">This will permanently update every product markup in the database.</p>
      </div>
      <div class="modal-footer justify-content-center gap-50">
        <button type="button" class="btn btn-outline-secondary" onclick="goBackToMarkup()">Go Back</button>
        <form id="markupForm" method="POST" action="<?php echo e(route('admin.products.bulkMarkup')); ?>" class="d-inline">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="markup_percent" id="markupHiddenInput">
          <button type="submit" class="btn btn-danger waves-effect">Yes, Apply Markup</button>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .gap-25  { gap: 0.25rem !important; }
  .gap-50  { gap: 0.5rem  !important; }
  .me-25   { margin-right: 0.25rem !important; }
  .mt-50   { margin-top: 0.5rem !important; }

  .product-thumb {
    width: 40px; height: 40px;
    object-fit: cover;
    border-radius: 6px;
  }

  /* Toggle green */
  .product-toggle:checked       { background-color:#28c76f !important; border-color:#28c76f !important; }
  .product-toggle:focus         { box-shadow: 0 0 0 3px rgba(40,199,111,.25) !important; }

  /* Desktop: give the Image & Status columns room so they don't clog together */
  @media (min-width: 769px) {
    #productsTable td[data-label="Image"],
    #productsTable thead th:nth-child(6) {
      padding-left: 1.25rem;
      padding-right: 1.25rem;
    }
    #productsTable td[data-label="Status"],
    #productsTable thead th:nth-child(7) {
      padding-left: 1.5rem;
      padding-right: 1.5rem;
      min-width: 128px;
    }
  }

  /* Sort arrows */
  thead th a { color: inherit; }
  thead th a:hover { color: #5aab1f; }

  /* Pagination */
  .pagination .page-item.active .page-link { background-color:#5aab1f !important; border-color:#5aab1f !important; color:#fff !important; }
  .pagination .page-link { color:#5aab1f; }
  .pagination .page-item.disabled .page-link { pointer-events: none; }

  /* Breadcrumb always visible */
  @media (max-width: 767px) {
    .breadcrumb-wrapper,
    .breadcrumb-wrapper .breadcrumb { display: flex !important; }
  }

  /* Responsive toolbar */
  @media (max-width: 576px) {
    #filterForm { flex-wrap: wrap; }
    #filterForm > div:first-child { flex: 1 1 100%; }
    #filterForm select { flex: 1 1 0; min-width: 0; width: auto !important; }
    #filterForm .btn-icon { flex-shrink: 0; }
  }

  /* Responsive table */
  @media (max-width: 768px) {
    #productsTable thead { display: none; }
    #productsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #productsTable td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
    }
    #productsTable td:last-child { border-bottom: none; }
    #productsTable td::before {
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
    // Tooltips
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .forEach(el => new bootstrap.Tooltip(el));

    // AJAX toggle
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.querySelectorAll('.product-toggle').forEach(function (toggle) {
      toggle.addEventListener('change', function () {
        const id = this.dataset.id, url = this.dataset.url, checked = this.checked;
        const label = document.querySelector('.toggle-label-' + id);
        label.textContent = checked ? 'Active' : 'Inactive';
        label.style.color = checked ? '#28c76f' : '#b0b0b0';
        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-HTTP-Method-Override': 'PATCH' },
          body: JSON.stringify({ _method: 'PATCH' }),
        }).catch(function () {
          toggle.checked = !checked;
          label.textContent = !checked ? 'Active' : 'Inactive';
          label.style.color = !checked ? '#28c76f' : '#b0b0b0';
          alert('Failed to update status. Please try again.');
        });
      });
    });
  });

  function proceedToConfirm() {
    const val = parseFloat(document.getElementById('markupInput').value);
    const err = document.getElementById('markupError');
    if (!val || val <= 0) { err.classList.remove('d-none'); return; }
    err.classList.add('d-none');
    document.getElementById('markupHiddenInput').value = val;
    document.getElementById('confirmPercent').textContent = val;
    const m1 = bootstrap.Modal.getInstance(document.getElementById('markupModal'));
    m1.hide();
    document.getElementById('markupModal').addEventListener('hidden.bs.modal', function h() {
      this.removeEventListener('hidden.bs.modal', h);
      new bootstrap.Modal(document.getElementById('markupConfirmModal')).show();
    });
  }

  function goBackToMarkup() {
    const m2 = bootstrap.Modal.getInstance(document.getElementById('markupConfirmModal'));
    m2.hide();
    document.getElementById('markupConfirmModal').addEventListener('hidden.bs.modal', function h() {
      this.removeEventListener('hidden.bs.modal', h);
      new bootstrap.Modal(document.getElementById('markupModal')).show();
    });
  }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/products/index.blade.php ENDPATH**/ ?>