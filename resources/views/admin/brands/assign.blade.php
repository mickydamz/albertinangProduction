@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper container-xxl p-0">

    <div class="content-header row">
      <div class="col-12 mb-2">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h2 class="content-header-title mb-0">Brand Assignment</h2>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Assign Brands</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card">

        {{-- Toolbar --}}
        <div class="card-header border-bottom">
          <div class="d-flex flex-wrap align-items-center gap-50 w-100">

            {{-- Search --}}
            <div style="flex: 1 1 auto;">
              <input type="search" id="brandSearch" class="form-control"
                     placeholder="Search brand…" style="width:100%;">
            </div>

            {{-- Zero-height line break on mobile --}}
            <div class="d-block d-sm-none w-100" style="height:0;"></div>

            {{-- Stat badges --}}
            <span class="badge bg-light-primary">{{ $brands->count() }} Brands</span>
            <span class="badge bg-light-success">{{ $brands->filter(fn($b) => !empty($b['manager_id']))->count() }} Assigned</span>
            <span class="badge bg-light-warning text-warning">{{ $brands->filter(fn($b) => empty($b['manager_id']))->count() }} Unassigned</span>

          </div>
        </div>

        {{-- Table --}}
        <div class="card-datatable table-responsive">
          <table class="table table-hover align-middle" id="brandTable">
            <thead>
              <tr>
                <th>#</th>
                <th>Brand</th>
                <th class="text-center">Products</th>
                <th>Current Manager</th>
                <th>Assign Manager</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($brands as $index => $brand)
                @php $currentManager = $managers->firstWhere('id', $brand['manager_id']); @endphp
                <tr>
                  <td data-label="#">{{ $index + 1 }}</td>

                  <td data-label="Brand">
                    <span class="fw-bolder">{{ $brand['brand'] }}</span>
                  </td>

                  <td data-label="Products" class="text-center">
                    <span class="badge rounded-pill bg-light-primary text-primary">{{ $brand['total'] }}</span>
                  </td>

                  <td data-label="Current Manager">
                    @if($currentManager)
                      <div class="d-flex align-items-center gap-50">
                        <div class="avatar avatar-sm">
                          <span class="avatar-content bg-light-success text-success rounded-circle">
                            {{ strtoupper(substr($currentManager->name, 0, 1)) }}
                          </span>
                        </div>
                        <span class="fw-bold text-success">{{ $currentManager->name }}</span>
                      </div>
                    @else
                      <span class="badge rounded-pill bg-light-secondary text-secondary">Unassigned</span>
                    @endif
                  </td>

                  <td data-label="Assign Manager">
                    <form action="{{ route('admin.brands.assign.post') }}" method="POST"
                          class="d-flex align-items-center gap-50">
                      @csrf
                      <input type="hidden" name="brand" value="{{ $brand['brand'] }}">
                      <select name="manager_id" class="form-select form-select-sm" style="min-width:0; flex:1 1 auto;"
                              onchange="this.form.submit()">
                        <option value="">— Remove Manager —</option>
                        @foreach($managers as $manager)
                          <option value="{{ $manager->id }}"
                            {{ $brand['manager_id'] == $manager->id ? 'selected' : '' }}>
                            {{ $manager->name }}
                          </option>
                        @endforeach
                      </select>
                    </form>
                  </td>

                  <td data-label="Actions" class="text-center">
                    <a href="{{ route('admin.products.index', ['brand' => $brand['brand']]) }}"
                       class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                       data-bs-toggle="tooltip" title="View Products">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                      <p class="mb-0">No brands found.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Footer --}}
        <div class="card-footer">
          <div class="dataTables_info">
            {{ $brands->count() }} brand(s) — {{ $brands->sum('total') }} total products
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  .gap-50 { gap: 0.5rem !important; }

  .avatar-content {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
  }

  /* ── Responsive toolbar ── */
  @media (max-width: 576px) {
    .card-header .d-flex {
      flex-wrap: wrap;
    }
  }

  /* ── Responsive table ── */
  @media (max-width: 768px) {
    #brandTable thead { display: none; }
    #brandTable tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #ebe9f1;
      border-radius: 6px;
    }
    #brandTable td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
    }
    #brandTable td:last-child { border-bottom: none; }
    #brandTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-right: auto;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #brandTable td form {
      flex: 1;
      justify-content: flex-end;
    }
  }

  /* Breadcrumb always visible */
  @media (max-width: 767px) {
    .breadcrumb-wrapper,
    .breadcrumb-wrapper .breadcrumb { display: flex !important; }
  }
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Tooltips
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .forEach(el => new bootstrap.Tooltip(el));

    // Live search
    document.getElementById('brandSearch').addEventListener('input', function () {
      const q = this.value.toLowerCase();
      document.querySelectorAll('#brandTable tbody tr').forEach(row => {
        const cell = row.querySelector('td:nth-child(2)');
        if (cell) row.style.display = cell.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  });
</script>
@endpush

@endsection