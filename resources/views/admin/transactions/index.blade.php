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
                        <h2 class="content-header-title mb-0">Transactions</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Transactions</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Transaction
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
                    <h4 class="card-title mb-0">All Transactions</h4>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Total Amount</th>
                                <th>Escrow Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td data-label="User">
                                        <span class="fw-bolder">{{ $transaction->user->name }}</span>
                                    </td>
                                    <td data-label="Total Amount">
                                        <span class="fw-bolder text-primary">₦{{ number_format($transaction->total_amount, 2) }}</span>
                                    </td>
                                    <td data-label="Escrow Amount">
                                        <span class="fw-bolder">₦{{ number_format($transaction->escrow_amount, 2) }}</span>
                                    </td>
                                    <td data-label="Date">
                                        {{ $transaction->created_at->format('d M Y') }}
                                        <br><small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                    </td>
                                    <td data-label="Status">
                                        @if($transaction->status == 1)
                                            <span class="badge badge-light-success">Complete</span>
                                        @else
                                            <span class="badge badge-light-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td data-label="Payment Method">
                                        @if($transaction->payment_method_id == 1)
                                            <span class="badge badge-light-primary">Full Payment</span>
                                        @elseif($transaction->payment_method_id == 2)
                                            <span class="badge badge-light-info">Half Payment</span>
                                        @elseif($transaction->payment_method_id == 3)
                                            <span class="badge badge-light-warning">Escrow</span>
                                        @else
                                            <span class="badge badge-light-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td data-label="Actions" class="text-center">
                                        <div class="d-flex justify-content-center gap-50">
                                            <a href="{{ route('admin.transactions.show', $transaction->id) }}"
                                               class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                                               data-bs-toggle="tooltip" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.transactions.edit', $transaction->id) }}"
                                               class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('admin.transactions.destroy', $transaction->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this transaction?');">
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
                                            <i class="fas fa-right-left mb-1" style="font-size:2rem; opacity:.25;"></i>
                                            <p class="mb-0">No transactions found.</p>
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
