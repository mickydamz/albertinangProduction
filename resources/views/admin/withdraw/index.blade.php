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
                        <h2 class="content-header-title mb-0">Withdrawals</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Withdrawals</li>
                        </ol>
                    </div>
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
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-1 text-info"></i> User Withdrawals
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($withdrawals as $withdrawal)
                                    <tr>
                                        <td data-label="User">
                                            <span class="fw-bolder">{{ $withdrawal->user->name }}</span>
                                        </td>
                                        <td data-label="Amount">
                                            <span class="fw-bolder text-primary">${{ number_format($withdrawal->amount, 2) }}</span>
                                        </td>
                                        <td data-label="Payment Method">{{ $withdrawal->paymentMethod->name }}</td>
                                        <td data-label="Status">
                                            @php
                                                $statusMap = [
                                                    '1' => ['label' => 'Completed', 'badge' => 'badge-light-success'],
                                                    '0' => ['label' => 'Pending',   'badge' => 'badge-light-warning'],
                                                    '2' => ['label' => 'Failed',    'badge' => 'badge-light-danger'],
                                                ];
                                                $s = $statusMap[$withdrawal->status] ?? ['label' => 'Unknown', 'badge' => 'badge-light-secondary'];
                                            @endphp
                                            <span class="badge {{ $s['badge'] }}">{{ $s['label'] }}</span>
                                        </td>
                                        <td data-label="Details">{{ $withdrawal->withdrawal_details }}</td>
                                        <td data-label="Date" class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>{{ $withdrawal->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td data-label="Actions" class="text-center">
                                            <div class="d-flex justify-content-center gap-50">
                                                @if($withdrawal->status == 0)
                                                    <form action="{{ route('admin.withdrawal.approve', $withdrawal->id) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-icon btn-relief-outline-success waves-effect"
                                                                data-bs-toggle="tooltip" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.withdrawals.destroy', $withdrawal->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this withdrawal?');">
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
                                                <i class="fas fa-money-bill-transfer mb-1" style="font-size:2rem; opacity:.25;"></i>
                                                <p class="mb-0">No withdrawals found.</p>
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
