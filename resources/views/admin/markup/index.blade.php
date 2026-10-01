@extends('layouts.adminlayout')

@section('content')

@php
    /*
    ┌─────────────────────────────────────────────────────────────────────────┐
    │  Global markup stats                                                     │
    │  bulkAll() writes to categories, so we read from there.                 │
    └─────────────────────────────────────────────────────────────────────────┘
    */

    // Distinct markup values across products (for display pills)
    $productMarkupValues = \App\Models\Product::whereNotNull('markup_percent')
                            ->select('markup_percent')
                            ->distinct()
                            ->pluck('markup_percent');

    // Global value = most common category markup (this is where bulkAll writes)
    $globalMarkupValue = \App\Models\Category::whereNotNull('markup_percent')
                            ->selectRaw('markup_percent, COUNT(*) as cnt')
                            ->groupBy('markup_percent')
                            ->orderByDesc('cnt')
                            ->value('markup_percent');

    // Cast to int for JS — avoids null / float weirdness
    $sliderDefault = (int) ($globalMarkupValue ?? 0);

@endphp

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Price Markup Manager</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Markup</li>
                        </ol>
                </div>
            </div>
        </div>

        <div class="content-body">

            {{-- ── Flash message ────────────────────────────────────────────── --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- GLOBAL MARKUP BANNER                                           --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div class="global-markup-bar mb-2">

                {{-- Current value (big) --}}
                <div class="gmb-left">
                    <div class="gmb-label">Global markup</div>
                    <div class="gmb-value-row">
                        @if($globalMarkupValue)
                            <span class="gmb-pct">{{ $globalMarkupValue }}%</span>
                            <span class="gmb-active-badge">
                                <i data-feather="zap" style="width:12px;height:12px;"></i> active
                            </span>
                        @else
                            <span class="gmb-pct">0%</span>
                            <span class="gmb-inactive-badge">not set</span>
                        @endif
                    </div>
                </div>

                {{-- Edit button --}}
                <div class="gmb-actions">
                    <button type="button"
                            class="btn btn-warning btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="#globalMarkupModal">
                        <i data-feather="edit-2" style="width:14px;height:14px;"></i>
                        Edit global markup
                    </button>
                </div>
            </div>
            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- CATEGORY TABLE                                                 --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <section id="markup-manager">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Category &amp; Subcategory Markup</h4>
                                <div class="d-flex gap-2">
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           placeholder="Search categories…"
                                           id="categorySearch">
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:30%;">Name</th>
                                                <th style="width:30%;">Subcategories</th>
                                                <th style="width:20%;">Category markup</th>
                                                <th style="width:20%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categories as $category)
                                                @php
                                                    $subcatsWithMarkup = $category->subcategories
                                                        ->filter(fn($s) => !is_null($s->markup_percent));
                                                    $hasSubcatMarkups  = $subcatsWithMarkup->isNotEmpty();
                                                @endphp
                                                <tr>
                                                    {{-- Name --}}
                                                    <td data-label="Name">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <i data-feather="folder" class="text-primary"></i>
                                                            <strong>{{ $category->name }}</strong>
                                                        </div>
                                                    </td>

                                                    {{-- Subcategories --}}
                                                    <td data-label="Subcategories" class="subcategories-cell">
                                                        @if($category->subcategories->isNotEmpty())
                                                            <div class="d-flex align-items-center gap-2 flex-wrap">

                                                                {{-- Dropdown --}}
                                                                <div class="dropdown">
                                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                                            type="button"
                                                                            id="dropdownMenu-{{ $category->id }}"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                        Subcategories ({{ $category->subcategories->count() }})
                                                                    </button>
                                                                 <ul class="dropdown-menu p-0"
    aria-labelledby="dropdownMenu-{{ $category->id }}">
    @foreach ($category->subcategories as $Subcategory)
        @php
            $effectiveMarkup = $Subcategory->markup_percent ?? $category->markup_percent ?? 0;
            $isInherited = is_null($Subcategory->markup_percent);
        @endphp
        <li class="border-bottom">
            <div class="dropdown-item-text d-flex justify-content-between align-items-center px-3 py-2 m-0"
                 style="cursor:pointer;"
                 onclick="event.stopPropagation(); setSubcategoryMarkup(
                     {{ $Subcategory->id }},
                     '{{ addslashes($Subcategory->name) }}',
                     {{ $effectiveMarkup }}
                 )">
                <div class="d-flex align-items-center gap-2">
                    <i data-feather="folder" class="text-success" style="width:16px;height:16px;"></i>
                    <span class="Subcategory-name">{{ $Subcategory->name }}</span>
                    @if(!$isInherited)
                        <span class="badge rounded-pill badge-light-success ms-1">
                            {{ $effectiveMarkup }}%
                        </span>
                    @else
                        <span class="badge rounded-pill markup-null-badge ms-1"
                              title="Inherited from category">
                            {{ $effectiveMarkup }}%
                        </span>
                    @endif
                </div>
                {{-- Clear individual subcategory --}}
                <div class="d-flex gap-1" onclick="event.stopPropagation();">
                    @if(!is_null($Subcategory->markup_percent))
                        <form method="POST"
                              action="{{ route('admin.markup.Subcategory.clear', $Subcategory) }}"
                              class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger px-2"
                                    title="Clear this subcategory's markup">
                                <i data-feather="x" style="width:14px;height:14px;"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </li>
    @endforeach
</ul>
                                                                </div>

                                                                {{-- Override count badge + clear-all button --}}
                                                                @if($hasSubcatMarkups)
                                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                                        @foreach($subcatsWithMarkup->take(3) as $sub)
                                                                            <span class="badge badge-light-info"
                                                                                  style="font-size:0.7rem;cursor:default;"
                                                                                  title="{{ $sub->name }}: {{ $sub->markup_percent }}%">
                                                                                {{ \Illuminate\Support\Str::limit($sub->name, 12, '…') }} ({{ $sub->markup_percent }}%)
                                                                            </span>
                                                                        @endforeach
                                                                        @if($subcatsWithMarkup->count() > 3)
                                                                            <span class="badge bg-secondary bg-opacity-25 text-secondary" style="font-size:0.7rem;">
                                                                                +{{ $subcatsWithMarkup->count() - 3 }} more
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    <form method="POST"
                                                                          action="{{ route('admin.markup.category.clearSubcategories', $category) }}"
                                                                          class="d-inline"
                                                                          onsubmit="return confirm('Clear all subcategory markup overrides for \'{{ addslashes($category->name) }}\'?')">
                                                                        @csrf @method('DELETE')
                                                                        <button type="submit"
                                                                                class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                                                title="Clear all subcategory overrides">
                                                                            <i data-feather="x-circle" style="width:13px;height:13px;"></i>
                                                                            Clear all
                                                                        </button>
                                                                    </form>
                                                                @endif

                                                            </div>
                                                        @else
                                                            <span class="text-muted">No subcategories</span>
                                                        @endif
                                                    </td>

                                                    {{-- Category markup — always numeric --}}
                                                    <td data-label="Category markup">
                                                        @if(!is_null($category->markup_percent))
                                                            <span class="badge rounded-pill badge-light-primary">{{ $category->markup_percent }}%</span>
                                                        @else
                                                            <span class="badge rounded-pill markup-null-badge">0%</span>
                                                        @endif
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td data-label="Actions" class="actions-cell">
                                                        <div class="d-flex gap-1 flex-wrap">
                                                            <button type="button"
                                                                    class="btn btn-warning btn-sm"
                                                                    onclick="setCategoryMarkup(
                                                                        {{ $category->id }},
                                                                        '{{ addslashes($category->name) }}',
                                                                        {{ $category->markup_percent ?? 0 }}
                                                                    )">
                                                                <i class="fas fa-tag me-1"></i> Set markup
                                                            </button>
                                                            @if(!is_null($category->markup_percent))
                                                                <form method="POST"
                                                                      action="{{ route('admin.markup.category.clear', $category) }}">
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

        </div>{{-- /content-body --}}
    </div>{{-- /content-wrapper --}}
