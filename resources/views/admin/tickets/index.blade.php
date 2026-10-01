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
              <h2 class="content-header-title mb-0">Support Tickets</h2>
              <div style="width:1px; height:20px; background:#ebe9f1;"></div>
              <nav>
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active">Tickets</li>
                </ol>
              </nav>
            </div>
          </div>
          <a href="{{ route('admin.tickets.create') }}"
             class="btn btn-primary waves-effect waves-float waves-light"
             style="flex-shrink:0;">
            <i class="fas fa-plus me-1"></i> New Ticket
          </a>
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
      @if(session('status'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <div class="alert-body">{{ session('status') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card">

        {{-- Toolbar --}}
        <div class="card-header border-bottom">
          <form method="GET" action="{{ route('admin.tickets.index') }}" id="filterForm"
                class="d-flex flex-wrap align-items-center gap-50 w-100">

            {{-- Search --}}
            <div style="flex: 1 1 auto;">
              <input type="search" name="search" value="{{ old('search', request('search')) }}"
                     class="form-control" placeholder="Search subject or user…" style="width:100%;">
            </div>

            <div class="d-block d-sm-none w-100" style="height:0;"></div>

            {{-- Status --}}
            <select name="status" class="form-select" style="width:140px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="open"    {{ request('status') === 'open'    ? 'selected' : '' }}>Open</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="closed"  {{ request('status') === 'closed'  ? 'selected' : '' }}>Closed</option>
            </select>

            {{-- Priority --}}
            <select name="priority" class="form-select" style="width:140px; flex-shrink:0; min-width:0;"
                    onchange="this.form.submit()">
              <option value="">All Priorities</option>
              <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
              <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
              <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
            </select>

            {{-- Search button --}}
            <button type="submit" class="btn btn-primary waves-effect waves-float waves-light" style="flex-shrink:0;">
              <i class="fas fa-search me-1"></i> Search
            </button>

            {{-- Reset --}}
            <a href="{{ route('admin.tickets.index') }}"
               class="btn btn-icon btn-outline-secondary waves-effect"
               data-bs-toggle="tooltip" title="Reset" style="flex-shrink:0;">
              <i class="fas fa-rotate"></i>
            </a>

          </form>
        </div>

        {{-- Table --}}
        <div class="card-datatable table-responsive">
          <table class="table table-hover align-middle" id="ticketsTable">
            <thead>
              <tr>
                <th>Subject</th>
                <th>User</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tickets as $ticket)
                @php
                  $statusMap = [
                    'open'    => ['bg' => 'bg-light-primary',   'text' => 'text-primary'],
                    'pending' => ['bg' => 'bg-light-warning',   'text' => 'text-warning'],
                    'closed'  => ['bg' => 'bg-light-success',   'text' => 'text-success'],
                  ];
                  $priorityMap = [
                    'low'    => ['bg' => 'bg-light-success',   'text' => 'text-success'],
                    'medium' => ['bg' => 'bg-light-warning',   'text' => 'text-warning'],
                    'high'   => ['bg' => 'bg-light-danger',    'text' => 'text-danger'],
                  ];
                  $sc = $statusMap[$ticket->status]     ?? ['bg' => 'bg-light-secondary', 'text' => 'text-secondary'];
                  $pc = $priorityMap[$ticket->priority] ?? ['bg' => 'bg-light-secondary', 'text' => 'text-secondary'];
                  $thumbs     = array_slice($ticket->images ?? [], 0, 3);
                  $extraCount = max(0, count($ticket->images ?? []) - 3);
                @endphp
                <tr>
                  <td data-label="Subject">
                    <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                       class="fw-bolder text-body text-decoration-none"
                       style="display:block; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                      {{ $ticket->subject }}
                    </a>
                    @if(!empty($thumbs))
                      <div class="d-flex flex-wrap gap-1 mt-50">
                        @foreach($thumbs as $img)
                          <img src="{{ Storage::url($img) }}" alt="attachment"
                               style="width:30px;height:30px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                        @endforeach
                        @if($extraCount > 0)
                          <span class="badge bg-light-secondary text-secondary"
                                style="font-size:10px; align-self:center;">+{{ $extraCount }}</span>
                        @endif
                      </div>
                    @endif
                  </td>

                  <td data-label="User">
                    <div class="d-flex flex-column">
                      <span class="fw-bolder text-truncate" style="max-width:140px;">{{ $ticket->user->name ?? '—' }}</span>
                      <small class="text-muted text-truncate" style="max-width:140px;">{{ $ticket->user->email ?? '' }}</small>
                    </div>
                  </td>

                  <td data-label="Priority">
                    <span class="badge rounded-pill {{ $pc['bg'] }} {{ $pc['text'] }}">
                      {{ ucfirst($ticket->priority) }}
                    </span>
                  </td>

                  <td data-label="Status">
                    <span class="badge rounded-pill {{ $sc['bg'] }} {{ $sc['text'] }}">
                      {{ ucfirst($ticket->status) }}
                    </span>
                  </td>

                  <td data-label="Last Updated">
                    {{ $ticket->updated_at->format('d M Y') }}
                    <br><small class="text-muted">{{ $ticket->updated_at->diffForHumans() }}</small>
                  </td>

                  <td data-label="Actions" class="text-center">
                    <div class="d-flex justify-content-center gap-50">
                      <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                         data-bs-toggle="tooltip" title="View & Reply">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.tickets.edit', $ticket->id) }}"
                         class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                         data-bs-toggle="tooltip" title="Edit">
                        <i class="fas fa-pen"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6">
                    <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-1"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                      <p class="mb-0">No tickets found.</p>
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
              Showing {{ $tickets->firstItem() ?? 0 }} to {{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }} entries
            </div>
          </div>
          <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
            @if ($tickets->lastPage() > 1)
              @php
                $cur   = $tickets->currentPage();
                $last  = $tickets->lastPage();
                $start = max(1, $cur - 2);
                $end   = min($last, $cur + 2);
                $paged = $tickets->appends(request()->query());
              @endphp
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item {{ $tickets->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $tickets->onFirstPage() ? '#' : $paged->previousPageUrl() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                  </a>
                </li>
                @if ($start > 1)
                  <li class="page-item"><a class="page-link" href="{{ $paged->url(1) }}">1</a></li>
                  @if ($start > 2)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                @endif
                @for ($p = $start; $p <= $end; $p++)
                  <li class="page-item {{ $p === $cur ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paged->url($p) }}">{{ $p }}</a>
                  </li>
                @endfor
                @if ($end < $last)
                  @if ($end < $last - 1)<li class="page-item disabled"><span class="page-link">…</span></li>@endif
                  <li class="page-item"><a class="page-link" href="{{ $paged->url($last) }}">{{ $last }}</a></li>
                @endif
                <li class="page-item {{ !$tickets->hasMorePages() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $tickets->hasMorePages() ? $paged->nextPageUrl() : '#' }}">
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
  .me-50  { margin-right: 0.5rem !important; }
  .mt-50  { margin-top: 0.5rem !important; }

  .pagination .page-item.active .page-link { background-color: #5aab1f !important; border-color: #5aab1f !important; color: #fff !important; }
  .pagination .page-link { color: #5aab1f; }
  .pagination .page-item.disabled .page-link { pointer-events: none; }

  @media (max-width: 576px) {
    #filterForm { flex-wrap: wrap; }
    #filterForm > div:first-child { flex: 1 1 100%; }
    #filterForm select { flex: 1 1 0; min-width: 0; width: auto !important; }
  }

  @media (max-width: 768px) {
    #ticketsTable thead { display: none; }
    #ticketsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #ticketsTable td {
      display: flex; justify-content: space-between; align-items: center;
      padding: 0.5rem 0.75rem; border: none; border-bottom: 1px solid #f3f2f7; font-size: 0.875rem;
    }
    #ticketsTable td:last-child { border-bottom: none; }
    #ticketsTable td::before {
      content: attr(data-label); font-weight: 600; color: #b9b9c3;
      font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;
      flex-shrink: 0; padding-right: 0.5rem;
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
