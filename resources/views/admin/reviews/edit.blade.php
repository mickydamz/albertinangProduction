@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Review</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
            </div>
        </div>

        <div class="content-body">
            <section id="basic-horizontal-layouts">
                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Review</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                  
    {{-- User Search --}}
                                    <div class="mb-1">
                                        <label class="form-label">User:</label>
                                        <div class="position-relative">
                                            <input
                                                type="text"
                                                id="user-search"
                                                class="form-control"
                                                placeholder="Type to search users by name or email…"
                                                autocomplete="off"
                                            >
                                            <div id="user-suggestions" style="
                                                display:none; position:absolute; top:100%; left:0; right:0;
                                                background:#fff; border:1px solid #d8d6de; border-top:none;
                                                border-radius:0 0 0.357rem 0.357rem; max-height:220px;
                                                overflow-y:auto; z-index:1050; box-shadow:0 4px 16px rgba(0,0,0,.1);
                                            "></div>
                                        </div>
                                        <div id="selected-user" style="display:none; margin-top:8px; padding:8px 12px; background:#f0effe; border:1px solid #ddd9fb; border-radius:6px; font-size:13px; color:#5e5873; align-items:center; justify-content:space-between;">
                                            <span id="selected-user-name"></span>
                                            <button type="button" id="clear-user" style="background:none;border:none;color:#ea5455;font-size:16px;cursor:pointer;line-height:1;">×</button>
                                        </div>
                                        <input type="hidden" name="user_id" id="user_id" required>
                                        @error('user_id')
                                            <div class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Product Search --}}
                                    <div class="mb-1">
                                        <label class="form-label">Product:</label>
                                        <div class="position-relative">
                                            <input
                                                type="text"
                                                id="product-search"
                                                class="form-control"
                                                placeholder="Type to search products…"
                                                autocomplete="off"
                                            >
                                            <div id="product-suggestions" style="
                                                display:none; position:absolute; top:100%; left:0; right:0;
                                                background:#fff; border:1px solid #d8d6de; border-top:none;
                                                border-radius:0 0 0.357rem 0.357rem; max-height:220px;
                                                overflow-y:auto; z-index:1050; box-shadow:0 4px 16px rgba(0,0,0,.1);
                                            "></div>
                                        </div>
                                        <div id="selected-product" style="display:none; margin-top:8px; padding:8px 12px; background:#f0effe; border:1px solid #ddd9fb; border-radius:6px; font-size:13px; color:#5e5873; align-items:center; justify-content:space-between;">
                                            <span style="display:flex; align-items:center; gap:10px;">
                                                <img id="selected-product-img" src="" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:4px; border:1px solid #ddd9fb; background:#fff;">
                                                <span id="selected-product-name"></span>
                                            </span>
                                            <button type="button" id="clear-product" style="background:none;border:none;color:#ea5455;font-size:16px;cursor:pointer;line-height:1;">×</button>
                                        </div>
                                        <input type="hidden" name="product_id" id="product_id" required>
                                        @error('product_id')
                                            <div class="text-danger mt-25" style="font-size:.857rem;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Rating Input -->
                                    <div class="mb-1">
                                        <label for="rating" class="form-label">Rating (1-5):</label>
                                        <input type="number" name="rating" id="rating" class="form-control" min="1" max="5" value="{{ $review->rating }}" required>
                                    </div>

                                    <!-- Review Content -->
                                    <div class="mb-1">
                                        <label for="content" class="form-label">Review Content:</label>
                                        <textarea name="content" id="content" class="form-control" rows="10" required>{{ $review->content }}</textarea>
                                    </div>

                                    <!-- Date Input -->
                                    <div class="mb-1">
                                        <label for="date" class="form-label">Review Date:</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{ $review->created_at->format('Y-m-d') }}">
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary">Update Review</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

