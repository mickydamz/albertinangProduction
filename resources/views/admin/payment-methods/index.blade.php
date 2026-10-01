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
                        <h2 class="content-header-title mb-0">Payment Methods</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Payment Methods</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Payment Method
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0">Payment Methods List</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentMethods as $paymentMethod)
                                    <tr>
                                        <td data-label="ID">{{ $paymentMethod->id }}</td>
                                        <td data-label="Name">
                                            <span class="fw-bolder">{{ $paymentMethod->name }}</span>
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <a href="{{ route('admin.payment-methods.edit', $paymentMethod->id) }}"
                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form action="{{ route('admin.payment-methods.destroy', $paymentMethod->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this payment method?');">
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
                                        <td colspan="3">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-credit-card mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No payment methods found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
