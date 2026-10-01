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
                        <h2 class="content-header-title mb-0">Weight &amp; Delivery Threshold Manager</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Weight Manager</li>
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

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- GLOBAL THRESHOLDS BANNER                                       --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="threshold-bar">
                        <div class="thr-icon"><i data-feather="package" style="width:22px;height:22px;color:#7367f0;"></i></div>
                        <div class="thr-body">
                            <div class="thr-label">Weight threshold</div>
                            <div class="thr-value">{{ $weightThresholdKg }} kg</div>
                            <div class="thr-hint">Total cart weight above this triggers truck delivery</div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#thresholdsModal">
                            <i data-feather="edit-2" style="width:13px;height:13px;"></i> Edit
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="threshold-bar">
                        <div class="thr-icon"><i data-feather="dollar-sign" style="width:22px;height:22px;color:#28c76f;"></i></div>
                        <div class="thr-body">
                            <div class="thr-label">Order value threshold</div>
                            <div class="thr-value">₦{{ number_format($orderValueThresholdNgn) }}</div>
                            <div class="thr-hint">Orders at or above this value get truck delivery automatically</div>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm"
                                data-bs-toggle="modal" data-bs-target="#thresholdsModal">
                            <i data-feather="edit-2" style="width:13px;height:13px;"></i> Edit
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- TRICKLE-DOWN EXPLANATION                                       --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div class="alert alert-light border mb-3" style="font-size:13px;">
                <i data-feather="info" style="width:14px;height:14px;"></i>
                <strong>Trickle-down weight resolution:</strong>
                Product weight → Subcategory estimated weight → Category estimated weight → <strong>5 kg</strong> (system default).
                A more specific override always wins. Set weight on a subcategory (e.g. <em>Electric Irons = 2 kg</em>) and every
                product in it inherits that weight unless overridden at the product level.
                Truck delivery is triggered when total cart weight exceeds the threshold <strong>OR</strong> order value hits the value threshold.
            </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- CATEGORY / SUBCATEGORY WEIGHT TABLE                            --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section id="weight-manager">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Category &amp; Subcategory Weights</h4>
                                <input type="text" class="form-control form-control-sm" style="max-width:220px;"
                                       placeholder="Search categories…" id="categorySearch">
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:28%;">Category</th>
                                                <th style="width:32%;">Subcategories</th>
                                                <th style="width:20%;">Category weight</th>
                                                <th style="width:20%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                @php
                                                    $subsWithWeight = $category->subcategories->filter(fn($s) => !is_null($s->estimated_weight_kg));
                                                @endphp
                                                <tr>
                                                    {{-- Name --}}
                                                    <td data-label="Category">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i data-feather="folder" class="text-primary"></i>
                                                            <strong>{{ $category->name }}</strong>
                                                        </div>
                                                    </td>

                                                    {{-- Subcategories --}}
                                                    <td data-label="Subcategories" class="subcategories-cell">
                                                        @if($category->subcategories->isNotEmpty())
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                                <div class="dropdown">
                                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                                            type="button"
                                                                            data-bs-toggle="dropdown">
                                                                        Subcategories ({{ $category->subcategories->count() }})
                                                                    </button>
                                                                    <ul class="dropdown-menu p-0">
                                                                        @foreach ($category->subcategories as $sub)
                                                                            @php
                                                                                $effectiveWeight = $sub->estimated_weight_kg ?? $category->estimated_weight_kg ?? 5.0;
                                                                                $isInherited     = is_null($sub->estimated_weight_kg);
                                                                            @endphp
                                                                            <li class="border-bottom">
                                                                                <div class="dropdown-item-text d-flex justify-content-between align-items-center px-3 py-2"
                                                                                     style="cursor:pointer;"
                                                                                     onclick="event.stopPropagation(); setSubcategoryWeight({{ $sub->id }}, '{{ addslashes($sub->name) }}', {{ $effectiveWeight }})">
                                                                                    <div class="d-flex align-items-center gap-2">
                                                                                        <i data-feather="tag" style="width:14px;height:14px;color:#28c76f;"></i>
                                                                                        <span>{{ $sub->name }}</span>
                                                                                        @if(!$isInherited)
                                                                                            <span class="badge rounded-pill badge-light-success ms-1">{{ $sub->estimated_weight_kg }} kg</span>
                                                                                        @else
                                                                                            <span class="badge rounded-pill weight-null-badge ms-1" title="Inherited">~{{ $effectiveWeight }} kg</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="d-flex gap-1" onclick="event.stopPropagation();">
                                                                                        @if(!is_null($sub->estimated_weight_kg))
                                                                                            <form method="POST"
                                                                                                  action="{{ route('admin.weight.Subcategory.clear', $sub) }}">
                                                                                                @csrf @method('DELETE')
                                                                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2" title="Clear">
                                                                                                    <i data-feather="x" style="width:13px;height:13px;"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>

                                                                @if($subsWithWeight->isNotEmpty())
                                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                                        @foreach($subsWithWeight->take(3) as $sub)
                                                                            <span class="badge badge-light-info" style="font-size:0.7rem;">
                                                                                {{ \Illuminate\Support\Str::limit($sub->name, 12, '…') }} ({{ $sub->estimated_weight_kg }} kg)
                                                                            </span>
                                                                        @endforeach
                                                                        @if($subsWithWeight->count() > 3)
                                                                            <span class="badge bg-secondary bg-opacity-25 text-secondary" style="font-size:0.7rem;">
                                                                                +{{ $subsWithWeight->count() - 3 }} more
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    <form method="POST"
                                                                          action="{{ route('admin.weight.category.clearSubcategories', $category) }}"
                                                                          onsubmit="return confirm('Clear all subcategory weight overrides for \'{{ addslashes($category->name) }}\'?')">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                                                                            <i data-feather="x-circle" style="width:13px;height:13px;"></i> Clear all
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No subcategories</span>
                                                        @endif
                                                    </td>

                                                    {{-- Category weight --}}
                                                    <td data-label="Category weight">
                                                        @if(!is_null($category->estimated_weight_kg))
                                                            <span class="badge rounded-pill badge-light-primary">{{ $category->estimated_weight_kg }} kg</span>
                                                        @else
                                                            <span class="badge rounded-pill weight-null-badge">~5 kg default</span>
                                                        @endif
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td data-label="Actions">
                                                        <div class="d-flex gap-1 flex-wrap">
                                                            <button type="button" class="btn btn-primary btn-sm"
                                                                    onclick="setCategoryWeight({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->estimated_weight_kg ?? 5 }})">
                                                                <i class="fas fa-weight-hanging me-1"></i> Set weight
                                                            </button>
                                                            @if(!is_null($category->estimated_weight_kg))
                                                                <form method="POST" action="{{ route('admin.weight.category.clear', $category) }}">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                        <i class="fas fa-xmark me-1"></i> Clear
                                                                    </button>
                                                                </form>
                                                            @endif
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


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- THRESHOLDS MODAL                                                            --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="thresholdsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Delivery Thresholds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.weight.thresholds.update') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Truck delivery is triggered when <strong>either</strong> threshold is met by a customer's order.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Weight threshold (kg)</label>
                        <input type="number" name="truck_weight_threshold_kg" class="form-control"
                               value="{{ $weightThresholdKg }}" min="1" step="0.5" required>
                        <small class="text-muted">Total cart weight above this triggers truck delivery. Current: <strong>{{ $weightThresholdKg }} kg</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Order value threshold (₦)</label>
                        <input type="number" name="truck_order_value_threshold_ngn" class="form-control"
                               value="{{ $orderValueThresholdNgn }}" min="0" step="1000" required>
                        <small class="text-muted">Orders at or above this value get truck delivery. Current: <strong>₦{{ number_format($orderValueThresholdNgn) }}</strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save thresholds</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- CATEGORY WEIGHT MODAL                                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="categoryWeightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set category weight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryWeightForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Category</label>
                        <p class="fw-bold mb-0" id="categoryWeightName"></p>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Sets the estimated weight per unit for all products in this category that do not have a subcategory or product-level override.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Estimated weight per unit (kg)</label>
                        <input type="number" name="estimated_weight_kg" id="categoryWeightInput"
                               class="form-control" min="0" step="0.1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply weight</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SUBCATEGORY WEIGHT MODAL                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="subcategoryWeightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set subcategory weight</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="subcategoryWeightForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Subcategory</label>
                        <p class="fw-bold mb-0" id="subcategoryWeightName"></p>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Overrides the category weight for all products in this subcategory (unless a product has its own weight set).
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Estimated weight per unit (kg)</label>
                        <input type="number" name="estimated_weight_kg" id="subcategoryWeightInput"
                               class="form-control" min="0" step="0.1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply weight</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- STYLES                                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<style>