@php
    $productPayload = $products->map(fn ($p) => [
        'id'    => $p->id,
        'name'  => $p->name,
        'image' => $p->images->isNotEmpty() ? asset('storage/' . $p->images->first()->image_url) : null,
    ])->values();

    $userPayload = $users->map(fn ($u) => [
        'id'    => $u->id,
        'name'  => $u->name,
        'email' => $u->email,
    ])->values();
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($productPayload);
    const users    = @json($userPayload);

    const PLACEHOLDER_IMG = 'https://placehold.co/80x80/eef3e8/3d8012?text=No+Image';

    // ── Generic searchable picker ─────────────────────────────────────────────
    // Wires a text input + suggestions dropdown + selected chip + hidden field.
    // `withImage` renders a thumbnail in both the suggestions and the chip.
    function initPicker(cfg) {
        const searchInput    = document.getElementById(cfg.search);
        const suggestionsBox = document.getElementById(cfg.suggestions);
        const hiddenInput    = document.getElementById(cfg.hidden);
        const selectedBox    = document.getElementById(cfg.selected);
        const selectedName   = document.getElementById(cfg.selectedName);
        const selectedImg    = cfg.selectedImg ? document.getElementById(cfg.selectedImg) : null;
        const clearBtn       = document.getElementById(cfg.clear);

        function matchText(item) {
            return (item.name + ' ' + (item.email || '')).toLowerCase();
        }

        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            suggestionsBox.innerHTML = '';

            if (!q) { suggestionsBox.style.display = 'none'; return; }

            const hits = cfg.items.filter(item => matchText(item).includes(q)).slice(0, 15);

            if (!hits.length) {
                suggestionsBox.innerHTML = '<div style="padding:10px 14px;font-size:13px;color:#b9b9c3;">No ' + cfg.noun + ' found.</div>';
                suggestionsBox.style.display = 'block';
                return;
            }

            hits.forEach(item => {
                const row = document.createElement('div');
                row.style.cssText = 'display:flex;align-items:center;gap:10px;padding:9px 14px;font-size:13px;cursor:pointer;color:#6e6b7b;border-bottom:1px solid #f0f0f0;';

                if (cfg.withImage) {
                    const img = document.createElement('img');
                    img.src = item.image || PLACEHOLDER_IMG;
                    img.alt = '';
                    img.style.cssText = 'width:34px;height:34px;object-fit:cover;border-radius:4px;border:1px solid #ebe9f1;background:#fff;flex:0 0 auto;';
                    img.onerror = function () { this.src = PLACEHOLDER_IMG; };
                    row.appendChild(img);
                }

                const label = document.createElement('span');
                label.textContent = cfg.label(item);
                row.appendChild(label);

                row.addEventListener('mouseenter', () => row.style.background = '#f0effe');
                row.addEventListener('mouseleave', () => row.style.background = '');
                row.addEventListener('mousedown', e => {
                    e.preventDefault();
                    select(item);
                });
                suggestionsBox.appendChild(row);
            });

            suggestionsBox.style.display = 'block';
        });

        searchInput.addEventListener('blur', () => {
            setTimeout(() => suggestionsBox.style.display = 'none', 150);
        });

        function select(item) {
            hiddenInput.value         = item.id;
            selectedName.textContent  = cfg.label(item);
            if (selectedImg) selectedImg.src = item.image || PLACEHOLDER_IMG;
            selectedBox.style.display = 'flex';
            searchInput.value         = '';
            searchInput.style.display = 'none';
            suggestionsBox.style.display = 'none';
        }

        clearBtn.addEventListener('click', function () {
            hiddenInput.value         = '';
            selectedBox.style.display = 'none';
            searchInput.style.display = '';
            searchInput.value         = '';
            searchInput.focus();
        });

        return { select };
    }

    const productPicker = initPicker({
        items: products, noun: 'products', withImage: true,
        search: 'product-search', suggestions: 'product-suggestions',
        hidden: 'product_id', selected: 'selected-product',
        selectedName: 'selected-product-name', selectedImg: 'selected-product-img',
        clear: 'clear-product',
        label: p => p.name,
    });

    const userPicker = initPicker({
        items: users, noun: 'users', withImage: false,
        search: 'user-search', suggestions: 'user-suggestions',
        hidden: 'user_id', selected: 'selected-user',
        selectedName: 'selected-user-name', clear: 'clear-user',
        label: u => u.email ? (u.name + ' (' + u.email + ')') : u.name,
    });

    // Pre-select the review's current product and user (respecting old() on failure).
    const currentProductId = {{ (int) old('product_id', $review->product_id) }};
    const currentUserId    = {{ (int) old('user_id', $review->user_id) }};

    const currentProduct = products.find(p => p.id === currentProductId);
    if (currentProduct) productPicker.select(currentProduct);

    const currentUser = users.find(u => u.id === currentUserId);
    if (currentUser) userPicker.select(currentUser);
});
</script>
@endsection
