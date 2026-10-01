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
          <h2 class="content-header-title mb-0">Users List</h2>
          <div style="width:1px; height:20px; background:#ebe9f1;"></div>
          <nav>
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </nav>
        </div>
      </div>

      <a href="{{ route('admin.users.create') }}"
         class="btn btn-primary waves-effect waves-float waves-light"
         style="flex-shrink:0;">
        <i class="fas fa-plus me-1"></i> Add New User
      </a>

    </div>
  </div>
</div>

    <div class="content-body">

      {{-- Flash Messages --}}
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      {{-- Table Card --}}
      <div class="card">
        <div class="card-header border-bottom">

          {{-- Single toolbar: search + role + status + reset --}}
          <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm"
                class="d-flex flex-wrap align-items-center gap-50 w-100">

            {{-- Search --}}
            <div class="dataTables_filter" style="flex: 1 1 auto;">
              <input type="search" name="search" value="{{ old('search', $search ?? '') }}"
                     class="form-control" placeholder="Search name, email, phone, city…" style="width:100%;">
            </div>

            {{-- Line break on mobile: pushes dropdowns to next row --}}
            <div class="d-block d-sm-none w-100" style="height:0;"></div>

            {{-- Role --}}
            <select name="role" class="form-select" style="width:120px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Roles</option>
              @foreach (['admin','manager','user'] as $r)
                <option value="{{ $r }}" {{ ($role ?? '') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
              @endforeach
            </select>

            {{-- Status --}}
            <select name="status" class="form-select" style="width:130px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="green"  {{ ($status ?? '') === 'green'  ? 'selected' : '' }}>Active</option>
              <option value="yellow" {{ ($status ?? '') === 'yellow' ? 'selected' : '' }}>Warning</option>
              <option value="banned" {{ ($status ?? '') === 'banned' ? 'selected' : '' }}>Banned</option>
            </select>

            {{-- Reset --}}
            <a href="{{ route('admin.users.index') }}" class="btn btn-icon btn-outline-secondary waves-effect"
               data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Filters" style="flex-shrink:0;">
              <i class="fas fa-rotate"></i>
            </a>

          </form>
        </div>

        <div class="card-datatable table-responsive">

          @php $emailVerify = \App\Models\Setting::get('email_verification_enabled', '0') === '1'; @endphp
          <table class="datatables-users table" id="usersTable">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                @if($emailVerify)<th>Verified</th>@endif
                <th>Registered At</th>
                <th>Referred By</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($users as $user)
                <tr>
                  <td data-label="User">
                    <div class="d-flex justify-content-start align-items-center gap-1 user-name">
                      <div class="avatar avatar-sm flex-shrink-0">
                        @if ($user->avatar)
                          <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle">
                        @else
                          <span class="avatar-content bg-light-primary rounded-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                          </span>
                        @endif
                      </div>
                      <div class="d-flex flex-column" style="min-width:0;">
                        <span class="fw-bolder text-truncate" style="max-width:160px;">{{ $user->name }}</span>
                        <small class="text-truncate text-muted" style="max-width:160px;">{{ $user->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td data-label="Role">
                    @php
                      $roleColors = ['admin'=>'danger','manager'=>'info','supplier'=>'warning','affiliate'=>'success','user'=>'secondary'];
                      $roleColor  = $roleColors[$user->role] ?? 'secondary';
                    @endphp
                    <span class="badge rounded-pill bg-light-{{ $roleColor }} text-{{ $roleColor }}">{{ ucfirst($user->role) }}</span>
                  </td>
                  @if($emailVerify)
                  <td data-label="Verified">
                    @if($user->email_verified_at)
                      <span class="badge rounded-pill bg-light-success text-success">Verified</span>
                    @else
                      <span class="badge rounded-pill bg-light-warning text-warning">Unverified</span>
                    @endif
                  </td>
                  @endif
                <td data-label="Registered At">
                  <span>{{ $user->created_at->format('d M Y') }}</span>
                  <br><small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                </td>
                  <td data-label="Referred By">
                    <span class="text-truncate">{{ $user->referredBy?->name ?? '—' }}</span>
                  </td>
                  <td data-label="Actions">
                    <div class="d-flex gap-50">
                      <a href="{{ route('admin.users.show', $user->id) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                         data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.users.edit', $user) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-primary waves-effect"
                         data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                        <i class="fas fa-pen"></i>
                      </a>
                      <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete {{ addslashes($user->name) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1 text-muted"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                      <p class="mb-0">No users found matching your filters.</p>
                      <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mt-1">Clear All Filters</a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>



          {{-- Pagination --}}
          <div class="row mx-2 my-1 align-items-center">
            <div class="col-sm-12 col-md-6">
              <div class="dataTables_info">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries
              </div>
            </div>
            <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
              @if ($users->lastPage() > 1)
                <ul class="pagination pagination-sm mb-0">

                  {{-- Prev --}}
                  <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}" aria-label="Previous">
                      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                  </li>

                  {{-- Page numbers --}}
                  @php
                    $current  = $users->currentPage();
                    $last     = $users->lastPage();
                    $start    = max(1, $current - 2);
                    $end      = min($last, $current + 2);
                  @endphp

                  @if ($start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $users->url(1) }}">1</a></li>
                    @if ($start > 2)
                      <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                  @endif

                  @for ($p = $start; $p <= $end; $p++)
                    <li class="page-item {{ $p === $current ? 'active' : '' }}">
                      <a class="page-link" href="{{ $users->url($p) }}">{{ $p }}</a>
                    </li>
                  @endfor

                  @if ($end < $last)
                    @if ($end < $last - 1)
                      <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item"><a class="page-link" href="{{ $users->url($last) }}">{{ $last }}</a></li>
                  @endif

                  {{-- Next --}}
                  <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}" aria-label="Next">
                      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                  </li>

                </ul>
              @endif
            </div>
          </div>

        </div>
      </div>
      {{-- /Table Card --}}

    </div>
  </div>
</div>

<style>
  /* Always show the header button on mobile */
  .content-header-right {
    display: flex !important;
  }

  /* Show breadcrumb on mobile */
  @media (max-width: 767px) {
    .breadcrumb-wrapper,
    .breadcrumb-wrapper .breadcrumb {
      display: flex !important;
    }
  }

  /* Vuexy gap utility not always present */
  .gap-50 { gap: 0.5rem !important; }

  /* Avatar initials fallback */
  .avatar-content {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #7367f0;
    background: rgba(115,103,240,.12) !important;
  }
  .avatar img {
    width: 32px;
    height: 32px;
    object-fit: cover;
  }

  /* Keep DT search input looking Vuexy */
  .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0;
  }
  .dataTables_filter input {
    min-width: 200px;
  }

  /* Pagination active page */
  .pagination .page-item.active .page-link {
    background-color: #5aab1f !important;
    border-color: #5aab1f !important;
    color: #fff !important;
  }
  .pagination .page-link {
    color: #5aab1f;
  }

  /* Filter pills */
  .badge-light-primary {
    background: rgba(115,103,240,.12);
    color: #7367f0;
  }

  /* ── Responsive toolbar ── */
  @media (max-width: 576px) {
    #filterForm {
      flex-wrap: wrap;
    }
    #filterForm .dataTables_filter {
      flex: 1 1 100%;
      order: -1;
    }
    #filterForm select {
      flex: 1 1 0;
      min-width: 0;
      width: auto !important;
    }
    #filterForm .btn-icon {
      flex-shrink: 0;
    }
  }

  /* ── Responsive table: card layout on mobile ── */
  @media (max-width: 768px) {
    .datatables-users thead {
      display: none;
    }
    .datatables-users tr {
      display: block;
      margin-bottom: 1rem;
      border: 1px solid #ebe9f1;
      border-radius: 6px;
    }
    .datatables-users td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
    }
    .datatables-users td:last-child {
      border-bottom: none;
    }
    .datatables-users td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-right: auto;
    }
  }
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function (el) { new bootstrap.Tooltip(el); });  });
</script>
@endpush

@endsection