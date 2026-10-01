@extends('layouts.managerlayout')

@section('content')
@php
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
@endphp



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
                    <span class="text-muted">Welcome back, <strong>{{ auth()->user()->name }}</strong></span>
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
                                <h2 class="fw-bolder mb-0">{{ $totalProducts }}</h2>
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
                                <h2 class="fw-bolder mb-0">{{ $totalBrands }}</h2>
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
                                <h2 class="fw-bolder mb-0">{{ $inStock }}</h2>
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
                                <h2 class="fw-bolder mb-0">{{ $outOfStock }}</h2>
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
                            <a class="nav-link {{ $activeBrand === null ? 'active' : '' }}"
                               href="{{ request()->fullUrlWithQuery(['brand' => null, 'page' => null]) }}">
                                All
                                <span class="badge rounded-pill bg-light-secondary ms-50">{{ $totalProducts }}</span>
                            </a>
                        </li>
                        @foreach($brands as $brand)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeBrand === $brand->brand ? 'active' : '' }}"
                                   href="{{ request()->fullUrlWithQuery(['brand' => $brand->brand, 'page' => null]) }}">
                                    {{ $brand->brand }}
                                    <span class="badge rounded-pill bg-light-secondary ms-50">{{ $brand->total }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Toolbar (auto-submit) -->
                <div class="card-body border-bottom py-1">
                    <form method="GET" action="{{ route('manager.products.index') }}" id="managerFilterForm"
                          class="d-flex flex-wrap align-items-center gap-50 w-100">

                        @if($activeBrand)
                            <input type="hidden" name="brand" value="{{ $activeBrand }}">
                        @endif
                        <input type="hidden" name="sort" value="{{ $sortBy }}">
                        <input type="hidden" name="dir"  value="{{ $sortDir }}">

                        <div style="flex: 1 1 auto; min-width: 200px;">
                            <input type="search" name="search" value="{{ $search }}"
                                   class="form-control" placeholder="Search name, description, brand…">
                        </div>

                        <div class="d-block d-sm-none w-100" style="height:0;"></div>

                        <select name="status" class="form-select" style="width:140px; flex-shrink:0;"
                                onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <select name="stock" class="form-select" style="width:150px; flex-shrink:0;"
                                onchange="this.form.submit()">
                            <option value="">All Stock</option>
                            <option value="in"  {{ $filterStock === 'in'  ? 'selected' : '' }}>In stock</option>
                            <option value="out" {{ $filterStock === 'out' ? 'selected' : '' }}>Out of stock</option>
                        </select>

                        <a href="{{ route('manager.products.index', $activeBrand ? ['brand' => $activeBrand] : []) }}"
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
                                    <a href="{{ $sortLink('name') }}" class="d-flex align-items-center text-body text-decoration-none">Product {!! $sortIcon('name') !!}</a>
                                </th>

                                <th>Brand</th>
                                <th style="cursor:pointer; white-space:nowrap; user-select:none;">
                                    <a href="{{ $sortLink('price') }}" class="d-flex align-items-center text-body text-decoration-none">Price {!! $sortIcon('price') !!}</a>
                                </th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="cursor:pointer; white-space:nowrap; user-select:none;">
                                    <a href="{{ $sortLink('stock') }}" class="d-flex align-items-center justify-content-center text-body text-decoration-none">Stock {!! $sortIcon('stock') !!}</a>
                                </th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td data-label="Product" class="fw-bold">{{ $product->name }}</td>

                                    <td data-label="Brand"><span class="badge bg-light-primary">{{ $product->brand }}</span></td>
                                    <td data-label="Price">₦{{ number_format($product->price, 2) }}</td>
                                    <td data-label="Status">
                                        <div class="form-check form-switch d-flex align-items-center gap-50 ps-0 m-0">
                                            <input class="form-check-input product-toggle m-0" type="checkbox" role="switch"
                                                   id="toggle-{{ $product->id }}"
                                                   data-id="{{ $product->id }}"
                                                   data-url="{{ route('manager.products.toggleActive', $product->id) }}"
                                                   {{ $product->is_active ? 'checked' : '' }}
                                                   style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                                            <label for="toggle-{{ $product->id }}" class="form-check-label toggle-label-{{ $product->id }} m-0"
                                                   style="font-size:12px; font-weight:500; cursor:pointer;
                                                          color:{{ $product->is_active ? '#28c76f' : '#b0b0b0' }};">
                                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td data-label="Stock" class="text-center">
                                        @if($product->stock > 0)
                                            <span class="badge bg-light-success">{{ $product->stock }}</span>
                                        @else
                                            <span class="badge bg-light-danger">Out of stock</span>
                                        @endif
                                    </td>
                                    <td data-label="Action" class="text-center">
                                        <a href="{{ route('manager.products.edit', $product->id) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        @if($search || $filterStatus || $filterStock)
                                            No products match your search/filters.
                                        @elseif($activeBrand)
                                            No products for "{{ $activeBrand }}".
                                        @else
                                            No products assigned yet.
                                        @endif
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
                                <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $products->onFirstPage() ? '#' : $paged->previousPageUrl() }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                    </a>
                                </li>
                                @if ($start > 1)
                                    <li class="page-item"><a class="page-link" href="{{ $paged->url(1) }}">1</a></li>
                                    @if ($start > 2)
                                        <li class="page-item disabled"><span class="page-link">…</span></li>
                                    @endif
                                @endif
                                @for ($p = $start; $p <= $end; $p++)
                                    <li class="page-item {{ $p === $cur ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $paged->url($p) }}">{{ $p }}</a>
                                    </li>
                                @endfor
                                @if ($end < $last)
                                    @if ($end < $last - 1)
                                        <li class="page-item disabled"><span class="page-link">…</span></li>
                                    @endif
                                    <li class="page-item"><a class="page-link" href="{{ $paged->url($last) }}">{{ $last }}</a></li>
                                @endif
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

@push('scripts')
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
@endpush
@endsection