</div>{{-- /app-content --}}


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- GLOBAL MARKUP MODAL                                                        --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="globalMarkupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">Global markup</h5>
                    <small class="text-muted">Applies to every product</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.markup.bulk') }}">
                @csrf
                <div class="modal-body">

                    {{-- ── Current value box (always visible) ──────────────── --}}
                    <div class="global-current-display mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small mb-1">Current global markup</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="global-current-pct">{{ $globalMarkupValue ?? 0 }}%</span>
                                    @if($globalMarkupValue)
                                        <span class="badge badge-light-warning">active</span>
                                    @else
                                        <span class="badge markup-null-badge">not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($productMarkupValues->count() > 1)
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1" style="font-size:0.7rem;">
                                Active markup values across products:
                            </small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($productMarkupValues as $val)
                                    <span class="badge rounded-pill badge-light-warning" style="font-size:0.7rem;">{{ $val }}%</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Warning note --}}
                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="alert-triangle" style="width:14px;height:14px;"></i>
                        Overrides all category &amp; subcategory markups for every product.
                    </div>

                    {{-- Slider --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold mb-2">Set new global markup</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range"
                                   class="form-range flex-grow-1"
                                   name="markup_percent"
                                   min="0"
                                   max="100"
                                   step="1"
                                   value="{{ $sliderDefault }}"
                                   id="globalMarkupSlider"
                                   oninput="updateGlobalSlider()">
                            <span class="badge bg-warning text-dark px-3 py-2"
                                  id="globalMarkupValueBadge"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">
                                {{ $sliderDefault }}%
                            </span>
                        </div>

                        {{-- Before → After row --}}
                        <div class="global-change-preview mt-2"
                             id="globalChangePreview"
                             style="{{ $globalMarkupValue ? '' : 'display:none;' }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small">Before:</span>
                                <span class="badge badge-light-secondary" id="globalBeforeVal">{{ $sliderDefault }}%</span>
                                <i data-feather="arrow-right" style="width:14px;height:14px;color:#6e84a3;"></i>
                                <span class="text-muted small">After:</span>
                                <span class="badge badge-light-warning" id="globalAfterVal">{{ $sliderDefault }}%</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                        <i data-feather="zap" style="width:14px;height:14px;"></i>
                        Apply to all products
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- CATEGORY MARKUP MODAL                                                      --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="categoryMarkupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set category markup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryMarkupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Category</label>
                        <p class="fw-bold mb-0" id="categoryMarkupName"></p>
                    </div>

                    {{-- Before → After --}}
                    <div class="markup-current-display mb-3" id="categoryCurrentDisplay">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Current:</span>
                            <span class="badge badge-light-primary" id="categoryCurrentVal">0%</span>
                            <i data-feather="arrow-right" style="width:14px;height:14px;color:#6e84a3;"></i>
                            <span class="text-muted small">New:</span>
                            <span class="badge badge-light-warning" id="categoryNewVal">0%</span>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Affects all products and subcategories within this category.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Markup percentage</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1"
                                   name="markup_percent" min="0" max="100" step="1" value="0"
                                   id="categoryMarkupSlider"
                                   oninput="updateCategoryMarkupPreview()">
                            <span class="badge bg-primary px-3 py-2"
                                  id="categoryMarkupValue"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">0%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply markup</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SUBCATEGORY MARKUP MODAL                                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="SubcategoryMarkupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set subcategory markup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="SubcategoryMarkupForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small mb-1">Subcategory</label>
                        <p class="fw-bold mb-0" id="SubcategoryMarkupName"></p>
                    </div>

                    {{-- Before → After --}}
                    <div class="markup-current-display mb-3" id="SubcategoryCurrentDisplay">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Current:</span>
                            <span class="badge badge-light-success" id="SubcategoryCurrentVal">0%</span>
                            <i data-feather="arrow-right" style="width:14px;height:14px;color:#6e84a3;"></i>
                            <span class="text-muted small">New:</span>
                            <span class="badge badge-light-warning" id="SubcategoryNewVal">0%</span>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size:0.78rem;">
                        <i data-feather="info" style="width:14px;height:14px;"></i>
                        Overrides the category markup for all products in this subcategory.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Markup percentage</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1"
                                   name="markup_percent" min="0" max="100" step="1" value="0"
                                   id="SubcategoryMarkupSlider"
                                   oninput="updateSubcategoryMarkupPreview()">
                            <span class="badge bg-success px-3 py-2"
                                  id="SubcategoryMarkupValue"
                                  style="font-size:1.1rem;min-width:56px;text-align:center;">0%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply markup</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- STYLES                                                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<style>
/* ── Null/unset markup badge ───────────────────────────────────────────────── */
.markup-null-badge {
    background: rgba(108,117,125,0.12);
    color: #6c757d;
    border: 1px solid rgba(108,117,125,0.25);
    font-weight: 600;
}

/* ── Global markup banner ──────────────────────────────────────────────────── */
.global-markup-bar {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1rem 1.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #ff9f43;
    border-radius: 8px;
    flex-wrap: wrap;
}
.gmb-left      { display:flex; flex-direction:column; gap:4px; min-width:120px; }
.gmb-label     { font-size:0.72rem; text-transform:uppercase; letter-spacing:0.06em; color:#6e84a3; font-weight:600; }
.gmb-value-row { display:flex; align-items:center; gap:10px; }
.gmb-pct       { font-size:2rem; font-weight:700; color:#2c3e50; line-height:1; }
.gmb-active-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:rgba(255,159,67,0.15); color:#e08a00;
    font-size:0.72rem; font-weight:600;
    padding:3px 8px; border-radius:20px;
    border:1px solid rgba(255,159,67,0.35);
}
.gmb-inactive-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:rgba(108,117,125,0.1); color:#6c757d;
    font-size:0.72rem; font-weight:600;
    padding:3px 8px; border-radius:20px;
    border:1px solid rgba(108,117,125,0.2);
}
.gmb-actions   { margin-left:auto; }

/* ── Global modal — current-value box ─────────────────────────────────────── */
.global-current-display {
    background:#f8f9fa;
    border:1px solid #e2e8f0;
    border-radius:8px;
    padding:0.85rem 1rem;
}
.global-current-pct {
    font-size:1.75rem;
    font-weight:700;
    color:#2c3e50;
    line-height:1;
}

/* ── Before → After boxes ──────────────────────────────────────────────────── */
.markup-current-display {
    background:#f8f9fa;
    border:1px solid #e2e8f0;
    border-radius:6px;
    padding:0.6rem 0.85rem;
}
.global-change-preview {
    background:#fff8ec;
    border:1px solid #ffe0a3;
    border-radius:6px;
    padding:0.5rem 0.75rem;
}

/* ── Table ─────────────────────────────────────────────────────────────────── */
.table-hover tbody tr:hover { background-color:#f8f9fa; }

.dropdown-menu {
    min-width:360px;
    max-height:300px;
    overflow-y:auto;
    border-radius:8px;
    box-shadow:0 4px 16px rgba(0,0,0,0.12);
    border:1px solid #d5d9d9;
}
.dropdown-item-text {
    font-size:0.9rem;
    color:#0f1111;
    display:block;
    width:100%;
    transition:background-color 0.15s ease;
}
.dropdown-item-text:hover { background-color:#f0f2f2; }
.Subcategory-name { font-size:0.9rem; font-weight:500; }

/* ── Responsive ────────────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .global-markup-bar { flex-direction:column; align-items:flex-start; }
        .gmb-actions       { margin-left:0; width:100%; }
    .gmb-actions .btn  { width:100%; justify-content:center; }

    .table thead { display:none; }
    .table tr {
        display:flex; flex-direction:column;
        margin-bottom:1.5rem;
        border:1px solid #e0e0e0;
        border-radius:8px;
        padding:1rem;
        background:#fff;
    }
    .table td {
        display:flex; align-items:center; justify-content:space-between;
        padding:0.6rem 0; border:none;
    }
    .table td::before { content:attr(data-label); font-weight:600; color:#37475a; flex:1; }
    .table td[data-label="Name"]            { order:1; font-size:1.1rem; }
    .table td[data-label="Category markup"] { order:2; }
    .table td[data-label="Subcategories"]   { order:3; border-top:1px solid #e0e0e0; padding-top:0.85rem; margin-top:0.85rem; flex-wrap:wrap; }
    .table td[data-label="Actions"]         { order:4; justify-content:flex-end; gap:0.5rem; }
    .dropdown, .dropdown-toggle { width:100%; }
    .dropdown-menu { width:100%; min-width:100%; }
}
</style>


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                                    --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<script>
const GLOBAL_PREVIOUS = {{ $sliderDefault }};

document.addEventListener('DOMContentLoaded', function () {

    /* Bootstrap dropdowns */
    document.querySelectorAll('.dropdown-toggle').forEach(function (el) {
        new bootstrap.Dropdown(el);
    });

    /* Feather icons — initial render */
    if (typeof feather !== 'undefined') feather.replace();

    /* Re-render feather icons every time a modal opens */
    document.querySelectorAll('.modal').forEach(function (el) {
        el.addEventListener('shown.bs.modal', function () {
            if (typeof feather !== 'undefined') feather.replace();
        });
    });

    /* Close dropdown when a subcategory row is clicked */
    document.querySelectorAll('.dropdown-item-text').forEach(function (item) {
        item.addEventListener('click', function (e) {
            // Only close if the click was on the item itself, not on the clear button
            if (!e.target.closest('form') && !e.target.closest('button')) {
                const toggle = this.closest('.dropdown')?.querySelector('.dropdown-toggle');
                const dd = toggle ? bootstrap.Dropdown.getInstance(toggle) : null;
                if (dd) dd.hide();
            }
        });
    });

    /* Category search filter */
    document.getElementById('categorySearch').addEventListener('input', function (e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(function (row) {
            const cell = row.querySelector('td[data-label="Name"]');
            if (cell) row.style.display = cell.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });
});

/* ── Global markup slider ─────────────────────────────────────────────────── */
function updateGlobalSlider() {
    const val     = parseInt(document.getElementById('globalMarkupSlider').value);
    const badge   = document.getElementById('globalMarkupValueBadge');
    const preview = document.getElementById('globalChangePreview');
    const after   = document.getElementById('globalAfterVal');

    badge.textContent = val + '%';

    if (GLOBAL_PREVIOUS > 0) {
        preview.style.display = '';
        after.textContent     = val + '%';
        after.className       = 'badge ' + (
            val > GLOBAL_PREVIOUS ? 'badge-light-danger'  :
            val < GLOBAL_PREVIOUS ? 'badge-light-success' :
            'badge-light-warning'
        );
    }
}

/* ── Category markup modal ────────────────────────────────────────────────── */
function setCategoryMarkup(categoryId, categoryName, currentMarkup) {
    const markup = currentMarkup || 0;
    
    console.log('Setting category markup:', { categoryId, categoryName, currentMarkup, markup });

    document.getElementById('categoryMarkupName').textContent  = categoryName;
    document.getElementById('categoryMarkupSlider').value      = markup;
    document.getElementById('categoryMarkupValue').textContent = markup + '%';
    document.getElementById('categoryCurrentVal').textContent  = markup + '%';
    document.getElementById('categoryNewVal').textContent      = markup + '%';
    document.getElementById('categoryMarkupForm').action       = '/admin/markup/category/' + categoryId;

    // Always show current display for context
    document.getElementById('categoryCurrentDisplay').style.display = '';

    new bootstrap.Modal(document.getElementById('categoryMarkupModal')).show();
}

function updateCategoryMarkupPreview() {
    const val     = parseInt(document.getElementById('categoryMarkupSlider').value);
    const current = parseInt(document.getElementById('categoryCurrentVal').textContent) || 0;
    const newEl   = document.getElementById('categoryNewVal');

    document.getElementById('categoryMarkupValue').textContent = val + '%';
    newEl.textContent = val + '%';
    newEl.className   = 'badge ' + (
        val > current ? 'badge-light-danger'  :
        val < current ? 'badge-light-success' :
        'badge-light-warning'
    );
}

/* ── Subcategory markup modal ─────────────────────────────────────────────── */
function setSubcategoryMarkup(SubcategoryId, SubcategoryName, currentMarkup) {
    const markup = currentMarkup || 0;
    
    console.log('Setting subcategory markup:', { SubcategoryId, SubcategoryName, currentMarkup, markup });

    // Set all display elements with the current markup value
    document.getElementById('SubcategoryMarkupName').textContent  = SubcategoryName;
    document.getElementById('SubcategoryMarkupSlider').value      = markup;
    document.getElementById('SubcategoryMarkupValue').textContent = markup + '%';
    document.getElementById('SubcategoryCurrentVal').textContent  = markup + '%';
    document.getElementById('SubcategoryNewVal').textContent      = markup + '%';
    document.getElementById('SubcategoryMarkupForm').action       = '/admin/markup/Subcategory/' + SubcategoryId;

    // Always show the current display - consistent with category modal
    document.getElementById('SubcategoryCurrentDisplay').style.display = '';

    // Show the modal with a slight delay to ensure DOM is ready
    setTimeout(function() {
        new bootstrap.Modal(document.getElementById('SubcategoryMarkupModal')).show();
    }, 100);
}

function updateSubcategoryMarkupPreview() {
    const val     = parseInt(document.getElementById('SubcategoryMarkupSlider').value);
    const current = parseInt(document.getElementById('SubcategoryCurrentVal').textContent) || 0;
    const newEl   = document.getElementById('SubcategoryNewVal');

    document.getElementById('SubcategoryMarkupValue').textContent = val + '%';
    newEl.textContent = val + '%';
    newEl.className   = 'badge ' + (
        val > current ? 'badge-light-danger'  :
        val < current ? 'badge-light-success' :
        'badge-light-warning'
    );
}
</script>

@endsection