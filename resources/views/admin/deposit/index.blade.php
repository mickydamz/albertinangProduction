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
                        <h2 class="content-header-title mb-0">Deposits</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Deposits</li>
                        </ol>
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
                    <h4 class="card-title mb-0">Deposits List</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Receipt</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td data-label="Date">
                                            {{ $deposit->created_at->format('d M Y') }}
                                            <br><small class="text-muted">{{ $deposit->created_at->format('H:i') }}</small>
                                        </td>
                                        <td data-label="Amount">
                                            <span class="fw-bolder text-primary">₦{{ number_format($deposit->amount, 2) }}</span>
                                        </td>
                                        <td data-label="Payment Method">
                                            {{ $deposit->paymentMethod->name ?? '—' }}
                                        </td>
                                        <td data-label="Receipt">
                                            @if($deposit->transaction_picture)
                                                <a href="{{ asset('storage/' . $deposit->transaction_picture) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $deposit->transaction_picture) }}"
                                                         alt="Receipt" style="width:60px; height:60px; object-fit:cover; border-radius:4px; border:1px solid #dee2e6;">
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td data-label="Status">
                                            @if($deposit->status == 1)
                                                <span class="badge badge-light-success">Approved</span>
                                            @else
                                                <span class="badge badge-light-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                <form action="{{ route('admin.deposits.approve', $deposit->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-relief-outline-success waves-effect"
                                                            title="Approve">
                                                        <i class="fas fa-check me-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.deposits.reject', $deposit->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-relief-outline-danger waves-effect"
                                                            title="Reject">
                                                        <i class="fas fa-xmark me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                                <i class="fas fa-money-bill-transfer mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No deposits found.</p>
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
    .table td[data-label="Actions"] { justify-content: flex-end; flex-wrap: wrap; }
    .table td[data-label="Actions"]::before { display: none; }
}
</style>

@endsection
