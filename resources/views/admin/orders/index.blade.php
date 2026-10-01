@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">

    {{-- Breadcrumb --}}
    <div class="content-header row">
      <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
          <div style="min-width:0; flex:1 1 auto;">
            <div class="d-flex align-items-center gap-1">
              <h2 class="content-header-title mb-0">Orders</h2>
              <div style="width:1px; height:20px; background:#ebe9f1;"></div>
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active">Orders</li>
                </ol>
              </nav>
            </div>
          </div>

          <div class="d-flex align-items-center flex-wrap gap-50" style="flex-shrink:0;">
            <a href="{{ route('admin.orders.create') }}"
               class="btn btn-primary waves-effect waves-float waves-light">
                <i class="fas fa-plus me-1"></i> Create Order
            </a>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary waves-effect">
              <i class="fas fa-rotate-left me-25"></i>Returns
              @php $pendingReturns = \App\Models\OrderReturn::where('status','pending')->count(); @endphp
              @if($pendingReturns > 0)
                <span class="badge rounded-pill bg-warning ms-25">{{ $pendingReturns }}</span>
              @endif
            </a>
            <a href="{{ route('admin.cancellations.index') }}" class="btn btn-outline-secondary waves-effect">
              <i class="fas fa-ban me-25"></i>Cancellations
              @php $pendingCancellations = \App\Models\OrderCancellation::where('status','pending')->count(); @endphp
              @if($pendingCancellations > 0)
                <span class="badge rounded-pill bg-danger ms-25">{{ $pendingCancellations }}</span>
              @endif
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">

      {{-- Flash Messages --}}
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

        {{-- Toolbar --}}
        <div class="card-header border-bottom">
          <form method="GET" action="{{ route('admin.orders.index') }}" id="filterForm"
                class="d-flex flex-wrap align-items-center gap-50 w-100">

            {{-- Preserve sort state across filter submits --}}
            <input type="hidden" name="sort" value="{{ $sortBy ?? '' }}">
            <input type="hidden" name="dir"  value="{{ $sortDir ?? '' }}">

            {{-- Search --}}
            <div style="flex: 1 1 auto;">
              <input type="search" name="search" value="{{ old('search', $search ?? '') }}"
                     class="form-control" placeholder="Search order number, status, user, reference…" style="width:100%;">
            </div>

            <div class="d-block d-sm-none w-100" style="height:0;"></div>

            {{-- Status --}}
            <select name="status" class="form-select" style="width:150px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Statuses</option>
              @foreach(['pending','paid','processing','shipped','ready_for_pickup','delivered','completed','cancelled','refunded'] as $s)
                <option value="{{ $s }}" {{ ($filterStatus ?? '') === $s ? 'selected' : '' }}>
                  {{ ucfirst(str_replace('_', ' ', $s)) }}
                </option>
              @endforeach
            </select>

            {{-- Payment --}}
            <select name="payment" class="form-select" style="width:130px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Payments</option>
              <option value="stripe"   {{ ($filterPayment ?? '') === 'stripe'   ? 'selected' : '' }}>Stripe</option>
              <option value="paystack" {{ ($filterPayment ?? '') === 'paystack' ? 'selected' : '' }}>Paystack</option>
            </select>

            {{-- Search button --}}
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light" style="flex-shrink:0;">
              <i class="fas fa-search me-1"></i> Search
            </button>

            {{-- Reset --}}
            <a href="{{ route('admin.orders.index') }}"
               class="btn btn-icon btn-outline-secondary waves-effect"
               data-bs-toggle="tooltip" title="Reset filters" style="flex-shrink:0;">
              <i class="fas fa-rotate"></i>
            </a>

          </form>
        </div>

        {{-- Table --}}
        @php
          $sortableHeaders = [
            ['col' => 'id',             'label' => 'Order'],
            ['col' => 'customer_name',  'label' => 'Customer'],
            ['col' => '',               'label' => 'Items'],
            ['col' => 'total',          'label' => 'Total / Coupon'],
            ['col' => 'payment_method', 'label' => 'Payment'],
            ['col' => '',               'label' => 'Reference'],
            ['col' => 'status',         'label' => 'Status'],
            ['col' => 'created_at',     'label' => 'Date'],
            ['col' => '',               'label' => 'Actions'],
          ];

          $pageQuery = array_filter([
            'search'  => $search ?? '',
            'status'  => $filterStatus ?? '',
            'payment' => $filterPayment ?? '',
            'sort'    => $sortBy ?? '',
            'dir'     => $sortDir ?? '',
          ]);
        @endphp

        <div class="card-datatable table-responsive">
          <table class="table table-hover align-middle" id="ordersTable">
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
              @forelse ($orders as $order)
                <tr>
                  <td data-label="Order">
                    <span class="fw-bolder">#{{ $order->order_number ?? $order->id }}</span>
                  </td>

                  <td data-label="Customer">
                    @if ($order->user)
                      <div class="d-flex flex-column">
                        <span class="fw-bolder text-truncate" style="max-width:140px;">{{ $order->user->name }}</span>
                        <small class="text-muted text-truncate" style="max-width:140px;">{{ $order->user->email }}</small>
                      </div>
                    @else
                      <span class="text-muted">Guest</span>
                    @endif
                  </td>

                  <td data-label="Items">
                    <span class="badge rounded-pill bg-light-secondary text-secondary">
                      {{ $order->items->count() }} item(s)
                    </span>
                  </td>

                  <td data-label="Total / Coupon">
                    <span class="fw-bolder">₦{{ number_format($order->total, 2) }}</span>
                    @if ($order->coupon_code_used)
                      <div class="mt-25">
                        <span class="badge rounded-pill bg-light-success text-success" style="font-size:0.7rem;">
                          {{ $order->coupon_code_used }}
                        </span>
                        <div style="font-size:0.75rem; color:#28c76f; margin-top:2px;">
                          -₦{{ number_format($order->coupon_discount_ngn, 2) }} off
                        </div>
                      </div>
                    @endif
                  </td>

                  <td data-label="Payment">
                    @if ($order->payment_method === 'stripe')
                      <span class="badge rounded-pill bg-light-primary text-primary">Stripe</span>
                    @elseif ($order->payment_method === 'paystack')
                      <span class="badge rounded-pill bg-light-success text-success">Paystack</span>
                    @else
                      <span class="badge rounded-pill bg-light-secondary text-secondary">
                        {{ $order->payment_method ?? 'N/A' }}
                      </span>
                    @endif
                  </td>

                  <td data-label="Reference">
                    @if ($order->reference)
                      <code class="ref-code" data-bs-toggle="tooltip" title="{{ $order->reference }}">
                        {{ $order->reference }}
                      </code>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>

                  <td data-label="Status">
                    @php
                      $statusColors = [
                        'pending'          => ['bg' => 'bg-light-warning',   'text' => 'text-warning'],
                        'paid'             => ['bg' => 'bg-light-info',      'text' => 'text-info'],
                        'processing'       => ['bg' => 'bg-light-info',      'text' => 'text-info'],
                        'shipped'          => ['bg' => 'bg-light-primary',   'text' => 'text-primary'],
                        'ready_for_pickup' => ['bg' => 'bg-light-warning',   'text' => 'text-warning'],
                        'delivered'        => ['bg' => 'bg-light-success',   'text' => 'text-success'],
                        'completed'        => ['bg' => 'bg-light-success',   'text' => 'text-success'],
                        'cancelled'        => ['bg' => 'bg-light-danger',    'text' => 'text-danger'],
                        'refunded'         => ['bg' => 'bg-light-secondary', 'text' => 'text-secondary'],
                      ];
                      $sc = $statusColors[$order->status] ?? ['bg' => 'bg-light-secondary', 'text' => 'text-secondary'];
                    @endphp
                    <span class="badge rounded-pill {{ $sc['bg'] }} {{ $sc['text'] }}">
                      {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                  </td>

                  <td data-label="Date">
                    {{ $order->created_at->format('d M Y') }}
                    <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                  </td>

                  <td data-label="Actions" class="text-center">
                    <div class="d-flex justify-content-center gap-50">
                      <a href="{{ route('admin.orders.show', $order) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                         data-bs-toggle="tooltip" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.orders.edit', $order) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                         data-bs-toggle="tooltip" title="Edit Status">
                        <i class="fas fa-pen"></i>
                      </a>
                      <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                            onsubmit="return confirm('Delete order #{{ $order->id }}? This cannot be undone.');" class="d-inline">
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
                  <td colspan="9">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                      <p class="mb-0">No orders found.</p>
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
              Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
            @if ($orders->lastPage() > 1)
              @php
                $cur   = $orders->currentPage();
                $last  = $orders->lastPage();
                $start = max(1, $cur - 2);
                $end   = min($last, $cur + 2);
                $paged = $orders->appends($pageQuery);
              @endphp
              <ul class="pagination pagination-sm mb-0">

                {{-- Previous --}}
                <li class="page-item {{ $orders->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $orders->onFirstPage() ? '#' : $paged->previousPageUrl() }}">
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
                <li class="page-item {{ !$orders->hasMorePages() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $orders->hasMorePages() ? $paged->nextPageUrl() : '#' }}">
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
  .gap-25 { gap: 0.25rem !important; }
  .gap-50 { gap: 0.5rem  !important; }
  .me-25  { margin-right: 0.25rem !important; }
  .mt-25  { margin-top: 0.25rem !important; }

  /* Avatar initials */
  .avatar-content {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    font-size: 0.875rem; font-weight: 600;
    color: #7367f0;
    background: rgba(115,103,240,.12) !important;
  }
  .avatar img { width: 32px; height: 32px; object-fit: cover; }

  /* Reference code pill */
  .ref-code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 4px;
    color: #333;
    font-size: 11px;
    display: inline-block;
    max-width: 130px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
    cursor: default;
  }

  /* Sort arrows */
  thead th a { color: inherit; }
  thead th a:hover { color: #5aab1f; }

  /* Pagination */
  .pagination .page-item.active .page-link { background-color: #5aab1f !important; border-color: #5aab1f !important; color: #fff !important; }
  .pagination .page-link { color: #5aab1f; }
  .pagination .page-item.disabled .page-link { pointer-events: none; }

  /* Responsive toolbar */
  @media (max-width: 576px) {
    #filterForm { flex-wrap: wrap; }
    #filterForm > div:first-child { flex: 1 1 100%; }
    #filterForm select { flex: 1 1 0; min-width: 0; width: auto !important; }
    #filterForm .btn-icon { flex-shrink: 0; }
  }

  /* Responsive table */
  @media (max-width: 768px) {
    #ordersTable thead { display: none; }
    #ordersTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #ordersTable td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
    }
    #ordersTable td:last-child { border-bottom: none; }
    #ordersTable td::before {
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
  });
</script>
@endpush

@endsection