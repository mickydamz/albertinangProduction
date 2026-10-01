@extends('layouts.app')

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="container-fluid">
                <h1 class="text-center my-1">{{ $supplier->name }} Store</h1>

                @if($products->isEmpty())
                    <p class="text-center font-italic text-muted">You have no products listed.</p>
                @else
                    <div class="row match-height">
                        @foreach($products as $product)
                            <div class="col-lg-3 col-md-6 col-12 mb-4">
                                <!-- Make the card clickable -->
                                <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                    <div class="card h-100">
                                        <div class="card-body text-start">
                                            <div class="text-center my-1">
                                                @if($product->images->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" style="width: 12rem; height: 12rem; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <img src="http://127.0.0.1:8000/app-assets/images/illustration/product-placeholder.svg" alt="{{ $product->name }}" style="width: 12rem; height: 12rem; object-fit: cover; border-radius: 8px;">
                                                @endif
                                            </div>
                                            <div class="row border-top mx-0">
                                                <div class="col-12 py-1">
                                                <h3 class="fw-bolder mb-2">{{ \Illuminate\Support\Str::limit($product->name, 12) }}</h3>
                                                <h5 class="fw-bolder mb-2">{{ $product->category->name ?? 'Uncategorized' }}</h5>
                                                    <h5 class="fw-bolder mb-0">${{ number_format($product->price, 2) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