.weight-null-badge {
    background: rgba(108,117,125,0.12);
    color: #6c757d;
    border: 1px solid rgba(108,117,125,0.25);
    font-weight: 600;
}

.threshold-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    height: 100%;
}
.thr-icon { flex-shrink: 0; }
.thr-body { flex: 1; min-width: 0; }
.thr-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: #6e84a3; font-weight: 600; }
.thr-value { font-size: 1.6rem; font-weight: 700; color: #2c3e50; line-height: 1.1; }
.thr-hint  { font-size: 0.72rem; color: #6e84a3; margin-top: 2px; }

.dropdown-menu {
    min-width: 360px;
    max-height: 300px;
    overflow-y: auto;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.dropdown-item-text { font-size: 0.9rem; color: #0f1111; display: block; width: 100%; transition: background .15s; }
.dropdown-item-text:hover { background: #f0f2f2; }
.table-hover tbody tr:hover { background-color: #f8f9fa; }

@media (max-width: 768px) {
    .threshold-bar { flex-direction: column; align-items: flex-start; }
    .table thead { display: none; }
    .table tr { display: flex; flex-direction: column; margin-bottom: 1.5rem; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1rem; }
    .table td { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0; border: none; }
    .table td::before { content: attr(data-label); font-weight: 600; color: #37475a; flex: 1; }
}
</style>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();
    document.querySelectorAll('.modal').forEach(el => {
        el.addEventListener('shown.bs.modal', () => { if (typeof feather !== 'undefined') feather.replace(); });
    });

    document.getElementById('categorySearch').addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(row => {
            const cell = row.querySelector('td[data-label="Category"]');
            if (cell) row.style.display = cell.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });
});

function setCategoryWeight(id, name, current) {
    document.getElementById('categoryWeightName').textContent = name;
    document.getElementById('categoryWeightInput').value      = current;
    document.getElementById('categoryWeightForm').action      = '/admin/weight/category/' + id;
    new bootstrap.Modal(document.getElementById('categoryWeightModal')).show();
}

function setSubcategoryWeight(id, name, current) {
    document.getElementById('subcategoryWeightName').textContent = name;
    document.getElementById('subcategoryWeightInput').value      = current;
    document.getElementById('subcategoryWeightForm').action      = '/admin/weight/Subcategory/' + id;
    new bootstrap.Modal(document.getElementById('subcategoryWeightModal')).show();
}
</script>

@endsection
