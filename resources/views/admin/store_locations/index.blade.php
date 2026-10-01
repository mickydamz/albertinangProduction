@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Store Locations</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Store Locations</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.store-locations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Location
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

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">All Store Locations</h4>
                    @include('admin.partials.search-box', ['action' => route('admin.store-locations.index'), 'value' => $search, 'placeholder' => 'Search name, address, phone or email…'])
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Hours</th>
                                    <th class="text-center" style="width:70px;">HQ</th>
                                    <th class="text-center" style="width:70px;">Order</th>
                                    <th class="text-center" style="width:100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $loc)
                                    <tr>
                                        <td data-label="Name"><span class="fw-bolder">{{ $loc->name }}</span></td>
                                        <td data-label="Address">{{ $loc->address }}</td>
                                        <td data-label="Phone">{{ $loc->phone ?? '—' }}</td>
                                        <td data-label="Hours">{{ $loc->hours ?? '—' }}</td>
                                        <td data-label="HQ" class="text-center">
                                            @if($loc->is_hq)
                                                <span class="badge badge-light-success"><i class="fas fa-check"></i> HQ</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td data-label="Order" class="text-center text-muted">{{ $loc->sort_order }}</td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <a href="{{ route('admin.store-locations.edit', $loc->id) }}"
                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('admin.store-locations.destroy', $loc->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this store location?');">
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
                                        <td colspan="7">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-store mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No store locations found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($locations->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $locations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gap-50 { gap: 0.5rem !important; }
@media (max-width: 768px) {
    .table thead { display: none; }
    .table tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    .table td {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.5rem 0.75rem; border: none; border-bottom: 1px solid #f3f2f7;
    }
    .table td:last-child { border-bottom: none; }
    .table td::before {
        content: attr(data-label); font-weight: 600; color: #b9b9c3;
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em;
        flex-shrink: 0; padding-right: 0.5rem;
    }
    .table td[data-label="Actions"] { justify-content: flex-end; }
    .table td[data-label="Actions"]::before { display: none; }
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
