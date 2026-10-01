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
                        <h2 class="content-header-title mb-0">Locations</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Locations</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Location
                    </a>
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
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Locations List</h4>
                    @include('admin.partials.search-box', ['action' => route('admin.locations.index'), 'value' => $search, 'placeholder' => 'Search locations or state…'])
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>State</th>
                                    <th>Std Fee</th>
                                    <th>Truck Fee</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locations as $location)
                                    <tr>
                                        <td data-label="Name"><span class="fw-bolder">{{ $location->name }}</span></td>
                                        <td data-label="State">{{ $location->state?->name ?? '—' }}</td>
                                        <td data-label="Std Fee">
                                            {{ $location->shipping_cost ? '₦'.number_format($location->shipping_cost, 0) : '—' }}
                                        </td>
                                        <td data-label="Truck Fee">
                                            {{ $location->truck_shipping_cost ? '₦'.number_format($location->truck_shipping_cost, 0) : '—' }}
                                        </td>
                                        <td data-label="Status">
                                            @if ($location->is_active)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <a href="{{ route('admin.locations.show', $location->id) }}"
                                                   class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                                                   data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.locations.edit', $location->id) }}"
                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                <form action="{{ route('admin.locations.toggleActive', $location->id) }}"
                                                      method="POST" class="d-inline-flex align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-check form-switch m-0">
                                                        <input type="checkbox" class="form-check-input" role="switch"
                                                               {{ $location->is_active ? 'checked' : '' }}
                                                               onchange="this.form.submit()"
                                                               title="{{ $location->is_active ? 'Deactivate' : 'Activate' }}">
                                                    </div>
                                                </form>

                                                <form action="{{ route('admin.locations.destroy', $location->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete {{ addslashes($location->name) }}? This cannot be undone.');">
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
                                        <td colspan="6">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-map-marker-alt mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No locations found. <a href="{{ route('admin.locations.create') }}">Add one now.</a></p>
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
