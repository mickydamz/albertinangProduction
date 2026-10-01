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
                        <h2 class="content-header-title mb-0">Categories &amp; Subcategories</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Categories</li>
                        </ol>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Category</a>
                        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-1"></i> Add Subcategory</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="categories-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Category & Subcategory List</h4>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" placeholder="Search categories..." id="categorySearch">
                                </div>
                            </div>
                            <div class="card-body">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Subcategories</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                <tr>
                                                    <td data-label="Name">{{ $category->name }}</td>
                                                    <td data-label="Status">
                                                        <div class="form-check form-switch d-flex align-items-center gap-2 ps-0 m-0">
                                                            <input class="form-check-input cat-toggle m-0"
                                                                   type="checkbox" role="switch"
                                                                   id="cat-toggle-{{ $category->id }}"
                                                                   data-id="{{ $category->id }}"
                                                                   data-url="{{ route('admin.categories.toggleActive', $category->id) }}"
                                                                   {{ $category->is_active ? 'checked' : '' }}
                                                                   style="width:42px; height:22px; cursor:pointer; flex-shrink:0;">
                                                            <label for="cat-toggle-{{ $category->id }}"
                                                                   class="form-check-label cat-toggle-label-{{ $category->id }} m-0"
                                                                   style="font-size:12px; font-weight:500; cursor:pointer;
                                                                          color:{{ $category->is_active ? '#28c76f' : '#b0b0b0' }};">
                                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td data-label="Subcategories" class="subcategories-cell">
                                                        @if($category->subcategories->isNotEmpty())
                                                            <div class="dropdown">
                                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle w-100" type="button" id="dropdownMenu-{{ $category->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Subcategories ({{ $category->subcategories->count() }})
                                                                </button>
                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-{{ $category->id }}">
                                                                    @foreach ($category->subcategories as $Subcategory)
                                                                        <li>
                                                                            <div class="dropdown-item d-flex justify-content-between align-items-center">
                                                                                <span class="Subcategory-name">{{ $Subcategory->name }}</span>
                                                                                <div class="Subcategory-actions d-flex align-items-center gap-2">
                                                                                    <div class="form-check form-switch m-0 ps-0">
                                                                                        <input class="form-check-input subcat-toggle m-0"
                                                                                               type="checkbox" role="switch"
                                                                                               id="subcat-toggle-{{ $Subcategory->id }}"
                                                                                               data-id="{{ $Subcategory->id }}"
                                                                                               data-url="{{ route('admin.subcategories.toggleActive', $Subcategory->id) }}"
                                                                                               {{ $Subcategory->is_active ? 'checked' : '' }}
                                                                                               style="width:36px; height:20px; cursor:pointer;">
                                                                                    </div>
                                                                                    <a href="{{ route('admin.subcategories.edit', $Subcategory->id) }}"
                                                                                       class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                                                       data-bs-toggle="tooltip" title="Edit">
                                                                                      <i class="fas fa-pen"></i>
                                                                                    </a>
                                                                                    <button class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                                                                            data-bs-toggle="tooltip" title="Delete"
                                                                                            onclick="confirmDelete('{{ route('admin.subcategories.destroy', $Subcategory->id) }}', 'Subcategory')">
                                                                                      <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No subcategories</span>
                                                        @endif
                                                    </td>
                                                    <td data-label="Actions" class="actions-cell">
                                                        <div class="d-flex gap-50">
                                                          <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                             class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                             data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-pen"></i>
                                                          </a>
                                                          <button class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect"
                                                                  data-bs-toggle="tooltip" title="Delete"
                                                                  onclick="confirmDelete('{{ route('admin.categories.destroy', $category->id) }}', 'category')">
                                                            <i class="fas fa-trash"></i>
                                                          </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
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
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this <span id="deleteType"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .dropdown-menu {
        min-width: 300px;
        max-height: 250px;
        overflow-y: auto;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .dropdown-item {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .Subcategory-name {
        flex: 1;
        font-size: 0.95rem;
    }

    .Subcategory-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
        border-radius: 4px;
    }

    .form-control-sm {
        max-width: 200px;
    }

    /* Gap utility */
    .gap-50 { gap: 0.5rem !important; }

    /* Status toggle green */
    .cat-toggle:checked, .subcat-toggle:checked { background-color:#28c76f !important; border-color:#28c76f !important; }
    .cat-toggle:focus,   .subcat-toggle:focus   { box-shadow: 0 0 0 3px rgba(40,199,111,.25) !important; }

    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table tr {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            background-color: #fff;
        }

        .table td {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border: none;
        }

        .table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #37475a;
            flex: 1;
        }

        .table td[data-label="Name"] {
            order: 1;
            font-size: 1.2rem;
            font-weight: 500;
            color: #232f3e;
        }

        .table td[data-label="Status"] {
            order: 2;
        }

        .table td.actions-cell {
            order: 3;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .table td.subcategories-cell {
            order: 4;
            border-top: 1px solid #e0e0e0;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .dropdown {
            width: 100%;
        }

        .dropdown-toggle {
            width: 100%;
            text-align: left;
            background-color: #f5f6f5;
            border: 1px solid #d5d9d9;
            border-radius: 4px;
        }

        .dropdown-menu {
            width: 100%;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tooltips
        [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            .forEach(function (el) { new bootstrap.Tooltip(el); });

        // Initialize Bootstrap dropdowns
        if (typeof bootstrap !== 'undefined') {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.forEach(function (dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        }

        // Search functionality
        document.getElementById('categorySearch').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.table tbody tr').forEach(row => {
                const name = row.querySelector('td[data-label="Name"]').textContent.toLowerCase();
                row.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });

        // Delete confirmation modal
        window.confirmDelete = function (url, type) {
            document.getElementById('deleteType').textContent = type;
            document.getElementById('deleteForm').action = url;
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        };

        // AJAX status toggles (categories + subcategories)
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function wireToggle(toggle) {
            toggle.addEventListener('click', e => e.stopPropagation()); // keep dropdown open
            toggle.addEventListener('change', function () {
                const url = this.dataset.url, checked = this.checked, id = this.dataset.id;
                const label = document.querySelector('.cat-toggle-label-' + id);
                if (label) {
                    label.textContent = checked ? 'Active' : 'Inactive';
                    label.style.color = checked ? '#28c76f' : '#b0b0b0';
                }
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-HTTP-Method-Override': 'PATCH'
                    },
                    body: JSON.stringify({ _method: 'PATCH' }),
                }).catch(function () {
                    toggle.checked = !checked;
                    if (label) {
                        label.textContent = !checked ? 'Active' : 'Inactive';
                        label.style.color = !checked ? '#28c76f' : '#b0b0b0';
                    }
                    alert('Failed to update status. Please try again.');
                });
            });
        }

        document.querySelectorAll('.cat-toggle, .subcat-toggle').forEach(wireToggle);
    });
</script>
@endsection