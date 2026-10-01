@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        {{-- ══════════════ Breadcrumb ══════════════ --}}
        <div class="content-header row">
            <div class="col-12 mb-1">
                <h2 class="content-header-title float-start mb-0">Product</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">#{{ $product->id }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            @php
                $images    = $product->images;
                $first     = $images->first();
                $mainUrl   = $first ? asset('storage/' . $first->image_url) : 'https://placehold.co/500x400/eef3e8/3d8012?text=No+Image';
                $inStock   = (int) $product->stock > 0;
                $hasOld    = $product->old_price && $product->old_price > $product->price;
                $discount  = $hasOld ? round((($product->old_price - $product->price) / $product->old_price) * 100) : 0;
                $rating    = $product->review_rating;   // live review average (0 when no real reviews)
            @endphp

            {{-- ══════════════ Hero header ══════════════ --}}
            <div class="card mb-2">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $mainUrl }}" alt="{{ $product->name }}" width="72" height="72"
                             class="rounded border" style="object-fit:cover;background:#f8f9fa;"
                             onerror="this.src='https://placehold.co/72x72/eef3e8/3d8012?text=+'">
                        <div>
                            <h3 class="mb-25">{{ $product->name }}</h3>
                            <span class="fw-bolder fs-4 text-success">&#8358;{{ number_format((float) $product->price, 0) }}</span>
                            @if($hasOld)
                                <s class="text-muted ms-50">&#8358;{{ number_format((float) $product->old_price, 0) }}</s>
                                @if($discount >= 5)<span class="badge bg-danger ms-50">-{{ $discount }}%</span>@endif
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }} fs-6">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-50"></i>Edit
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-50"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- ══════════════ Stat cards ══════════════ --}}
            <div class="row g-2 mb-2">
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-success rounded"><div class="avatar-content"><i class="fas fa-tag"></i></div></div>
                        <div><h4 class="mb-0">&#8358;{{ number_format((float) $product->price, 0) }}</h4><small class="text-muted">Price</small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-{{ $inStock ? 'primary' : 'danger' }} rounded"><div class="avatar-content"><i class="fas fa-boxes-stacked"></i></div></div>
                        <div><h4 class="mb-0">{{ (int) $product->stock }}</h4><small class="text-muted">{{ $inStock ? 'In stock' : 'Out of stock' }}</small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-warning rounded"><div class="avatar-content"><i class="fas fa-star"></i></div></div>
                        <div><h4 class="mb-0">{{ number_format($rating, 1) }}</h4><small class="text-muted">{{ $product->review_rating_count }} review(s)</small></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="card mb-0 h-100"><div class="card-body d-flex align-items-center gap-1">
                        <div class="avatar bg-light-info rounded"><div class="avatar-content"><i class="fas fa-copyright"></i></div></div>
                        <div><h4 class="mb-0 text-truncate" style="max-width:120px;">{{ $product->brand->name ?? $product->brand ?? '—' }}</h4><small class="text-muted">Brand</small></div>
                    </div></div>
                </div>
            </div>

            <div class="row g-2">
                {{-- Gallery --}}
                <div class="col-lg-5 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom"><h4 class="card-title"><i class="fas fa-images text-primary me-50"></i>Gallery</h4></div>
                        <div class="card-body text-center pt-1">
                            <img src="{{ $mainUrl }}" alt="{{ $product->name }}" class="img-fluid rounded mb-1"
                                 style="max-height:280px;object-fit:contain;"
                                 onerror="this.src='https://placehold.co/500x400/eef3e8/3d8012?text=No+Image'">
                            <div class="d-flex flex-wrap justify-content-center gap-50">
                                @forelse($images->take(6) as $img)
                                    <img src="{{ asset('storage/' . $img->image_url) }}" width="56" height="56"
                                         class="rounded border" style="object-fit:cover;" alt="thumb"
                                         onerror="this.style.display='none';">
                                @empty
                                    <small class="text-muted">No images uploaded.</small>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div class="col-lg-7 col-12">
                    <div class="card h-100 mb-0">
                        <div class="card-header border-bottom"><h4 class="card-title"><i class="fas fa-circle-info text-primary me-50"></i>Details</h4></div>
                        <div class="card-body pt-1">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Category</span><strong>{{ $product->category->name ?? '—' }}</strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Subcategory</span><strong>{{ $product->Subcategory->name ?? '—' }}</strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Brand</span><strong>{{ $product->brand->name ?? $product->brand ?? '—' }}</strong></li>
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Price</span><strong class="text-success">&#8358;{{ number_format((float) $product->price, 0) }}</strong></li>
                                @if($hasOld)
                                    <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Old Price</span><s class="text-muted">&#8358;{{ number_format((float) $product->old_price, 0) }}</s></li>
                                @endif
                                <li class="d-flex justify-content-between py-50 border-bottom"><span class="text-muted">Stock</span><strong class="text-{{ $inStock ? 'success' : 'danger' }}">{{ (int) $product->stock }}</strong></li>
                                <li class="d-flex justify-content-between py-50"><span class="text-muted">Created</span><strong>{{ $product->created_at?->format('d M Y') ?? '—' }}</strong></li>
                            </ul>

                            @if($product->description)
                                <hr>
                                <h6 class="fw-bolder"><i class="fas fa-align-left text-muted me-50"></i>Description</h6>
                                <p class="text-muted mb-0">{{ $product->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
