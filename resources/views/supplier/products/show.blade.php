@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Add this to your layout file if Bootstrap isn't already included -->
<!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
        </div>
        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success">{!! session('success') !!}</div>
            @endif

            <section class="app-ecommerce-details">
                <div class="card">
                    <div class="card-body">
                        <div class="row my-2">
                        <div class="col-12 col-md-5 d-flex align-items-center justify-content-center mb-2 mb-md-0">
    <div id="productCarousel" class="carousel slide" data-ride="carousel" style="width: 350px; height: 250px;">
        <div class="carousel-inner">
        @foreach ($product->images as $index => $image)
 
    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
        <img src="{{ asset('storage/' . $image->image_url) }}" class="img-fluid" alt="Product Image {{ $index + 1 }}">
    </div>
@endforeach

        </div>
        <!-- Carousel controls -->
        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

                            <div class="col-12 col-md-7">
                                <h4>{{ $product->name }}</h4>
                                <!-- <span class="card-text item-company">By <a href="{{ route('supplier.profile', ['supplier' => $product->supplier->id]) }}" class="company-name">{{ $product->supplier->name }}</a></span> -->
                                <div class="ecommerce-details-price d-flex flex-wrap mt-1">
                                    <h4 class="item-price me-1">${{ number_format($product->price, 2) }}</h4>
                                    <ul class="unstyled-list list-inline ps-1 border-start">
                                        @for ($i = 0; $i < 5; $i++)
                                            <li class="ratings-list-item">
                                                <i data-feather="star" class="{{ $i < $product->review_rating ? 'filled-star' : 'unfilled-star' }}"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                </div>
                                <p class="card-text">Available - <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">{{ $product->stock > 0 ? 'In stock' : 'Out of stock' }}</span></p>
                                <p class="card-text">{{ $product->description }}</p>
                                <ul class="product-features list-unstyled">
                                    <li><i data-feather="shopping-cart"></i> <span>Free Shipping</span></li>
                                    <li>
                                        <i data-feather="dollar-sign"></i>
                                        <span>EMI options available</span>
                                    </li>
                                </ul>
                                <hr />
                                <div class="d-flex flex-column flex-sm-row pt-1">
                                  
                                    <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary me-0 me-sm-1 mb-1 mb-sm-0">
                                        <!-- <i data-feather="arrow-left" class="me-50"></i> -->
                                        <span>All Products</span>
                                    </a>
                                    <!-- <div class="btn-group dropdown-icon-wrapper btn-share">
                                        <a href="{{ route('cart.index') }}" class="btn btn-primary me-0 me-sm-1 mb-1 mb-sm-0">
                                            <i data-feather="shopping-cart" class="me-50"></i>
                                            <span>Proceed</span>
                                        </a>
                                       
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>


<style>
    .carousel-control-prev-icon, 
    .carousel-control-next-icon {
        background-color: #5aab1f; /* Set the background color to blue */
        color: black;
    }
</style>

                  
                    <!-- Item features ends -->
                </div>
            </section>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
