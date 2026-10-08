@extends('layouts.adminlayout')

@section('content')
@include('admin.orders.request-tabs')

<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row align-items-center">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="content-header-title mb-0">Return Requests</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Returns</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.returns.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> New Return
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>The return action was not saved.</strong>
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    Reopen Review, complete the required information and save again.
                </div>
            @endif
            @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── Stats ── --}}
            <div class="row mb-2">
                @php
                    $pending  = $returns->getCollection()->where('status','pending')->count();
                    $approved = $returns->getCollection()->where('status','approved')->count();
                    $rejected = $returns->getCollection()->where('status','rejected')->count();
                @endphp
                <div class="col-md-4">
                    <div class="card border-warning mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-clock text-warning fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ $returns->total() }}</div>
                                <div class="small text-muted">Total Requests</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-hourglass-half text-warning fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ \App\Models\OrderReturn::where('status','pending')->count() }}</div>
                                <div class="small text-muted">Pending Review</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success mb-1">
                        <div class="card-body d-flex align-items-center gap-2 py-2">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                            <div>
                                <div class="fw-bold">{{ \App\Models\OrderReturn::where('status','approved')->count() }}</div>
                                <div class="small text-muted">Approved</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">All Return Requests</h4>
                    @include('admin.partials.search-box', ['action' => route('admin.returns.index'), 'value' => $search, 'placeholder' => 'Search order #, customer or reason…'])
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="returnsTable">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Reason</th>
                                    <th>Evidence</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $return)
                                    <tr>
                                        <td data-label="Order">
                                            <a href="{{ route('admin.orders.show', $return->order) }}" class="fw-bold">
                                                #{{ $return->order->order_number ?? $return->order_id }}
                                            </a>
                                        </td>
                                        <td data-label="Customer">
                                            <div class="fw-bold">{{ $return->user->name ?? 'N/A' }}</div>
                                            <div class="small text-muted">{{ $return->user->email ?? '' }}</div>
                                        </td>
                                        <td data-label="Reason" style="max-width:240px;">
                                            <div style="font-size:0.85rem;white-space:normal;">
                                                {{ Str::limit($return->reason, 100) }}
                                            </div>
                                        </td>
                                        <td data-label="Evidence">
                                            @if($return->evidence_path)
                                                <a href="{{ Storage::url($return->evidence_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-image me-1"></i> View
                                                </a>
                                            @else
                                                <span class="text-muted small">None</span>
                                            @endif
                                        </td>
                                        <td data-label="Status">
                                            @php
                                                $colors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'];
                                            @endphp
                                            <span class="badge bg-{{ $colors[$return->status] ?? 'secondary' }}">
                                                {{ ucfirst($return->status) }}
                                                <div class="small">{{ $return->stageLabel() }}</div>
                                                @if($return->refund_status)
                                                    <div class="small">{{ \App\Support\RefundProgress::forStatus($return->refund_status)['label'] }}</div>
                                                @endif
                                            </span>
                                        </td>
                                        <td data-label="Submitted" class="small text-muted">{{ $return->created_at->diffForHumans() }}</td>
                                        <td data-label="Actions">
                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reviewReturnModal{{ $return->id }}">
                                                <i class="fas fa-eye me-1"></i> Review
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Review modal --}}
                                    <div class="modal fade" id="reviewReturnModal{{ $return->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Review Return — Order #{{ $return->order->order_number ?? $return->order_id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.returns.review', $return) }}">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Customer Reason</label>
                                                            <div class="p-3 bg-light rounded" style="font-size:0.875rem;">{{ $return->reason }}</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Decision <span class="text-danger">*</span></label>
                                                            @if($return->order?->payment_method === 'paystack')
                                                            @include('admin.orders.return-actions', ['return' => $return])
                                                            @else
<select name="status" class="form-select" required>
                                                                <option value="">— Select —</option>
                                                                <option value="approved" @selected($return->status === 'approved')>Approve</option>
                                                                <option value="rejected" @selected($return->status === 'rejected')>Reject</option>
                                                                <option value="refunded" @selected($return->status === 'refunded')>Refund (issue refund to customer)</option>
                                                            </select>@endif
                                                        </div>
                                                        @if($return->order?->payment_method !== 'paystack')
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Admin Note <span class="text-muted fw-normal">(optional)</span></label>
                                                            <textarea name="admin_notes" class="form-control" rows="3"
                                                                      placeholder="Add a note for the customer…">{{ $return->admin_notes }}</textarea>
                                                        </div>
                                                        @endif
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
                                        <td colspan="7" class="text-center text-muted py-4">No return requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($returns->hasPages())
                    <div class="card-footer">{{ $returns->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
  /* Responsive table — stack rows into cards on mobile (matches Products) */
  @media (max-width: 768px) {
    #returnsTable thead { display: none; }
    #returnsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #returnsTable td {
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
    #returnsTable td:last-child { border-bottom: none; }
    #returnsTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #returnsTable td > * { text-align: right; }
  }
</style>
@endsection
