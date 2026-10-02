@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row align-items-center">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Cancellation Requests</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Cancellations</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.cancellations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Cancellation
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── Stats ── --}}
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="card border-secondary mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-ban text-secondary fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ $cancellations->total() }}</div>
                                <div class="small text-muted">Total Requests</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-danger mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ \App\Models\OrderCancellation::where('status','approved')->count() }}</div>
                                <div class="small text-muted">Cancelled</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-rotate-left text-success fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ \App\Models\OrderCancellation::where('status','refunded')->count() }}</div>
                                <div class="small text-muted">Refunded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">All Cancellation Requests</h4>
                    @include('admin.partials.search-box', ['action' => route('admin.cancellations.index'), 'value' => $search, 'placeholder' => 'Search order #, customer or reason…'])
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="cancellationsTable">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Order Total</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cancellations as $cancellation)
                                    <tr>
                                        <td data-label="Order">
                                            <a href="{{ route('admin.orders.show', $cancellation->order) }}" class="fw-bold">
                                                #{{ $cancellation->order->order_number ?? $cancellation->order_id }}
                                            </a>
                                            <div class="small text-muted">{{ $cancellation->order->status ?? '' }}</div>
                                        </td>
                                        <td data-label="Customer">
                                            <div class="fw-bold">{{ $cancellation->user->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $cancellation->user->email ?? '' }}</div>
                                        </td>
                                        <td data-label="Order Total" class="fw-bold">
                                            ₦{{ number_format($cancellation->order->total ?? 0, 2) }}
                                        </td>
                                        <td data-label="Reason" style="max-width:240px;">
                                            <div style="font-size:0.85rem;white-space:normal;">
                                                {{ Str::limit($cancellation->reason, 100) }}
                                            </div>
                                        </td>
                                        <td data-label="Status">
                                            @php
                                                $colors = ['pending'=>'warning','approved'=>'danger','refunded'=>'success','rejected'=>'secondary'];
                                            @endphp
                                            <span class="badge bg-{{ $colors[$cancellation->status] ?? 'secondary' }}">
                                                {{ ucfirst($cancellation->status) }}
                                                @if($cancellation->refund_status)
                                                    <div class="small">Refund: {{ ucfirst(str_replace('-', ' ', $cancellation->refund_status)) }}</div>
                                                @endif
                                            </span>
                                        </td>
                                        <td data-label="Submitted" class="small text-muted">{{ $cancellation->created_at->diffForHumans() }}</td>
                                        <td data-label="Actions">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reviewCancelModal{{ $cancellation->id }}">
                                                <i class="fas fa-eye me-1"></i> Review
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Review modal --}}
                                    <div class="modal fade" id="reviewCancelModal{{ $cancellation->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Review Cancellation — Order #{{ $cancellation->order->order_number ?? $cancellation->order_id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.cancellations.review', $cancellation) }}">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Customer Reason</label>
                                                            <div class="p-3 bg-light rounded" style="font-size:0.875rem;">{{ $cancellation->reason }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Order Total</label>
                                                            <div class="fw-bold text-success">₦{{ number_format($cancellation->order->total ?? 0, 2) }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Decision <span class="text-danger">*</span></label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="">— Select —</option>
                                                                <option value="approved" @selected($cancellation->status === 'approved')>Approve cancellation</option>
                                                                <option value="rejected" @selected($cancellation->status === 'rejected')>Reject cancellation</option>
                                                                <option value="refunded" @selected($cancellation->status === 'refunded')>Approve &amp; refund customer</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Admin Note <span class="text-muted fw-normal">(optional)</span></label>
                                                            <textarea name="admin_notes" class="form-control" rows="3"
                                                                      placeholder="e.g. Cancellation approved, refund in 5–10 business days…">{{ $cancellation->admin_notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancel</button>
                                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Decision</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No cancellation requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cancellations->hasPages())
                    <div class="card-footer">{{ $cancellations->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
  /* Responsive table — stack rows into cards on mobile (matches Products) */
  @media (max-width: 768px) {
    #cancellationsTable thead { display: none; }
    #cancellationsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #cancellationsTable td {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
      max-width: none !important;
    }
    #cancellationsTable td:last-child { border-bottom: none; }
    #cancellationsTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #cancellationsTable td > * { text-align: right; }
  }
</style>
@endsection