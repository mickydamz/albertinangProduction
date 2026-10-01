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
                        <h2 class="content-header-title mb-0">Affiliate List</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Affiliates</li>
                        </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header border-bottom">
                    <h4 class="card-title mb-0">All Affiliates</h4>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Referrals</th>
                                <th>Clicks</th>
                                <th>Bounties</th>
                                <th>Items Shipped</th>
                                <th>Earnings</th>
                                <th>Orders</th>
                                <th>Conversions</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($affiliates as $affiliate)
                                <tr>
                                    <td data-label="Name">
                                        <span class="fw-bolder">{{ $affiliate->name }}</span>
                                    </td>
                                    <td data-label="Referrals">{{ $affiliate->referrals->count() }}</td>
                                    <td data-label="Clicks">{{ $affiliate->total_clicks }}</td>
                                    <td data-label="Bounties">{{ $affiliate->total_bounties }}</td>
                                    <td data-label="Items Shipped">{{ $affiliate->total_items_shipped }}</td>
                                    <td data-label="Earnings">{{ $affiliate->total_earnings }}</td>
                                    <td data-label="Orders">{{ $affiliate->total_orders }}</td>
                                    <td data-label="Conversions">{{ $affiliate->conversions }}</td>
                                    <td data-label="Actions" class="text-center">
                                        <div class="d-flex justify-content-center gap-50">
                                            <a href="{{ route('admin.affiliates.show', $affiliate->id) }}"
                                               class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                                               data-bs-toggle="tooltip" title="View Referrals">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.affiliates.edit', $affiliate->id) }}"
                                               class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="d-flex flex-column align-items-center py-3 text-center text-muted">
                                            <i class="fas fa-users mb-1" style="font-size:2rem; opacity:.25;"></i>
                                            <p class="mb-0">No affiliates found.</p>
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
