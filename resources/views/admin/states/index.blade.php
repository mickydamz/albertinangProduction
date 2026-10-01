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
                        <h2 class="content-header-title mb-0">States</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">States</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.states.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New State
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
                    <h4 class="card-title mb-0">States List</h4>
                    @include('admin.partials.search-box', ['action' => route('admin.states.index'), 'value' => $search, 'placeholder' => 'Search states…'])
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Locations</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($states as $state)
                                    <tr>
                                        <td data-label="Name"><span class="fw-bolder">{{ $state->name }}</span></td>
                                        <td data-label="Locations">
                                            <span class="badge badge-light-secondary">{{ $state->locations_count }} location(s)</span>
                                        </td>
                                        <td data-label="Status">
                                            @if ($state->is_active)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <a href="{{ route('admin.states.edit', $state->id) }}"
                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>

                                                <form action="{{ route('admin.states.toggleActive', $state->id) }}"
                                                      method="POST" class="d-inline-flex align-items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-check form-switch m-0">
                                                        <input type="checkbox" class="form-check-input" role="switch"
                                                               {{ $state->is_active ? 'checked' : '' }}
                                                               onchange="this.form.submit()"
                                                               title="{{ $state->is_active ? 'Deactivate' : 'Activate' }}">
                                                    </div>
                                                </form>

                                                <form action="{{ route('admin.states.destroy', $state->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete {{ addslashes($state->name) }}? All its locations will be unlinked.');">
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
                                        <td colspan="4">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-globe-africa mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No states found. <a href="{{ route('admin.states.create') }}">Add one now.</a></p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($states->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {{ $states->links() }}
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
