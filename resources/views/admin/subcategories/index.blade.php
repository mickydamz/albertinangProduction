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
                        <h2 class="content-header-title mb-0">Subcategories</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
                            <li class="breadcrumb-item active">Subcategories</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Subcategory
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="subcategories-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h4 class="card-title">Subcategory List</h4>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Parent Category</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($subcategories as $Subcategory)
                                                <tr>
                                                    <td data-label="Name">{{ $Subcategory->name }}</td>
                                                    <td data-label="Parent Category">
                                                        @if ($Subcategory->category)
                                                            <a href="{{ route('admin.categories.edit', $Subcategory->category->id) }}">{{ $Subcategory->category->name }}</a>
                                                        @else
                                                            <span class="text-muted">No Parent</span>
                                                        @endif
                                                    </td>
                                                    <td data-label="Actions" class="text-center">
                                                        <div class="d-flex justify-content-center gap-50">
                                                            <a href="{{ route('admin.subcategories.edit', $Subcategory->id) }}"
                                                               class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                               data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fas fa-pen"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                                                    data-bs-toggle="tooltip" title="Delete"
                                                                    onclick="confirmDelete('{{ route('admin.subcategories.destroy', $Subcategory->id) }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted py-3">No subcategories found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Are you sure you want to delete this subcategory?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .gap-50 { gap: 0.5rem !important; }

    @media (max-width: 768px) {
        .table thead { display: none; }
        .table tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
        .table td { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border: none; border-bottom: 1px solid #f3f2f7; font-size: 0.875rem; }
        .table td:last-child { border-bottom: none; }
        .table td::before { content: attr(data-label); font-weight: 600; color: #b9b9c3; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; flex-shrink: 0; padding-right: 0.5rem; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            .forEach(el => new bootstrap.Tooltip(el));

        window.confirmDelete = function (url) {
            document.getElementById('deleteForm').action = url;
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        };
    });
</script>
@endsection
