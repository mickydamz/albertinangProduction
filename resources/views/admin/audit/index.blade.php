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
                        <h2 class="content-header-title mb-0">Audit Trail</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Audit Trail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">

            <div class="card">
                <div class="card-header border-bottom">
                    <form method="GET" action="{{ route('admin.audit.index') }}" id="auditFilterForm"
                          class="d-flex flex-wrap align-items-center gap-50 w-100">

                        <h4 class="card-title mb-0 me-1" style="flex-shrink:0;">Activity Log</h4>
                        <div style="width:1px; height:20px; background:#ebe9f1; flex-shrink:0;"></div>

                        {{-- Event --}}
                        <select name="event" class="form-select form-select-sm" style="width:130px; flex-shrink:0; min-width:0;"
                                onchange="this.form.submit()">
                            <option value="">All events</option>
                            <option value="created" @selected($event === 'created')>Created</option>
                            <option value="updated" @selected($event === 'updated')>Updated</option>
                            <option value="deleted" @selected($event === 'deleted')>Deleted</option>
                        </select>

                        {{-- Model --}}
                        <select name="model" class="form-select form-select-sm" style="width:150px; flex-shrink:0; min-width:0;"
                                onchange="this.form.submit()">
                            <option value="">All models</option>
                            @foreach($modelTypes as $type)
                                <option value="{{ $type }}" @selected($model === $type)>{{ $type }}</option>
                            @endforeach
                        </select>

                        {{-- Search --}}
                        <div style="flex:1 1 auto; min-width:160px;">
                            <input type="search" name="search" value="{{ $search }}"
                                   class="form-control form-control-sm" placeholder="Admin name, model or record id…">
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary waves-effect" style="flex-shrink:0;">Search</button>

                        <a href="{{ route('admin.audit.index') }}"
                           class="btn btn-sm btn-icon btn-outline-secondary waves-effect"
                           data-bs-toggle="tooltip" title="Reset" style="flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 .49-3.96"></path></svg>
                        </a>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="auditTable">
                            <thead>
                                <tr>
                                    <th>When</th>
                                    <th>Admin</th>
                                    <th>Event</th>
                                    <th>Record</th>
                                    <th>Changes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $eventColors = ['created'=>'success','updated'=>'info','deleted'=>'danger'];
                                    @endphp
                                    <tr>
                                        <td data-label="When" class="small text-muted" style="white-space:nowrap;">
                                            {{ $log->created_at->format('d M Y, H:i') }}
                                            <div style="font-size:11px;">{{ $log->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td data-label="Admin">
                                            <div class="fw-bolder">{{ $log->user_name ?? 'System' }}</div>
                                            @if($log->ip_address)
                                                <div class="small text-muted" style="font-size:11px;">{{ $log->ip_address }}</div>
                                            @endif
                                        </td>
                                        <td data-label="Event">
                                            <span class="badge bg-light-{{ $eventColors[$log->event] ?? 'secondary' }}">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </td>
                                        <td data-label="Record">
                                            <span class="fw-bolder">{{ $log->model_label }}</span>
                                            <span class="text-muted">#{{ $log->auditable_id }}</span>
                                        </td>
                                        <td data-label="Changes" style="max-width:360px;">
                                            @php
                                                // Safely turn any value (incl. arrays/objects) into a short string.
                                                $auditStr = function ($v) {
                                                    if (is_null($v))   return '—';
                                                    if (is_bool($v))   return $v ? 'true' : 'false';
                                                    if (is_array($v))  return \Illuminate\Support\Str::limit(json_encode($v), 40);
                                                    return \Illuminate\Support\Str::limit((string) $v, 40);
                                                };
                                            @endphp
                                            @if($log->event === 'updated' && $log->new_values)
                                                @foreach($log->new_values as $field => $newVal)
                                                    <div class="small" style="white-space:normal;">
                                                        <span class="text-muted">{{ $field }}:</span>
                                                        <span class="text-danger" style="text-decoration:line-through;">{{ $auditStr($log->old_values[$field] ?? null) }}</span>
                                                        <span class="text-success">→ {{ $auditStr($newVal) }}</span>
                                                    </div>
                                                @endforeach
                                            @elseif($log->event === 'created')
                                                <span class="small text-muted">Record created</span>
                                            @elseif($log->event === 'deleted')
                                                <span class="small text-muted">Record deleted</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No audit records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="row mx-2 my-1 align-items-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 d-flex justify-content-end mt-1 mt-md-0">
                            @if ($logs->lastPage() > 1)
                                <ul class="pagination pagination-sm mb-0">

                                    {{-- Prev --}}
                                    <li class="page-item {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $logs->previousPageUrl() ?? '#' }}" aria-label="Previous">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                        </a>
                                    </li>

                                    {{-- Page numbers --}}
                                    @php
                                        $current = $logs->currentPage();
                                        $last    = $logs->lastPage();
                                        $start   = max(1, $current - 2);
                                        $end     = min($last, $current + 2);
                                    @endphp

                                    @if ($start > 1)
                                        <li class="page-item"><a class="page-link" href="{{ $logs->url(1) }}">1</a></li>
                                        @if ($start > 2)
                                            <li class="page-item disabled"><span class="page-link">…</span></li>
                                        @endif
                                    @endif

                                    @for ($p = $start; $p <= $end; $p++)
                                        <li class="page-item {{ $p === $current ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $logs->url($p) }}">{{ $p }}</a>
                                        </li>
                                    @endfor

                                    @if ($end < $last)
                                        @if ($end < $last - 1)
                                            <li class="page-item disabled"><span class="page-link">…</span></li>
                                        @endif
                                        <li class="page-item"><a class="page-link" href="{{ $logs->url($last) }}">{{ $last }}</a></li>
                                    @endif

                                    {{-- Next --}}
                                    <li class="page-item {{ !$logs->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $logs->nextPageUrl() ?? '#' }}" aria-label="Next">
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
</div>

<style>
    .pagination .page-item.active .page-link {
        background-color: #5aab1f !important;
        border-color: #5aab1f !important;
        color: #fff !important;
    }
    .pagination .page-link {
        color: #5aab1f;
    }

    /* Responsive table — stack rows into cards on mobile (matches Products) */
    @media (max-width: 768px) {
        #auditTable thead { display: none; }
        #auditTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
        #auditTable td {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            border: none;
            border-bottom: 1px solid #f3f2f7;
            font-size: 0.875rem;
            max-width: none !important;
            white-space: normal !important;
        }
        #auditTable td:last-child { border-bottom: none; }
        #auditTable td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #b9b9c3;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
            padding-right: 0.5rem;
        }
        #auditTable td > * { text-align: right; }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipEls.forEach(function (el) { new bootstrap.Tooltip(el); });
    });
</script>
@endpush

@endsection