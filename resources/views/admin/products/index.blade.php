@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">

 {{-- Breadcrumb --}}
<div class="content-header row">
  <div class="col-12 mb-2">
    <div class="d-flex align-items-center justify-content-between">

      <div style="min-width:0; flex:1 1 auto;">
        <div class="d-flex align-items-center gap-1">
          <h2 class="content-header-title mb-0">Products</h2>
          <div style="width:1px; height:20px; background:#ebe9f1;"></div>
          <nav>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Products</li>
            </ol>
          </nav>
        </div>
      </div>

      <div class="d-flex align-items-center gap-50" style="flex-shrink:0;">
        <a href="{{ route('admin.products.create') }}"
           class="btn btn-primary waves-effect waves-float waves-light">
            <i class="fas fa-plus me-1"></i> Add New Product
        </a>
      </div>

    </div>
  </div>
</div>

    <div class="content-body">

      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card">

        @include('admin.products.partials.filters')

        {{-- Table --}}
        @php
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

          $pageQuery = request()->except('page');
        @endphp

        <div class="card-datatable table-responsive">
          <table class="table table-hover align-middle" id="productsTable">
            <thead>
              <tr>
                @foreach ($sortableHeaders as $header)
                  <th
                    @if($header['col']) style="cursor:pointer; white-space:nowrap; user-select:none;" @endif
                    @if($header['label'] === 'Actions') class="text-center" @endif
                  >
                    @if($header['col'])
                      @php
                        $isActive = ($sortBy ?? '') === $header['col'];
                        $nextDir  = ($isActive && ($sortDir ?? '') === 'asc') ? 'desc' : 'asc';
                        $url      = request()->fullUrlWithQuery(array_merge($pageQuery, [
                          'sort' => $header['col'],
                          'dir'  => $nextDir,
                          'page' => 1,
                        ]));
                      @endphp
                      <a href="{{ $url }}"
                         class="d-flex align-items-center gap-25 text-body text-decoration-none">
                        {{ $header['label'] }}
                        <span style="display:inline-flex; flex-direction:column; line-height:1; font-size:9px; color:#b9b9c3; margin-left:3px;">
                          <span style="{{ $isActive && ($sortDir ?? '') === 'asc'  ? 'color:#5aab1f;' : '' }}">▲</span>
                          <span style="{{ $isActive && ($sortDir ?? '') === 'desc' ? 'color:#5aab1f;' : '' }}">▼</span>
                        </span>
                      </a>
                    @else
                      {{ $header['label'] }}
                    @endif
                  </th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @forelse ($products as $product)
                <tr>
                  <td data-label="Name">
                    <a href="{{ route('product.show', $product->id) }}" class="fw-bolder">
                      {{ \Illuminate\Support\Str::limit($product->name, 20) }}
                    </a>
                  </td>
                  <td data-label="Description">
                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 30) }}</small>
                  </td>
                  <td data-label="Price">₦{{ number_format($product->price, 2) }}</td>
                  <td data-label="Stock">{{ $product->stock }}</td>
                  <td data-label="Category">
                    {{ $product->category?->name ?? 'N/A' }}
                    @if($product->Subcategory)
                      <br><small class="text-muted">{{ $product->Subcategory->name }}</small>
                    @endif
                  </td>
                  <td data-label="Image">
                    @if ($product->images->isNotEmpty())
                      <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                           alt="{{ $product->name }}" class="product-thumb">
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td data-label="Status">
                    <div class="form-check form-switch d-flex align-items-center gap-50 ps-0 m-0">
                      <input class="form-check-input product-toggle m-0"
                             type="checkbox" role="switch"
                             id="toggle-{{ $product->id }}"
                             data-id="{{ $product->id }}"
                             data-url="{{ route('admin.products.toggleActive', $product->id) }}"
                             {{ $product->is_active ? 'checked' : '' }}
                             style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                      <label for="toggle-{{ $product->id }}"
                             class="form-check-label toggle-label-{{ $product->id }} m-0"
                             style="font-size:12px; font-weight:500; cursor:pointer;
                                    color:{{ $product->is_active ? '#28c76f' : '#b0b0b0' }};">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                      </label>
                    </div>
                  </td>
                  <td data-label="Rating">
                    <div class="d-flex gap-25">
                      @for ($i = 1; $i <= 5; $i++)
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                             fill="{{ $i <= $product->review_rating ? '#ffc107' : '#e0e0e0' }}" stroke="none">
                          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                      @endfor
                    </div>
                  </td>
                  <td data-label="Actions" class="text-center">
                    <div class="d-flex justify-content-center gap-50">
                      <a href="{{ route('admin.products.show', $product->id) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                         data-bs-toggle="tooltip" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.products.edit', $product->id) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-primary waves-effect"
                         data-bs-toggle="tooltip" title="Edit">
                        <i class="fas fa-pen"></i>
                      </a>
                      <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                            onsubmit="return confirm('Delete this product? This cannot be undone.');" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                data-bs-toggle="tooltip" title="Delete">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                      <p class="mb-0">No products found.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        <div class="row mx-2 my-1 align-items-center">
          <div class="col-sm-12 col-md-6">
            <div class="dataTables_info">
              Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
            @if ($products->lastPage() > 1)
              @php
                $cur   = $products->currentPage();
                $last  = $products->lastPage();
                $start = max(1, $cur - 2);
                $end   = min($last, $cur + 2);
                $paged = $products->appends($pageQuery);
              @endphp
              <ul class="pagination pagination-sm mb-0">

                {{-- Previous --}}
                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $products->onFirstPage() ? '#' : $paged->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                  </a>
                </li>

                {{-- First page + ellipsis --}}
                @if ($start > 1)
                  <li class="page-item">
                    <a class="page-link" href="{{ $paged->url(1) }}">1</a>
                  </li>
                  @if ($start > 2)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                  @endif
                @endif

                {{-- Page window --}}
                @for ($p = $start; $p <= $end; $p++)
                  <li class="page-item {{ $p === $cur ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paged->url($p) }}">{{ $p }}</a>
                  </li>
                @endfor

                {{-- Ellipsis + last page --}}
                @if ($end < $last)
                  @if ($end < $last - 1)
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                  @endif
                  <li class="page-item">
                    <a class="page-link" href="{{ $paged->url($last) }}">{{ $last }}</a>
                  </li>
                @endif

                {{-- Next --}}
                <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $products->hasMorePages() ? $paged->nextPageUrl() : '#' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                  </a>
                </li>

              </ul>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ── Markup Step 1 Modal ── --}}
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

{{-- ── Markup Step 2 Confirm Modal ── --}}
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
        <form id="markupForm" method="POST" action="{{ route('admin.products.bulkMarkup') }}" class="d-inline">
          @csrf
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

@push('scripts')
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
@endpush

@endsection